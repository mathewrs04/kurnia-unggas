@extends('layouts.app')
@section('content_title', 'Detail Pembelian')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Detail Pembelian</h4>
            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            {{-- Informasi Pembelian --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-shopping-cart"></i> Informasi Pembelian
                    </h5>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="200"><strong>Kode Pembelian</strong></td>
                            <td>: {{ $pembelian->kode_pembelian }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Pembelian</strong></td>
                            <td>: {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>: 
                                @if($pembelian->status == 'belum ada DO')
                                    <span class="badge badge-secondary">Belum Ada DO</span>
                                @elseif($pembelian->status == 'belum bayar')
                                    <span class="badge badge-warning">Belum Bayar</span>
                                @else
                                    <span class="badge badge-success">Sudah Bayar</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="200"><strong>Peternak</strong></td>
                            <td>: {{ $pembelian->peternak->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Alamat</strong></td>
                            <td>: {{ $pembelian->peternak->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>No. Telepon</strong></td>
                            <td>: {{ $pembelian->peternak->no_telp ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Detail Pembelian --}}
            @foreach($pembelian->pembelianDetails as $detail)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-clipboard-list"></i> Detail Pembelian
                        </h5>
                    </div>

                    {{-- Informasi Timbangan --}}
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-weight"></i> Data Timbangan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="180"><strong>Jenis Timbangan</strong></td>
                                        <td>: {{ $detail->timbangan->jenis ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($detail->timbangan->tanggal)->format('d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Jumlah Ekor</strong></td>
                                        <td>: {{ number_format($detail->timbangan->total_jumlah_ekor ?? 0) }} ekor</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Berat</strong></td>
                                        <td>: {{ number_format($detail->timbangan->total_berat ?? 0, 2) }} kg</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Karyawan</strong></td>
                                        <td>: {{ $detail->timbangan->karyawans->pluck('nama')->join(', ') ?: '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Batch & DO --}}
                    <div class="col-md-6">
                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-box"></i> Batch Pembelian</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="180"><strong>Kode Batch</strong></td>
                                        <td>: {{ $detail->batchPembelian->kode_batch ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Stok Ekor</strong></td>
                                        <td>: {{ number_format($detail->batchPembelian->stok_ekor ?? 0) }} ekor</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Stok Kg</strong></td>
                                        <td>: {{ number_format($detail->batchPembelian->stok_kg ?? 0, 2) }} kg</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($detail->deliveryOrder)
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0"><i class="fas fa-truck"></i> Delivery Order</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="180"><strong>Kode DO</strong></td>
                                        <td>: {{ $detail->deliveryOrder->kode_do }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal DO</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($detail->deliveryOrder->tanggal_do)->format('d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Berat DO</strong></td>
                                        <td>: {{ number_format($detail->deliveryOrder->total_berat, 2) }} kg</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Susut</strong></td>
                                        <td>: <span class="badge badge-danger">{{ number_format($detail->susut_kg ?? 0, 2) }} kg</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada Delivery Order yang di-link
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Data Keranjang --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-shopping-basket"></i> Data Keranjang
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="80" class="text-center">No</th>
                                        <th class="text-center">Jumlah Ekor</th>
                                        <th class="text-center">Berat Ayam (kg)</th>
                                        <th class="text-center">Rata-rata Berat/Ekor (kg)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalEkor = 0;
                                        $totalBerat = 0;
                                    @endphp
                                    @foreach($detail->timbangan->keranjangs as $index => $keranjang)
                                        @php
                                            $totalEkor += $keranjang->jumlah_ekor;
                                            $totalBerat += $keranjang->berat_ayam;
                                            $rataRata = $keranjang->jumlah_ekor > 0 ? $keranjang->berat_ayam / $keranjang->jumlah_ekor : 0;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-center">{{ number_format($keranjang->jumlah_ekor) }}</td>
                                            <td class="text-right">{{ number_format($keranjang->berat_ayam, 2) }}</td>
                                            <td class="text-right">{{ number_format($rataRata, 3) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="thead-light">
                                    <tr>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">{{ number_format($totalEkor) }} ekor</th>
                                        <th class="text-right">{{ number_format($totalBerat, 2) }} kg</th>
                                        <th class="text-right">
                                            {{ $totalEkor > 0 ? number_format($totalBerat / $totalEkor, 3) : '0.000' }} kg/ekor
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Informasi Pembayaran --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-money-bill-wave"></i> Informasi Pembayaran
                        </h5>
                    </div>
                    <div class="col-md-6 offset-md-3">
                        <div class="card {{ $pembelian->status == 'sudah bayar' ? 'border-success' : 'border-secondary' }}">
                            <div class="card-header {{ $pembelian->status == 'sudah bayar' ? 'bg-success' : 'bg-secondary' }} text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-dollar-sign"></i> 
                                    {{ $pembelian->status == 'sudah bayar' ? 'Detail Pembayaran' : 'Belum Ada Pembayaran' }}
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($pembelian->status == 'sudah bayar')
                                    @php
                                        $beratBersih = ($detail->timbangan->total_berat ?? 0) - ($detail->susut_kg ?? 0);
                                    @endphp
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="180"><strong>Berat Bersih</strong></td>
                                            <td>: {{ number_format($beratBersih, 2) }} kg</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Harga per Kg</strong></td>
                                            <td>: Rp {{ number_format($detail->harga_beli_per_kg ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Subtotal</strong></td>
                                            <td><strong>: Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </table>
                                    <div class="alert alert-success mt-3 mb-0">
                                        <i class="fas fa-check-circle"></i> Pembayaran telah lunas
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        @if($pembelian->status == 'belum ada DO')
                                            Silakan link Delivery Order terlebih dahulu sebelum melakukan pembayaran
                                        @else
                                            Pembayaran belum dilakukan. Silakan proses pembayaran melalui halaman index.
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Action Buttons --}}
            <div class="row">
                <div class="col-md-12 text-right">
                    <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                    @if($pembelian->status != 'sudah bayar')
                        <a href="{{ route('pembelian.edit', $pembelian->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('pembelian.destroy', $pembelian->id) }}" 
                           data-confirm-delete="true" 
                           class="btn btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
