@extends('layouts.app')
@section('content_title', 'Detail Biaya Operasional')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">{{ $biaya->no_nota }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('biaya-operasional.edit', $biaya->id) }}" class="btn btn-warning text-white btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('biaya-operasional.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Tanggal</dt>
                    <dd class="col-sm-8">{{ $biaya->tanggal_biaya->format('d/m/Y') }}</dd>

                    <dt class="col-sm-4">Produk</dt>
                    <dd class="col-sm-8">{{ $biaya->produk->nama_produk ?? '-' }}</dd>

                    <dt class="col-sm-4">Metode Pembayaran</dt>
                    <dd class="col-sm-8">{{ $biaya->metodePembayaran->nama_metode ?? '-' }}</dd>

                    <dt class="col-sm-4">Harga Satuan</dt>
                    <dd class="col-sm-8">Rp {{ number_format($biaya->harga_satuan, 0, ',', '.') }}</dd>

                    <dt class="col-sm-4">Jumlah</dt>
                    <dd class="col-sm-8">{{ $biaya->jumlah }}</dd>

                    <dt class="col-sm-4">Subtotal</dt>
                    <dd class="col-sm-8">Rp {{ number_format($biaya->subtotal, 0, ',', '.') }}</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <label>Foto Nota</label>
                <div class="border rounded p-3">
                    @if ($biaya->foto_nota)
                        <a href="{{ asset('storage/' . $biaya->foto_nota) }}" target="_blank" class="d-block mb-2">Buka file</a>
                        <img src="{{ asset('storage/' . $biaya->foto_nota) }}" alt="Nota" class="img-fluid" onerror="this.style.display='none'">
                    @else
                        <p class="text-muted mb-0">Tidak ada file</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
