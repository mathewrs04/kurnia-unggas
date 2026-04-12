@extends('layouts.app')
@section('content_title', 'Laporan Keuntungan / Kerugian Bulanan')
@section('content')

    {{-- Filter Card --}}
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h4 class="card-title mb-0"><i class="fas fa-filter mr-2"></i>Filter Laporan</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('report.keuntungan.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Tahun</label>
                            <select name="tahun" class="form-control">
                                @foreach ($tahunList as $t)
                                    <option value="{{ $t }}" {{ (int) $tahun === $t ? 'selected' : '' }}>
                                        {{ $t }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Report Card --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="fas fa-chart-line mr-2"></i>
                Laporan Keuntungan / Kerugian Bulanan &mdash; Tahun {{ $tahun }}
            </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Bulan</th>
                            <th class="text-right">Total Penjualan</th>
                            <th class="text-right">Total Pembelian</th>
                            <th class="text-right">Biaya Operasional</th>
                            <th class="text-right">Laba / Rugi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $no => $row)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $row['nama_bulan'] }}</td>
                                <td class="text-right">Rp {{ number_format($row['total_penjualan'], 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($row['total_pembelian'], 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($row['total_biaya_operasional'], 0, ',', '.') }}</td>
                                <td class="text-right font-weight-bold">
                                    @if ($row['laba_rugi'] >= 0)
                                        <span class="text-success">
                                            Rp {{ number_format($row['laba_rugi'], 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            (Rp {{ number_format(abs($row['laba_rugi']), 0, ',', '.') }})
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="font-weight-bold bg-light">
                        <tr>
                            <td colspan="2" class="text-right">Grand Total</td>
                            <td class="text-right">Rp {{ number_format($grandTotalPenjualan, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($grandTotalPembelian, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($grandTotalBiayaOperasional, 0, ',', '.') }}</td>
                            <td class="text-right">
                                @if ($grandLabaRugi >= 0)
                                    <span class="text-success">
                                        Rp {{ number_format($grandLabaRugi, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-danger">
                                        (Rp {{ number_format(abs($grandLabaRugi), 0, ',', '.') }})
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Summary Cards --}}
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Penjualan</span>
                            <span class="info-box-number">Rp {{ number_format($grandTotalPenjualan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Pembelian</span>
                            <span class="info-box-number">Rp {{ number_format($grandTotalPembelian, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-secondary">
                        <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Biaya Operasional</span>
                            <span class="info-box-number">Rp {{ number_format($grandTotalBiayaOperasional, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box {{ $grandLabaRugi >= 0 ? 'bg-primary' : 'bg-danger' }}">
                        <span class="info-box-icon">
                            <i class="fas {{ $grandLabaRugi >= 0 ? 'fa-chart-line' : 'fa-chart-line' }}"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $grandLabaRugi >= 0 ? 'Total Laba' : 'Total Rugi' }}</span>
                            <span class="info-box-number">
                                @if ($grandLabaRugi >= 0)
                                    Rp {{ number_format($grandLabaRugi, 0, ',', '.') }}
                                @else
                                    (Rp {{ number_format(abs($grandLabaRugi), 0, ',', '.') }})
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
