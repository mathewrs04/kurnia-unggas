<div>
    <button type="button" class="btn {{ $id ? 'btn-default' : 'btn-primary' }}" data-toggle="modal" data-target="#formPeternak{{ $id ?? '' }}">
        {{ $id ? 'Edit' : 'Tambah Peternak' }}
    </button>
    <div class="modal fade" id="formPeternak{{ $id ?? '' }}">
        <form action="{{ route('master.peternak.store') }}" method="POST">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $id ? 'Edit Peternak' : 'Tambah Peternak' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id ?? '' }}">
                        <div class="form-group">
                            <label for="">Pemasok</label>
                            <select class="form-control" id="pemasok_id" name="pemasok_id" value="{{ $id ? $pemasok_id : old('pemasok_id') }}">
                                <option value="">Pilih Pemasok</option>
                                @foreach ($pemasok as $item)
                                    <option value="{{ $item->id }}" {{ $pemasok_id || old('pemasok_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_pabrik }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="{{ $id ? $nama : old('nama') }}">
                        </div>
                        <div class="form-group">
                            <label for="">Alamat</label>
                            <input type="text" class="form-control" id="alamat" name="alamat" value="{{ $id ? $alamat : old('alamat') }}">
                        </div>
                        <div class="form-group">
                            <label for="">No. Telp</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp" value="{{ $id ? $no_telp : old('no_telp') }}">
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
