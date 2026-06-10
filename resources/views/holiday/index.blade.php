@extends('layouts.app')
@section('content_title', 'Data Hari Libur')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Data Hari Libur</h4>
        </div>
        <div class="card-body">
           

            <div class="mb-3">
                <x-holiday.form-holiday />
            </div>
            <x-alert :errors="$errors" />
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Pre Days</th>
                        <th>Post Days</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($holidays as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->pre_days }}</td>
                            <td>{{ $item->post_days }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <x-holiday.form-holiday :id="$item->id" :name="$item->name" :date="$item->date" :pre_days="$item->pre_days" :post_days="$item->post_days" />
                                    <a href="{{ route('master.holiday.destroy', $item->id) }}" data-confirm-delete="true"
                                        class="btn btn-danger btn-sm">
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
