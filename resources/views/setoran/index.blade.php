@extends('layouts.app')
@section('content_title', 'Setoran')
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('setoran.index') }}" method="GET" class="row">
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
                    <a href="{{ route('setoran.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Nominal Setoran</p>
                    <h3 class="mb-0">Rp {{ number_format($totalNominal, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->isKasir())
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title mb-0">Input Setoran</h4>
            </div>
            <div class="card-body">
                <x-alert :errors="$errors" type="danger" />
                <form action="{{ route('setoran.store') }}" method="POST" class="row">
                    @csrf
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label for="tanggal_setoran" class="form-label">Tanggal Setoran</label>
                        <input type="date" name="tanggal_setoran" id="tanggal_setoran"
                            value="{{ old('tanggal_setoran', now()->toDateString()) }}" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label for="nominal" class="form-label">Nominal</label>
                        <input type="number" min="1" name="nominal" id="nominal" value="{{ old('nominal') }}"
                            class="form-control" placeholder="Masukkan nominal" required>
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" id="keterangan" value="{{ old('keterangan') }}"
                            class="form-control" placeholder="Contoh: Setoran shift pagi">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Daftar Setoran</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="table1">
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
                        @if (auth()->user()->isPenanggungJawab())
                            <th>Aksi</th>
                        @endif
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
                            @if (auth()->user()->isPenanggungJawab())
                                <td>
                                    @if ($setoran->status === \App\Models\Setoran::STATUS_MENUNGGU_ACC)
                                        <form action="{{ route('setoran.approve', $setoran->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('ACC setoran ini?')">
                                                <i class="fas fa-check"></i> ACC
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">Selesai</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Belum ada data setoran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
