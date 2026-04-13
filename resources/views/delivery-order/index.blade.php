@extends('layouts.app')
@section('content_title', 'Data Delivery Order')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Delivery Order</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger d-flex flex-column">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <small class="text-white my-2">{{ $error }}</small>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="mb-3">
                <a href="{{ route('delivery-order.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Delivery Order
                </a>
            </div>
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode DO</th>
                        <th>Tanggal DO</th>
                        <th>Peternak</th>
                        <th>Total Jumlah Ekor</th>
                        <th>Total Berat (kg)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deliveryOrders as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->kode_do }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->tanggal_do)) }}</td>
                            <td>{{ $item->peternak->nama ?? 'N/A' }}</td>
                            <td>{{ $item->total_jumlah_ekor }}</td>
                            <td>{{ number_format($item->total_berat, 2) }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('delivery-order.show', $item->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('delivery-order.edit', $item->id) }}" class="btn btn-warning btn-sm text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('delivery-order.destroy', $item->id) }}" data-confirm-delete="true"
                                        class="btn btn-danger btn-sm">
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