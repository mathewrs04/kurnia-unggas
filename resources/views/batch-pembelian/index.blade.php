@extends('layouts.app')
@section('content_title', 'Data Batch Pembelian')
@section('content')

    {{-- ===== SUMMARY CARD ===== --}}
    <div class="row mb-3">
        {{-- Kotak 1: Total Stok Ayam (Ekor) --}}
        <div class="col-md-4 d-flex">
            <div class="info-box w-100 {{ $totalStokEkor < $stokMinimal && $stokMinimal > 0 ? 'bg-danger' : 'bg-info' }}">
                <span class="info-box-icon">
                    <i class="fas fa-warehouse"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Stok Ayam (Ekor) — Semua Batch</span>
                    <span class="info-box-number">{{ number_format($totalStokEkor) }} ekor</span>
                    @if ($stokMinimal > 0)
                        <div class="progress">
                            <div class="progress-bar"
                                 style="width: {{ min(100, ($totalStokEkor / $stokMinimal) * 100) }}%">
                            </div>
                        </div>
                        <span class="progress-description">
                            Minimal: {{ number_format($stokMinimal) }} ekor
                            @if ($totalStokEkor < $stokMinimal)
                                &nbsp;<strong>⚠ Di bawah batas!</strong>
                            @endif
                        </span>
                    @else
                        <span class="progress-description text-white-50">Stok minimal belum diatur</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kotak 2: Total Stok Kg --}}
        <div class="col-md-4 d-flex">
            <div class="info-box w-100 bg-success">
                <span class="info-box-icon"><i class="fas fa-weight"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Stok Kg — Semua Batch</span>
                    <span class="info-box-number">{{ number_format($totalStokKg, 2) }} kg</span>
                </div>
            </div>
        </div>

        {{-- Kotak 3: Stok Minimal Global --}}
        <div class="col-md-4 d-flex">
            <div class="info-box w-100 bg-warning">
                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Stok Minimal Global (Ayam/Ekor)</span>
                    <span class="info-box-number">{{ number_format($stokMinimal) }} ekor</span>
                    <span class="progress-description">
                        <a href="{{ route('setting.index') }}" class="text-white">
                            <i class="fas fa-cog"></i> Ubah di Pengaturan
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TABEL BATCH ===== --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Batch Pembelian</h4>
        </div>
        <div class="card-body">
            <x-alert :errors="$errors" />
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Batch</th>
                        <th>Harga Beli Per Kg</th>
                        <th>Stok Ayam (Ekor)</th>
                        <th>Stok Kg</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($batchPembelians as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->kode_batch }}</td>
                            <td>{{ number_format($item->harga_beli_per_kg) }}</td>
                            <td>{{ number_format($item->stok_ekor) }}</td>
                            <td>{{ number_format($item->stok_kg, 2) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-batch-pembelian.form-batch-pembelian :id="$item->id" />
                                    <a href="{{ route('master.batch-pembelian.destroy', $item->id) }}" data-confirm-delete="true"
                                        class="btn btn-danger">
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
@endsection