<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date')->get();
        confirmDelete('HapusData', 'Yakin hapus data ini?');
        return view('holiday.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date,' . $id,
            'pre_days' => 'required|integer|min:0',
            'post_days' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama hari libur wajib diisi.',
            'name.unique' => 'Nama hari libur sudah ada.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.unique' => 'Tanggal sudah terdaftar.',
            'pre_days.required' => 'Pre days wajib diisi.',
            'post_days.required' => 'Post days wajib diisi.',
        ]);

        Holiday::updateOrCreate(
            ['id' => $id],
            [
                'name' => $request->name,
                'date' => $request->date,
                'pre_days' => $request->pre_days,
                'post_days' => $request->post_days,
            ]
        );

        toast()->success('Data berhasil disimpan!');
        return redirect()->route('master.holiday.index');
    }

    public function destroy(string $id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        toast()->success('Data berhasil dihapus!');
        return redirect()->route('master.holiday.index');
    }
}
