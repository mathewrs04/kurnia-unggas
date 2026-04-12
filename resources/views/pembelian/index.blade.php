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
                                @if($item->status == 'belum_bayar')
                                    <span class="badge badge-warning">Belum Bayar</span>
                                @else
                                    <span class="badge badge-success">Sudah Bayar</span>
                                @endif
                            </td>
                            <td>{{ $item->peternak->nama ?? '-' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                        
                                    @if($item->status == 'belum_bayar')
                                        <button type="button" class="btn btn-success btn-sm me-2" 
                                                data-toggle="modal" 
                                                data-target="#modalBayar" 
                                                onclick="setBayarData({{ $item->id }}, '{{ $item->kode_pembelian }}', {{ $item->pembelianDetails->first()->timbangan->total_berat ?? 0 }})">
                                            <i class="fas fa-money-bill"></i> Bayar
                                        </button>
                                    @endif
                                    <a href="{{ route('pembelian.show', $item->id) }}" class="btn btn-info btn-sm me-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('pembelian.destroy', $item->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" data-confirm-delete="true">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
                                    <label for="total_berat_display">Total Berat(Kg)</label>
                                    <input type="text" id="total_berat_display" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="harga_per_kg">Harga per Kg <span class="text-danger">*</span></label>
                                    <input type="number" name="harga_per_kg" id="harga_per_kg" 
                                           class="form-control" placeholder="Masukkan harga per kg" 
                                           min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="metode_pembayaran">Metode Pembayaran <span class="text-danger">*</span></label>
                                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                                        <option value="">-- Pilih Metode Pembayaran --</option>
                                        @foreach ($metodePembayarans as $metode)
                                            <option value="{{ $metode->id }}">{{ $metode->nama_metode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="subtotal">Total yang Harus Dibayar</label>
                                    <input type="text" name="subtotal" id="subtotal" class="form-control form-control-lg" 
                                           readonly style="font-size: 1.5rem; font-weight: bold; color: #28a745;">
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

@push('scripts')
    <script>
        let totalHarusBayar = 0;


        // Function untuk set data modal Bayar
        function setBayarData(id, kodePembelian, totalBerat) {
            // Set action form
            $('#formBayar').attr('action', '/pembelian/' + id + '/bayar');
            
            // Set kode pembelian
            $('#kodePembelianText').text(kodePembelian);
            
            // Set data berat
            $('#total_berat_display').val(parseFloat(totalBerat).toFixed(2) + ' Kg');
            
            // Reset form
            $('#harga_per_kg').val('');
            $('#subtotal').val('');

            totalHarusBayar = 0;
        }

        $(document).ready(function() {
            // Hitung total saat harga per kg diinput
            $('#harga_per_kg').on('input', function() {
                let hargaPerKg = parseFloat($(this).val()) || 0;
                let totalBerat = parseFloat($('#total_berat_display').val()) || 0;
                
                totalHarusBayar = hargaPerKg * totalBerat;
                
                $('#subtotal').val('Rp ' + totalHarusBayar.toLocaleString('id-ID'));             
            
            });

            // Sebelum submit form bayar, update subtotal ke nilai integer
            $('#formBayar').on('submit', function(e) {
                // Ubah subtotal dari display format ke integer
                $('#subtotal').val(Math.round(totalHarusBayar));
            });

           
            // Reset modal saat ditutup
            $('#modalBayar').on('hidden.bs.modal', function () {
                $('#formBayar')[0].reset();
                $('#subtotal').val('');
                totalHarusBayar = 0;
            });
        });
    </script>
@endpush
