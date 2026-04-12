@extends('layouts.app')
@section('content_title', 'Data DeliveryOrder')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data DeliveryOrder</h4>
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deliveryOrders as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->kode_do }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-deliveryOrder.form-deliveryOrder :id="$item->id" />
                                    <a href="{{ route('delivery-order.destroy', $item->id) }}" data-confirm-delete="true"
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