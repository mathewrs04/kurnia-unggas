<?php

namespace App\Http\Controllers;

use App\Models\Timbangan;
use Illuminate\Http\Request;

class TimbanganController extends Controller
{
    public function index()
    {
        $timbangans = Timbangan::all();
        return view('timbangan.index', compact('timbangans'));
    }
}
