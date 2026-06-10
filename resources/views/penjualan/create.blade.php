@extends('layouts.app')
@section('content_title', 'Penjualan')
@section('content')

    <x-alert :errors="$errors" />

    <form action="{{ route('penjualan.store') }}" method="POST" id="formPenjualan">
        @csrf

        {{-- ===== HEADER NOTA ===== --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label>Pelanggan</label>
                        <select name="pelanggan_id" class="form-control" required>
                            @foreach ($pelanggans as $p)
                                <option value="{{ $p->id }}" {{ old('pelanggan_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label>Tipe Penjualan</label>
                        <select name="tipe_penjualan" id="tipe_penjualan" class="form-control" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="eceran" {{ old('tipe_penjualan') == 'eceran' ? 'selected' : '' }}>Eceran</option>
                            <option value="partai" {{ old('tipe_penjualan') == 'partai' ? 'selected' : '' }}>Partai</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label>Tanggal Jual</label>
                        <input type="date" name="tanggal_jual" class="form-control"
                            value="{{ old('tanggal_jual', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label>No Nota</label>
                        <input type="text" name="no_nota" class="form-control" value="{{ $noNota }}" readonly>
                    </div>

                    <div class="col-md-1 mb-2 d-flex align-items-center">
                        <div class="custom-control custom-checkbox mt-4">
                            <input class="custom-control-input" type="checkbox" id="is_dikirim" name="is_dikirim"
                                value="1" {{ old('is_dikirim') ? 'checked' : '' }}>
                            <label for="is_dikirim" class="custom-control-label">Kirim?</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== KARYAWAN (hanya untuk partai) ===== --}}
        <div id="sectionKaryawan" style="display:none" class="card mb-3">
            <div class="card-header"><b>Penanggung Jawab Timbangan</b></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Karyawan <small class="text-muted">(bisa pilih lebih dari 1)</small></label>
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
        </div>

        {{-- ===== PENJUALAN AYAM (MULTI-BATCH) ===== --}}
        <div class="card mb-3">
            <div class="card-header">
                <b>Penjualan Ayam</b>
                <button type="button" id="tambahAyam" class="btn btn-sm btn-secondary float-right">
                    <i class="fas fa-plus"></i> Tambah Batch Ayam
                </button>
            </div>
            <div class="card-body p-0">
                <div id="ayamContainer"></div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <div class="mr-4">
                    <small class="text-muted">Subtotal Ayam</small>
                    <div class="font-weight-bold" id="subtotal_ayam_display">Rp 0</div>
                </div>
            </div>
        </div>

        {{-- ===== JASA ===== --}}
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
                    <button type="button" id="tambahJasa" class="btn btn-sm btn-secondary">
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

        {{-- ===== TOTAL & PEMBAYARAN ===== --}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Grand Total (Rp)</label>
                        <input type="text" id="subtotal_display"
                            class="form-control font-weight-bold text-success text-right" readonly value="0"
                            style="font-size: 1.2rem;">
                        <input type="hidden" name="subtotal" value="0">
                    </div>

                    <div class="col-md-4">
                        <label>Diskon (Rp)</label>
                        <input type="number" name="diskon" value="{{ old('diskon', 0) }}"
                            class="form-control text-right text-danger">
                    </div>

                    <div class="col-md-4">
                        <label>Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="metode_pembayaran_id" id="metode_pembayaran_id" class="form-control" required>
                            <option value="">-- Pilih Metode --</option>
                            @foreach ($metodePembayarans as $metode)
                                <option value="{{ $metode->id }}"
                                    {{ old('metode_pembayaran_id') == $metode->id ? 'selected' : '' }}>
                                    {{ $metode->nama_metode }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col text-right">
                        <button class="btn btn-success px-5 font-weight-bold" style="font-size: 1.1rem;">
                            <i class="fas fa-save mr-2"></i> Simpan & Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2-karyawan').select2({
                theme: 'bootstrap',
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
            });
        });

        /* ===== Data dari server ===== */
        const hargaProduk = @json($produks->pluck('harga_satuan', 'id'));
        const hargaAyamData = @json($hargaAyams);
        @php
            $batchesMapped = $batches->map(function($b) {
                return ['id' => $b->id, 'kode_batch' => $b->kode_batch, 'stok_ekor' => $b->stok_ekor, 'stok_kg' => $b->stok_kg];
            });
        @endphp
        const batchesData = @json($batchesMapped);

        const form = document.getElementById('formPenjualan');
        const tipeSel = document.getElementById('tipe_penjualan');

        let ayamIndex = 0;
        let jasaIndex = 0;

        const num = v => parseFloat(v) || 0;
        const formatRp = n => new Intl.NumberFormat('id-ID').format(Math.round(n));

        /* ===== Tipe penjualan toggle ===== */
        function applyTipe() {
            const tipe = tipeSel.value;
            const isPartai = tipe === 'partai';
            document.getElementById('sectionKaryawan').style.display = isPartai ? 'block' : 'none';

            // Update semua baris ayam sesuai tipe
            document.querySelectorAll('.ayam-row').forEach(row => {
                row.querySelector('.section-eceran').style.display = tipe === 'eceran' ? 'block' : 'none';
                row.querySelector('.section-partai').style.display = isPartai ? 'block' : 'none';
            });

            updateHargaAyam();
            hitungSubtotal();
        }

        tipeSel.addEventListener('change', applyTipe);
        form.querySelector('[name="tanggal_jual"]').addEventListener('change', () => {
            updateHargaAyam();
            hitungSubtotal();
        });

        /* ===== Update harga otomatis ===== */
        function updateHargaAyam() {
            const tanggal = form.querySelector('[name="tanggal_jual"]').value;
            const tipe = tipeSel.value;
            const record = hargaAyamData[tanggal];

            document.querySelectorAll('.harga-per-kg').forEach(input => {
                if (tanggal && tipe && record) {
                    input.value = tipe === 'eceran' ? record.harga_eceran : record.harga_partai;
                }
            });
        }

        /* ===== Build options batch ===== */
        function buildBatchOptions(selectedId = '') {
            let html = '<option value="">-- Pilih Batch --</option>';
            batchesData.forEach(b => {
                const sel = b.id == selectedId ? 'selected' : '';
                html += `<option value="${b.id}" ${sel}>${b.kode_batch} (${b.stok_ekor} ekor / ${b.stok_kg} kg)</option>`;
            });
            return html;
        }

        /* ===== Tambah baris ayam ===== */
        function tambahAyamRow(data = {}) {
            const idx = ayamIndex++;
            const tipe = tipeSel.value;

            const html = `
            <div class="ayam-row border-bottom p-3" id="ayamRow${idx}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong class="text-secondary">Batch Ayam #${idx + 1}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger hapusAyam" data-idx="${idx}">
                        <i class="fas fa-times"></i> Hapus
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label>Batch</label>
                        <select name="ayam[${idx}][batch_id]" class="form-control">
                            ${buildBatchOptions(data.batch_id || '')}
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Harga / Kg (Rp)</label>
                        <input type="number" name="ayam[${idx}][harga_per_kg]" class="form-control harga-per-kg ayam-harga"
                            value="${data.harga_per_kg || ''}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Subtotal Ayam Ini (Rp)</label>
                        <input type="text" class="form-control font-weight-bold ayam-subtotal-display" readonly value="0"
                            style="background-color:#f8f9fa;">
                    </div>
                </div>

                {{-- Eceran --}}
                <div class="section-eceran" style="display:${tipe === 'eceran' ? 'block' : 'none'}">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Jumlah Ayam (Ekor)</label>
                            <input type="number" name="ayam[${idx}][jumlah_ekor]" class="form-control ayam-ekor"
                                value="${data.jumlah_ekor || ''}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Berat (Kg)</label>
                            <input type="number" step="0.01" name="ayam[${idx}][jumlah_berat]" class="form-control ayam-berat"
                                value="${data.jumlah_berat || ''}">
                        </div>
                    </div>
                </div>

                {{-- Partai --}}
                <div class="section-partai" style="display:${tipe === 'partai' ? 'block' : 'none'}">
                    <table class="table mt-2">
                        <thead>
                            <tr>
                                <th>Ayam (Ekor) <small class="text-muted">(max 20)</small></th>
                                <th>Berat Total</th>
                                <th>Berat Keranjang</th>
                                <th>Berat Ayam</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="keranjangBody" id="keranjangBody${idx}"></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-secondary tambahKeranjang" data-idx="${idx}">
                        + Keranjang
                    </button>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Total Berat</label>
                            <input type="number" step="0.01" name="ayam[${idx}][jumlah_berat]" class="form-control ayam-berat" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Total Ayam (Ekor)</label>
                            <input type="number" name="ayam[${idx}][jumlah_ekor]" class="form-control ayam-ekor" readonly>
                        </div>
                    </div>
                </div>
            </div>`;

            document.getElementById('ayamContainer').insertAdjacentHTML('beforeend', html);

            // Restore keranjang jika ada
            if (data.keranjangs) {
                Object.values(data.keranjangs).forEach(k => tambahKeranjangRow(idx, k));
                updatePartai(idx);
            } else if (tipe === 'partai') {
                tambahKeranjangRow(idx);
            }

            updateHargaAyam();
        }

        /* ===== Tambah keranjang di baris ayam tertentu ===== */
        let keranjangCounters = {};
        function tambahKeranjangRow(ayamIdx, data = {}) {
            if (!keranjangCounters[ayamIdx]) keranjangCounters[ayamIdx] = 0;
            const kIdx = keranjangCounters[ayamIdx]++;

            const ekor = data.jumlah_ekor || '';
            const total = data.berat_total || '';
            const keranjang = data.berat_keranjang || '15';
            const ayam = data.berat_ayam || '';

            document.getElementById(`keranjangBody${ayamIdx}`).insertAdjacentHTML('beforeend', `
            <tr>
                <td><input type="number" name="ayam[${ayamIdx}][keranjangs][${kIdx}][jumlah_ekor]"
                    class="form-control ekor" value="${ekor}" min="1" max="20"></td>
                <td><input type="number" step="0.01" name="ayam[${ayamIdx}][keranjangs][${kIdx}][berat_total]"
                    class="form-control total" value="${total}"></td>
                <td><input type="number" step="0.01" name="ayam[${ayamIdx}][keranjangs][${kIdx}][berat_keranjang]"
                    class="form-control keranjang" value="${keranjang}"></td>
                <td><input type="number" step="0.01" name="ayam[${ayamIdx}][keranjangs][${kIdx}][berat_ayam]"
                    class="form-control ayam" readonly value="${ayam}"></td>
                <td><button type="button" class="btn btn-danger btn-sm hapusKeranjang">X</button></td>
            </tr>`);
        }

        /* ===== Update total partai untuk 1 baris ayam ===== */
        function updatePartai(ayamIdx) {
            const body = document.getElementById(`keranjangBody${ayamIdx}`);
            if (!body) return;

            let berat = 0;
            let ekor = 0;
            body.querySelectorAll('tr').forEach(r => {
                berat += num(r.querySelector('.ayam')?.value);
                ekor += num(r.querySelector('.ekor')?.value);
            });

            const row = document.getElementById(`ayamRow${ayamIdx}`);
            if (!row) return;
            const beratInput = row.querySelector('.section-partai .ayam-berat');
            const ekorInput = row.querySelector('.section-partai .ayam-ekor');
            if (beratInput) beratInput.value = berat.toFixed(2);
            if (ekorInput) ekorInput.value = ekor;
        }

        /* ===== Event delegation ===== */
        document.addEventListener('click', e => {
            // Hapus baris ayam
            if (e.target.classList.contains('hapusAyam') || e.target.closest('.hapusAyam')) {
                const btn = e.target.classList.contains('hapusAyam') ? e.target : e.target.closest('.hapusAyam');
                const idx = btn.dataset.idx;
                document.getElementById(`ayamRow${idx}`)?.remove();
                hitungSubtotal();
            }

            // Tambah keranjang
            if (e.target.classList.contains('tambahKeranjang')) {
                const idx = e.target.dataset.idx;
                tambahKeranjangRow(idx);
            }

            // Hapus keranjang
            if (e.target.classList.contains('hapusKeranjang')) {
                const tbody = e.target.closest('tbody');
                e.target.closest('tr').remove();
                // Cari ayamIdx dari id tbody
                const tbodyId = tbody?.id || '';
                const ayamIdx = tbodyId.replace('keranjangBody', '');
                if (ayamIdx !== '') updatePartai(ayamIdx);
                hitungSubtotal();
            }
        });

        /* ===== Input realtime ===== */
        form.addEventListener('input', e => {
            const row = e.target.closest('tr');
            if (row && (e.target.classList.contains('total') || e.target.classList.contains('keranjang'))) {
                const total = num(row.querySelector('.total').value);
                const keranjang = num(row.querySelector('.keranjang').value);
                row.querySelector('.ayam').value = Math.max(total - keranjang, 0).toFixed(2);
            }

            // Cari ayam row parent untuk update total
            const ayamRow = e.target.closest('.ayam-row');
            if (ayamRow) {
                const idx = ayamRow.id.replace('ayamRow', '');
                updatePartai(idx);
            }

            hitungSubtotal();
        });

        /* ===== Hitung subtotal keseluruhan ===== */
        function hitungSubtotal() {
            const tipe = tipeSel.value;
            let totalAyam = 0;
            let totalJasa = 0;

            document.querySelectorAll('.ayam-row').forEach(ayamRow => {
                const idx = ayamRow.id.replace('ayamRow', '');
                const harga = num(ayamRow.querySelector('.ayam-harga')?.value);
                let berat = 0;

                if (tipe === 'eceran') {
                    berat = num(ayamRow.querySelector('.section-eceran .ayam-berat')?.value);
                } else {
                    berat = num(ayamRow.querySelector('.section-partai .ayam-berat')?.value);
                }

                const sub = berat * harga;
                totalAyam += sub;

                const displayEl = ayamRow.querySelector('.ayam-subtotal-display');
                if (displayEl) displayEl.value = formatRp(sub);
            });

            /* jasa */
            document.querySelectorAll('#jasaBody tr').forEach(r => {
                const id = r.querySelector('.jasaProduk')?.value;
                const qty = num(r.querySelector('.jasaJumlah')?.value);
                const subJasa = (hargaProduk[id] || 0) * qty;
                totalJasa += subJasa;
                const tdSub = r.querySelector('.jasaSubtotalDisplay');
                if (tdSub) tdSub.innerText = formatRp(subJasa);
            });

            document.getElementById('subtotal_ayam_display').innerText = 'Rp ' + formatRp(totalAyam);
            document.getElementById('subtotal_jasa_display').value = formatRp(totalJasa);

            let grandTotal = totalAyam + totalJasa;
            const diskon = num(form.querySelector('[name="diskon"]').value);
            grandTotal -= diskon;
            if (grandTotal < 0) grandTotal = 0;

            form.querySelector('[name="subtotal"]').value = grandTotal.toFixed(2);
            document.getElementById('subtotal_display').value = formatRp(grandTotal);
        }

        /* ===== Tambah Jasa ===== */
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
                <td>
                    <button type="button" class="btn btn-danger btn-sm hapusJasa">X</button>
                </td>
            </tr>`);
            jasaIndex++;
        }

        document.getElementById('tambahJasa').onclick = () => tambahJasaRow();
        document.getElementById('tambahAyam').onclick = () => {
            if (!tipeSel.value) {
                alert('Pilih tipe penjualan terlebih dahulu!');
                return;
            }
            tambahAyamRow();
        };

        document.addEventListener('click', e => {
            if (e.target.classList.contains('hapusJasa')) {
                e.target.closest('tr').remove();
                hitungSubtotal();
            }
        });

        /* ===== Restore old values saat validasi gagal ===== */
        @if (old('tipe_penjualan'))
            tipeSel.value = '{{ old('tipe_penjualan') }}';
            applyTipe();
        @endif

        @if (old('ayam'))
            const oldAyam = @json(old('ayam'));
            Object.values(oldAyam).forEach(item => tambahAyamRow(item));
        @else
            // Tambahkan 1 baris ayam kosong di awal
            if (tipeSel.value) tambahAyamRow();
        @endif

        @if (old('jasa'))
            const oldJasa = @json(old('jasa'));
            Object.values(oldJasa).forEach(item => tambahJasaRow(item));
        @endif

        hitungSubtotal();
        updateHargaAyam();

        // Inisialisasi awal applyTipe
        applyTipe();
    </script>
@endpush
