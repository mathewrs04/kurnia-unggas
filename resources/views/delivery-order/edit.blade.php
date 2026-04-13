@extends('layouts.app')
@section('content_title', 'Edit Delivery Order')
@section('content')
    <form action="{{ route('delivery-order.update', $deliveryOrder->id) }}" method="POST" id="formDeliveryOrder">
        @csrf
        @method('PUT')

        <x-alert :errors="$errors" />

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Ubah Data Delivery Order</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kode_do">Kode DO <span class="text-danger">*</span></label>
                            <input type="text" id="kode_do" name="kode_do" class="form-control"
                                value="{{ old('kode_do', $deliveryOrder->kode_do) }}" readonly required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_do">Tanggal DO <span class="text-danger">*</span></label>
                            <input type="date" id="tanggal_do" name="tanggal_do" class="form-control"
                                value="{{ old('tanggal_do', optional($deliveryOrder->tanggal_do)->format('Y-m-d')) }}" required>
                            @error('tanggal_do')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="peternak_id">Peternak <span class="text-danger">*</span></label>
                            <select name="peternak_id" id="peternak_id" class="form-control" required>
                                <option value="{{ $deliveryOrder->peternak_id }}">{{ $deliveryOrder->peternak->nama ?? 'Peternak saat ini' }}</option>
                            </select>
                            @error('peternak_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="total_jumlah_ekor">Total Jumlah Ekor <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="total_jumlah_ekor"
                                name="total_jumlah_ekor" placeholder="Total ekor"
                                value="{{ old('total_jumlah_ekor', $deliveryOrder->total_jumlah_ekor) }}" min="1" required>
                            @error('total_jumlah_ekor')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="total_berat">Total Berat (kg) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="total_berat"
                                name="total_berat" placeholder="Total berat"
                                value="{{ old('total_berat', $deliveryOrder->total_berat) }}" step="0.01" min="0" required>
                            @error('total_berat')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('delivery-order.show', $deliveryOrder->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#peternak_id').select2({
                theme: 'bootstrap',
                placeholder: 'Cari peternak...',
                ajax: {
                    url: "{{ route('get-data.peternak') }}",
                    dataType: 'json',
                    delay: 250,
                    data: (params) => {
                        return {
                            search: params.term
                        };
                    },
                    processResults: (data) => {
                        return {
                            results: data.map((item) => ({
                                id: item.id,
                                text: item.nama
                            }))
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });

            @if (old('peternak_id') && old('peternak_id') != $deliveryOrder->peternak_id)
                @php $oldPeternakDO = \App\Models\Peternak::find(old('peternak_id')); @endphp
                @if ($oldPeternakDO)
                    const oldPeternakOption = new Option(@json($oldPeternakDO->nama), {{ old('peternak_id') }}, true, true);
                    $('#peternak_id').append(oldPeternakOption).trigger('change');
                @endif
            @endif
        });
    </script>
@endpush
