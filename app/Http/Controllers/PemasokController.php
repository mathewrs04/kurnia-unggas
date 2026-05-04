<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasok;

class PemasokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pemasok = Pemasok::all();
        confirmDelete('HapusData', 'Yakin hapus data ini?');
        return view('pemasok.index', compact('pemasok'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'nama_pabrik' => 'required|string|max:255|unique:pemasoks,nama_pabrik,'.$id,
            'nama_marketing' => 'required|string|max:255',
            'no_telp_marketing' => 'required|string|max:45',
        ],
        [
            'nama_pabrik.required' => 'Nama pabrik wajib diisi.',
            'nama_pabrik.unique' => 'Nama pabrik sudah ada.',
            'nama_marketing.required' => 'Nama marketing wajib diisi.',
            'no_telp_marketing.required' => 'No. telp marketing wajib diisi.',
        ]);

        Pemasok::updateOrCreate(
            ['id' => $id],
            [
                'nama_pabrik' => $request->nama_pabrik,
                'nama_marketing' => $request->nama_marketing,
                'no_telp_marketing' => $request->no_telp_marketing,
                'user_id' => $id ? Pemasok::find($id)->user_id : auth()->id(),
            ]
        );

        toast()->success('Data berhasil disimpan!');
        return redirect()->route('master.pemasok.index');
    }

   
    public function destroy(string $id)
    {
        $pemasok = Pemasok::findOrFail($id);
        $pemasok->delete();
        toast()->success('Data berhasil dihapus!');
        return redirect()->route('master.pemasok.index');
    }

    public function laporan(Request $request)
    {
        $dariTanggal = $request->get('dari_tanggal', now()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', now()->toDateString());

        $pemasoks = Pemasok::with([
            'peternaks' => function ($query) use ($dariTanggal, $sampaiTanggal) {
                $query->withCount([
                    'pembelians as pembelians_count' => function ($pembelianQuery) use ($dariTanggal, $sampaiTanggal) {
                        $pembelianQuery
                            ->whereDate('tanggal_pembelian', '>=', $dariTanggal)
                            ->whereDate('tanggal_pembelian', '<=', $sampaiTanggal);
                    }
                ])->withSum([
                    'pembelianDetails as pembelian_details_sum_subtotal' => function ($detailQuery) use ($dariTanggal, $sampaiTanggal) {
                        $detailQuery->whereHas('pembelian', function ($pembelianQuery) use ($dariTanggal, $sampaiTanggal) {
                            $pembelianQuery
                                ->whereDate('tanggal_pembelian', '>=', $dariTanggal)
                                ->whereDate('tanggal_pembelian', '<=', $sampaiTanggal);
                        });
                    }
                ], 'subtotal')
                    ->orderByDesc('pembelian_details_sum_subtotal')
                    ->orderByDesc('pembelians_count')
                    ->orderBy('nama');
            }
        ])
            ->orderBy('nama_pabrik')
            ->get();

        $pemasoks = $pemasoks
            ->map(function ($pemasok) {
                $pemasok->total_peternak = $pemasok->peternaks->count();
                $pemasok->total_pembelian = $pemasok->peternaks->sum('pembelians_count');
                $pemasok->total_nominal = $pemasok->peternaks->sum('pembelian_details_sum_subtotal');
                return $pemasok;
            })
            ->sortByDesc('total_nominal')
            ->values();

        $summary = [
            'total_pemasok' => $pemasoks->count(),
            'total_peternak' => $pemasoks->sum('total_peternak'),
            'total_pembelian' => $pemasoks->sum('total_pembelian'),
            'total_nominal' => $pemasoks->sum('total_nominal'),
        ];

        return view('report.pemasok-peternak', compact('pemasoks', 'dariTanggal', 'sampaiTanggal', 'summary'));
    }

    public function laporanPemasokPeternak(Request $request)
    {
        $dariTanggal = $request->get('dari_tanggal', now()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', now()->toDateString());

        $pemasoks = Pemasok::query()
            ->whereHas('peternaks.pembelians', function ($query) use ($dariTanggal, $sampaiTanggal) {
                $query->whereBetween('tanggal_pembelian', [$dariTanggal, $sampaiTanggal]);
            })
            ->with(['peternaks' => function ($query) use ($dariTanggal, $sampaiTanggal) {
                $query->whereHas('pembelians', function ($subQuery) use ($dariTanggal, $sampaiTanggal) {
                    $subQuery->whereBetween('tanggal_pembelian', [$dariTanggal, $sampaiTanggal]);
                })
                ->withCount(['pembelians as pembelians_count' => function ($subQuery) use ($dariTanggal, $sampaiTanggal) {
                    $subQuery->whereBetween('tanggal_pembelian', [$dariTanggal, $sampaiTanggal]);
                }])
                ->withSum(['pembelianDetails as pembelian_details_sum_subtotal' => function ($subQuery) use ($dariTanggal, $sampaiTanggal) {
                    $subQuery->whereBetween('pembelians.tanggal_pembelian', [$dariTanggal, $sampaiTanggal]);
                }], 'subtotal')
                ->orderByDesc('pembelian_details_sum_subtotal')
                ->orderBy('nama');
            }])
            ->orderBy('nama_pabrik')
            ->get();

        $pemasoks->each(function ($pemasok) {
            $pemasok->total_peternak = $pemasok->peternaks->count();
            $pemasok->total_pembelian = $pemasok->peternaks->sum('pembelians_count');
            $pemasok->total_nominal = $pemasok->peternaks->sum(function ($peternak) {
                return $peternak->pembelian_details_sum_subtotal ?? 0;
            });
        });

        $totalPemasok = $pemasoks->count();
        $totalPeternak = $pemasoks->sum('total_peternak');
        $totalPembelian = $pemasoks->sum('total_pembelian');
        $totalNominal = $pemasoks->sum('total_nominal');

        return view('report.pemasok-peternak', compact(
            'pemasoks',
            'dariTanggal',
            'sampaiTanggal',
            'totalPemasok',
            'totalPeternak',
            'totalPembelian',
            'totalNominal'
        ));
    }
}
