@extends('layouts.app')
@section('content_title', 'Data Batch Pembelian')
@section('content')
   <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Batch Pembelian</h4>
        </div>
        <div class="card-body">
           
            <div>
                <x-batch-pembelian.form-batch-pembelian />
            </div>
            <x-alert :errors="$errors" />
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Batch</th>
                        <th>Harga Beli Per Kg</th>
                        <th>Stok Ekor</th>
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