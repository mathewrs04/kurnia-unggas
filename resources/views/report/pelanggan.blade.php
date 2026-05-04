@extends('layouts.app')
@section('content_title', 'Laporan Pelanggan')
@section('content')

<div class="card card-primary card-outline mb-3">
    <div class="card-header">
        <h4 class="card-title mb-0"><i class="fas fa-users mr-2"></i>Report Pelanggan</h4>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-0">
            Laporan ini menampilkan jumlah transaksi dan total nominal transaksi per pelanggan.
            Urutan dimulai dari nominal transaksi terbesar.
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0"><i class="fas fa-chart-bar mr-2"></i>Ranking Pelanggan</h4>
        <span class="badge badge-primary">{{ $pelanggans->count() }} pelanggan</span>
    </div>
    <div class="card-body">
        @if ($pelanggans->isEmpty())
            <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Tidak ada data pelanggan.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped table-sm" id="tableReportPelanggan">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" width="5%">Rank</th>
                            <th>Nama Pelanggan</th>
                            <th class="text-center" width="15%">Jumlah Transaksi</th>
                            <th class="text-right" width="25%">Total Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelanggans as $pelanggan)
                            <tr>
                                <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                                <td>{{ $pelanggan->nama }}</td>
                                <td class="text-center">{{ number_format($pelanggan->penjualans_count ?? 0) }}</td>
                                <td class="text-right font-weight-bold">
                                    Rp {{ number_format($pelanggan->penjualans_sum_subtotal ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="font-weight-bold bg-light">
                        <tr>
                            <td colspan="2" class="text-right">Total</td>
                            <td class="text-center">{{ number_format($pelanggans->sum('penjualans_count')) }}</td>
                            <td class="text-right">
                                Rp {{ number_format($pelanggans->sum(fn ($item) => $item->penjualans_sum_subtotal ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        $('#tableReportPelanggan').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            pageLength: 10,
            order: [[3, 'desc'], [2, 'desc'], [1, 'asc']],
        });
    });
</script>
@endpush
