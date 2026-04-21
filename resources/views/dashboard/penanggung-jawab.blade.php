@extends('layouts.app')
@section('content_title', 'Dashboard Penanggung Jawab')
@section('content')

<!-- Alert Warning untuk Stok Kritis -->
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
    </div>
@endif

<!-- Info Boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Penjualan Bulan Ini</span>
                <span class="info-box-number">
                    @php
                        $bulanIni = \Carbon\Carbon::now()->format('Y-m');
                        $totalPenjualanBulanIni = \App\Models\Penjualan::whereRaw("DATE_FORMAT(tanggal_jual, '%Y-%m') = ?", [$bulanIni])->sum('subtotal');
                    @endphp
                    Rp {{ number_format($totalPenjualanBulanIni, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-box"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pembelian Bulan Ini</span>
                <span class="info-box-number">
                    @php
                        $totalPembelianBulanIni = \App\Models\Pembelian::whereRaw("DATE_FORMAT(tanggal_pembelian, '%Y-%m') = ?", [$bulanIni])
                            ->with('pembelianDetails')->get()->sum(function ($pembelian) {
                                return $pembelian->pembelianDetails->sum('subtotal');
                            });
                    @endphp
                    Rp {{ number_format($totalPembelianBulanIni, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Grafik -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Penjualan (6 Bulan Terakhir)</h3>
            </div>
            <div class="card-body">
                <canvas id="grafikPenjualan" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Pembelian (6 Bulan Terakhir)</h3>
            </div>
            <div class="card-body">
                <canvas id="grafikPembelian" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Menu Utama -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Menu Utama</h3>
            </div>
            <div class="card-body">
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Grafik Penjualan
    const gradientPenjualan = document.getElementById('grafikPenjualan').getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradientPenjualan.addColorStop(0, 'rgba(40, 167, 69, 0.5)');
    gradientPenjualan.addColorStop(1, 'rgba(40, 167, 69, 0)');

    new Chart(document.getElementById('grafikPenjualan'), {
        type: 'line',
        data: {
            labels: @json($grafikPenjualan->pluck('bulan')),
            datasets: [{
                label: 'Penjualan (Rp)',
                data: @json($grafikPenjualan->pluck('total')),
                borderColor: '#28a745',
                backgroundColor: gradientPenjualan,
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + formatCurrency(value);
                        }
                    }
                }
            }
        }
    });

    // Grafik Pembelian
    const gradientPembelian = document.getElementById('grafikPembelian').getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradientPembelian.addColorStop(0, 'rgba(255, 193, 7, 0.5)');
    gradientPembelian.addColorStop(1, 'rgba(255, 193, 7, 0)');

    new Chart(document.getElementById('grafikPembelian'), {
        type: 'line',
        data: {
            labels: @json($grafikPembelian->pluck('bulan')),
            datasets: [{
                label: 'Pembelian (Rp)',
                data: @json($grafikPembelian->pluck('total')),
                borderColor: '#ffc107',
                backgroundColor: gradientPembelian,
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + formatCurrency(value);
                        }
                    }
                }
            }
        }
    });

    // Helper function to format currency
    function formatCurrency(value) {
        if (value >= 1000000) {
            return (value / 1000000).toFixed(1) + 'M';
        } else if (value >= 1000) {
            return (value / 1000).toFixed(1) + 'K';
        }
        return value.toString();
    }
</script>

@endsection
