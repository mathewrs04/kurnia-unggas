@extends('layouts.app')
@section('content_title', 'Stok Opname Ayam')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Riwayat Stok Opname</h4>
                <a href="{{ route('stok-opname.create') }}" class="btn btn-primary">Tambah Stok Opname</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Batch</th>
                        <th>Stok Sistem (ekor)</th>
                        <th>Stok Sistem (kg)</th>
                        <th>Berat Aktual (kg)</th>
                        <th>Susut (kg)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stokOpnames as $index => $opname)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $opname->tanggal_opname->format('d/m/Y') }}</td>
                            <td>{{ optional($opname->batch)->kode_batch }}</td>
                            <td>{{ number_format($opname->stok_ekor_sistem) }}</td>
                            <td>{{ number_format($opname->stok_kg_sistem, 2) }}</td>
                            <td>{{ number_format($opname->berat_aktual_kg, 2) }}</td>
                            <td class="text-danger">{{ number_format($opname->susut_kg, 2) }}</td>
                            <td>
                                <a href="{{ route('stok-opname.show', $opname->id) }}" class="btn btn-default btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('stok-opname.edit', $opname->id) }}" class="btn btn-warning btn-sm mx-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('stok-opname.destroy', $opname->id) }}" method="POST" class="d-inline">
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
