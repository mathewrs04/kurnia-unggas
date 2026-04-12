@extends('layouts.app')
@section('content_title', 'Biaya Operasional')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Daftar Biaya Operasional</h4>
                <a href="{{ route('biaya-operasional.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Biaya
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Nota</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Metode Pembayaran</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($biayaOperasionals as $index => $biaya)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $biaya->no_nota }}</td>
                            <td>{{ optional($biaya->tanggal_biaya)->format('d/m/Y') }}</td>
                            <td>{{ $biaya->produk->nama_produk ?? '-' }}</td>
                            <td>{{ $biaya->metodePembayaran->nama_metode ?? '-' }}</td>
                            <td>Rp {{ number_format($biaya->harga_satuan, 0, ',', '.') }}</td>
                            <td>{{ $biaya->jumlah }}</td>
                            <td>Rp {{ number_format($biaya->subtotal, 0, ',', '.') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('biaya-operasional.show', $biaya->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('biaya-operasional.edit', $biaya->id) }}"
                                        class="btn btn-warning btn-sm text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('biaya-operasional.destroy', $biaya->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" data-confirm-delete="true">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
