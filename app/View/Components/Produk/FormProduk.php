<?php

namespace App\View\Components\Produk;

use App\Models\Produk;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormProduk extends Component
{
    /**
     * Create a new component instance.
     */

    public $id, $nama_produk, $tipe_produk, $satuan, $harga_satuan;
    public function __construct($id = null)
    {
         if($id){
                $produk = Produk::find($id);
                $this->id = $produk->id;
                $this->nama_produk = $produk->nama_produk;
                $this->tipe_produk = $produk->tipe_produk;
                $this->satuan = $produk->satuan;
                $this->harga_satuan = $produk->harga_satuan;
          }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.produk.form-produk');
    }
}
