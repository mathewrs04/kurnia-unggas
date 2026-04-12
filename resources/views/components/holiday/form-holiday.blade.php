<div>
    <button type="button" class="btn {{ $id ? 'btn-default' : 'btn-primary' }}" data-toggle="modal" data-target="#formHoliday{{ $id ?? '' }}">
        {{ $id ? 'Edit' : 'Tambah Hari Libur' }}
    </button>
    <div class="modal fade" id="formHoliday{{ $id ?? '' }}">
        <form action="{{ route('master.holiday.store') }}" method="POST">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $id ? 'Edit Hari Libur' : 'Tambah Hari Libur' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id ?? '' }}">
                        <div class="form-group">
                            <label>Nama Libur</label>
                            <input type="text" class="form-control" name="name" value="{{ $name ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" class="form-control" name="date" value="{{ $date ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label>Pre Days</label>
                            <input type="number" class="form-control" name="pre_days" min="0" value="{{ $pre_days ?? 0 }}" required>
                        </div>
                        <div class="form-group">
                            <label>Post Days</label>
                            <input type="number" class="form-control" name="post_days" min="0" value="{{ $post_days ?? 0 }}" required>
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
