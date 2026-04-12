<?php

namespace App\View\Components\HargaAyam;

use App\Models\HargaAyam;
use App\Models\Produk;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormHargaAyam extends Component
{
    public $id, $produks_id, $tanggal, $harga_eceran, $harga_partai;
    public $produkList;

    public function __construct($id = null)
    {
        $this->produkList = Produk::where('tipe_produk', 'ayam_hidup')->first();

        if ($id) {
            $harga = HargaAyam::find($id);
            $this->id           = $harga->id;
            $this->produks_id   = $harga->produks_id;
            $this->tanggal      = $harga->tanggal;
            $this->harga_eceran = $harga->harga_eceran;
            $this->harga_partai = $harga->harga_partai;
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.harga-ayam.form-harga-ayam');
    }
}
