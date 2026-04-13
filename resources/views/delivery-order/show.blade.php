@extends('layouts.app')
@section('content_title', 'Detail Delivery Order')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4 class="card-title mb-0">{{ $deliveryOrder->kode_do }}</h4>
            <div class="ml-auto d-flex align-items-center">
                <a href="{{ route('delivery-order.edit', $deliveryOrder->id) }}" class="btn btn-warning text-white btn-sm mr-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('delivery-order.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Kode DO</dt>
                        <dd class="col-sm-7">{{ $deliveryOrder->kode_do }}</dd>

                        <dt class="col-sm-5">Tanggal DO</dt>
                        <dd class="col-sm-7">{{ optional($deliveryOrder->tanggal_do)->format('d/m/Y') }}</dd>

                        <dt class="col-sm-5">Peternak</dt>
                        <dd class="col-sm-7">{{ $deliveryOrder->peternak->nama ?? '-' }}</dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Total Jumlah Ekor</dt>
                        <dd class="col-sm-7">{{ number_format($deliveryOrder->total_jumlah_ekor) }} ekor</dd>

                        <dt class="col-sm-5">Total Berat</dt>
                        <dd class="col-sm-7">{{ number_format($deliveryOrder->total_berat, 2) }} kg</dd>

                        <dt class="col-sm-5">Dibuat Pada</dt>
                        <dd class="col-sm-7">{{ optional($deliveryOrder->created_at)->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
