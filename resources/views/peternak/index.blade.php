@extends('layouts.app')
@section('content_title', 'Data Peternak')
@section('content')
   <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Peternak</h4>
        </div>
        <div class="card-body">
           
            <div>
                <x-peternak.form-peternak />
            </div>
            <x-alert :errors="$errors" />
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Peternak</th>
                        <th>Alamat</th>
                        <th>No Telepon</th>
                        <th>Pemasok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peternaks as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>{{ $item->no_telp }}</td>
                            <td>{{ $item->pemasok->nama_pabrik }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-peternak.form-peternak :id="$item->id" />
                                    <a href="{{ route('master.peternak.destroy', $item->id) }}" data-confirm-delete="true"
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