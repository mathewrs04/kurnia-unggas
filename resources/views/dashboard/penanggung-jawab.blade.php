@extends('layouts.app')
@section('content_title', 'Dashboard Penanggung Jawab')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5>Selamat Datang, <strong>{{ auth()->user()->name }}</strong>!</h5>
                <p>Sebagai Penanggung Jawab, Anda memiliki akses ke semua fitur sistem.</p>
                
                <div class="mt-4">
                    <h6>Menu Utama:</h6>
                    <div class="list-group">
                        <a href="{{ route('pembelian.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-box mr-2"></i> Kelola Pembelian
                        </a>
                        <a href="{{ route('penjualan.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-cart mr-2"></i> Kelola Penjualan
                        </a>
                        <a href="{{ route('master.pemasok.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-truck mr-2"></i> Kelola Pemasok
                        </a>
                        <a href="{{ route('master.peternak.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-tie mr-2"></i> Kelola Peternak
                        </a>
                        <a href="{{ route('master.pelanggan.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-users mr-2"></i> Kelola Pelanggan
                        </a>
                        <a href="{{ route('master.produk.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tag mr-2"></i> Kelola Produk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
