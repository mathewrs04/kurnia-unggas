@extends('layouts.app')
@section('content_title', 'Mortalitas Ayam')
@section('content')
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Catat Mortalitas</h4>
            
        </div>
        <div class="card-body">
            <x-mortalitas.form-mortalitas :batch="$batch" />
            <x-alert :errors="$errors" />
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Riwayat Mortalitas</h4>
        </div>
        <div class="card-body">
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Batch</th>
                        <th>Jumlah Ekor</th>
                        <th>Berat (kg)</th>
                        <th>Catatan</th>
                        <th>Sisa Stok Ekor</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mortalitas as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->tanggal_mati->format('d/m/Y') }}</td>
                            <td>{{ optional($item->batch)->kode_batch }}</td>
                            <td>{{ number_format($item->jumlah_ekor) }}</td>
                            <td>{{ $item->berat_kg ? number_format($item->berat_kg, 2) : '-' }}</td>
                            <td>{{ $item->catatan ?? '-' }}</td>
                            <td>{{ optional($item->batch)->stok_ekor !== null ? number_format($item->batch->stok_ekor) : '-' }}</td>
                            <td>
                                <x-mortalitas.form-mortalitas :id="$item->id" :batch="$batch" />
                                <form action="{{ route('mortalitas-ayam.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" data-confirm-delete="true">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
