<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Timbangan;
use Illuminate\Http\Request;

class TimbanganController extends Controller
{
    public function index(Request $request)
    {
        $jenis       = $request->input('jenis');
        $tanggalDari = $request->input('tanggal_dari');
        $tanggalSampai = $request->input('tanggal_sampai');
        $filtered    = $request->hasAny(['jenis', 'tanggal_dari', 'tanggal_sampai']);

        $timbangans = collect();

        if ($filtered) {
            $query = Timbangan::with('karyawans')->orderBy('tanggal');

            if ($jenis) {
                $query->where('jenis', $jenis);
            }
            if ($tanggalDari) {
                $query->whereDate('tanggal', '>=', $tanggalDari);
            }
            if ($tanggalSampai) {
                $query->whereDate('tanggal', '<=', $tanggalSampai);
            }

            $timbangans = $query->get();
        }

        return view('timbangan.index', compact('timbangans', 'filtered', 'jenis', 'tanggalDari', 'tanggalSampai'));
    }

    public function store(Request $request)
    {
        $id = $request->id;

        $request->validate([
            'jenis'             => 'required|in:timbangan_data_pembelian,timbangan_data_penjualan,timbangan_stok_opname',
            'tanggal'           => 'required|date',
            'total_jumlah_ekor' => 'required|integer|min:0',
            'total_berat'       => 'required|numeric|min:0',
            'karyawan_ids'      => 'nullable|array',
            'karyawan_ids.*'    => 'integer|exists:karyawans,id',
        ], [
            'jenis.required'             => 'Jenis timbangan wajib dipilih.',
            'jenis.in'                   => 'Jenis timbangan tidak valid.',
            'tanggal.required'           => 'Tanggal wajib diisi.',
            'total_jumlah_ekor.required' => 'Total jumlah ekor wajib diisi.',
            'total_jumlah_ekor.integer'  => 'Total jumlah ekor harus berupa angka.',
            'total_berat.required'       => 'Total berat wajib diisi.',
            'total_berat.numeric'        => 'Total berat harus berupa angka.',
            'karyawan_ids.*.exists'      => 'Salah satu karyawan tidak valid.',
        ]);

        $timbangan = Timbangan::updateOrCreate(
            ['id' => $id],
            [
                'jenis'             => $request->jenis,
                'tanggal'           => $request->tanggal,
                'total_jumlah_ekor' => $request->total_jumlah_ekor,
                'total_berat'       => $request->total_berat,
            ]
        );

        $timbangan->karyawans()->sync($request->karyawan_ids ?? []);

        toast()->success('Data timbangan berhasil disimpan!');
        return redirect()->route('master.timbangan.index');
    }

    public function destroy(string $id)
    {
        $timbangan = Timbangan::findOrFail($id);
        $timbangan->karyawans()->detach();
        $timbangan->delete();
        toast()->success('Data timbangan berhasil dihapus!');
        return redirect()->route('master.timbangan.index');
    }

    public function laporan(Request $request)
    {
        $jenis         = $request->input('jenis');
        $tanggalDari   = $request->input('tanggal_dari');
        $tanggalSampai = $request->input('tanggal_sampai');
        $filtered      = $request->hasAny(['jenis', 'tanggal_dari', 'tanggal_sampai']);

        $timbangans = collect();

        if ($filtered) {
            $query = Timbangan::with('karyawans')->orderBy('tanggal');

            if ($jenis) {
                $query->where('jenis', $jenis);
            }
            if ($tanggalDari) {
                $query->whereDate('tanggal', '>=', $tanggalDari);
            }
            if ($tanggalSampai) {
                $query->whereDate('tanggal', '<=', $tanggalSampai);
            }

            $timbangans = $query->get();
        }

        return view('timbangan.laporan', compact('timbangans', 'filtered', 'jenis', 'tanggalDari', 'tanggalSampai'));
    }
}
