@extends('layouts.app')
@section('content_title', 'Data Pembelian')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Pembelian</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger d-flex flex-column">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <small class="text-white my-2">{{ $error }}</small>
                        @endforeach
                    </ul>
                </div>

            @endif
            <a href="{{ route('pembelian.create') }}" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Tambah Pembelian
            </a>
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pembelian</th>
                        <th>Kode Pembelian</th>
                        <th>Status</th>
                        <th>Peternak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembelians as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d/m/Y') }}</td>
                            <td>{{ $item->kode_pembelian }}</td>
                            <td>
                                @if($item->status == 'belum bayar')
                                    <span class="badge badge-warning">Belum Bayar</span>
                                @else
                                    <span class="badge badge-success">Sudah Bayar</span>
                                @endif
                            </td>
                            <td>{{ $item->peternak->nama ?? '-' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->status == 'belum bayar')
                                        <button type="button" class="btn btn-success btn-sm me-2" 
                                                data-toggle="modal" 
                                                data-target="#modalBayar" 
                                                onclick="setBayarData({{ $item->id }}, '{{ $item->kode_pembelian }}', {{ $item->pembelianDetails->first()->timbangan->total_berat ?? 0 }}, {{ $item->pembelianDetails->first()->susut_kg ?? 0 }})">
                                            <i class="fas fa-money-bill"></i> Bayar
                                        </button>
                                    @endif
                                    <a href="{{ route('pembelian.show', $item->id) }}" class="btn btn-info btn-sm me-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pembelian.destroy', $item->id) }}" data-confirm-delete="true"
                                        class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Bayar Pembelian -->
    <div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-labelledby="modalBayarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="" method="POST" id="formBayar">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="modalBayarLabel">
                            <i class="fas fa-money-bill"></i> Pembayaran Pembelian
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Silakan masukkan detail pembayaran untuk pembelian <strong id="kodePembelianText"></strong>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_berat_display">Total Berat (Kg)</label>
                                    <input type="text" id="total_berat_display" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="susut_kg_display">Susut (Kg)</label>
                                    <input type="text" id="susut_kg_display" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="berat_bersih_display">Berat Bersih (Kg)</label>
                                    <input type="text" id="berat_bersih_display" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="harga_per_kg">Harga per Kg <span class="text-danger">*</span></label>
                                    <input type="number" name="harga_per_kg" id="harga_per_kg" 
                                           class="form-control" placeholder="Masukkan harga per kg" 
                                           min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="total_harus_bayar">Total yang Harus Dibayar</label>
                                    <input type="text" id="total_harus_bayar" class="form-control form-control-lg" 
                                           readonly style="font-size: 1.5rem; font-weight: bold; color: #28a745;">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nominal_bayar">Nominal Pembayaran <span class="text-danger">*</span></label>
                                    <input type="number" name="nominal_bayar" id="nominal_bayar" 
                                           class="form-control form-control-lg" 
                                           placeholder="Masukkan nominal pembayaran" 
                                           min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="kembalian">Kembalian</label>
                                    <input type="text" id="kembalian" class="form-control form-control-lg" 
                                           readonly style="font-size: 1.5rem; font-weight: bold; color: #007bff;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tanggal_bayar">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" 
                                           class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="catatan_bayar">Catatan</label>
                                    <textarea name="catatan_bayar" id="catatan_bayar" 
                                              class="form-control" rows="3" 
                                              placeholder="Catatan pembayaran (opsional)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Proses Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        let totalHarusBayar = 0;

        function setBayarData(id, kodePembelian, totalBerat, susutKg) {
            // Set action form
            $('#formBayar').attr('action', '/pembelian/' + id + '/bayar');
            
            // Set kode pembelian
            $('#kodePembelianText').text(kodePembelian);
            
            // Set data berat
            $('#total_berat_display').val(parseFloat(totalBerat).toFixed(2) + ' Kg');
            $('#susut_kg_display').val(parseFloat(susutKg).toFixed(2) + ' Kg');
            
            // Hitung berat bersih
            let beratBersih = parseFloat(totalBerat) - parseFloat(susutKg);
            $('#berat_bersih_display').val(beratBersih.toFixed(2) + ' Kg');
            
            // Store berat bersih for calculation
            $('#berat_bersih_display').data('value', beratBersih);
            
            // Reset form
            $('#harga_per_kg').val('');
            $('#nominal_bayar').val('');
            $('#total_harus_bayar').val('');
            $('#kembalian').val('');
            $('#catatan_bayar').val('');
            totalHarusBayar = 0;
        }

        $(document).ready(function() {
            // Hitung total saat harga per kg diinput
            $('#harga_per_kg').on('input', function() {
                let hargaPerKg = parseFloat($(this).val()) || 0;
                let beratBersih = parseFloat($('#berat_bersih_display').data('value')) || 0;
                
                totalHarusBayar = hargaPerKg * beratBersih;
                
                $('#total_harus_bayar').val('Rp ' + totalHarusBayar.toLocaleString('id-ID'));
                
                // Recalculate kembalian jika sudah ada nominal
                let nominalBayar = parseFloat($('#nominal_bayar').val()) || 0;
                if (nominalBayar > 0) {
                    let kembalian = nominalBayar - totalHarusBayar;
                    $('#kembalian').val('Rp ' + kembalian.toLocaleString('id-ID'));
                    
                    // Validasi
                    if (kembalian < 0) {
                        $('#kembalian').css('color', '#dc3545');
                        $('#kembalian').val('KURANG: Rp ' + Math.abs(kembalian).toLocaleString('id-ID'));
                    } else {
                        $('#kembalian').css('color', '#007bff');
                    }
                }
            });

            // Hitung kembalian saat nominal bayar diinput
            $('#nominal_bayar').on('input', function() {
                let nominalBayar = parseFloat($(this).val()) || 0;
                let kembalian = nominalBayar - totalHarusBayar;
                
                if (totalHarusBayar > 0) {
                    $('#kembalian').val('Rp ' + kembalian.toLocaleString('id-ID'));
                    
                    // Validasi
                    if (kembalian < 0) {
                        $('#kembalian').css('color', '#dc3545');
                        $('#kembalian').val('KURANG: Rp ' + Math.abs(kembalian).toLocaleString('id-ID'));
                    } else {
                        $('#kembalian').css('color', '#007bff');
                    }
                } else {
                    $('#kembalian').val('');
                }
            });

            // Validasi sebelum submit
            $('#formBayar').on('submit', function(e) {
                let nominalBayar = parseFloat($('#nominal_bayar').val()) || 0;
                
                if (nominalBayar < totalHarusBayar) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Kurang',
                        text: 'Nominal pembayaran kurang dari total yang harus dibayar!',
                        confirmButtonColor: '#d33'
                    });
                    return false;
                }

                if (totalHarusBayar <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Harga Belum Diisi',
                        text: 'Silakan masukkan harga per kg terlebih dahulu!',
                        confirmButtonColor: '#d33'
                    });
                    return false;
                }
            });

            // Reset modal saat ditutup
            $('#modalBayar').on('hidden.bs.modal', function () {
                $('#formBayar')[0].reset();
                $('#total_harus_bayar').val('');
                $('#kembalian').val('');
                totalHarusBayar = 0;
            });
        });
    </script>
@endpush
