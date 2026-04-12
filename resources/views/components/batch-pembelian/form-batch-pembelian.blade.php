<div>
    <button type="button" class="btn {{ $id ? 'btn-default' : 'btn-primary' }}" data-toggle="modal" data-target="#formBatchPembelian{{ $id ?? '' }}">
        {{ $id ? 'Edit' : 'Tambah Batch Pembelian' }}
    </button>
    <div class="modal fade" id="formBatchPembelian{{ $id ?? '' }}">
        <form action="{{ route('master.batch-pembelian.store') }}" method="POST">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $id ? 'Edit Batch Pembelian' : 'Tambah Batch Pembelian' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id ?? '' }}">
                        <div class="form-group">
                            <label for="harga_beli_per_kg">Harga Beli Per Kg</label>
                            <input type="number" class="form-control" id="harga_beli_per_kg" name="harga_beli_per_kg" value="{{ $id ? $harga_beli_per_kg : old('harga_beli_per_kg') }}">
                        </div>
                        <div class="form-group">
                            <label for="stok_ekor">Stok Ekor</label>
                            <input type="number" class="form-control" id="stok_ekor" name="stok_ekor" value="{{ $id ? $stok_ekor : old('stok_ekor') }}">
                        </div>
                        <div class="form-group">
                            <label for="stok_ekor_minimal">Stok Ekor Minimal</label>
                            <input type="number" class="form-control" id="stok_ekor_minimal" name="stok_ekor_minimal" value="{{ $id ? $stok_ekor_minimal : old('stok_ekor_minimal') }}">
                        </div>
                        <div class="form-group">
                            <label for="stok_kg">Stok Kg</label>
                            <input type="number" step="0.01" class="form-control" id="stok_kg" name="stok_kg" value="{{ $id ? $stok_kg : old('stok_kg') }}">
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
