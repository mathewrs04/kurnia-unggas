@extends('layouts.app')
@section('content_title', 'Setting Harga Ayam')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Setting Harga Ayam</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <x-harga-ayam.form-harga-ayam />
            </div>
            <x-alert :errors="$errors" />
            <table class="table table-sm" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Tanggal</th>
                        <th>Harga Eceran</th>
                        <th>Harga Partai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hargaAyams as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                            <td>Rp {{ number_format($item->harga_eceran, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->harga_partai, 0, ',', '.') }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <x-harga-ayam.form-harga-ayam :id="$item->id" />
                                    <a href="{{ route('master.harga-ayam.destroy', $item->id) }}"
                                        data-confirm-delete="true" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if ($hargaAyams->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data harga.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

        </div>
    </div>
@endsection
