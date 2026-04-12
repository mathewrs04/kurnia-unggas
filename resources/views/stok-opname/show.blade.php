@extends('layouts.app')
@section('content_title', 'Detail Stok Opname')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Detail Stok Opname</h4>
            <a href="{{ route('stok-opname.edit', $stokOpname->id) }}" class="btn btn-warning">Edit</a>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <tr>
                    <th>Tanggal</th>
                    <td>{{ $stokOpname->tanggal_opname->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Batch</th>
                    <td>{{ optional($stokOpname->batch)->kode_batch }}</td>
                </tr>
                <tr>
                    <th>Stok Sistem (ekor)</th>
                    <td>{{ number_format($stokOpname->stok_ekor_sistem) }}</td>
                </tr>
                <tr>
                    <th>Stok Sistem (kg)</th>
                    <td>{{ number_format($stokOpname->stok_kg_sistem, 2) }}</td>
                </tr>
                <tr>
                    <th>Berat Aktual (kg)</th>
                    <td>{{ number_format($stokOpname->berat_aktual_kg, 2) }}</td>
                </tr>
                <tr>
                    <th>Susut (kg)</th>
                    <td class="text-danger">{{ number_format($stokOpname->susut_kg, 2) }}</td>
                </tr>
                <tr>
                    <th>Timbangan</th>
                    <td>{{ optional($stokOpname->timbangan)->jenis ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td>{{ $stokOpname->catatan ?? '-' }}</td>
                </tr>
            </table>
            <a href="{{ route('stok-opname.index') }}" class="btn btn-default">Kembali</a>
        </div>
    </div>
@endsection
