@extends('layouts.app')
@section('content_title', 'Penjualan')
@section('content')

    <x-alert :errors="$errors" />

    <form action="{{ route('penjualan.store') }}" method="POST" id="formPenjualan">
        @csrf


        <div class="card mb-3">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-4 mb-2">
                        <label>Pelanggan</label>
                        <select name="pelanggan_id" class="form-control" required>
                            @foreach ($pelanggans as $p)
                                <option value="{{ $p->id }}" {{ old('pelanggan_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Tanggal Jual</label>
                        <input type="date" name="tanggal_jual" class="form-control" value="{{ old('tanggal_jual') }}"
                            required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>No Nota</label>
                        <input type="text" name="no_nota" class="form-control" value="{{ $noNota }}" readonly>
                    </div>

                </div>
            </div>
        </div>


        <div class="card mb-3">
            <div class="card-header"><b>Penjualan Ayam</b></div>
            <div class="card-body">

                <div class="row">

                    <div class="col-md-4 mb-2">
                        <label>Tipe Penjualan</label>
                        <select name="ayam[tipe_penjualan]" id="tipe_penjualan" class="form-control">
                            <option value="" {{ old('ayam.tipe_penjualan') == '' ? 'selected' : '' }}>-- Tidak Ada --
                            </option>
                            <option value="eceran" {{ old('ayam.tipe_penjualan') == 'eceran' ? 'selected' : '' }}>Eceran
                            </option>
                            <option value="partai" {{ old('ayam.tipe_penjualan') == 'partai' ? 'selected' : '' }}>Partai
                            </option>
                        </select>
                    </div>

                    <div class="col-md-8 mb-2">
                        <label>Batch</label>
                        <select name="ayam[batch_id]" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach ($batches as $b)
                                <option value="{{ $b->id }}" {{ old('ayam.batch_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->kode_batch }} ({{ $b->stok_ekor }} ekor / {{ $b->stok_kg }} kg)
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>


                <div id="eceran" style="display:none" class="mt-2">
                    <div class="row">

                        <div class="col-md-6 mb-2">
                            <label>Jumlah Ekor</label>
                            <input type="number" name="ayam[jumlah_ekor]" class="form-control"
                                value="{{ old('ayam.jumlah_ekor') }}">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Berat (Kg)</label>
                            <input type="number" step="0.01" name="ayam[jumlah_berat]" class="form-control"
                                value="{{ old('ayam.jumlah_berat') }}">
                        </div>

                    </div>
                </div>


                <div id="partai" style="display:none">

                    <table class="table mt-2">
                        <thead>
                            <tr>
                                <th>Ekor</th>
                                <th>Berat Total</th>
                                <th>Berat Keranjang</th>
                                <th>Berat Ayam</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="keranjangBody"></tbody>
                    </table>

                    <button type="button" id="tambahKeranjang" class="btn btn-sm btn-secondary">
                        + Keranjang
                    </button>

                    <div class="row mt-3">

                        <div class="col-md-6 mb-2">
                            <label>Total Berat</label>
                            <input type="number" step="0.01" name="ayam[jumlah_berat]" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Total Ekor</label>
                            <input type="number" name="ayam[jumlah_ekor]" class="form-control" readonly>
                        </div>

                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Karyawan Penanggung Jawab <small class="text-muted">(bisa pilih lebih dari
                                    1)</small></label>
                            <select name="karyawan_ids[]" class="form-control select2-karyawan" multiple="multiple"
                                style="width:100%">
                                @foreach ($karyawans as $k)
                                    <option value="{{ $k->id }}"
                                        {{ in_array($k->id, old('karyawan_ids', [])) ? 'selected' : '' }}>
                                        {{ $k->nama }} ({{ $k->posisi }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>


                <div class="row mt-3">
                    <div class="col-md-4">
                        <label>Harga / Kg (Rp)</label>
                        <input type="number" name="ayam[harga_per_kg]" id="harga_per_kg" class="form-control"
                            value="{{ old('ayam.harga_per_kg') }}">
                    </div>
                    <div class="col-md-4">
                        <label>Subtotal Ayam (Rp)</label>
                        <input type="text" id="subtotal_ayam_display" class="form-control font-weight-bold" readonly
                            value="0" style="background-color: #f8f9fa;">
                    </div>
                </div>

            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><b>Jasa</b></div>
            <div class="card-body">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Jasa</th>
                            <th width="150">Jumlah</th>
                            <th width="200" class="text-right">Subtotal (Rp)</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody id="jasaBody"></tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button type="button" id="tambahJasa" class="btn btn-primary btn-sm">
                        + Tambah Jasa
                    </button>
                    
                    <div class="col-md-4 px-0">
                        <label>Subtotal Jasa (Rp)</label>
                        <input type="text" id="subtotal_jasa_display" class="form-control font-weight-bold" readonly
                            value="0" style="background-color: #f8f9fa;">
                    </div>
                </div>

            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <div class="row align-items-end">
                    
                    <div class="col-md-4">
                        <label>Grand Total (Rp)</label>
                        <input type="text" id="subtotal_display" class="form-control font-weight-bold text-success text-right" readonly value="0" style="font-size: 1.2rem;">
                        <input type="hidden" name="subtotal" value="0">
                    </div>

                    <div class="col-md-4">
                        <label>Diskon (Rp)</label>
                        <input type="number" name="diskon" value="{{ old('diskon', 0) }}" class="form-control text-right text-danger">
                    </div>

                    <div class="col-md-4">
                        <button class="btn btn-success w-100 font-weight-bold" style="font-size: 1.1rem;">
                            <i class="fas fa-save mr-2"></i> Simpan & Cetak
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </form>





@endsection

@push('scripts')
    {{-- ================= SCRIPT ================= --}}
    <script>
        $(document).ready(function() {
            $('.select2-karyawan').select2({
                theme: 'bootstrap',
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
            });
        });


        let keranjangIndex = 0;
        let jasaIndex = 0;

        const form = document.getElementById('formPenjualan');
        const tipe = document.getElementById('tipe_penjualan');
        const hargaProduk = @json($produks->pluck('harga_satuan', 'id'));
        const hargaAyamData = @json($hargaAyams); // keyed by tanggal, nilai terbaru per tanggal

        function updateHargaAyam() {
            const tanggal = form.querySelector('[name="tanggal_jual"]').value;
            const tipeVal = tipe.value;
            const record = hargaAyamData[tanggal];
            const hargaInput = form.querySelector('[name="ayam[harga_per_kg]"]');
            if (tanggal && tipeVal && record) {
                hargaInput.value = tipeVal === 'eceran' ? record.harga_eceran : record.harga_partai;
            }
        }

        form.querySelector('[name="tanggal_jual"]').addEventListener('change', () => {
            updateHargaAyam();
            hitungSubtotal();
        });


        /* ===== helper ===== */
        const num = v => parseFloat(v) || 0;
        const formatRp = n => {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(n);
        };

        /* ===== restore old values ===== */
        @if (old('ayam.tipe_penjualan'))
            tipe.value = '{{ old('ayam.tipe_penjualan') }}';
        @endif

        // Set initial state untuk disable/enable input
        const initialTipe = tipe.value;
        const isEceran = initialTipe === 'eceran';
        const isPartai = initialTipe === 'partai';

        document.getElementById('eceran').style.display = isEceran ? 'block' : 'none';
        document.getElementById('partai').style.display = isPartai ? 'block' : 'none';

        document.querySelectorAll('#eceran input').forEach(input => {
            input.disabled = !isEceran;
        });

        document.querySelectorAll('#partai input').forEach(input => {
            input.disabled = !isPartai;
        });

        // Pastikan button tambah keranjang tidak disabled
        const btnTambahKeranjang = document.getElementById('tambahKeranjang');
        if (btnTambahKeranjang) {
            btnTambahKeranjang.disabled = false;
        }

        // Set initial keranjangs
        @if (old('ayam.keranjangs'))
            const oldKeranjangs = @json(old('ayam.keranjangs'));
            Object.values(oldKeranjangs).forEach((item) => {
                tambahKeranjangRow(item);
            });
            updatePartai();
        @else
            tambahKeranjangRow(); // Tambahkan setidaknya 1 baris kosong di awal
        @endif

        // Set initial jasa
        @if (old('jasa'))
            const oldJasa = @json(old('jasa'));
            Object.values(oldJasa).forEach((item) => {
                tambahJasaRow(item);
            });
        @else
            // tambahkan jika diperlukan 1 baris kosong
            // tambahJasaRow(); // Jasa opsional, biarkan kosong dulu
        @endif

        hitungSubtotal();
        updateHargaAyam();

        /* ===== toggle tipe ===== */
        tipe.onchange = () => {
            const isEceran = tipe.value === 'eceran';
            const isPartai = tipe.value === 'partai';

            document.getElementById('eceran').style.display = isEceran ? 'block' : 'none';
            document.getElementById('partai').style.display = isPartai ? 'block' : 'none';

            // Disable/enable input berdasarkan tipe (kecuali button)
            document.querySelectorAll('#eceran input').forEach(input => {
                input.disabled = !isEceran;
            });

            document.querySelectorAll('#partai input').forEach(input => {
                input.disabled = !isPartai;
            });

            // Enable button tambah keranjang
            const btnTambahKeranjang = document.getElementById('tambahKeranjang');
            if (btnTambahKeranjang) {
                btnTambahKeranjang.disabled = false;
            }

            updateHargaAyam();
            hitungSubtotal();
        };


        /* ===== tambah keranjang ===== */
        function tambahKeranjangRow(data = {}) {
            const ekor = data.jumlah_ekor || '';
            const total = data.berat_total || '';
            const keranjang = data.berat_keranjang || '';
            const ayam = data.berat_ayam || '';

            document.getElementById('keranjangBody').insertAdjacentHTML('beforeend', `
            <tr>
                <td><input type="number" name="ayam[keranjangs][${keranjangIndex}][jumlah_ekor]" class="form-control ekor" value="${ekor}"></td>

                <td><input type="number" step="0.01" name="ayam[keranjangs][${keranjangIndex}][berat_total]" class="form-control total" value="${total}"></td>

                <td><input type="number" step="0.01" name="ayam[keranjangs][${keranjangIndex}][berat_keranjang]" class="form-control keranjang" value="${keranjang}"></td>

                <td><input type="number" step="0.01" name="ayam[keranjangs][${keranjangIndex}][berat_ayam]" class="form-control ayam" readonly value="${ayam}"></td>

                <td><button type="button" class="btn btn-danger btn-sm hapus">X</button></td>
            </tr>
            `);

            keranjangIndex++;
        }

        document.getElementById('tambahKeranjang').onclick = () => {
            tambahKeranjangRow();
        };


        /* ===== tambah jasa ===== */
        function tambahJasaRow(data = {}) {
            const produkId = data.produk_id || '';
            const jumlah = data.jumlah_ekor || '';

            let optionsHtml = '<option value="">-- pilih --</option>';
            @foreach ($jasaProduks as $j)
                optionsHtml +=
                    `<option value="{{ $j->id }}" ${produkId == '{{ $j->id }}' ? 'selected' : ''}>{{ $j->nama_produk }} - Rp{{ number_format($j->harga_satuan, 0, ',', '.') }}</option>`;
            @endforeach

            document.getElementById('jasaBody').insertAdjacentHTML('beforeend', `
            <tr>
                <td>
                    <select name="jasa[${jasaIndex}][produk_id]" class="form-control form-control-sm jasaProduk">
                    ${optionsHtml}
                    </select>
                </td>

                <td>
                    <input type="number" name="jasa[${jasaIndex}][jumlah_ekor]" class="form-control form-control-sm jasaJumlah text-center" value="${jumlah}">
                </td>
                
                <td class="text-right align-middle">
                    <span class="jasaSubtotalDisplay font-weight-bold">0</span>
                </td>

                <td class="text-center align-middle">
                    <button type="button" class="btn btn-outline-danger btn-sm hapus py-0 px-2" title="Hapus"><i class="fas fa-times hapus"></i></button>
                </td>
            </tr>
            `);

            jasaIndex++;
        }

        document.getElementById('tambahJasa').onclick = () => {
            tambahJasaRow();
        };


        /* ===== hapus row ===== */
        document.addEventListener('click', e => {
            if (e.target.classList.contains('hapus')) {
                e.target.closest('tr').remove();
                updatePartai();
                hitungSubtotal();
            }
        });


        /* ===== input realtime ===== */
        form.addEventListener('input', e => {

            const row = e.target.closest('tr');

            /* hitung berat ayam */
            if (row && (e.target.classList.contains('total') || e.target.classList.contains('keranjang'))) {

                const total = num(row.querySelector('.total').value);
                const keranjang = num(row.querySelector('.keranjang').value);

                row.querySelector('.ayam').value = Math.max(total - keranjang, 0).toFixed(2);
            }

            updatePartai();
            hitungSubtotal();

        });


        /* ===== total partai ===== */
        function updatePartai() {

            let berat = 0;
            let ekor = 0;

            document.querySelectorAll('#keranjangBody tr').forEach(r => {
                berat += num(r.querySelector('.ayam')?.value);
                ekor += num(r.querySelector('.ekor')?.value);
            });

            document.querySelector('#partai input[name="ayam[jumlah_berat]"]').value = berat.toFixed(2);
            document.querySelector('#partai input[name="ayam[jumlah_ekor]"]').value = ekor;
        }


        /* ===== subtotal ===== */
        function hitungSubtotal() {

            let totalAyam = 0;
            let totalJasa = 0;
            const hargaKg = num(form.querySelector('[name="ayam[harga_per_kg]"]').value);

            /* eceran */
            if (tipe.value === 'eceran') {
                const berat = num(form.querySelector('#eceran [name="ayam[jumlah_berat]"]').value);
                totalAyam += berat * hargaKg;
            }

            /* partai */
            if (tipe.value === 'partai') {
                const berat = num(document.querySelector('#partai [name="ayam[jumlah_berat]"]').value);
                totalAyam += berat * hargaKg;
            }

            /* jasa */
            document.querySelectorAll('#jasaBody tr').forEach(r => {
                const id = r.querySelector('.jasaProduk')?.value;
                const qty = num(r.querySelector('.jasaJumlah')?.value);
                const subJasa = (hargaProduk[id] || 0) * qty;
                totalJasa += subJasa;
                
                // Update subtotal text per baris jasa
                let tdSub = r.querySelector('.jasaSubtotalDisplay');
                if (tdSub) tdSub.innerText = formatRp(subJasa);
            });

            // Update display subtotal
            document.getElementById('subtotal_ayam_display').value = formatRp(totalAyam);
            document.getElementById('subtotal_jasa_display').value = formatRp(totalJasa);

            /* total */
            let grandTotal = totalAyam + totalJasa;

            /* diskon */
            const diskon = num(form.querySelector('[name="diskon"]').value);
            grandTotal -= diskon;
            
            // Cegah minus
            if (grandTotal < 0) grandTotal = 0;

            form.querySelector('[name="subtotal"]').value = grandTotal.toFixed(2);
            document.getElementById('subtotal_display').value = formatRp(grandTotal);
        }
    </script>
@endpush
