@extends('layouts.app')
@section('content_title', 'Dashboard Kasir')
@section('content')

<div class="row">
    <div class="col-12 col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Selamat Datang, {{ auth()->user()->name }}!</h3>
            </div>
            <div class="card-body">
                <p>Sebagai Kasir, Anda dapat melakukan transaksi penjualan.</p>
                
                <div class="mb-3">
                    <strong>Penjualan Hari Ini:</strong>
                    <h2>{{ $penjualanHariIni }} Transaksi</h2>
                </div>
                
                <a href="{{ route('penjualan.create') }}" class="btn btn-success btn-lg btn-block">
                    <i class="fas fa-plus mr-2"></i> Buat Penjualan Baru
                </a>
                
                <a href="{{ route('penjualan.index') }}" class="btn btn-primary btn-lg btn-block mt-2">
                    <i class="fas fa-list mr-2"></i> Lihat Riwayat Penjualan
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Informasi</h3>
            </div>
            <div class="card-body">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info mr-2"></i> Tips</h5>
                    <p>Pastikan untuk memeriksa stock ayam sebelum melakukan penjualan.</p>
                </div>
                
                <div class="callout callout-success">
                    <h5><i class="fas fa-check mr-2"></i> Praktik Terbaik</h5>
                    <p>Selalu konfirmasi data pelanggan dan produk sebelum menyimpan transaksi.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
