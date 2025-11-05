<?php

namespace App\View\Components\Pemasok;

use App\Models\Pemasok;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormPemasok extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $nama_pabrik, $nama_marketing, $no_telp_marketing;
    public function __construct($id = null)
    {
        if($id){
            $pemasok = Pemasok::find($id);
            $this->id = $pemasok->id;
            $this->nama_pabrik = $pemasok->nama_pabrik;
            $this->nama_marketing = $pemasok->nama_marketing;
            $this->no_telp_marketing = $pemasok->no_telp_marketing;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pemasok.form-pemasok');
    }
}
