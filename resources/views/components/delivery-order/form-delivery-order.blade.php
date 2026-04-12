@props(['kodeDO' => '', 'peternaks' => []])

<div>
    <button type="button" class="btn {{ $id ? 'btn-default' : 'btn-primary' }}" data-toggle="modal" data-target="#formDeliveryOrder{{ $id ?? '' }}">
        {{ $id ? 'Edit' : 'Tambah Delivery Order' }}
    </button>

    <div class="modal fade" id="formDeliveryOrder{{ $id ?? '' }}">
        <form action="{{ route('delivery-order.store') }}" method="POST">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Delivery Order</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @csrf

                        {{-- Kode DO --}}
                        <div class="form-group">
                            <label for="kode_do">Kode DO</label>
                            <input type="text" id="kode_do" name="kode_do" class="form-control"
                                value="{{ $kodeDO }}" readonly required>
                            <small class="text-muted">Kode DO otomatis di-generate</small>
                        </div>

                        {{-- Tanggal DO --}}
                        <div class="form-group">
                            <label for="tanggal_do">Tanggal DO</label>
                            <input type="date" id="tanggal_do" name="tanggal_do" class="form-control"
                                value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="peternak_id">Peternak <span class="text-danger">*</span></label>
                            <select name="peternak_id" id="peternak_id" class="form-control" required>
                                <option value="">-- Pilih Peternak --</option>
                                @foreach ($peternaks as $peternak)
                                    <option value="{{ $peternak->id }}"
                                        {{ old('peternak_id') == $peternak->id ? 'selected' : '' }}>
                                        {{ $peternak->nama ?? 'Nama tidak tersedia' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('peternak_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                    
                       

                        <div class="form-group">
                            <label for="total_berat">Total Berat (kg)</label>
                            <input type="text" class="form-control" id="total_berat" name="total_berat" required>
                        </div>

                        <div class="form-group">
                            <label for="total_jumlah_ekor">Total Jumlah Ekor</label>
                            <input type="number" class="form-control" id="total_jumlah_ekor" name="total_jumlah_ekor" required>
                        </div>


                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Delivery Order</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

