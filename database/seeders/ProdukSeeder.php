<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Produk::insert([
            [
                'nama_produk' => 'Ayam Hidup',
                'tipe_produk' => 'ayam_hidup',
                'satuan' => 'kg',
                'harga_satuan' => 0,
            ],
            [
                'nama_produk' => 'Jasa Pemotongan',
                'tipe_produk' => 'jasa',
                'satuan' => 'ekor',
                'harga_satuan' => 2000,
            ],
            [
                'nama_produk' => 'Jasa Pembubutan',
                'tipe_produk' => 'jasa',
                'satuan' => 'ekor',
                'harga_satuan' => 1500,
            ],
        ]);
    }
}
