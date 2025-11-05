<div>
    <button type="button" class="btn {{ $id ? 'btn-default' : 'btn-primary' }}" data-toggle="modal" data-target="#formPemasok{{ $id ?? '' }}">
        {{ $id ? 'Edit' : 'Tambah Pemasok' }}
    </button>
    <div class="modal fade" id="formPemasok{{ $id ?? '' }}">
        <form action="{{ route('master.pemasok.store') }}" method="POST">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $id ? 'Edit Pemasok' : 'Tambah Pemasok' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id ?? '' }}">
                        <div class="form-group">
                            <label for="">Nama Pabrik</label>
                            <input type="text" class="form-control" id="nama_pabrik" name="nama_pabrik" value="{{ $nama_pabrik }}">
                        </div>
                        <div class="form-group">
                            <label for="">Nama Marketing</label>
                            <input type="text" class="form-control" id="nama_marketing" name="nama_marketing" value="{{ $nama_marketing }}">
                        </div>
                        <div class="form-group">
                            <label for="">No. Telp Marketing</label>
                            <input type="text" class="form-control" id="no_telp_marketing" name="no_telp_marketing" value="{{ $no_telp_marketing }}">
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
