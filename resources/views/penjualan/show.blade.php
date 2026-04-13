@extends('layouts.app')
@section('content_title', 'Detail Penjualan')
@section('content')

<!-- Area Tampilan Layar Web -->
<div class="d-print-none">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Informasi Penjualan</h3>
        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary ml-auto">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">No Nota</th>
                        <td>: {{ $penjualan->no_nota }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Penjualan</th>
                        <td>: {{ $penjualan->tanggal_jual->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Pelanggan</th>
                        <td>: {{ $penjualan->pelanggan->nama }}</td>
                    </tr>
                    <tr>
                        <th>Status Pengiriman</th>
                        <td>: 
                            @if($penjualan->status == App\Models\Penjualan::STATUS_LANGSUNG)
                                <span class="badge badge-success">Langsung</span>
                            @elseif($penjualan->status == App\Models\Penjualan::STATUS_BELUM_DIKIRIM)
                                <span class="badge badge-warning">Belum Dikirim</span>
                            @elseif($penjualan->status == App\Models\Penjualan::STATUS_SUDAH_DIKIRIM)
                                <span class="badge badge-info">Sudah Dikirim</span>
                            @else
                                <span class="badge badge-secondary">{{ $penjualan->status }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Alamat</th>
                        <td>: {{ $penjualan->pelanggan->alamat }}</td>
                    </tr>
                    <tr>
                        <th>No Telepon</th>
                        <td>: {{ $penjualan->pelanggan->no_telp }}</td>
                    </tr>
                    <tr>
                        <th>Total Penjualan</th>
                        <td>: <strong class="text-success">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Detail Ayam -->
@if($detailsAyam->count() > 0)
<div class="card">
    <div class="card-header bg-danger text-white">
        <h3 class="card-title"><i class="fas fa-drumstick-bite"></i> Detail Penjualan Ayam Hidup</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Deskripsi</th>
                        <th>Batch</th>
                   
                        <th>Jumlah Ekor</th>
                        <th>Berat (kg)</th>
                        <th>Harga per Kg</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailsAyam as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->deskripsi }}</td>
                        <td>{{ $detail->batch ? $detail->batch->kode_batch : '-' }}</td>
                        
                        <td>{{ number_format($detail->jumlah_ekor) }}</td>
                        <td>{{ $detail->jumlah_berat ? number_format($detail->jumlah_berat, 2) : '-' }}</td>
                        <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3  " class="text-right">Total Ayam</th>
                        <th>{{ number_format($detailsAyam->sum('jumlah_ekor')) }} ekor</th>
                        <th>{{ number_format($detailsAyam->sum('jumlah_berat'), 2) }} kg</th>
                        <th></th>
                        <th>Rp {{ number_format($detailsAyam->sum('subtotal'), 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Detail Jasa -->
@if($detailsJasa->count() > 0)
<div class="card">
    <div class="card-header bg-info text-white">
        <h3 class="card-title"><i class="fas fa-tools"></i> Detail Penjualan Jasa</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk Jasa</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Ekor</th>
                        <th>Harga per Ekor</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailsJasa as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->produk->nama_produk }}</td>
                        <td>{{ $detail->deskripsi }}</td>
                        <td>{{ number_format($detail->jumlah_ekor) }}</td>
                        <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Total Jasa</th>
                        <th>{{ number_format($detailsJasa->sum('jumlah_ekor')) }} ekor</th>
                        <th></th>
                        <th>Rp {{ number_format($detailsJasa->sum('subtotal'), 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Summary Total -->
<div class="card">
    <div class="card-header bg-success text-white">
        <h3 class="card-title"><i class="fas fa-calculator"></i> Ringkasan Pembayaran</h3>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th width="70%" class="text-right">Subtotal Ayam:</th>
                <td class="text-right">Rp {{ number_format($detailsAyam->sum('subtotal'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th class="text-right">Subtotal Jasa:</th>
                <td class="text-right">Rp {{ number_format($detailsJasa->sum('subtotal'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th class="text-right">Total Sebelum Diskon:</th>
                <td class="text-right">Rp {{ number_format($totalSebelumDiskon, 0, ',', '.') }}</td>
            </tr>
            @if($diskon > 0)
            <tr>
                <th class="text-right">Diskon:</th>
                <td class="text-right text-danger">- Rp {{ number_format($diskon, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr style="border-top: 2px solid #000;">
                <th class="text-right" style="font-size: 20px;">TOTAL BAYAR:</th>
                <td class="text-right text-success" style="font-size: 24px; font-weight: bold;">
                    Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak
        </button>
    </div>
</div>

<!-- Akhir Area Web -->
</div>

<!-- Area Struk Cetak (Mode Thermal / Kertas Kecil) -->
<div class="d-none d-print-block print-receipt">
    
    <div class="text-center font-weight-bold" style="font-size: 1.2rem; margin-bottom: 5px;">
        KURNIA UNGGAS
    </div>
    <div class="text-center" style="font-size: 0.9rem; margin-bottom: 10px; line-height: 1.2;">
        No: {{ $penjualan->no_nota }}<br>
        Tgl: {{ $penjualan->tanggal_jual->format('d/m/Y') }}<br>
        Pelanggan: {{ $penjualan->pelanggan->nama }}
    </div>

    <div style="border-top: 1px dashed #000; margin-bottom: 5px;"></div>

    <table style="width: 100%; font-size: 0.9rem; line-height: 1.2;">
        @foreach($detailsAyam as $detail)
        <tr>
            <td colspan="2">Ayam Hidup</td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 0.8rem;">
                {{ number_format($detail->jumlah_ekor) }}ekor 
                @if($detail->jumlah_berat) ({{ number_format($detail->jumlah_berat, 2) }}kg) @endif
            </td>
        </tr>
        <tr>
            <td>@ {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach

        @foreach($detailsJasa as $detail)
        <tr>
            <td colspan="2">{{ $detail->produk->nama_produk }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 0.8rem;">{{ number_format($detail->jumlah_ekor) }}ekor</td>
        </tr>
        <tr>
            <td>@ {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>

    <table style="width: 100%; font-size: 0.9rem; line-height: 1.2;">
        @if($diskon > 0)
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($totalSebelumDiskon, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="text-right">-{{ number_format($diskon, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="font-weight-bold" style="font-size: 1rem;">
            <td>Total</td>
            <td class="text-right">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div style="border-top: 1px dashed #000; margin: 10px 0 5px;"></div>
    
    <div class="text-center" style="font-size: 0.8rem;">
        Terima Kasih<br>
        Barang yang dibeli tidak dapat ditukar
    </div>
</div>

<style>
    /* CSS Khusus Mode Print */
    @media print {
        @page {
            margin: 0;
            size: 58mm 500mm; /* Standar printer thermal kecil (lebar 58mm atau 80mm) */
        }
        body * {
            visibility: hidden;
        }
        .print-receipt, .print-receipt * {
            visibility: visible;
        }
        .print-receipt {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 5px;
            font-family: monospace, sans-serif;
            color: #000;
        }
    }
</style>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('print_nota'))
            window.print();
        @endif
    });
</script>
@endpush
