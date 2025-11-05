<div>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#formDeliveryOrder">
        Tambah Delivery Order
    </button>

    <div class="modal fade" id="formDeliveryOrder">
        <form action="{{ route('master.delivery-order.store') }}" method="POST">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Delivery Order</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @csrf

                        {{-- Pilih Timbangan Jenis DO --}}
                        <div class="form-group">
                            <label for="jenis_timbangan">Jenis Timbangan</label>
                            <input type="text" id="jenis_timbangan" class="form-control" value="Timbangan Data DO"
                                readonly>
                        </div>


                        {{-- Keranjang --}}
                        <div class="form-group">
                            <label>Keranjang</label>
                            <table class="table table-bordered" id="keranjangTableDO">
                                <thead>
                                    <tr>
                                        <th>Jumlah Ekor</th>
                                        <th>Berat (kg)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="number" name="keranjang[0][jumlah_ekor]" class="form-control"
                                                required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="keranjang[0][berat]"
                                                class="form-control berat-input" required>
                                        </td>
                                        <td>
                                            <button type="button"
                                                class="btn btn-danger btn-sm removeKeranjang">Hapus</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success btn-sm" id="addKeranjangDO">
                                Tambah Keranjang
                            </button>
                        </div>

                        {{-- Total Berat --}}
                        <div class="form-group">
                            <label>Total Berat (kg)</label>
                            <input type="text" class="form-control" id="totalBeratDO" name="total_berat" readonly>
                        </div>

                        {{-- 🔹 Script inline --}}
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                let keranjangTable = document.querySelector('#keranjangTableDO tbody');
                                let addKeranjangBtn = document.getElementById('addKeranjangDO');
                                let totalBeratInput = document.getElementById('totalBeratDO');

                                // Hitung total berat
                                function hitungTotalBerat() {
                                    let total = 0;
                                    keranjangTable.querySelectorAll('.berat-input').forEach(function(input) {
                                        let val = parseFloat(input.value) || 0;
                                        total += val;
                                    });
                                    totalBeratInput.value = total.toFixed(2);
                                }

                                // Tambah keranjang
                                addKeranjangBtn.addEventListener('click', function() {
                                    let rowCount = keranjangTable.rows.length;
                                    let row = keranjangTable.insertRow();
                                    row.innerHTML = `
                                        <td>
                                            <input type="number" name="keranjang[${rowCount}][jumlah_ekor]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="keranjang[${rowCount}][berat]" class="form-control berat-input" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm removeKeranjang">Hapus</button>
                                        </td>
                                    `;
                                    hitungTotalBerat();
                                });

                                // Hapus keranjang
                                keranjangTable.addEventListener('click', function(e) {
                                    if (e.target.classList.contains('removeKeranjang')) {
                                        e.target.closest('tr').remove();
                                        hitungTotalBerat();
                                    }
                                });

                                // Update total berat saat input berubah
                                keranjangTable.addEventListener('input', function(e) {
                                    if (e.target.classList.contains('berat-input')) {
                                        hitungTotalBerat();
                                    }
                                });

                                // Hitung awal
                                hitungTotalBerat();
                            });
                        </script>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Delivery Order</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
