@extends('layouts.app')
@section('content_title', 'Tambah Pembelian')
@section('content')

    <form action="{{ route('pembelian.store') }}" method="POST" id="formPembelian">
        @csrf
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
                                <option value="">-- Pilih Peternak --</option>
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
                                   value="{{ date('Y-m-d') }}" required>
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
                                   value="{{ $kodePembelian }}" required>
                            @error('kode_pembelian')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <input type="hidden" name="status" value="belum bayar">
                            <input type="text" class="form-control" value="Belum Bayar" readonly>
                            <small class="text-muted">Status otomatis "Belum Bayar" saat pembelian dibuat</small>
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
                            <input type="text" class="form-control" value="Timbangan Data Pembelian" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_timbangan">Tanggal Timbangan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_timbangan" id="tanggal_timbangan" class="form-control" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_karyawan">Nama Karyawan</label>
                            <input type="text" name="nama_karyawan" id="nama_karyawan" class="form-control" 
                                   placeholder="Nama karyawan yang menimbang">
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
                            <select name="delivery_order_id" id="delivery_order_id" class="form-control">
                                <option value="">-- Pilih DO (Opsional) --</option>
                            </select>
                            @error('delivery_order_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="susut_kg">Susut (Kg)</label>
                            <input type="number" name="susut_kg" id="susut_kg" class="form-control" 
                                   placeholder="Susut dalam Kg (opsional)" step="0.01" min="0" value="0">
                            @error('susut_kg')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small class="text-muted">Harga beli per kg dan subtotal akan diisi saat proses pembayaran</small>
                        </div>
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
                            <i class="fas fa-save"></i> Simpan Pembelian
                        </button>
                        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
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
            let keranjangIndex = 1;

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
                minimumInputLength: 2
            });

            // Initialize Select2 for Delivery Order
            $('#delivery_order_id').select2({
                theme: 'bootstrap',
                placeholder: 'Pilih delivery order (opsional)...',
                allowClear: true
            });

            // Load Delivery Order options dari server
            @foreach($deliveryOrders as $do)
                $('#delivery_order_id').append(new Option('{{ $do->kode_do }}', {{ $do->id }}));
            @endforeach

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
        });
    </script>
@endpush
