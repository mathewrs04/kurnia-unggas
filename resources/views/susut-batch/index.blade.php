@extends('layouts.app')
@section('content_title', 'Susut Batch Selesai')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Ringkasan Susut (stok ekor = 0)</h4>
        </div>
        <div class="card-body">
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Batch</th>
                        <th>Total Opname</th>
                        <th>Total Susut (kg)</th>
                        <th>Total Mortalitas (ekor)</th>
                        <th>Catatan Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($batches as $index => $batch)
                        @php
                            $totalSusut = $batch->stokOpnames->sum('susut_kg');
                            $lastOpname = $batch->stokOpnames->sortByDesc('tanggal_opname')->first();
                            $totalMortalitas = $batch->mortalitas->sum('jumlah_ekor');
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $batch->kode_batch }}</td>
                            <td>{{ $batch->stokOpnames->count() }} kali</td>
                            <td class="text-danger">{{ number_format($totalSusut, 2) }} kg</td>
                            <td>{{ number_format($totalMortalitas) }} ekor</td>
                            <td>{{ $lastOpname?->catatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
