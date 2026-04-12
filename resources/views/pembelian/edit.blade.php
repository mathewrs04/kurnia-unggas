@extends('layouts.app')
@section('content_title', 'Edit Pembelian')
@section('content')

    <form action="{{ route('pembelian.edit', $pembelian->id) }}" method="POST" id="formPembelian">
        @csrf
        @method('PUT')
        <x-alert :errors="$errors" />
        <!-- Card Data Pembelian -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Data Pembelian</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="peternak_id">Peternak <span class="text-danger">*</span></label>
                            <select name="peternak_id" id="peternak_id" class="form-control" required>
                                <option value="{{ $pembelian->peternak_id }}">{{ $pembelian->peternak->nama }}</option>
                            </select>
                            @error('peternak_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_pembelian">Tanggal Pembelian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" class="form-control" 
                                   value="{{ old('tanggal_pembelian', $pembelian->tanggal_pembelian->format('Y-m-d')) }}" required>
                            @error('tanggal_pembelian')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kode_pembelian">Kode Pembelian <span class="text-danger">*</span></label>
                            <input type="text" name="kode_pembelian" id="kode_pembelian" class="form-control" 
                                   value="{{ $pembelian->kode_pembelian }}" readonly>
                            @error('kode_pembelian')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <input type="text" class="form-control" value="{{ ucwords($pembelian->status) }}" readonly>
                            <small class="text-muted">Status tidak dapat diubah melalui edit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $detail = $pembelian->pembelianDetails->first();
            $timbangan = $detail->timbangan ?? null;
        @endphp

        <!-- Card Timbangan dan Keranjang -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Data Timbangan</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jenis_timbangan">Jenis Timbangan</label>
                            <input type="text" class="form-control" value="{{ $timbangan->jenis ?? 'Timbangan Data Pembelian' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Karyawan Penanggung Jawab <small class="text-muted">(bisa pilih lebih dari 1)</small></label>
                            @php $selectedKaryawanIds = old('karyawan_ids', $timbangan->karyawans->pluck('id')->toArray() ?? []); @endphp
                            <select name="karyawan_ids[]" class="form-control select2-karyawan" multiple="multiple" style="width:100%">
                                @foreach ($karyawans as $k)
                                    <option value="{{ $k->id }}" {{ in_array($k->id, $selectedKaryawanIds) ? 'selected' : '' }}>
                                        {{ $k->nama }} ({{ $k->posisi }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h4>Data Keranjang</h4>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tabelKeranjang">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="35%">Jumlah Ekor</th>
                                <th width="35%">Berat Ayam (Kg)</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="keranjangBody">
                            @if(!old('keranjangs'))
                                @if($timbangan && $timbangan->keranjangs->count() > 0)
                                    @foreach($timbangan->keranjangs as $index => $keranjang)
                                        <tr class="keranjang-item">
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <input type="number" name="keranjangs[{{ $index }}][jumlah_ekor]" 
                                                       class="form-control jumlah-ekor" placeholder="Jumlah ekor" 
                                                       value="{{ $keranjang->jumlah_ekor }}" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" name="keranjangs[{{ $index }}][berat_ayam]" 
                                                       class="form-control berat-ayam" placeholder="Berat dalam Kg" 
                                                       value="{{ $keranjang->berat_ayam }}" step="0.01" min="0" required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang" 
                                                        {{ $timbangan->keranjangs->count() == 1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="keranjang-item">
                                        <td class="text-center">1</td>
                                        <td>
                                            <input type="number" name="keranjangs[0][jumlah_ekor]" class="form-control jumlah-ekor" 
                                                   placeholder="Jumlah ekor" min="1" required>
                                        </td>
                                        <td>
                                            <input type="number" name="keranjangs[0][berat_ayam]" class="form-control berat-ayam" 
                                                   placeholder="Berat dalam Kg" step="0.01" min="0" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
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
                                <td><input type="number" id="totalBerat" class="form-control" step="0.01" readonly></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card Detail Pembelian -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Detail Pembelian</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="delivery_order_id">Delivery Order</label>
                            <select name="delivery_order_id" id="delivery_order_id" class="form-control" 
                                    {{ $pembelian->status == 'sudah bayar' ? 'disabled' : '' }}>
                                <option value="">-- Pilih DO (Opsional) --</option>
                                @foreach($deliveryOrders as $do)
                                    <option value="{{ $do->id }}" {{ $detail && $detail->delivery_order_id == $do->id ? 'selected' : '' }}>
                                        {{ $do->kode_do }} - {{ \Carbon\Carbon::parse($do->tanggal_do)->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('delivery_order_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    @if($pembelian->status == 'sudah bayar')
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga_beli_per_kg">Harga Beli per Kg</label>
                                <input type="text" class="form-control" 
                                       value="Rp {{ number_format($detail->harga_beli_per_kg ?? 0, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                    @endif
                </div>
                @if($pembelian->status != 'sudah bayar')
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Harga beli per kg dan subtotal akan diisi saat proses pembayaran
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Pembelian sudah dibayar. Delivery Order dan data pembayaran tidak dapat diubah.
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Pembelian
                        </button>
                        <a href="{{ route('pembelian.show', $pembelian->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let keranjangIndex = {{ $timbangan && $timbangan->keranjangs ? $timbangan->keranjangs->count() : 1 }};

            // Initialize Select2 for Peternak
            $('#peternak_id').select2({
                theme: 'bootstrap',
                placeholder: 'Cari peternak...',
                ajax: {
                    url: "{{ route('get-data.peternak') }}",
                    dataType: 'json',
                    delay: 250,
                    data: (params) => {
                        return {
                            search: params.term
                        };
                    },
                    processResults: (data) => {
                        return {
                            results: data.map((item) => ({
                                id: item.id,
                                text: item.nama
                            }))
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });

            // Initialize Select2 for Delivery Order
            $('#delivery_order_id').select2({
                theme: 'bootstrap',
                placeholder: 'Pilih delivery order (opsional)...',
                allowClear: true
            });

            // Tambah Keranjang
            $('#btnTambahKeranjang').click(function() {
                let newRow = `
                    <tr class="keranjang-item">
                        <td class="text-center">${keranjangIndex + 1}</td>
                        <td>
                            <input type="number" name="keranjangs[${keranjangIndex}][jumlah_ekor]" 
                                   class="form-control jumlah-ekor" placeholder="Jumlah ekor" min="1" required>
                        </td>
                        <td>
                            <input type="number" name="keranjangs[${keranjangIndex}][berat_ayam]" 
                                   class="form-control berat-ayam" placeholder="Berat dalam Kg" step="0.01" min="0" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#keranjangBody').append(newRow);
                keranjangIndex++;
                updateNomor();
                updateTotal();
            });

            // Hapus Keranjang
            $(document).on('click', '.btn-hapus-keranjang', function() {
                if ($('.keranjang-item').length > 1) {
                    $(this).closest('tr').remove();
                    updateNomor();
                    updateTotal();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Minimal harus ada 1 keranjang!'
                    });
                }
            });

            // Update nomor urut
            function updateNomor() {
                $('.keranjang-item').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                    $(this).find('.jumlah-ekor').attr('name', `keranjangs[${index}][jumlah_ekor]`);
                    $(this).find('.berat-ayam').attr('name', `keranjangs[${index}][berat_ayam]`);
                });
                
                // Enable/disable tombol hapus
                if ($('.keranjang-item').length === 1) {
                    $('.btn-hapus-keranjang').prop('disabled', true);
                } else {
                    $('.btn-hapus-keranjang').prop('disabled', false);
                }
            }

            // Update total jumlah ekor dan berat
            function updateTotal() {
                let totalEkor = 0;
                let totalBerat = 0;

                $('.keranjang-item').each(function() {
                    let ekor = parseFloat($(this).find('.jumlah-ekor').val()) || 0;
                    let berat = parseFloat($(this).find('.berat-ayam').val()) || 0;
                    totalEkor += ekor;
                    totalBerat += berat;
                });

                $('#totalJumlahEkor').val(totalEkor);
                $('#totalBerat').val(totalBerat.toFixed(2));
            }

            // Event listener untuk perubahan input keranjang
            $(document).on('input', '.jumlah-ekor, .berat-ayam', function() {
                updateTotal();
            });

            // Validasi form sebelum submit
            $('#formPembelian').on('submit', function(e) {
                let totalBerat = parseFloat($('#totalBerat').val()) || 0;
                
                if (totalBerat <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Total berat harus lebih dari 0!'
                    });
                    return false;
                }
            });

            // Initialize total
            updateTotal();

            // ===== Restore old() values setelah validasi gagal =====
            @if(old('keranjangs'))
                const oldKeranjangsEdit = @json(old('keranjangs'));
                keranjangIndex = 0;
                Object.values(oldKeranjangsEdit).forEach(function(item) {
                    let index = keranjangIndex;
                    let newRow = `
                        <tr class="keranjang-item">
                            <td class="text-center">${index + 1}</td>
                            <td>
                                <input type="number" name="keranjangs[${index}][jumlah_ekor]"
                                       class="form-control jumlah-ekor" placeholder="Jumlah ekor" min="1"
                                       value="${item.jumlah_ekor || ''}" required>
                            </td>
                            <td>
                                <input type="number" name="keranjangs[${index}][berat_ayam]"
                                       class="form-control berat-ayam" placeholder="Berat dalam Kg"
                                       step="0.01" min="0" value="${item.berat_ayam || ''}" required>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang"
                                        ${index === 0 ? 'disabled' : ''}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#keranjangBody').append(newRow);
                    keranjangIndex++;
                });
                updateNomor();
                updateTotal();
            @endif

            $('.select2-karyawan').select2({
                theme: 'bootstrap',
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
            });
        });
    </script>
@endpush
