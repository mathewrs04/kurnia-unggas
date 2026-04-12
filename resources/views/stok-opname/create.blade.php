@extends('layouts.app')
@section('content_title', 'Tambah Stok Opname')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Catat Stok Opname</h4>
        </div>

        <div class="card-body">
            <x-alert :errors="$errors" />

            <form action="{{ route('stok-opname.store') }}" method="POST" id="formStokOpname">
                @csrf

                <div class="row">
                    {{-- Batch --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Batch Pembelian</label>
                            <select name="batch_pembelian_id" class="form-control" required>
                                <option value="">-- pilih batch --</option>
                                @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}" {{ old('batch_pembelian_id') == $batch->id ? 'selected' : '' }}>
                                        {{ $batch->kode_batch }}
                                        (stok: {{ number_format($batch->stok_ekor) }} ekor /
                                        {{ number_format($batch->stok_kg, 2) }} kg)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Opname</label>
                            <input type="date" name="tanggal_opname" class="form-control"
                                value="{{ old('tanggal_opname', now()->toDateString()) }}" required>
                        </div>
                    </div>

                    {{-- Timbangan --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Jenis Timbangan</label>
                            <input type="text" class="form-control" value="Timbangan Data Stok Opname" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Karyawan --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Karyawan Penanggung Jawab <small class="text-muted">(bisa pilih lebih dari 1)</small></label>
                            <select name="karyawan_ids[]" class="form-control select2-karyawan" multiple="multiple" style="width:100%">
                                @foreach ($karyawans as $k)
                                    <option value="{{ $k->id }}" {{ in_array($k->id, old('karyawan_ids', [])) ? 'selected' : '' }}>
                                        {{ $k->nama }} ({{ $k->posisi }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ================= TABEL KERANJANG ================= --}}
                <hr>
                <h5 class="mb-3">Data Keranjang</h5>

                <table class="table">
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

                {{-- ================= TOTAL ================= --}}
                <div class="row mt-3">
                    <div class="col-md-6 mb-2">
                        <label>Total Ekor</label>
                        <input type="number" name="jumlah_ekor" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Total Berat Aktual</label>
                        <input type="number" step="0.01" name="jumlah_berat_aktual" class="form-control" readonly>
                    </div>
                </div>



                {{-- BUTTON --}}
                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('stok-opname.index') }}" class="btn btn-default mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Opname</button>
                </div>
            </form>
        </div>
    </div>
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

        let keranjangIndex = 0;
        const form = document.getElementById('formStokOpname');

        /* helper number */
        const num = v => parseFloat(v) || 0;

        /* ================= RESTORE OLD VALUES ================= */
        @if(old('keranjangs'))
            const oldKeranjangs = @json(old('keranjangs'));
            Object.values(oldKeranjangs).forEach((item) => {
                tambahRowKeranjang(item);
            });
            hitungTotal();
        @endif

        /* ================= TAMBAH ROW ================= */
        function tambahRowKeranjang(data = {}) {
            const ekor = data.jumlah_ekor || '';
            const total = data.berat_total || '';
            const keranjang = data.berat_keranjang || '';
            const ayam = data.berat_ayam || '';

            document.getElementById('keranjangBody')
                .insertAdjacentHTML('beforeend', `
        <tr>
            <td>
                <input type="number" name="keranjangs[${keranjangIndex}][jumlah_ekor]"
                class="form-control ekor" value="${ekor}">
            </td>

            <td>
                <input type="number" step="0.01"
                name="keranjangs[${keranjangIndex}][berat_total]"
                class="form-control total" value="${total}">
            </td>

            <td>
                <input type="number" step="0.01"
                name="keranjangs[${keranjangIndex}][berat_keranjang]"
                class="form-control keranjang" value="${keranjang}">
            </td>

            <td>
                <input type="number" step="0.01"
                name="keranjangs[${keranjangIndex}][berat_ayam]"
                class="form-control ayam" readonly value="${ayam}">
            </td>

            <td>
                <button type="button" class="btn btn-danger btn-sm hapus">X</button>
            </td>
        </tr>
    `);

            keranjangIndex++;
        }

        document.getElementById('tambahKeranjang').onclick = () => {
            tambahRowKeranjang();
        };


        /* ================= HAPUS ROW ================= */
        document.addEventListener('click', e => {
            if (e.target.classList.contains('hapus')) {
                e.target.closest('tr').remove();
                hitungTotal();
            }
        });


        /* ================= INPUT REALTIME ================= */
        form.addEventListener('input', e => {

            const row = e.target.closest('tr');

            /* hitung berat ayam */
            if (row && (
                    e.target.classList.contains('total') ||
                    e.target.classList.contains('keranjang')
                )) {
                const total = num(row.querySelector('.total').value);
                const keranjang = num(row.querySelector('.keranjang').value);

                row.querySelector('.ayam').value =
                    Math.max(total - keranjang, 0).toFixed(2);
            }

            hitungTotal();
        });


        /* ================= HITUNG TOTAL ================= */
        function hitungTotal() {

            let totalEkor = 0;
            let totalBerat = 0;

            document.querySelectorAll('#keranjangBody tr').forEach(r => {
                totalEkor += num(r.querySelector('.ekor')?.value);
                totalBerat += num(r.querySelector('.ayam')?.value);
            });

            document.querySelector('input[name="jumlah_ekor"]').value = totalEkor;
            document.querySelector('input[name="jumlah_berat_aktual"]').value = totalBerat.toFixed(2);
        }
    </script>
@endpush
