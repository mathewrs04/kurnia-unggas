@extends('layouts.app')
@section('content_title', 'Dashboard Pemilik Usaha')
@section('content')

<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Penjualan Bulan Ini</span>
                <span class="info-box-number">
                    Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
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
                    Rp {{ number_format($totalPembelian, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-file-invoice-dollar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Biaya Operasional</span>
                <span class="info-box-number">
                    Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon {{ $keuntungan >= 0 ? 'bg-primary' : 'bg-danger' }} elevation-1">
                <i class="fas fa-money-bill-wave"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">{{ $keuntungan >= 0 ? 'Keuntungan' : 'Kerugian' }}</span>
                <span class="info-box-number">
                    Rp {{ number_format(abs($keuntungan), 0, ',', '.') }}
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

<!-- Tabel Ringkasan -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ringkasan Keuangan Bulan Ini</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Pemasukan (Penjualan)</td>
                            <td class="text-success">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Pengeluaran (Pembelian)</td>
                            <td class="text-danger">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Pengeluaran (Biaya Operasional)</td>
                            <td class="text-danger">Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="font-weight-bold">
                            <td>Total {{ $keuntungan >= 0 ? 'Keuntungan' : 'Kerugian' }}</td>
                            <td class="{{ $keuntungan >= 0 ? 'text-primary' : 'text-danger' }}">
                                Rp {{ number_format(abs($keuntungan), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk grafik penjualan
    const dataPenjualan = {
        labels: {!! json_encode($grafikPenjualan->pluck('bulan')) !!},
        datasets: [{
            label: 'Penjualan',
            data: {!! json_encode($grafikPenjualan->pluck('total')) !!},
            backgroundColor: 'rgba(40, 167, 69, 0.2)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 2,
            fill: true
        }]
    };

    // Data untuk grafik pembelian
    const dataPembelian = {
        labels: {!! json_encode($grafikPembelian->pluck('bulan')) !!},
        datasets: [{
            label: 'Pembelian',
            data: {!! json_encode($grafikPembelian->pluck('total')) !!},
            backgroundColor: 'rgba(255, 193, 7, 0.2)',
            borderColor: 'rgba(255, 193, 7, 1)',
            borderWidth: 2,
            fill: true
        }]
    };

    // Config Chart
    const config = {
        type: 'line',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            return label;
                        }
                    }
                }
            }
        }
    };

    // Render grafik
    new Chart(document.getElementById('grafikPenjualan'), {
        ...config,
        data: dataPenjualan
    });

    new Chart(document.getElementById('grafikPembelian'), {
        ...config,
        data: dataPembelian
    });
</script>
@endpush
