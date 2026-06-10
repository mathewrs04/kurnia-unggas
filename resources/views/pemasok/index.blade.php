@extends('layouts.app')
@section('content_title', 'Data Pemasok')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Pemasok</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <x-pemasok.form-pemasok />
            </div>
            <x-alert :errors="$errors" />
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pabrik</th>
                        <th>Nama Marketing</th>
                        <th>No Telepon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pemasok as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama_pabrik }}</td>
                            <td>{{ $item->nama_marketing }}</td>
                            <td>{{ $item->no_telp_marketing }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-pemasok.form-pemasok :id="$item->id" />
                                    <a href="{{ route('master.pemasok.destroy', $item->id) }}" data-confirm-delete="true"
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
