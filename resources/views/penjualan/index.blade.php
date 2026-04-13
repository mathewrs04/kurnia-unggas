@extends('layouts.app')
@section('content_title', 'Data Penjualan')
@section('content')

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">Daftar Penjualan</h3>
            <a href="{{ route('penjualan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Penjualan
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="tablePenjualan" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Nota</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Subtotal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualans as $penjualan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $penjualan->no_nota }}</td>
                    <td>{{ $penjualan->tanggal_jual->format('d/m/Y') }}</td>
                    <td>{{ $penjualan->pelanggan->nama }}</td>
                    <td>Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</td>
                    <td>
                        @if($penjualan->status == App\Models\Penjualan::STATUS_LANGSUNG)
                            <span class="badge badge-success">Langsung</span>
                        @elseif($penjualan->status == App\Models\Penjualan::STATUS_BELUM_DIKIRIM)
                            <span class="badge badge-warning">Belum Dikirim</span>
                        @elseif($penjualan->status == App\Models\Penjualan::STATUS_SUDAH_DIKIRIM)
                            <span class="badge badge-info">Sudah Dikirim</span>
                        @endif
                    </td>
                    <td>
                        @if($penjualan->status == App\Models\Penjualan::STATUS_BELUM_DIKIRIM)
                            <button type="button" class="btn btn-success btn-sm" onclick="confirmKirim({{ $penjualan->id }})">
                                <i class="fas fa-truck"></i> Kirim
                            </button>
                            <form id="kirim-form-{{ $penjualan->id }}" action="{{ route('penjualan.kirim', $penjualan->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('PUT')
                            </form>
                        @endif
                        <a href="{{ route('penjualan.show', $penjualan->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $penjualan->id }})">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                        <form id="delete-form-{{ $penjualan->id }}" action="{{ route('penjualan.destroy', $penjualan->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        $('#tablePenjualan').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#tablePenjualan_wrapper .col-md-6:eq(0)');
    });

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data penjualan ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    function confirmKirim(id) {
        if (confirm('Apakah penjualan ini sudah di kirim?')) {
            document.getElementById('kirim-form-' + id).submit();
        }
    }
</script>
@endpush
