<?php

namespace Database\Seeders;

use App\Models\BatchPembelian;
use App\Models\DeliveryOrder;
use App\Models\HargaAyam;
use App\Models\Karyawan;
use App\Models\Keranjang;
use App\Models\MetodePembayaran;
use App\Models\MortalitasAyam;
use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\Pembelian;
use App\Models\Peternak;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\StokOpname;
use App\Models\Timbangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SimulasiJanuari2026Seeder extends Seeder
{
    private string $runToken;
    private int $doSequence = 1;
    private int $pembelianSequence = 1;
    private int $notaSequence = 1;

    /** @var array<string, array{eceran:int, partai:int}> */
    private array $hargaByDate = [];

    public function run(): void
    {
        $this->runToken = 'S' . now()->format('His');

        DB::transaction(function () {
            $master = $this->prepareMasterData();
            $this->seedHargaAyamJanuari2026($master['user_id'], $master['produk_ayam_id']);

            $purchaseDates = $this->generatePurchaseDates();
            $purchaseDateSet = array_flip(array_map(fn (Carbon $date) => $date->toDateString(), $purchaseDates));

            $start = Carbon::create(2026, 1, 1);
            $end = Carbon::create(2026, 1, 31);

            $createdBatchIds = [];

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $dateKey = $date->toDateString();

                if (isset($purchaseDateSet[$dateKey])) {
                    $this->seedStokOpnameBeforeIncoming($date, $master['karyawan_ids'], $master['user_id']);

                    $batchId = $this->seedOnePurchaseWithDo(
                        $date,
                        $master['peternak_ids'],
                        $master['karyawan_ids'],
                        $master['metode_pembayaran_ids'],
                        $master['produk_ayam_id'],
                        $master['user_id']
                    );

                    $createdBatchIds[] = $batchId;
                }

                $this->seedDailySales(
                    $date,
                    $master['pelanggan_ids'],
                    $master['karyawan_ids'],
                    $master['produk_ayam_id'],
                    $master['jasa_produk_ids'],
                    $master['user_id']
                );
            }

            $this->seedOccasionalMortalitas($createdBatchIds, $master['user_id']);
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareMasterData(): array
    {
        $user = User::first();

        if (!$user) {
            $user = User::create([
                'name' => 'Seeder Admin',
                'email' => 'seeder-admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'penanggung_jawab',
            ]);
        }

        $pemasok = Pemasok::query()->firstOrCreate(
            ['nama_pabrik' => 'Pemasok Seeder Januari'],
            [
                'nama_marketing' => 'Marketing Seeder',
                'no_telp_marketing' => '081200000001',
                'user_id' => $user->id,
            ]
        );

        $peternakIds = Peternak::query()->pluck('id')->all();
        if (count($peternakIds) < 3) {
            for ($i = count($peternakIds) + 1; $i <= 3; $i++) {
                $peternak = Peternak::create([
                    'pemasok_id' => $pemasok->id,
                    'nama' => 'Peternak Seeder ' . $i,
                    'alamat' => 'Alamat Peternak Seeder ' . $i,
                    'no_telp' => '08121000000' . $i,
                    'user_id' => $user->id,
                ]);
                $peternakIds[] = $peternak->id;
            }
        }

        $pelangganIds = Pelanggan::query()->pluck('id')->all();
        if (count($pelangganIds) < 25) {
            for ($i = count($pelangganIds) + 1; $i <= 25; $i++) {
                $pelanggan = Pelanggan::create([
                    'nama' => 'Pelanggan Seeder ' . $i,
                    'alamat' => 'Alamat Pelanggan Seeder ' . $i,
                    'no_telp' => '0812200000' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                    'user_id' => $user->id,
                ]);
                $pelangganIds[] = $pelanggan->id;
            }
        }

        $karyawanIds = Karyawan::query()->pluck('id')->all();
        if (count($karyawanIds) < 4) {
            $baseNames = ['Andi', 'Budi', 'Candra', 'Dedi'];
            for ($i = count($karyawanIds); $i < 4; $i++) {
                $karyawan = Karyawan::create([
                    'nama' => 'Karyawan ' . $baseNames[$i],
                    'posisi' => 'Operator Timbang',
                    'user_id' => $user->id,
                ]);
                $karyawanIds[] = $karyawan->id;
            }
        }

        $metodeIds = MetodePembayaran::query()->pluck('id')->all();
        if (count($metodeIds) < 2) {
            $tunai = MetodePembayaran::query()->firstOrCreate(
                ['nama_metode' => 'Tunai'],
                ['keterangan' => 'Pembayaran tunai harian', 'user_id' => $user->id]
            );
            $transfer = MetodePembayaran::query()->firstOrCreate(
                ['nama_metode' => 'Transfer'],
                ['keterangan' => 'Pembayaran transfer bank', 'user_id' => $user->id]
            );
            $metodeIds = [$tunai->id, $transfer->id];
        }

        $produkAyam = Produk::query()->where('tipe_produk', 'ayam_hidup')->first();
        if (!$produkAyam) {
            $produkAyam = Produk::create([
                'nama_produk' => 'Ayam Hidup',
                'tipe_produk' => 'ayam_hidup',
                'satuan' => 'kg',
                'harga_satuan' => 0,
                'user_id' => $user->id,
            ]);
        }

        $jasaProdukIds = Produk::query()->where('tipe_produk', 'jasa')->pluck('id')->all();
        if (count($jasaProdukIds) < 2) {
            $jasa1 = Produk::query()->firstOrCreate(
                ['nama_produk' => 'Jasa Pemotongan'],
                [
                    'tipe_produk' => 'jasa',
                    'satuan' => 'ekor',
                    'harga_satuan' => 2000,
                    'user_id' => $user->id,
                ]
            );
            $jasa2 = Produk::query()->firstOrCreate(
                ['nama_produk' => 'Jasa Pembubutan'],
                [
                    'tipe_produk' => 'jasa',
                    'satuan' => 'ekor',
                    'harga_satuan' => 1500,
                    'user_id' => $user->id,
                ]
            );
            $jasaProdukIds = [$jasa1->id, $jasa2->id];
        }

        return [
            'user_id' => $user->id,
            'peternak_ids' => $peternakIds,
            'pelanggan_ids' => $pelangganIds,
            'karyawan_ids' => $karyawanIds,
            'metode_pembayaran_ids' => $metodeIds,
            'produk_ayam_id' => $produkAyam->id,
            'jasa_produk_ids' => $jasaProdukIds,
        ];
    }

    private function seedHargaAyamJanuari2026(int $userId, int $produkAyamId): void
    {
        $eceran = random_int(19000, 23000);

        for ($day = 1; $day <= 31; $day++) {
            $tanggal = Carbon::create(2026, 1, $day)->toDateString();
            $eceran = max(18000, min(25000, $eceran + random_int(-800, 800)));
            $partai = max(17000, $eceran - random_int(500, 1500));

            HargaAyam::updateOrCreate(
                [
                    'produks_id' => $produkAyamId,
                    'tanggal' => $tanggal,
                ],
                [
                    'user_id' => $userId,
                    'harga_eceran' => $eceran,
                    'harga_partai' => $partai,
                ]
            );

            $this->hargaByDate[$tanggal] = [
                'eceran' => $eceran,
                'partai' => $partai,
            ];
        }
    }

    /** @return Carbon[] */
    private function generatePurchaseDates(): array
    {
        $dates = [];
        $date = Carbon::create(2026, 1, 1);

        for ($i = 0; $i < 12; $i++) {
            $dates[] = $date->copy();
            $date->addDays($i % 2 === 0 ? 2 : 3);
        }

        return $dates;
    }

    private function seedOnePurchaseWithDo(
        Carbon $date,
        array $peternakIds,
        array $karyawanIds,
        array $metodePembayaranIds,
        int $produkAyamId,
        int $userId
    ): int {
        $peternakId = $peternakIds[array_rand($peternakIds)];
        $targetEkor = [180, 200, 200, 200, 220][array_rand([0, 1, 2, 3, 4])];

        $purchaseCrates = $this->buildCrates($targetEkor, 2.0, 2.3);
        $totalEkor = array_sum(array_column($purchaseCrates, 'jumlah_ekor'));
        $totalBeratAyam = round(array_sum(array_column($purchaseCrates, 'berat_ayam')), 2);

        $susutDoKg = random_int(10, 20);
        $beratDo = round($totalBeratAyam + $susutDoKg, 2);

        $deliveryOrder = DeliveryOrder::create([
            'kode_do' => sprintf('DO-%s-%s-%03d', $date->format('Ymd'), $this->runToken, $this->doSequence++),
            'peternak_id' => $peternakId,
            'total_jumlah_ekor' => $totalEkor,
            'total_berat' => $beratDo,
            'tanggal_do' => $date->toDateString(),
        ]);

        $timbangan = Timbangan::create([
            'jenis' => 'timbangan_data_pembelian',
            'tanggal' => $date->toDateString(),
            'total_jumlah_ekor' => $totalEkor,
            'total_berat' => $totalBeratAyam,
        ]);

        $randomKaryawan = $this->pickRandomSubset($karyawanIds, 1, min(2, count($karyawanIds)));
        $timbangan->karyawans()->sync($randomKaryawan);

        foreach ($purchaseCrates as $crate) {
            Keranjang::create([
                'timbangan_id' => $timbangan->id,
                'jumlah_ekor' => $crate['jumlah_ekor'],
                'berat_keranjang' => 15,
                'berat_total' => $crate['berat_total'],
                'berat_ayam' => $crate['berat_ayam'],
            ]);
        }

        $pembelian = Pembelian::create([
            'tanggal_pembelian' => $date->toDateString(),
            'kode_pembelian' => sprintf('PBL-%s-%s-%03d', $date->format('Ymd'), $this->runToken, $this->pembelianSequence++),
            'status' => Pembelian::STATUS_BELUM_BAYAR,
            'peternak_id' => $peternakId,
            'user_id' => $userId,
        ]);

        $batch = BatchPembelian::create([
            'kode_batch' => sprintf('BATCH-%s-%s-%03d', $date->format('Ym'), $this->runToken, $this->pembelianSequence),
            'stok_ekor' => $totalEkor,
            'stok_kg' => $totalBeratAyam,
            'stok_ekor_minimal' => 50,
            'user_id' => $userId,
        ]);

        DB::table('pembelian_details')->insert([
            'pembelian_id' => $pembelian->id,
            'produk_id' => $produkAyamId,
            'batch_pembelian_id' => $batch->id,
            'timbangan_id' => $timbangan->id,
            'delivery_order_id' => $deliveryOrder->id,
            'metode_pembayaran_id' => null,
            'harga_beli_per_kg' => null,
            'subtotal' => null,
            'tanggal_bayar' => null,
            'susut_kg' => $susutDoKg,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (random_int(1, 100) <= 70) {
            $hargaBeli = random_int(18000, 22000);
            $subtotal = (int) round($totalBeratAyam * $hargaBeli);

            DB::table('pembelian_details')
                ->where('pembelian_id', $pembelian->id)
                ->update([
                    'harga_beli_per_kg' => $hargaBeli,
                    'subtotal' => $subtotal,
                    'tanggal_bayar' => $date->toDateString(),
                    'metode_pembayaran_id' => $metodePembayaranIds[array_rand($metodePembayaranIds)],
                    'updated_at' => now(),
                ]);

            $batch->update(['harga_beli_per_kg' => $hargaBeli]);
            $pembelian->update(['status' => Pembelian::STATUS_SUDAH_BAYAR]);
        }

        return $batch->id;
    }

    private function seedDailySales(
        Carbon $date,
        array $pelangganIds,
        array $karyawanIds,
        int $produkAyamId,
        array $jasaProdukIds,
        int $userId
    ): void {
        $trxCount = random_int(18, 22);

        for ($i = 0; $i < $trxCount; $i++) {
            $batch = BatchPembelian::query()->where('stok_ekor', '>', 0)->orderBy('id')->first();
            if (!$batch) {
                break;
            }

            $isPartai = random_int(1, 100) <= 10;
            $targetEkor = $isPartai ? random_int(10, 20) : random_int(1, 3);
            $ekor = min($targetEkor, (int) $batch->stok_ekor);

            if ($ekor < 1) {
                continue;
            }

            if ($isPartai && $ekor < 10) {
                $isPartai = false;
            }

            $avgKgPerEkor = $this->randomFloat(1.95, 2.1, 3);
            $berat = round($ekor * $avgKgPerEkor, 2);
            $berat = min($berat, (float) $batch->stok_kg);

            if ($berat <= 0) {
                continue;
            }

            $tipe = $isPartai ? 'partai' : 'eceran';
            $harga = $this->hargaByDate[$date->toDateString()][$tipe] ?? ($isPartai ? 20000 : 21000);
            $subtotalAyam = (int) round($berat * $harga);

            $timbanganId = null;
            if ($isPartai) {
                $salesCrates = $this->buildCrates($ekor, 1.95, 2.1);
                $berat = round(array_sum(array_column($salesCrates, 'berat_ayam')), 2);
                $subtotalAyam = (int) round($berat * $harga);

                $timbangan = Timbangan::create([
                    'jenis' => 'timbangan_data_penjualan',
                    'tanggal' => $date->toDateString(),
                    'total_jumlah_ekor' => $ekor,
                    'total_berat' => $berat,
                ]);
                $timbangan->karyawans()->sync($this->pickRandomSubset($karyawanIds, 1, min(2, count($karyawanIds))));

                foreach ($salesCrates as $crate) {
                    Keranjang::create([
                        'timbangan_id' => $timbangan->id,
                        'jumlah_ekor' => $crate['jumlah_ekor'],
                        'berat_keranjang' => 15,
                        'berat_total' => $crate['berat_total'],
                        'berat_ayam' => $crate['berat_ayam'],
                    ]);
                }

                $timbanganId = $timbangan->id;
            }

            $includeJasa = random_int(1, 100) <= 40;
            $subtotalJasa = 0;
            $jasaRows = [];

            if ($includeJasa && !empty($jasaProdukIds)) {
                $jasaProdukId = $jasaProdukIds[array_rand($jasaProdukIds)];
                $jasaProduk = Produk::find($jasaProdukId);
                if ($jasaProduk) {
                    $qtyJasa = min($ekor, max(1, random_int(1, min(5, $ekor))));
                    $subtotalJasa = (int) ($qtyJasa * $jasaProduk->harga_satuan);
                    $jasaRows[] = [
                        'produk_id' => $jasaProduk->id,
                        'batch_id' => null,
                        'timbangan_id' => null,
                        'jumlah_ekor' => $qtyJasa,
                        'jumlah_berat' => null,
                        'harga_satuan' => $jasaProduk->harga_satuan,
                        'subtotal' => $subtotalJasa,
                        'keterangan' => 'Jasa tambahan',
                    ];
                }
            }

            $diskon = random_int(1, 100) <= 20 ? random_int(1000, 5000) : 0;
            $totalSubtotal = max(0, $subtotalAyam + $subtotalJasa - $diskon);
            $status = random_int(1, 100) <= 15 && $ekor >= 2
                ? Penjualan::STATUS_BELUM_DIKIRIM
                : Penjualan::STATUS_LANGSUNG;

            $penjualan = Penjualan::create([
                'no_nota' => sprintf('NJ-%s-%s-%04d', $date->format('Ymd'), $this->runToken, $this->notaSequence++),
                'tanggal_jual' => $date->toDateString(),
                'tipe_penjualan' => $tipe,
                'status' => $status,
                'diskon' => $diskon,
                'subtotal' => $totalSubtotal,
                'pelanggan_id' => $pelangganIds[array_rand($pelangganIds)],
                'user_id' => $userId,
            ]);

            DB::table('penjualan_details')->insert([
                'penjualan_id' => $penjualan->id,
                'produk_id' => $produkAyamId,
                'batch_id' => $batch->id,
                'timbangan_id' => $timbanganId,
                'metode_pembayaran_id' => null,
                'jumlah_ekor' => $ekor,
                'jumlah_berat' => $berat,
                'harga_satuan' => $harga,
                'subtotal' => $subtotalAyam,
                'keterangan' => $isPartai ? 'Penjualan partai' : 'Penjualan eceran',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($jasaRows as $jasaRow) {
                DB::table('penjualan_details')->insert([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $jasaRow['produk_id'],
                    'batch_id' => $jasaRow['batch_id'],
                    'timbangan_id' => $jasaRow['timbangan_id'],
                    'metode_pembayaran_id' => null,
                    'jumlah_ekor' => $jasaRow['jumlah_ekor'],
                    'jumlah_berat' => $jasaRow['jumlah_berat'],
                    'harga_satuan' => $jasaRow['harga_satuan'],
                    'subtotal' => $jasaRow['subtotal'],
                    'keterangan' => $jasaRow['keterangan'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $stokKgBaru = max(0, round((float) $batch->stok_kg - $berat, 2));
            $stokEkorBaru = max(0, (int) $batch->stok_ekor - $ekor);

            $batch->update([
                'stok_ekor' => $stokEkorBaru,
                'stok_kg' => $stokKgBaru,
            ]);
        }
    }

    private function seedStokOpnameBeforeIncoming(Carbon $date, array $karyawanIds, int $userId): void
    {
        $targetBatches = BatchPembelian::query()
            ->where('stok_ekor', '>', 0)
            ->where('stok_ekor', '<=', 50)
            ->orderBy('id')
            ->get();

        foreach ($targetBatches as $batch) {
            $stokSistemKg = (float) $batch->stok_kg;
            $stokEkor = (int) $batch->stok_ekor;

            if ($stokEkor <= 0 || $stokSistemKg <= 0) {
                continue;
            }

            $minActual = round($stokEkor * 1.7, 2);
            $susut = random_int(10, 20);
            $actualKg = round($stokSistemKg - $susut, 2);

            if ($actualKg < $minActual) {
                $actualKg = max($minActual, round($stokSistemKg - 10, 2));
                $susut = (int) round($stokSistemKg - $actualKg);
            }

            if ($susut < 10 || $actualKg <= 0) {
                continue;
            }

            $crates = $this->buildCratesFromActualWeight($stokEkor, $actualKg);

            $timbangan = Timbangan::create([
                'jenis' => 'timbangan_stok_opname',
                'tanggal' => $date->toDateString(),
                'total_jumlah_ekor' => $stokEkor,
                'total_berat' => $actualKg,
            ]);
            $timbangan->karyawans()->sync($this->pickRandomSubset($karyawanIds, 1, min(2, count($karyawanIds))));

            foreach ($crates as $crate) {
                Keranjang::create([
                    'timbangan_id' => $timbangan->id,
                    'jumlah_ekor' => $crate['jumlah_ekor'],
                    'berat_keranjang' => 15,
                    'berat_total' => $crate['berat_total'],
                    'berat_ayam' => $crate['berat_ayam'],
                ]);
            }

            StokOpname::create([
                'user_id' => $userId,
                'batch_pembelian_id' => $batch->id,
                'timbangan_id' => $timbangan->id,
                'tanggal_opname' => $date->toDateString(),
                'stok_ekor_sistem' => $stokEkor,
                'stok_kg_sistem' => $stokSistemKg,
                'berat_aktual_kg' => $actualKg,
                'susut_kg' => $susut,
            ]);

            $batch->update(['stok_kg' => $actualKg]);
        }
    }

    /** @param int[] $batchIds */
    private function seedOccasionalMortalitas(array $batchIds, int $userId): void
    {
        if (empty($batchIds)) {
            return;
        }

        shuffle($batchIds);
        $target = max(2, (int) floor(count($batchIds) * 0.35));
        $selected = array_slice($batchIds, 0, $target);

        foreach ($selected as $batchId) {
            $batch = BatchPembelian::find($batchId);
            if (!$batch || $batch->stok_ekor < 6) {
                continue;
            }

            $matiEkor = random_int(2, 3);
            $matiEkor = min($matiEkor, max(0, (int) $batch->stok_ekor - 3));
            if ($matiEkor <= 0) {
                continue;
            }

            $avgKg = (float) $batch->stok_kg / max((int) $batch->stok_ekor, 1);
            $avgKg = max(1.8, min(2.3, $avgKg));
            $beratMati = round($matiEkor * $avgKg, 2);
            $beratMati = min($beratMati, (float) $batch->stok_kg);

            $tanggal = Carbon::create(2026, 1, random_int(5, 31))->toDateString();

            MortalitasAyam::create([
                'user_id' => $userId,
                'batch_pembelian_id' => $batch->id,
                'tanggal_mati' => $tanggal,
                'berat_kg' => $beratMati,
                'jumlah_ekor' => $matiEkor,
                'catatan' => 'Mortalitas simulasi Januari 2026',
            ]);

            $batch->update([
                'stok_ekor' => max(0, (int) $batch->stok_ekor - $matiEkor),
                'stok_kg' => max(0, round((float) $batch->stok_kg - $beratMati, 2)),
            ]);
        }
    }

    /**
     * @return array<int, array{jumlah_ekor:int, berat_ayam:float, berat_total:float}>
     */
    private function buildCrates(int $jumlahEkor, float $minKgPerEkor, float $maxKgPerEkor): array
    {
        $remaining = $jumlahEkor;
        $crates = [];

        while ($remaining > 0) {
            $isi = min(20, $remaining);
            $avg = $this->randomFloat($minKgPerEkor, $maxKgPerEkor, 3);
            $beratAyam = round($isi * $avg, 2);

            $crates[] = [
                'jumlah_ekor' => $isi,
                'berat_ayam' => $beratAyam,
                'berat_total' => round($beratAyam + 15, 2),
            ];

            $remaining -= $isi;
        }

        return $crates;
    }

    /**
     * @return array<int, array{jumlah_ekor:int, berat_ayam:float, berat_total:float}>
     */
    private function buildCratesFromActualWeight(int $jumlahEkor, float $totalActualKg): array
    {
        $remainingEkor = $jumlahEkor;
        $remainingKg = $totalActualKg;
        $crates = [];

        while ($remainingEkor > 0) {
            $isi = min(20, $remainingEkor);

            if ($remainingEkor === $isi) {
                $beratAyam = round($remainingKg, 2);
            } else {
                $prop = $isi / $remainingEkor;
                $beratAyam = round($remainingKg * $prop, 2);
            }

            $crates[] = [
                'jumlah_ekor' => $isi,
                'berat_ayam' => $beratAyam,
                'berat_total' => round($beratAyam + 15, 2),
            ];

            $remainingEkor -= $isi;
            $remainingKg = round($remainingKg - $beratAyam, 2);
        }

        return $crates;
    }

    /**
     * @param int[] $source
     * @return int[]
     */
    private function pickRandomSubset(array $source, int $min, int $max): array
    {
        if (empty($source)) {
            return [];
        }

        $take = random_int($min, max($min, $max));
        shuffle($source);

        return array_slice($source, 0, min($take, count($source)));
    }

    private function randomFloat(float $min, float $max, int $precision = 2): float
    {
        $scale = 10 ** $precision;
        $minInt = (int) round($min * $scale);
        $maxInt = (int) round($max * $scale);

        return random_int($minInt, $maxInt) / $scale;
    }
}
