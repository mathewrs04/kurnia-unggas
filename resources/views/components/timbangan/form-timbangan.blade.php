<div>
    <button type="button" class="btn {{ $id ? 'btn-default' : 'btn-primary' }}" data-toggle="modal"
        data-target="#formTimbangan{{ $id ?? '' }}">
        {{ $id ? 'Edit' : 'Tambah Timbangan' }}
    </button>

    <div class="modal fade" id="formTimbangan{{ $id ?? '' }}">
        <form action="{{ route('master.timbangan.store') }}" method="POST">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $id ? 'Edit Timbangan' : 'Tambah Timbangan' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id ?? '' }}">

                        {{-- Jenis --}}
                        <div class="form-group">
                            <label for="">Jenis</label>
                            <select class="form-control" id="jenis" name="jenis">
                                <option value="">Pilih Jenis</option>
                                @php
                                    $enumJenis = [
                                        'timbangan data DO' => 'Timbangan Data DO',
                                        'timbangan data pembelian' => 'Timbangan Data Pembelian',
                                        'timbangan data penjualan' => 'Timbangan Data Penjualan',
                                        'timbangan stok opname' => 'Timbangan Stok Opname',
                                    ];
                                    $selectedJenis = $id ? $jenis : old('jenis');
                                @endphp
                                @foreach ($enumJenis as $key => $label)
                                    <option value="{{ $key }}" {{ $selectedJenis == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nama Karyawan --}}
                        <div class="form-group">
                            <label for="">Nama Karyawan</label>
                            <input type="text" class="form-control" id="nama_karyawan" name="nama_karyawan"
                                value="{{ $nama_karyawan }}">
                        </div>

                        {{-- Keranjang --}}
                        <div class="form-group">
                            <label>Keranjang</label>
                            <table class="table table-bordered" id="keranjangTable{{ $id ?? 'New' }}">
                                <thead>
                                    <tr>
                                        <th>Jumlah Ekor</th>
                                        <th>Berat (kg)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(old('keranjang'))
                                        @foreach(old('keranjang') as $i => $keranjang)
                                            <tr>
                                                <td>
                                                    <input type="number" name="keranjang[{{ $i }}][jumlah_ekor]"
                                                        class="form-control" value="{{ $keranjang['jumlah_ekor'] }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="keranjang[{{ $i }}][berat]"
                                                        class="form-control" value="{{ $keranjang['berat'] }}" required>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm removeKeranjang">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <input type="number" name="keranjang[0][jumlah_ekor]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="keranjang[0][berat]" class="form-control" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm removeKeranjang">Hapus</button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success btn-sm" id="addKeranjang{{ $id ?? 'New' }}">
                                Tambah Keranjang
                            </button>
                        </div>

                        {{-- 🔹 Inline Script (langsung jalan, tidak pakai @push/@stack) --}}
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                let keranjangTable = document.querySelector('#keranjangTable{{ $id ?? "New" }} tbody');
                                let addKeranjangBtn = document.getElementById('addKeranjang{{ $id ?? "New" }}');

                                if (addKeranjangBtn) {
                                    addKeranjangBtn.addEventListener('click', function () {
                                        let rowCount = keranjangTable.rows.length;
                                        let row = keranjangTable.insertRow();
                                        row.innerHTML = `
                                            <td>
                                                <input type="number" name="keranjang[${rowCount}][jumlah_ekor]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="keranjang[${rowCount}][berat]" class="form-control" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm removeKeranjang">Hapus</button>
                                            </td>
                                        `;
                                    });
                                }

                                if (keranjangTable) {
                                    keranjangTable.addEventListener('click', function (e) {
                                        if (e.target.classList.contains('removeKeranjang')) {
                                            e.target.closest('tr').remove();
                                        }
                                    });
                                }
                            });
                        </script>

                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </form>
    </div>
</div>
