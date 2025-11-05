@extends('layouts.app')
@section('content_title', 'Data Pemasok')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Pemasok</h4>
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
            <div>
                <x-pemasok.form-pemasok />
            </div>
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
