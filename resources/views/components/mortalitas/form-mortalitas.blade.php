<div>
    <button type="button" class="btn {{ $id ? 'btn-default' : 'btn-primary' }}" data-toggle="modal"
        data-target="#formMortalitas{{ $id ?? '' }}">
        {{ $id ? 'Edit' : 'Tambah Mortalitas' }}
    </button>
    <div class="modal fade" id="formMortalitas{{ $id ?? '' }}">
        <form action="{{ route('mortalitas-ayam.store') }}" method="POST">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $id ? 'Edit Mortalitas' : 'Tambah Mortalitas' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id ?? '' }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Batch Pembelian</label>
                                    <select name="batch_pembelian_id" class="form-control" required>
                                        <option value="">-- pilih batch --</option>
                                        @foreach ($batch as $b)
                                            <option value="{{ $b->id }}" {{ (string) old('batch_pembelian_id', $batch_pembelian_id ?? '') === (string) $b->id ? 'selected' : '' }}>
                                                {{ $b->kode_batch }} (stok: {{ number_format($b->stok_ekor) }} ekor)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="date" name="tanggal_mati" class="form-control"
                                        value="{{ old('tanggal_mati', $tanggal_mati ?? now()->toDateString()) }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jumlah Ekor Mati</label>
                                    <input type="number" name="jumlah_ekor" class="form-control" min="1"
                                        value="{{ old('jumlah_ekor', $jumlah_ekor ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Berat (kg)</label>
                                    <input type="number" step="0.01" name="berat_kg" class="form-control"
                                        value="{{ old('berat_kg', $berat_kg ?? '') }}" placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Catatan</label>
                                    <input type="text" name="catatan" class="form-control"
                                        value="{{ old('catatan', $catatan ?? '') }}" placeholder="Opsional, isi jika ada keterangan">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
