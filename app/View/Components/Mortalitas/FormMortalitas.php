<?php

namespace App\View\Components\Mortalitas;

use App\Models\BatchPembelian;
use App\Models\MortalitasAyam;
use Closure;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormMortalitas extends Component
{
    public $id,
           $batch_pembelian_id,
           $batch,
           $tanggal_mati,
           $jumlah_ekor,
           $berat_kg,
           $catatan;
   

    public function __construct($id = null)
    {
        $this->batch = BatchPembelian::all();
        if ($id) {
            $mortalitas = MortalitasAyam::find($id);
            $this->id = $mortalitas->id;
            $this->batch_pembelian_id = $mortalitas->batch_pembelian_id;
            $this->tanggal_mati = optional($mortalitas->tanggal_mati)->toDateString();
            $this->jumlah_ekor = $mortalitas->jumlah_ekor;
            $this->berat_kg = $mortalitas->berat_kg;
            $this->catatan = $mortalitas->catatan;            
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.mortalitas.form-mortalitas');
    }
}
