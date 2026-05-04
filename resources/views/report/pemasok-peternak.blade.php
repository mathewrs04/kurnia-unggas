@extends('layouts.app')
@section('content_title', 'Laporan Pemasok & Peternak')
@section('content')
    <div class="card card-primary card-outline mb-3">
        <div class="card-header">
            <h4 class="card-title mb-0"><i class="fas fa-truck-loading mr-2"></i>Report Pemasok & Peternak</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-0">
                Laporan ini menampilkan jumlah pembelian dan total nominal per peternak dalam setiap pemasok.
                Nominal dihitung dari subtotal pembelian.
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('report.pemasok-peternak.index') }}" method="GET" class="row">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label for="dari_tanggal" class="form-label">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" id="dari_tanggal" value="{{ $dariTanggal }}" class="form-control">
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <label for="sampai_tanggal" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" id="sampai_tanggal" value="{{ $sampaiTanggal }}" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                    <a href="{{ route('report.pemasok-peternak.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
            <div class="card card-primary">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pemasok</p>
                    <h3 class="mb-0">{{ number_format($summary['total_pemasok']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
            <div class="card card-info">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Peternak</p>
                    <h3 class="mb-0">{{ number_format($summary['total_peternak']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
            <div class="card card-warning">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pembelian</p>
                    <h3 class="mb-0">{{ number_format($summary['total_pembelian']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card card-success">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Nominal</p>
                    <h3 class="mb-0">Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    @if ($pemasoks->isEmpty())
        <div class="alert alert-warning mb-0">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            Tidak ada data pembelian pada rentang tanggal ini.
        </div>
    @else
        @foreach ($pemasoks as $pemasok)
            <div class="card mb-3">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div>
                        <h4 class="card-title mb-1">
                            <i class="fas fa-industry mr-2"></i>{{ $pemasok->nama_pabrik }}
                        </h4>
                        <small class="text-muted">
                            Marketing: {{ $pemasok->nama_marketing ?? '-' }} | Telp: {{ $pemasok->no_telp_marketing ?? '-' }}
                        </small>
                    </div>
                    <div class="mt-2 mt-md-0 text-md-right">
                        <span class="badge badge-info mr-1">{{ number_format($pemasok->total_peternak) }} peternak</span>
                        <span class="badge badge-primary mr-1">{{ number_format($pemasok->total_pembelian) }} pembelian</span>
                        <span class="badge badge-success">Rp {{ number_format($pemasok->total_nominal, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($pemasok->peternaks->isEmpty())
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Tidak ada data peternak.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped table-sm table-report-peternak">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" width="5%">Rank</th>
                                        <th>Nama Peternak</th>
                                        <th class="text-center" width="15%">Jumlah Pembelian</th>
                                        <th class="text-right" width="25%">Total Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pemasok->peternaks as $peternak)
                                        <tr>
                                            <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                                            <td>{{ $peternak->nama }}</td>
                                            <td class="text-center">{{ number_format($peternak->pembelians_count ?? 0) }}</td>
                                            <td class="text-right font-weight-bold">
                                                Rp {{ number_format($peternak->pembelian_details_sum_subtotal ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="font-weight-bold bg-light">
                                    <tr>
                                        <td colspan="2" class="text-right">Total</td>
                                        <td class="text-center">{{ number_format($pemasok->total_pembelian) }}</td>
                                        <td class="text-right">Rp {{ number_format($pemasok->total_nominal, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
@endsection

@push('scripts')
<script>
    $(function () {
        $('.table-report-peternak').each(function () {
            $(this).DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                pageLength: 10,
                order: [[3, 'desc'], [2, 'desc'], [1, 'asc']],
            });
        });
    });
</script>
@endpush
