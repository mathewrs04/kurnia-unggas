@extends('layouts.app')
@section('content_title', 'Laporan Timbangan')
@section('content')

    {{-- Filter Card --}}
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h4 class="card-title mb-0"><i class="fas fa-filter mr-2"></i>Filter Laporan Timbangan</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('report.timbangan.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label>Jenis Timbangan</label>
                            <select name="jenis" class="form-control">
                                <option value="">-- Semua Jenis --</option>
                                <option value="timbangan_data_pembelian"
                                    {{ $jenis === 'timbangan_data_pembelian' ? 'selected' : '' }}>
                                    Timbangan Data Pembelian
                                </option>
                                <option value="timbangan_data_penjualan"
                                    {{ $jenis === 'timbangan_data_penjualan' ? 'selected' : '' }}>
                                    Timbangan Data Penjualan
                                </option>
                                <option value="timbangan_stok_opname"
                                    {{ $jenis === 'timbangan_stok_opname' ? 'selected' : '' }}>
                                    Timbangan Stok Opname
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Tanggal Dari</label>
                            <input type="date" name="tanggal_dari" class="form-control"
                                value="{{ $tanggalDari ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai" class="form-control"
                                value="{{ $tanggalSampai ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Report Card --}}
    @if ($filtered)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="fas fa-clipboard-list mr-2"></i>Hasil Laporan
                    @if ($jenis)
                        &mdash;
                        @php
                            $jenisLabel = [
                                'timbangan_data_pembelian' => 'Timbangan Data Pembelian',
                                'timbangan_data_penjualan' => 'Timbangan Data Penjualan',
                                'timbangan_stok_opname'    => 'Timbangan Stok Opname',
                            ];
                        @endphp
                        {{ $jenisLabel[$jenis] ?? $jenis }}
                    @endif
                    @if ($tanggalDari || $tanggalSampai)
                        <small class="text-muted">
                            ({{ $tanggalDari ? \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') : '...' }}
                            s/d
                            {{ $tanggalSampai ? \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') : '...' }})
                        </small>
                    @endif
                </h4>
                <span class="badge badge-info">{{ $timbangans->count() }} data</span>
            </div>
            <div class="card-body">
                @if ($timbangans->isEmpty())
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Tidak ada data timbangan yang sesuai dengan filter.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm" id="table1">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Timbangan</th>
                                    <th class="text-right">Total Ekor</th>
                                    <th class="text-right">Total Berat (Kg)</th>
                                    <th>Karyawan Penanggung Jawab</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($timbangans as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                        <td>
                                            @php
                                                $badge = [
                                                    'timbangan_data_pembelian' => 'badge-primary',
                                                    'timbangan_data_penjualan' => 'badge-success',
                                                    'timbangan_stok_opname'    => 'badge-warning',
                                                ];
                                                $label = [
                                                    'timbangan_data_pembelian' => 'Data Pembelian',
                                                    'timbangan_data_penjualan' => 'Data Penjualan',
                                                    'timbangan_stok_opname'    => 'Stok Opname',
                                                ];
                                            @endphp
                                            <span class="badge {{ $badge[$item->jenis] ?? 'badge-secondary' }}">
                                                {{ $label[$item->jenis] ?? $item->jenis }}
                                            </span>
                                        </td>
                                        <td class="text-right">{{ number_format($item->total_jumlah_ekor) }}</td>
                                        <td class="text-right">{{ number_format($item->total_berat, 2) }}</td>
                                        <td>
                                            @if ($item->karyawans->isNotEmpty())
                                                @foreach ($item->karyawans as $karyawan)
                                                    <span class="badge badge-light border">
                                                        <i class="fas fa-user mr-1"></i>{{ $karyawan->nama }}
                                                        <small class="text-muted">({{ $karyawan->posisi }})</small>
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="font-weight-bold bg-light">
                                <tr>
                                    <td colspan="3" class="text-right">Total</td>
                                    <td class="text-right">{{ number_format($timbangans->sum('total_jumlah_ekor')) }}</td>
                                    <td class="text-right">{{ number_format($timbangans->sum('total_berat'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-1"></i>
            Silakan pilih filter di atas lalu klik <strong>Tampilkan</strong> untuk melihat laporan.
        </div>
    @endif

@endsection
