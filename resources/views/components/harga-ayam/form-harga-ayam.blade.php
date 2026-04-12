<div>
    <button type="button" class="btn {{ $id ? 'btn-default btn-sm' : 'btn-primary' }}"
        data-toggle="modal" data-target="#formHargaAyam{{ $id ?? '' }}">
        @if ($id)
            <i class="fas fa-edit"></i>
        @else
            Tambah Harga
        @endif
    </button>

    <div class="modal fade" id="formHargaAyam{{ $id ?? '' }}">
        <form action="{{ route('master.harga-ayam.store') }}" method="POST">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $id ? 'Edit Harga Ayam' : 'Tambah Harga Ayam' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id ?? '' }}">

                        <div class="form-group">
                            <label>Produk</label>
                            
                            <input type="hidden" name="produks_id" value="{{ $produkList->id ?? '' }}">
                            <input type="text" class="form-control" value="{{ $produkList->nama_produk ?? '-' }}" readonly disabled>
                        </div>

                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                value="{{ $tanggal ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label>Harga Eceran (Rp)</label>
                            <input type="number" name="harga_eceran" class="form-control" min="0"
                                value="{{ $harga_eceran ?? '' }}" placeholder="Contoh: 28000">
                        </div>

                        <div class="form-group">
                            <label>Harga Partai (Rp)</label>
                            <input type="number" name="harga_partai" class="form-control" min="0"
                                value="{{ $harga_partai ?? '' }}" placeholder="Contoh: 25000">
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
