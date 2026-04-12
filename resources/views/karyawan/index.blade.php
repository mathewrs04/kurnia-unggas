@extends('layouts.app')
@section('content_title', 'Data Karyawan')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Karyawan</h4>
        </div>
        <div class="card-body">

            <div>
                <x-karyawan.form-karyawan />
            </div>
            <x-alert :errors="$errors" />
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Posisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($karyawans as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->posisi }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-karyawan.form-karyawan :id="$item->id" />
                                    <a href="{{ route('master.karyawan.destroy', $item->id) }}"
                                        data-confirm-delete="true" class="btn btn-danger">
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
