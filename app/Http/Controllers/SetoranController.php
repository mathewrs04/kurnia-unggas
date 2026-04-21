<?php

namespace App\Http\Controllers;

use App\Models\Setoran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class SetoranController extends Controller
{
    public function index(Request $request)
    {
        $dariTanggal = $request->get('dari_tanggal', now()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', now()->toDateString());

        $setorans = Setoran::with(['kasir', 'approver'])
            ->tanggalRange($dariTanggal, $sampaiTanggal)
            ->latest()
            ->get();

        $totalNominal = $setorans->sum('nominal');

        return view('setoran.index', compact('setorans', 'dariTanggal', 'sampaiTanggal', 'totalNominal'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isKasir()) {
            abort(403, 'Hanya kasir yang dapat menambahkan setoran.');
        }

        $validated = $request->validate([
            'tanggal_setoran' => ['required', 'date'],
            'nominal' => ['required', 'numeric', 'min:1'],
            'keterangan' => ['nullable', 'string'],
        ]);

        Setoran::create([
            'kode_setoran' => Setoran::generateKodeSetoran(),
            'tanggal_setoran' => $validated['tanggal_setoran'],
            'nominal' => (int) $validated['nominal'],
            'status' => Setoran::STATUS_MENUNGGU_ACC,
            'keterangan' => $validated['keterangan'] ?? null,
            'kasir_id' => auth()->id(),
        ]);

        Alert::success('Berhasil', 'Setoran berhasil ditambahkan');
        return redirect()->route('setoran.index');
    }

    public function approve($id)
    {
        if (!auth()->user()->isPenanggungJawab()) {
            abort(403, 'Hanya penanggung jawab yang dapat menyetujui setoran.');
        }

        $setoran = Setoran::findOrFail($id);

        if ($setoran->status === Setoran::STATUS_DISETUJUI) {
            Alert::warning('Info', 'Setoran sudah disetujui sebelumnya');
            return redirect()->route('setoran.index');
        }

        $setoran->update([
            'status' => Setoran::STATUS_DISETUJUI,
            'acc_by' => auth()->id(),
            'acc_at' => Carbon::now(),
        ]);

        Alert::success('Berhasil', 'Setoran berhasil di-ACC');
        return redirect()->route('setoran.index');
    }

    public function report(Request $request)
    {
        $dariTanggal = $request->get('dari_tanggal', now()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', now()->toDateString());

        $setorans = Setoran::with(['kasir', 'approver'])
            ->tanggalRange($dariTanggal, $sampaiTanggal)
            ->orderBy('tanggal_setoran', 'desc')
            ->get();

        $totalNominal = $setorans->sum('nominal');
        $totalDisetujui = $setorans->where('status', Setoran::STATUS_DISETUJUI)->sum('nominal');
        $totalMenunggu = $setorans->where('status', Setoran::STATUS_MENUNGGU_ACC)->sum('nominal');

        return view('report.setoran.index', compact(
            'setorans',
            'dariTanggal',
            'sampaiTanggal',
            'totalNominal',
            'totalDisetujui',
            'totalMenunggu'
        ));
    }
}
