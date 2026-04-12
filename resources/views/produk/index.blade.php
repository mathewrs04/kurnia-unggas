@extends('layouts.app')
@section('content_title', 'Data Produk')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Produk</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <x-produk.form-produk />
            </div>
            <x-alert :errors="$errors" />
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Tipe Produk</th>
                        <th>Satuan</th>
                        <th>Harga Satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produks as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama_produk }}</td>
                            <td>{{ $item->tipe_produk_formatted }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-produk.form-produk :id="$item->id" />
                                    <a href="{{ route('master.produk.destroy', $item->id) }}" data-confirm-delete="true"
                                        class="btn btn-danger ml-2">
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
