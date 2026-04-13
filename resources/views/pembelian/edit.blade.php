@extends('layouts.app')
@section('content_title', 'Edit Pembelian')
@section('content')

    <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST" id="formPembelian">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kode_pembelian">Kode Pembelian <span class="text-danger">*</span></label>
                            <input type="text" name="kode_pembelian" id="kode_pembelian" class="form-control" 
                                   value="{{ $pembelian->kode_pembelian }}" readonly>
                            @error('kode_pembelian')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="delivery_order_id">Delivery Order</label>
                            <select name="delivery_order_id" id="delivery_order_id" class="form-control" 
                                    {{ $pembelian->status == \App\Models\Pembelian::STATUS_SUDAH_BAYAR ? 'disabled' : '' }}>
                                <option value="">-- Pilih DO (Opsional) --</option>
                                @foreach($deliveryOrders as $do)
                                    <option value="{{ $do->id }}" {{ $currentDeliveryOrderId == $do->id ? 'selected' : '' }}>
                                        {{ $do->kode_do }} - {{ \Carbon\Carbon::parse($do->tanggal_do)->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('delivery_order_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <input type="text" class="form-control" value="{{ ucwords($pembelian->status) }}" readonly>
                            <small class="text-muted">Status tidak dapat diubah melalui edit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                <h5 class="mb-3">Data Keranjang</h5>
                <div class="table-responsive">
                    <table class="table mt-2" id="tabelKeranjang">
                        <thead>
                            <tr>
                                <th>Jumlah Ekor</th>
                                <th>Berat Total (Kg)</th>
                                <th>Berat Keranjang (Kg)</th>
                                <th>Berat Ayam (Kg)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="keranjangBody">
                            @if(!old('keranjangs'))
                                @if($keranjangs->count() > 0)
                                    @foreach($keranjangs as $index => $keranjang)
                                        <tr class="keranjang-item">
                                            <td>
                                                <input type="number" name="keranjangs[{{ $index }}][jumlah_ekor]" 
                                                       class="form-control jumlah-ekor" placeholder="Jumlah ekor" 
                                                       value="{{ $keranjang->jumlah_ekor }}" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" name="keranjangs[{{ $index }}][berat_total]" 
                                                       class="form-control berat-total" placeholder="Berat total" 
                                                       value="{{ $keranjang->berat_total }}" step="0.01" min="0" required>
                                            </td>
                                            <td>
                                                <input type="number" name="keranjangs[{{ $index }}][berat_keranjang]" 
                                                       class="form-control berat-keranjang" placeholder="Berat keranjang" 
                                                       value="{{ $keranjang->berat_keranjang }}" step="0.01" min="0" required>
                                            </td>
                                            <td>
                                                <input type="number" name="keranjangs[{{ $index }}][berat_ayam]" 
                                                       class="form-control berat-ayam" placeholder="Auto-calculate" 
                                                       value="{{ $keranjang->berat_ayam }}" step="0.01" readonly>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang">
                                                    X
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="keranjang-item">
                                        <td>
                                            <input type="number" name="keranjangs[0][jumlah_ekor]" class="form-control jumlah-ekor" 
                                                   placeholder="Jumlah ekor" min="1" required>
                                        </td>
                                        <td>
                                            <input type="number" name="keranjangs[0][berat_total]" class="form-control berat-total" 
                                                   placeholder="Berat total" step="0.01" min="0" required>
                                        </td>
                                        <td>
                                            <input type="number" name="keranjangs[0][berat_keranjang]" class="form-control berat-keranjang" 
                                                   placeholder="Berat keranjang" step="0.01" min="0" value="15" required>
                                        </td>
                                        <td>
                                            <input type="number" name="keranjangs[0][berat_ayam]" class="form-control berat-ayam" 
                                                   placeholder="Auto-calculate" step="0.01" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang">
                                                X
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-sm btn-secondary" id="btnTambahKeranjang">
                    + Keranjang
                </button>

                <div class="row mt-3">
                    <div class="col-md-6 mb-2">
                        <label>Total Berat</label>
                        <input type="number" id="totalBerat" class="form-control" step="0.01" readonly>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Total Ekor</label>
                        <input type="number" id="totalJumlahEkor" class="form-control" readonly>
                    </div>
                </div>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            let keranjangIndex = {{ $keranjangCount }};

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
                        <td>
                            <input type="number" name="keranjangs[${keranjangIndex}][jumlah_ekor]" 
                                   class="form-control jumlah-ekor" placeholder="Jumlah ekor" min="1" required>
                        </td>
                        <td>
                            <input type="number" name="keranjangs[${keranjangIndex}][berat_total]" 
                                   class="form-control berat-total" placeholder="Berat total" step="0.01" min="0" required>
                        </td>
                        <td>
                            <input type="number" name="keranjangs[${keranjangIndex}][berat_keranjang]" 
                                   class="form-control berat-keranjang" placeholder="Berat keranjang" step="0.01" min="0" value="15" required>
                        </td>
                        <td>
                            <input type="number" name="keranjangs[${keranjangIndex}][berat_ayam]" 
                                   class="form-control berat-ayam" placeholder="Auto-calculate" step="0.01" readonly>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang">
                                X
                            </button>
                        </td>
                    </tr>
                `;
                $('#keranjangBody').append(newRow);
                keranjangIndex++;
                updateTotal();
            });

            // Hapus Keranjang
            $(document).on('click', '.btn-hapus-keranjang', function() {
                if ($('.keranjang-item').length > 1) {
                    $(this).closest('tr').remove();
                    reindexKeranjangInput();
                    updateTotal();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Minimal harus ada 1 keranjang!'
                    });
                }
            });

            function reindexKeranjangInput() {
                $('.keranjang-item').each(function(index) {
                    $(this).find('.jumlah-ekor').attr('name', `keranjangs[${index}][jumlah_ekor]`);
                    $(this).find('.berat-total').attr('name', `keranjangs[${index}][berat_total]`);
                    $(this).find('.berat-keranjang').attr('name', `keranjangs[${index}][berat_keranjang]`);
                    $(this).find('.berat-ayam').attr('name', `keranjangs[${index}][berat_ayam]`);
                });
            }

            function hitungBeratAyam(row) {
                let beratTotal = parseFloat(row.find('.berat-total').val()) || 0;
                let beratKeranjang = parseFloat(row.find('.berat-keranjang').val()) || 0;
                let beratAyam = beratTotal - beratKeranjang;
                row.find('.berat-ayam').val(beratAyam >= 0 ? beratAyam.toFixed(2) : 0);
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

            $(document).on('input', '.berat-total, .berat-keranjang', function() {
                let row = $(this).closest('tr');
                hitungBeratAyam(row);
                updateTotal();
            });

            // Event listener untuk perubahan input keranjang
            $(document).on('input', '.jumlah-ekor', function() {
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
                            <td>
                                <input type="number" name="keranjangs[${index}][jumlah_ekor]"
                                       class="form-control jumlah-ekor" placeholder="Jumlah ekor" min="1"
                                       value="${item.jumlah_ekor || ''}" required>
                            </td>
                            <td>
                                <input type="number" name="keranjangs[${index}][berat_total]"
                                       class="form-control berat-total" placeholder="Berat total"
                                       step="0.01" min="0" value="${item.berat_total || ''}" required>
                            </td>
                            <td>
                                <input type="number" name="keranjangs[${index}][berat_keranjang]"
                                       class="form-control berat-keranjang" placeholder="Berat keranjang"
                                       step="0.01" min="0" value="${item.berat_keranjang || 15}" required>
                            </td>
                            <td>
                                <input type="number" name="keranjangs[${index}][berat_ayam]"
                                       class="form-control berat-ayam" placeholder="Auto-calculate"
                                       step="0.01" value="${item.berat_ayam || ''}" readonly>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang">
                                    X
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#keranjangBody').append(newRow);
                    keranjangIndex++;
                });
                reindexKeranjangInput();
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
