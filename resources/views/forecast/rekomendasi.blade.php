@extends('layouts.app')
@section('content_title', 'Rekomendasi Pembelian Ayam')
@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title mb-0"><i class="fas fa-calculator mr-1"></i> Rekomendasi Pembelian Berdasarkan Prediksi</h3>
    </div>

    <div class="card-body">
        <form method="GET" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-4 mb-2">
                    <label for="mulai" class="font-weight-bold mb-1">Tanggal Mulai</label>
                    <input
                        type="date"
                        id="mulai"
                        name="mulai"
                        class="form-control @error('mulai') is-invalid @enderror"
                        value="{{ old('mulai', $mulai) }}"
                        required>
                    @error('mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label for="sampai" class="font-weight-bold mb-1">Tanggal Sampai</label>
                    <input
                        type="date"
                        id="sampai"
                        name="sampai"
                        class="form-control @error('sampai') is-invalid @enderror"
                        value="{{ old('sampai', $sampai) }}"
                        required>
                    @error('sampai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-2 d-flex">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search mr-1"></i> Tampilkan
                    </button>
                    <a href="{{ route('forecast.rekomendasi') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <div class="alert alert-info h-100 mb-0">
                    <div><strong>Periode Prediksi:</strong> {{ \Carbon\Carbon::parse($mulai)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</div>
                    <div><strong>Durasi:</strong> {{ $hariPrediksi }} hari</div>
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <div class="alert alert-light border h-100 mb-0">
                    <div><strong>Total Prediksi Penjualan:</strong> {{ number_format($totalPrediksi) }} ekor</div>
                    <small class="text-muted"><i class="fas fa-info-circle"></i> Jumlah beli dibulatkan ke kelipatan 200 ekor sesuai kebiasaan.</small>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="table1">
                <thead>
                    <tr>
                        <th>Kode Batch</th>
                        <th>Stok Saat Ini (ekor)</th>
                        <th>Stok Minimal (ekor)</th>
                        <th>Sisa Stok Setelah Prediksi</th>
                        <th>Rekomendasi Beli (ekor)</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekomendasi as $r)
                    <tr>
                        <td>{{ $r['batch']->kode_batch }}</td>
                        <td>{{ number_format($r['stok']) }}</td>
                        <td>{{ number_format($r['minimal']) }}</td>
                        <td class="{{ $r['sisa'] < $r['minimal'] ? 'text-danger font-weight-bold' : '' }}">
                            {{ number_format($r['sisa']) }}
                        </td>
                        <td class="text-primary font-weight-bold">
                            @if($r['beli'] > 0)
                                {{ number_format($r['beli']) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $r['pesan'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="alert alert-secondary mt-3 mb-0">
            <i class="fas fa-info-circle"></i> <strong>Catatan:</strong>
            Pembelian direkomendasikan jika sisa stok diperkirakan di bawah batas minimal batch.
        </div>
    </div>
</div>
@endsection