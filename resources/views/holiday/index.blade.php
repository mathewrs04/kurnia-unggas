@extends('layouts.app')
@section('content_title', 'Data Hari Libur')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Data Hari Libur</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger d-flex flex-column">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <small class="text-white my-1">{{ $error }}</small>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-3">
                <x-holiday.form-holiday />
            </div>

            <table class="table table-sm" id="table-holiday">
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
