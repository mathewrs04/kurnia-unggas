@extends('layouts.app')
@section('content_title', 'Data Timbangan')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Timbangan</h4>
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
                <x-timbangan.form-timbangan />
            </div>
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis</th>
                        <th>Nama Karyawan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($timbangans as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->jenis }}</td>
                            <td>{{ $item->nama_karyawan }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-timbangan.form-timbangan :id="$item->id" />
                                    <a href="{{ route('master.timbangan.destroy', $item->id) }}" data-confirm-delete="true"
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
