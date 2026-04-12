@extends('layouts.app')
@section('content_title', 'Laporan Penjualan Harian')
@section('content')

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('penjualan.laporan-harian') }}" method="GET" class="row">
            <div class="col-md-4">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}" class="form-control">
            </div>
            <div class="col-md-8 d-flex align-items-end mt-3 mt-md-0">
                <button type="submit" class="btn btn-primary mr-2">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
                <a href="{{ route('penjualan.laporan-harian') }}" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-body">
                <p class="text-muted mb-1">Total Ekor Terjual</p>
                <h3 class="mb-2">{{ number_format($totalEkor, 0, ',', '.') }} ekor</h3>
                <small class="text-muted">Tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Penjualan ({{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }})</h3>
    </div>
    <div class="card-body">
        <table id="tableLaporanPenjualan" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Nota</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Total Ekor</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penjualans as $penjualan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $penjualan->no_nota }}</td>
                    <td>{{ optional($penjualan->tanggal_jual)->format('d/m/Y') }}</td>
                    <td>{{ optional($penjualan->pelanggan)->nama }}</td>
                    <td>{{ number_format($penjualan->jumlah_ekor_produk_1 ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data penjualan pada tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        $('#tableLaporanPenjualan').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            pageLength: 10,
        });
    });
</script>
@endpush
