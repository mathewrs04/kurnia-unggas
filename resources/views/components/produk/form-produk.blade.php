<div>
    <button type="button" class="btn {{ $id ? 'btn-default' : 'btn-primary' }}" data-toggle="modal"
        data-target="#formProduk{{ $id ?? '' }}">
        {{ $id ? 'Edit' : 'Tambah Produk' }}
    </button>
    <div class="modal fade" id="formProduk{{ $id ?? '' }}">
        <form action="{{ route('master.produk.store') }}" method="POST">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $id ? 'Edit Produk' : 'Tambah Produk' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id ?? '' }}">

                        <div class="form-group">
                            <label for="">Nama Produk</label>
                            <input type="text" class="form-control" id="nama_produk" name="nama_produk"
                                value="{{ $id ? $nama_produk : old('nama_produk') }}">
                        </div>
                        <div class="form-group">
                            <label for="">Tipe Produk</label>
                            <select class="form-control" id="tipe_produk" name="tipe_produk">
                                <option value="">Pilih Tipe Produk</option>
                                <option value="ayam_hidup"
                                    {{ ($id && $tipe_produk == 'ayam_hidup') || old('tipe_produk') == 'ayam_hidup' ? 'selected' : '' }}>
                                    Ayam Hidup
                                </option>
                                <option value="jasa"
                                    {{ ($id && $tipe_produk == 'jasa') || old('tipe_produk') == 'jasa' ? 'selected' : '' }}>
                                    Jasa
                                </option>
                                <option value="barang_operasional"
                                    {{ ($id && $tipe_produk == 'barang_operasional') || old('tipe_produk') == 'barang_operasional' ? 'selected' : '' }}>
                                    Barang Operasional
                                </option>
                                <option value="biaya_operasional"
                                    {{ ($id && $tipe_produk == 'biaya_operasional') || old('tipe_produk') == 'biaya_operasional' ? 'selected' : '' }}>
                                    Biaya Operasional
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Satuan</label>
                            <input type="text" class="form-control" id="satuan" name="satuan"
                                value="{{ $id ? $satuan : old('satuan') }}">
                        </div>
                        <div class="form-group">
                            <label for="">Harga Satuan</label>
                            <input type="text" class="form-control" id="harga_satuan" name="harga_satuan"
                                value="{{ $id ? $harga_satuan : old('harga_satuan') }}">
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </form>
    </div>
</div>
