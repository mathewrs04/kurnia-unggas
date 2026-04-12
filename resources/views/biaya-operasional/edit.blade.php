@extends('layouts.app')
@section('content_title', 'Edit Biaya Operasional')
@section('content')
<form action="{{ route('biaya-operasional.update', $biaya->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Ubah Biaya Operasional</h4>
        </div>
        <div class="card-body">
            <x-alert :errors="$errors" />
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>No Nota</label>
                        <input type="text" name="no_nota" class="form-control" value="{{ old('no_nota', $biaya->no_nota) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Biaya</label>
                        <input type="date" name="tanggal_biaya" class="form-control" value="{{ old('tanggal_biaya', $biaya->tanggal_biaya->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <select name="metode_pembayaran_id" class="form-control" required>
                            <option value="">-- Pilih Metode --</option>
                            @foreach ($metodes as $metode)
                                <option value="{{ $metode->id }}" {{ old('metode_pembayaran_id', $biaya->metode_pembayaran_id) == $metode->id ? 'selected' : '' }}>{{ $metode->nama_metode }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Produk</label>
                        <select name="produk_id" class="form-control" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($produks as $produk)
                                <option value="{{ $produk->id }}" {{ old('produk_id', $biaya->produk_id) == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Foto Nota</label>
                        <input type="file" name="foto_nota" class="form-control" accept="image/*,.pdf">
                        @if ($biaya->foto_nota)
                            <small class="text-muted d-block mt-1">File saat ini: <a href="{{ asset('storage/' . $biaya->foto_nota) }}" target="_blank">Lihat</a></small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Harga Satuan</label>
                        <input type="number" name="harga_satuan" id="harga_satuan" class="form-control" min="0" value="{{ old('harga_satuan', $biaya->harga_satuan) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="{{ old('jumlah', $biaya->jumlah) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Subtotal</label>
                        <input type="text" id="subtotal_display" class="form-control" readonly>
                        <input type="hidden" name="subtotal" id="subtotal">
                        <small class="text-muted">Otomatis dihitung dari harga x jumlah</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex gap-2">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <a href="{{ route('biaya-operasional.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function hitungSubtotal() {
        const harga = parseInt(document.getElementById('harga_satuan').value) || 0;
        const jumlah = parseInt(document.getElementById('jumlah').value) || 0;
        const total = harga * jumlah;
        document.getElementById('subtotal_display').value = total > 0 ? 'Rp ' + total.toLocaleString('id-ID') : '';
        document.getElementById('subtotal').value = total;
    }

    document.getElementById('harga_satuan').addEventListener('input', hitungSubtotal);
    document.getElementById('jumlah').addEventListener('input', hitungSubtotal);
    hitungSubtotal();
</script>
@endpush
