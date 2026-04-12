@extends('layouts.app')
@section('content_title', 'Tambah Pembelian')
@section('content')

    @if ($selectedDO)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle"></i>
            <strong>Data dari Delivery Order:</strong> {{ $selectedDO->kode_do }} - Peternak:
            {{ $selectedDO->peternak->nama }} - Total Berat: {{ number_format($selectedDO->total_berat, 2) }} kg - Total
            Ekor: {{ number_format($selectedDO->total_jumlah_ekor) }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <x-alert :errors="$errors" />

    <form action="{{ route('pembelian.store') }}" method="POST" id="formPembelian">
        @csrf

        <!-- Card Data Pembelian -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Data Pembelian</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="peternak_id">Peternak <span class="text-danger">*</span></label>
                            <select name="peternak_id" id="peternak_id" class="form-control" required>
                                <option value="">-- Pilih Peternak --</option>
                            </select>
                            @error('peternak_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_pembelian">Tanggal Pembelian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" class="form-control"
                                value="{{ old('tanggal_pembelian', date('Y-m-d')) }}" required>
                            @error('tanggal_pembelian')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kode_pembelian">Kode Pembelian <span class="text-danger">*</span></label>
                            <input type="text" name="kode_pembelian" id="kode_pembelian" class="form-control"
                                value="{{ $kodePembelian }}" required>
                            @error('kode_pembelian')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <input type="hidden" name="status" value="belum bayar">
                            <input type="text" class="form-control" value="Belum Bayar" readonly>
                            <small class="text-muted">Status otomatis "Belum Bayar"</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Timbangan dan Keranjang -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Data Timbangan & Keranjang</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jenis_timbangan">Jenis Timbangan</label>
                            <input type="text" class="form-control" value="Timbangan Data Pembelian" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Karyawan Penanggung Jawab <small class="text-muted">(bisa pilih lebih dari 1)</small></label>
                            <select name="karyawan_ids[]" class="form-control select2-karyawan" multiple="multiple" style="width:100%">
                                @foreach ($karyawans as $k)
                                    <option value="{{ $k->id }}" {{ in_array($k->id, old('karyawan_ids', [])) ? 'selected' : '' }}>
                                        {{ $k->nama }} ({{ $k->posisi }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h4>Data Keranjang</h4>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tabelKeranjang">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Jumlah Ekor</th>
                                <th width="20%">Berat Keranjang (Kg)</th>
                                <th width="20%">Berat Total (Kg)</th>
                                <th width="20%">Berat Ayam (Kg)</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="keranjangBody">
                            @if(!old('keranjangs'))
                            <tr class="keranjang-item">
                                <td class="text-center">1</td>
                                <td>
                                    <input type="number" name="keranjangs[0][jumlah_ekor]" class="form-control jumlah-ekor"
                                        placeholder="Jumlah ekor" min="1" required>
                                </td>
                                <td>
                                    <input type="number" name="keranjangs[0][berat_keranjang]"
                                        class="form-control berat-keranjang" placeholder="Berat keranjang" step="0.01"
                                        min="0" required>
                                </td>
                                <td>
                                    <input type="number" name="keranjangs[0][berat_total]"
                                        class="form-control berat-total" placeholder="Berat total" step="0.01"
                                        min="0" required>
                                </td>
                                <td>
                                    <input type="number" name="keranjangs[0][berat_ayam]"
                                        class="form-control berat-ayam" placeholder="Auto-calculate" step="0.01"
                                        readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <button type="button" class="btn btn-success btn-sm" id="btnTambahKeranjang">
                                        <i class="fas fa-plus"></i> Tambah Keranjang
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1" class="text-right"><strong>Total:</strong></td>
                                <td><input type="number" id="totalJumlahEkor" class="form-control" readonly></td>
                                <td><input type="number" id="totalBerat" class="form-control" step="0.01" readonly>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card Detail Pembelian -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Detail Pembelian & Perhitungan Susut</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="delivery_order_id">Delivery Order <span class="text-danger">*</span></label>
                            <select name="delivery_order_id" id="delivery_order_id" class="form-control" required>
                                <option value="">-- Pilih DO (Wajib) --</option>
                            </select>

                            <small class="text-muted">Pilih DO yang sudah dibuat</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="susut_kg">Susut (kg)</label>
                            <input type="number" name="susut_kg" id="susut_kg" class="form-control"
                                placeholder="Auto-calculate" step="0.01" readonly>
                            @error('susut_kg')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small class="text-muted">Susut = Berat DO - Berat Pembelian</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <small class="text-muted">Harga beli per kg dan subtotal akan diisi saat proses pembayaran</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Pembelian
                        </button>
                        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2-karyawan').select2({
                theme: 'bootstrap',
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
            });

            let keranjangIndex = 1;

            // Pre-fill data dari DO jika ada
            @if ($selectedDO)
                // Set peternak yang sudah dipilih dari DO
                let peternakOption = new Option('{{ $selectedDO->peternak->nama }}',
                    {{ $selectedDO->peternak_id }}, true, true);
                $('#peternak_id').append(peternakOption).trigger('change');

                // Set tanggal pembelian sama dengan tanggal DO
                $('#tanggal_pembelian').val('{{ $selectedDO->tanggal_do->format('Y-m-d') }}');
            @endif

            // Initialize Select2 for Peternak
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

            // Initialize Select2 for Delivery Order
            $('#delivery_order_id').select2({
                theme: 'bootstrap',
                placeholder: 'Pilih delivery order (wajib)...',
                allowClear: true
            });

            // Load Delivery Order options dari server dengan data
            let deliveryOrderData = {
                @foreach ($deliveryOrders as $do)
                    {{ $do->id }}: {
                        kode_do: '{{ $do->kode_do }}',
                        total_berat: {{ $do->total_berat }},
                        peternak_id: {{ $do->peternak_id }},
                        peternak_nama: '{{ $do->peternak->nama ?? '' }}',
                        tanggal_do: '{{ $do->tanggal_do->format('Y-m-d') }}'
                    },
                @endforeach
            };

            @foreach ($deliveryOrders as $do)
                $('#delivery_order_id').append(new Option(
                    '{{ $do->kode_do }} ({{ number_format($do->total_berat, 2) }} kg)',
                    {{ $do->id }},
                    {{ $selectedDO && $selectedDO->id == $do->id ? 'true' : 'false' }},
                    {{ $selectedDO && $selectedDO->id == $do->id ? 'true' : 'false' }}
                ));
            @endforeach

            // Trigger change untuk DO yang sudah dipilih
            @if ($selectedDO)
                $('#delivery_order_id').trigger('change');
            @endif

            // Event listener untuk perubahan Delivery Order
            $('#delivery_order_id').on('change', function() {
                let doId = $(this).val();

                if (doId && deliveryOrderData[doId]) {
                    const doData = deliveryOrderData[doId];

                    // Set peternak sesuai DO yang dipilih
                    if (doData.peternak_id && doData.peternak_nama) {
                        let peternakOption = new Option(doData.peternak_nama, doData.peternak_id, true,
                            true);
                        $('#peternak_id').append(peternakOption).trigger('change');
                    }


                    $('#tanggal_pembelian').val(doData.tanggal_do);


                    // Hitung susut otomatis
                    let totalBeratDO = doData.total_berat;
                    let totalBeratPembelian = parseFloat($('#totalBerat').val()) || 0;
                    let susut = totalBeratDO - totalBeratPembelian;

                    $('#susut_kg').val(susut.toFixed(2));
                } else {
                    $('#susut_kg').val('0');
                    $('#peternak_id').val(null).trigger('change');

                }
            });

            // Tambah Keranjang
            $('#btnTambahKeranjang').click(function() {
                let index = $('.keranjang-item').length;
                let newRow = `
                    <tr class="keranjang-item">
                        <td class="text-center">${index + 1}</td>
                        <td>
                            <input type="number" name="keranjangs[${index}][jumlah_ekor]" 
                                   class="form-control jumlah-ekor" placeholder="Jumlah ekor" min="1" required>
                        </td>
                        <td>
                            <input type="number" name="keranjangs[${index}][berat_keranjang]" 
                                   class="form-control berat-keranjang" placeholder="Berat keranjang" step="0.01" min="0" required>
                        </td>
                        <td>
                            <input type="number" name="keranjangs[${index}][berat_total]" 
                                   class="form-control berat-total" placeholder="Berat total" step="0.01" min="0" required>
                        </td>
                        <td>
                            <input type="number" name="keranjangs[${index}][berat_ayam]" 
                                   class="form-control berat-ayam" placeholder="Auto-calculate" step="0.01" readonly>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#keranjangBody').append(newRow);
                keranjangIndex++;
                updateNomor();
                updateTotal();
            });

            // Hapus Keranjang
            $(document).on('click', '.btn-hapus-keranjang', function() {
                if ($('.keranjang-item').length > 1) {
                    $(this).closest('tr').remove();
                    updateNomor();
                    updateTotal();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Minimal harus ada 1 keranjang!'
                    });
                }
            });

            // Update nomor urut
            function updateNomor() {
                $('.keranjang-item').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                    $(this).find('.jumlah-ekor').attr('name', `keranjangs[${index}][jumlah_ekor]`);
                    $(this).find('.berat-keranjang').attr('name', `keranjangs[${index}][berat_keranjang]`);
                    $(this).find('.berat-total').attr('name', `keranjangs[${index}][berat_total]`);
                    $(this).find('.berat-ayam').attr('name', `keranjangs[${index}][berat_ayam]`);
                });

                // Enable/disable tombol hapus
                if ($('.keranjang-item').length === 1) {
                    $('.btn-hapus-keranjang').prop('disabled', true);
                } else {
                    $('.btn-hapus-keranjang').prop('disabled', false);
                }
            }

            // Hitung berat ayam otomatis (berat total - berat keranjang)
            function hitungBeratAyam(row) {
                let beratTotal = parseFloat(row.find('.berat-total').val()) || 0;
                let beratKeranjang = parseFloat(row.find('.berat-keranjang').val()) || 0;
                let beratAyam = beratTotal - beratKeranjang;
                row.find('.berat-ayam').val(beratAyam >= 0 ? beratAyam.toFixed(2) : 0);
            }

            // Event listener untuk auto-calculate berat ayam
            $(document).on('input', '.berat-total, .berat-keranjang', function() {
                let row = $(this).closest('tr');
                hitungBeratAyam(row);
                updateTotal();
            });

            // Update total jumlah ekor dan berat
            function updateTotal() {
                let totalEkor = 0;
                let totalBerat = 0;

                $('.keranjang-item').each(function() {
                    let ekor = parseFloat($(this).find('.jumlah-ekor').val()) || 0;
                    let berat = parseFloat($(this).find('.berat-ayam').val()) || 0;
                    totalEkor += ekor;
                    totalBerat += berat;
                });

                $('#totalJumlahEkor').val(totalEkor);
                $('#totalBerat').val(totalBerat.toFixed(2));

                // Update susut jika ada DO yang dipilih
                let doId = $('#delivery_order_id').val();
                if (doId && deliveryOrderData[doId]) {
                    let totalBeratDO = deliveryOrderData[doId].total_berat;
                    let susut = totalBeratDO - totalBerat;
                    $('#susut_kg').val(susut.toFixed(2));
                }
            }

            // Event listener untuk perubahan input jumlah ekor
            $(document).on('input', '.jumlah-ekor', function() {
                updateTotal();
            });

            // Initialize total
            updateTotal();

            // ===== Restore old() values setelah validasi gagal =====
            @if(old('peternak_id'))
                @php $oldPeternak = \App\Models\Peternak::find(old('peternak_id')); @endphp
                @if($oldPeternak)
                    let oldPeternakOption = new Option(@json($oldPeternak->nama), {{ old('peternak_id') }}, true, true);
                    $('#peternak_id').append(oldPeternakOption).trigger('change');
                @endif
            @endif

            @if(old('delivery_order_id') && !$selectedDO)
                $('#delivery_order_id').val({{ old('delivery_order_id') }}).trigger('change');
            @endif

            @if(old('susut_kg'))
                $('#susut_kg').val('{{ old('susut_kg') }}');
            @endif

            @if(old('keranjangs'))
                const oldKeranjangs = @json(old('keranjangs'));
                keranjangIndex = 0;
                Object.values(oldKeranjangs).forEach(function(item) {
                    let index = keranjangIndex;
                    let newRow = `
                        <tr class="keranjang-item">
                            <td class="text-center">${index + 1}</td>
                            <td>
                                <input type="number" name="keranjangs[${index}][jumlah_ekor]"
                                       class="form-control jumlah-ekor" placeholder="Jumlah ekor" min="1"
                                       value="${item.jumlah_ekor || ''}" required>
                            </td>
                            <td>
                                <input type="number" name="keranjangs[${index}][berat_keranjang]"
                                       class="form-control berat-keranjang" placeholder="Berat keranjang" step="0.01"
                                       min="0" value="${item.berat_keranjang || ''}" required>
                            </td>
                            <td>
                                <input type="number" name="keranjangs[${index}][berat_total]"
                                       class="form-control berat-total" placeholder="Berat total" step="0.01"
                                       min="0" value="${item.berat_total || ''}" required>
                            </td>
                            <td>
                                <input type="number" name="keranjangs[${index}][berat_ayam]"
                                       class="form-control berat-ayam" placeholder="Auto-calculate" step="0.01"
                                       value="${item.berat_ayam || ''}" readonly>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm btn-hapus-keranjang"
                                        ${index === 0 ? 'disabled' : ''}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#keranjangBody').append(newRow);
                    keranjangIndex++;
                });
                updateNomor();
                updateTotal();
            @endif
        });
    </script>
@endpush
