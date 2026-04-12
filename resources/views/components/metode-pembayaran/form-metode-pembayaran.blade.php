<div>
    <button type="button" class="btn {{ $id ? 'btn-default' : 'btn-primary' }}" data-toggle="modal" data-target="#formMetodePembayaran{{ $id ?? '' }}">
        {{ $id ? 'Edit' : 'Tambah Metode Pembayaran' }}
    </button>
    <div class="modal fade" id="formMetodePembayaran{{ $id ?? '' }}">
        <form action="{{ route('master.metode-pembayaran.store') }}" method="POST">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $id ? 'Edit Metode Pembayaran' : 'Tambah Metode Pembayaran' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id ?? '' }}">
                        <div class="form-group">
                            <label for="nama_metode">Nama Metode</label>
                            <input type="text" class="form-control" id="nama_metode" name="nama_metode" value="{{ $id ? $nama_metode : old('nama_metode') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" value="{{ $id ? $keterangan : old('keterangan') }}">
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
