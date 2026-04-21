@extends('layouts.app')
@section('content_title', 'Laporan Setoran')
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('report.setoran.index') }}" method="GET" class="row">
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
                    <a href="{{ route('report.setoran.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Setoran</p>
                    <h3 class="mb-0">Rp {{ number_format($totalNominal, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Disetujui</p>
                    <h3 class="mb-0">Rp {{ number_format($totalDisetujui, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-warning">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Menunggu ACC</p>
                    <h3 class="mb-0">Rp {{ number_format($totalMenunggu, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Detail Laporan Setoran</h4>
        </div>
        <div class="card-body">
            <table id="table1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Setoran</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>ACC Oleh</th>
                        <th>Waktu ACC</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($setorans as $setoran)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $setoran->kode_setoran }}</td>
                            <td>{{ optional($setoran->tanggal_setoran)->format('d/m/Y') }}</td>
                            <td>{{ optional($setoran->kasir)->name ?? '-' }}</td>
                            <td>Rp {{ number_format($setoran->nominal, 0, ',', '.') }}</td>
                            <td>
                                @if ($setoran->status === \App\Models\Setoran::STATUS_DISETUJUI)
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-warning">Menunggu ACC</span>
                                @endif
                            </td>
                            <td>{{ optional($setoran->approver)->name ?? '-' }}</td>
                            <td>{{ optional($setoran->acc_at)->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>{{ $setoran->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Belum ada data setoran pada rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
