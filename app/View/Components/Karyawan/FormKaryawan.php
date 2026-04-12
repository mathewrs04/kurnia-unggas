<?php

namespace App\View\Components\Karyawan;

use App\Models\Karyawan;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormKaryawan extends Component
{
    public $id, $nama, $posisi;

    public function __construct($id = null)
    {
        if ($id) {
            $karyawan = Karyawan::find($id);
            $this->id     = $karyawan->id;
            $this->nama   = $karyawan->nama;
            $this->posisi = $karyawan->posisi;
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.karyawan.form-karyawan');
    }
}
