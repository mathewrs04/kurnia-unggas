@extends('layouts.app')
@section('content_title', 'Edit Stok Opname')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Edit Stok Opname</h4>
        </div>
        <div class="card-body">
            <x-alert :errors="$errors" />
            <form action="{{ route('stok-opname.update', $stokOpname->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Batch Pembelian</label>
                            <select name="batch_pembelian_id" class="form-control" required>
                                <option value="">-- pilih batch --</option>
                                @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}"
                                        {{ old('batch_pembelian_id', optional($stokOpname)->batch_pembelian_id) == $batch->id ? 'selected' : '' }}>
                                        {{ $batch->kode_batch }} (stok: {{ number_format($batch->stok_ekor) }} ekor /
                                        {{ number_format($batch->stok_kg, 2) }} kg)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Opname</label>
                            <input type="date" name="tanggal_opname" class="form-control"
                                value="{{ old('tanggal_opname', optional(optional($stokOpname)->tanggal_opname)->toDateString() ?? now()->toDateString()) }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pilih Timbangan (opsional)</label>
                            <select name="timbangan_id" class="form-control">
                                <option value="">-- tanpa timbangan --</option>
                                @foreach ($timbangans as $timbangan)
                                    <option value="{{ $timbangan->id }}"
                                        {{ old('timbangan_id', optional($stokOpname)->timbangan_id) == $timbangan->id ? 'selected' : '' }}>
                                        {{ $timbangan->tanggal->format('d/m/Y') }} - {{ $timbangan->jenis }}
                                        ({{ number_format($timbangan->total_berat, 2) }} kg)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <h4>Data Keranjang</h4>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tabelKeranjang">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Jumlah Ekor</th>
                                <th width="20%">Berat Keranjang (Kg)</th>
                                <th width="20%">Berat Total (Kg)</th>
                                <th width="20%">Berat Ayam (Kg)</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="keranjangBody">
                            @if(!old('keranjangs'))
                            <tr class="keranjang-item">
                                <td class="text-center">1</td>
                                <td>
                                    <input type="number" name="keranjangs[0][jumlah_ekor]" class="form-control jumlah-ekor"
                                        placeholder="Jumlah ekor" min="1" required>
                                </td>
                                <td>
                                    <input type="number" name="keranjangs[0][berat_keranjang]"
                                        class="form-control berat-keranjang" placeholder="Berat keranjang" step="0.01"
                                        min="0" required>
                                </td>
                                <td>
                                    <input type="number" name="keranjangs[0][berat_total]" class="form-control berat-total"
                                        placeholder="Berat total" step="0.01" min="0" required>
                                </td>
                                <td>
                                    <input type="number" name="keranjangs[0][berat_ayam]" class="form-control berat-ayam"
                                        placeholder="Auto-calculate" step="0.01" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <button type="button" class="btn btn-success btn-sm" id="btnTambahKeranjang">
                                        <i class="fas fa-plus"></i> Tambah Keranjang
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1" class="text-right"><strong>Total:</strong></td>
                                <td><input type="number" id="totalJumlahEkor" class="form-control" readonly></td>
                                <td><input type="number" id="totalBerat" class="form-control" step="0.01" readonly>
                                </td>
                                <td><input type="number" id="totalBeratAyam" class="form-control" step="0.01" readonly>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Berat Aktual (kg)</label>
                            <input type="number" step="0.01" name="berat_aktual_kg" id="beratAktualKg"
                                class="form-control" value="{{ old('berat_aktual_kg', optional($stokOpname)->berat_aktual_kg) }}"
                                required readonly>
                            <small class="text-muted">Diisi otomatis dari total berat ayam per keranjang.</small>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Catatan</label>
                            <input type="text" name="catatan" class="form-control"
                                value="{{ old('catatan', optional($stokOpname)->catatan) }}"
                                placeholder="Opsional, isi jika ada keterangan tambahan">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('stok-opname.index') }}" class="btn btn-default mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let keranjangIndex = 1;

            function updateBeratAyam(row) {
                const beratTotalInput = row.querySelector('.berat-total');
                const beratKeranjangInput = row.querySelector('.berat-keranjang');
                const beratAyamInput = row.querySelector('.berat-ayam');

                const beratTotal = parseFloat(beratTotalInput.value) || 0;
                const beratKeranjang = parseFloat(beratKeranjangInput.value) || 0;

                const beratAyam = beratTotal - beratKeranjang;
                beratAyamInput.value = beratAyam.toFixed(2);
            }

            function updateTotals() {
                let totalEkor = 0;
                let totalBerat = 0;
                let totalBeratAyam = 0;

                document.querySelectorAll('.keranjang-item').forEach(function(row) {
                    const jumlahEkorInput = row.querySelector('.jumlah-ekor');
                    const beratTotalInput = row.querySelector('.berat-total');
                    const beratKeranjangInput = row.querySelector('.berat-keranjang');
                    const beratAyamInput = row.querySelector('.berat-ayam');

                    totalEkor += parseInt(jumlahEkorInput.value) || 0;
                    totalBerat += parseFloat(beratTotalInput.value) || 0;
                    const beratAyam = parseFloat(beratAyamInput.value) || ((parseFloat(beratTotalInput.value) || 0) - (parseFloat(beratKeranjangInput.value) || 0));
                    totalBeratAyam += beratAyam;
                });

                document.getElementById('totalJumlahEkor').value = totalEkor;
                document.getElementById('totalBerat').value = totalBerat.toFixed(2);
                document.getElementById('totalBeratAyam').value = totalBeratAyam.toFixed(2);
                document.getElementById('beratAktualKg').value = totalBeratAyam.toFixed(2);
            }

            document.getElementById('btnTambahKeranjang').addEventListener('click', function() {
                const tbody = document.getElementById('keranjangBody');
                const newRow = document.createElement('tr');
                newRow.classList.add('keranjang-item');
                newRow.innerHTML = `
                    <td class="text-center">${keranjangIndex + 1}</td>
                    <td>
                        <input type="number" name="keranjangs[${keranjangIndex}][jumlah_ekor]"
                            class="form-control jumlah-ekor" placeholder="Jumlah ekor" min="1" required>
                    </td>
                    <td>
                        <input type="number" name="keranjangs[${keranjangIndex}][berat_keranjang]"
                            class="form-control berat-keranjang" placeholder="Berat keranjang" step="0.01" min="0"
                            required>
                    </td>
                    <td>
                        <input type="number" name="keranjangs[${keranjangIndex}][berat_total]"
                            class="form-control berat-total" placeholder="Berat total" step="0.01" min="0" required>
                    </td>
                    <td>
                        <input type="number" name="keranjangs[${keranjangIndex}][berat_ayam]"
                            class="form-control berat-ayam" placeholder="Auto-calculate" step="0.01" readonly>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(newRow);
                keranjangIndex++;
                updateTotals();
            });

            document.getElementById('keranjangBody').addEventListener('input', function(event) {
                if (event.target.classList.contains('berat-total') || event.target.classList.contains('berat-keranjang')) {
                    const row = event.target.closest('.keranjang-item');
                    updateBeratAyam(row);
                    updateTotals();
                } else if (event.target.classList.contains('jumlah-ekor')) {
                    updateTotals();
                }
            });

            document.getElementById('keranjangBody').addEventListener('click', function(event) {
                if (event.target.classList.contains('btn-hapus-keranjang') || event.target.closest('.btn-hapus-keranjang')) {
                    const button = event.target.classList.contains('btn-hapus-keranjang') ? event.target : event.target.closest('.btn-hapus-keranjang');
                    const row = button.closest('.keranjang-item');
                    row.remove();
                    updateTotals();
                }
            });

            document.querySelectorAll('.keranjang-item').forEach(updateBeratAyam);
            updateTotals();

            // ===== Restore old() values setelah validasi gagal =====
            @if(old('keranjangs'))
                const oldKeranjangsEdit = @json(old('keranjangs'));
                keranjangIndex = Object.keys(oldKeranjangsEdit).length;
                Object.values(oldKeranjangsEdit).forEach(function(item, index) {
                    const tbody = document.getElementById('keranjangBody');
                    const newRow = document.createElement('tr');
                    newRow.classList.add('keranjang-item');
                    newRow.innerHTML = `
                        <td class="text-center">${index + 1}</td>
                        <td>
                            <input type="number" name="keranjangs[${index}][jumlah_ekor]"
                                class="form-control jumlah-ekor" placeholder="Jumlah ekor" min="1"
                                value="${item.jumlah_ekor || ''}" required>
                        </td>
                        <td>
                            <input type="number" name="keranjangs[${index}][berat_keranjang]"
                                class="form-control berat-keranjang" placeholder="Berat keranjang" step="0.01"
                                min="0" value="${item.berat_keranjang || ''}" required>
                        </td>
                        <td>
                            <input type="number" name="keranjangs[${index}][berat_total]"
                                class="form-control berat-total" placeholder="Berat total" step="0.01"
                                min="0" value="${item.berat_total || ''}" required>
                        </td>
                        <td>
                            <input type="number" name="keranjangs[${index}][berat_ayam]"
                                class="form-control berat-ayam" placeholder="Auto-calculate" step="0.01"
                                value="${item.berat_ayam || ''}" readonly>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang"
                                    ${index === 0 ? 'disabled' : ''}>
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(newRow);
                });
                document.querySelectorAll('.keranjang-item').forEach(updateBeratAyam);
                updateTotals();
            @endif

        });
    </script>
@endpush
