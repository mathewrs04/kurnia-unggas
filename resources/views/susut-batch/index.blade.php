@extends('layouts.app')
@section('content_title', 'Susut Batch Selesai')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Ringkasan Susut dari Awal Batch sampai Stok Ekor Habis</h4>
        </div>
        <div class="card-body">
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Batch</th>
                        <th>Berat Awal (kg)</th>
                        <th>Total Berat Terjual (kg)</th>
                        <th>Total Berat Mortalitas (kg)</th>
                        <th>Total Opname</th>
                        <th>Total Susut Opname (kg)</th>
                        <th>Susut Total (kg)</th>
                        <th>Catatan Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($batches as $index => $batch)
                        @php
                            $lastOpname = $batch->stokOpnames->sortByDesc('tanggal_opname')->first();
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $batch->kode_batch }}</td>
                            <td>{{ number_format($batch->stok_awal_kg, 2) }} kg</td>
                            <td>{{ number_format($batch->total_berat_terjual, 2) }} kg</td>
                            <td>{{ number_format($batch->total_berat_mortalitas, 2) }} kg</td>
                            <td>{{ $batch->stokOpnames->count() }} kali</td>
                            <td>{{ number_format($batch->total_susut_opname_kg, 2) }} kg</td>
                            <td class="text-danger font-weight-bold">{{ number_format($batch->susut_total_kg, 2) }} kg</td>
                            <td>{{ $lastOpname?->catatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="alert alert-secondary mt-3 mb-0">
                <strong>Catatan:</strong> Susut total = Berat Awal - (Total Berat Terjual + Total Berat Mortalitas). 
                Mortalitas dianggap sebagai keluaran (tidak termasuk susut), penjualan tercatat sebagai terjual normal. 
                Susut yang tersisa adalah berat yang tidak tercatat di kedua alur tersebut.
            </div>
        </div>
    </div>
@endsection
