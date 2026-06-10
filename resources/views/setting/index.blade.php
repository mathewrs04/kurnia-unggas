@extends('layouts.app')
@section('content_title', 'Pengaturan Sistem')
@section('content')

<div class="row">
    {{-- Card Info Stok Saat Ini --}}
    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-boxes"></i> Total Stok Saat Ini</h4>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th>Total Ekor</th>
                        <td><strong class="text-info" style="font-size:1.2rem">{{ number_format($totalStokEkor) }} ekor</strong></td>
                    </tr>
                    <tr>
                        <th>Total Berat</th>
                        <td><strong>{{ number_format($totalStokKg, 2) }} kg</strong></td>
                    </tr>
                    <tr>
                        <th>Stok Minimal</th>
                        <td>
                            <strong class="{{ $totalStokEkor < $stokMinimal ? 'text-danger' : 'text-success' }}">
                                {{ number_format($stokMinimal) }} ekor
                            </strong>
                        </td>
                    </tr>
                </table>

                @if ($stokMinimal > 0 && $totalStokEkor < $stokMinimal)
                    <div class="alert alert-danger mt-2 mb-0 py-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian!</strong> Stok total saat ini berada di bawah batas minimal.
                    </div>
                @elseif ($stokMinimal > 0)
                    <div class="alert alert-success mt-2 mb-0 py-2">
                        <i class="fas fa-check-circle"></i>
                        Stok total aman di atas batas minimal.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Card Form Setting --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-cog"></i> Pengaturan Sistem</h4>
            </div>
            <div class="card-body">
                <x-alert :errors="$errors" />

                <form action="{{ route('setting.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="stok_minimal_global">
                            Stok Minimal Global (Ekor)
                            <small class="text-muted">— batas minimal total ayam dari semua batch aktif</small>
                        </label>
                        <div class="input-group">
                            <input
                                type="number"
                                id="stok_minimal_global"
                                name="stok_minimal_global"
                                class="form-control"
                                value="{{ old('stok_minimal_global', $stokMinimal) }}"
                                min="0"
                                required
                            >
                            <div class="input-group-append">
                                <span class="input-group-text">ekor</span>
                            </div>
                        </div>
                        <small class="text-muted">
                            Jika total stok seluruh batch aktif di bawah angka ini, sistem akan menampilkan peringatan di dashboard.
                        </small>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
