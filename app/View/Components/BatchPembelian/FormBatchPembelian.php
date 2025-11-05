<?php

namespace App\View\Components\BatchPembelian;

use App\Models\BatchPembelian;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormBatchPembelian extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $kode_batch, $harga_beli_per_kg, $stok_ekor, $stok_ekor_minimal, $stok_kg;
    public function __construct($id = null)
    {
        if ($id) {
            $batchPembelian = BatchPembelian::find($id);
            $this->id = $batchPembelian->id;
            $this->kode_batch = $batchPembelian->kode_batch;
            $this->harga_beli_per_kg = $batchPembelian->harga_beli_per_kg;
            $this->stok_ekor = $batchPembelian->stok_ekor;
            $this->stok_ekor_minimal = $batchPembelian->stok_ekor_minimal;
            $this->stok_kg = $batchPembelian->stok_kg;
        } 
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.batch-pembelian.form-batch-pembelian');
    }
}
