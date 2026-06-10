<?php

namespace App\Http\Controllers;

use App\Models\BatchPembelian;
use App\Models\Setting;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        $totalStokEkor = BatchPembelian::where('stok_ekor', '>', 0)->sum('stok_ekor');
        $totalStokKg   = BatchPembelian::where('stok_ekor', '>', 0)->sum('stok_kg');
        $stokMinimal   = (int) Setting::get('stok_minimal_global', 0);

        return view('setting.index', compact('settings', 'totalStokEkor', 'totalStokKg', 'stokMinimal'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'stok_minimal_global' => 'required|integer|min:0',
        ], [
            'stok_minimal_global.required' => 'Stok minimal global wajib diisi.',
            'stok_minimal_global.integer'  => 'Stok minimal global harus berupa bilangan bulat.',
            'stok_minimal_global.min'      => 'Stok minimal global tidak boleh negatif.',
        ]);

        Setting::set('stok_minimal_global', $request->stok_minimal_global);

        Alert::success('Berhasil', 'Pengaturan berhasil disimpan.');
        return redirect()->route('setting.index');
    }
}
