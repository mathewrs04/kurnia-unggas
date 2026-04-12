@extends('layouts.app')
@section('content_title', 'Detail Penjualan')
@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Informasi Penjualan</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">No Nota</th>
                        <td>: {{ $penjualan->no_nota }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Penjualan</th>
                        <td>: {{ $penjualan->tanggal_jual->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Pelanggan</th>
                        <td>: {{ $penjualan->pelanggan->nama }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Alamat</th>
                        <td>: {{ $penjualan->pelanggan->alamat }}</td>
                    </tr>
                    <tr>
                        <th>No Telepon</th>
                        <td>: {{ $penjualan->pelanggan->no_telp }}</td>
                    </tr>
                    <tr>
                        <th>Total Penjualan</th>
                        <td>: <strong class="text-success">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@php
    $detailsAyam = $penjualan->penjualanDetails->where('produk.tipe_produk', 'ayam_hidup');
    $detailsJasa = $penjualan->penjualanDetails->where('produk.tipe_produk', 'jasa');
    $totalSebelumDiskon = $penjualan->penjualanDetails->sum('subtotal');
    $diskon = $totalSebelumDiskon - $penjualan->subtotal;
@endphp

<!-- Detail Ayam -->
@if($detailsAyam->count() > 0)
<div class="card">
    <div class="card-header bg-danger text-white">
        <h3 class="card-title"><i class="fas fa-drumstick-bite"></i> Detail Penjualan Ayam Hidup</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Deskripsi</th>
                        <th>Batch</th>
                   
                        <th>Jumlah Ekor</th>
                        <th>Berat (kg)</th>
                        <th>Harga per Kg</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailsAyam as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->deskripsi }}</td>
                        <td>{{ $detail->batch ? $detail->batch->kode_batch : '-' }}</td>
                        
                        <td>{{ number_format($detail->jumlah_ekor) }}</td>
                        <td>{{ $detail->jumlah_berat ? number_format($detail->jumlah_berat, 2) : '-' }}</td>
                        <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3  " class="text-right">Total Ayam</th>
                        <th>{{ number_format($detailsAyam->sum('jumlah_ekor')) }} ekor</th>
                        <th>{{ number_format($detailsAyam->sum('jumlah_berat'), 2) }} kg</th>
                        <th></th>
                        <th>Rp {{ number_format($detailsAyam->sum('subtotal'), 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Detail Jasa -->
@if($detailsJasa->count() > 0)
<div class="card">
    <div class="card-header bg-info text-white">
        <h3 class="card-title"><i class="fas fa-tools"></i> Detail Penjualan Jasa</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk Jasa</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Ekor</th>
                        <th>Harga per Ekor</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailsJasa as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->produk->nama_produk }}</td>
                        <td>{{ $detail->deskripsi }}</td>
                        <td>{{ number_format($detail->jumlah_ekor) }}</td>
                        <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Total Jasa</th>
                        <th>{{ number_format($detailsJasa->sum('jumlah_ekor')) }} ekor</th>
                        <th></th>
                        <th>Rp {{ number_format($detailsJasa->sum('subtotal'), 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Summary Total -->
<div class="card">
    <div class="card-header bg-success text-white">
        <h3 class="card-title"><i class="fas fa-calculator"></i> Ringkasan Pembayaran</h3>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th width="70%" class="text-right">Subtotal Ayam:</th>
                <td class="text-right">Rp {{ number_format($detailsAyam->sum('subtotal'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th class="text-right">Subtotal Jasa:</th>
                <td class="text-right">Rp {{ number_format($detailsJasa->sum('subtotal'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th class="text-right">Total Sebelum Diskon:</th>
                <td class="text-right">Rp {{ number_format($totalSebelumDiskon, 0, ',', '.') }}</td>
            </tr>
            @if($diskon > 0)
            <tr>
                <th class="text-right">Diskon:</th>
                <td class="text-right text-danger">- Rp {{ number_format($diskon, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr style="border-top: 2px solid #000;">
                <th class="text-right" style="font-size: 20px;">TOTAL BAYAR:</th>
                <td class="text-right text-success" style="font-size: 24px; font-weight: bold;">
                    Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak
        </button>
    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        .sidebar,
        .main-header,
        .main-footer,
        .card-footer,
        .btn {
            display: none !important;
        }

        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush
