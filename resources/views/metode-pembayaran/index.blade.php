@extends('layouts.app')
@section('content_title', 'Metode Pembayaran')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Metode Pembayaran</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <x-metode-pembayaran.form-metode-pembayaran />
            </div>
            <x-alert :errors="$errors" />
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Metode</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($metodePembayaran as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama_metode }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-metode-pembayaran.form-metode-pembayaran :id="$item->id" />
                                    <a href="{{ route('master.metode-pembayaran.destroy', $item->id) }}" data-confirm-delete="true"
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
