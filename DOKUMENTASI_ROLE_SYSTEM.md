# Sistem Role-Based Kurnia Unggas

## Deskripsi Sistem
Sistem ini telah diupdate dengan sistem role-based access control (RBAC) yang membagi akses berdasarkan 3 role utama.

## Role dan Hak Akses

### 1. Pemilik Usaha (`pemilik`)
**Hak Akses:**
- Melihat dashboard dengan ringkasan keuangan lengkap
- Grafik penjualan dan pembelian (6 bulan terakhir)
- Report keuntungan dan kerugian
- Laporan penjualan harian
- Fitur forecast penjualan

**Tidak Dapat:**
- Melakukan input data transaksi
- Mengelola master data

### 2. Penanggung Jawab Usaha (`penanggung_jawab`)
**Hak Akses:**
- Akses penuh ke semua fitur sistem
- Master data (Pemasok, Peternak, Pelanggan, Produk, dll)
- Delivery Order
- Pembelian
- Penjualan
- Stok Opname
- Mortalitas Ayam
- Susut Batch
- Biaya Operasional
- Laporan Penjualan
- Forecast

### 3. Kasir (`kasir`)
**Hak Akses:**
- Dashboard sederhana
- Input penjualan
- Lihat riwayat penjualan (yang dibuat sendiri)

**Tidak Dapat:**
- Akses master data
- Akses pembelian
- Akses laporan keuangan

## Setup Database

### 1. Jalankan Migration
```bash
php artisan migrate:fresh
```

### 2. Jalankan Seeder
```bash
php artisan db:seed
```

## Akun Default

Setelah menjalankan seeder, tersedia 3 akun:

| Role | Email | Password |
|------|-------|----------|
| Pemilik Usaha | pemilik@kurniaunggas.com | password123 |
| Penanggung Jawab | pj@kurniaunggas.com | password123 |
| Kasir | kasir@kurniaunggas.com | password123 |

## Fitur Utama

### Dashboard Role-Based
Setiap role memiliki dashboard yang disesuaikan:

- **Pemilik**: Info boxes dengan total penjualan, pembelian, biaya operasional, dan keuntungan. Dilengkapi grafik trend.
- **Penanggung Jawab**: Quick links ke semua fitur yang tersedia.
- **Kasir**: Form cepat untuk input penjualan dan informasi transaksi hari ini.

### Sidebar Menu
Menu sidebar otomatis menyesuaikan berdasarkan role user yang login. Hanya menampilkan menu yang sesuai dengan hak akses.

### Middleware Protection
Semua route dilindungi dengan middleware `role` yang memeriksa hak akses user sebelum mengizinkan akses ke halaman tertentu.

## Relasi dengan User

Semua tabel transaksi telah dihubungkan dengan `user_id`:
- `pemasoks`
- `peternaks`
- `pelanggans`
- `produks`
- `pembelians`
- `batch_pembelians`
- `penjualans`
- `biaya_operasionals`

Menggunakan constraint `nullOnDelete()` sehingga ketika user dihapus, data transaksi tetap tersimpan dengan `user_id = NULL`.

## Teknologi

- **Framework**: Laravel 11
- **UI**: AdminLTE 3
- **Chart**: Chart.js
- **Database**: MySQL

## Cara Menambah User Baru

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Nama User',
    'email' => 'email@example.com',
    'password' => Hash::make('password'),
    'role' => 'kasir', // atau 'pemilik' atau 'penanggung_jawab'
]);
```

## Helper Methods di User Model

```php
// Cek role
if (auth()->user()->isPemilik()) {
    // Kode untuk pemilik
}

if (auth()->user()->isPenanggungJawab()) {
    // Kode untuk penanggung jawab
}

if (auth()->user()->isKasir()) {
    // Kode untuk kasir
}
```

## Middleware Usage di Routes

```php
// Single role
Route::middleware(['role:pemilik'])->group(function () {
    // Routes hanya untuk pemilik
});

// Multiple roles
Route::middleware(['role:kasir,penanggung_jawab'])->group(function () {
    // Routes untuk kasir atau penanggung jawab
});
```

## Keamanan

- Semua route memerlukan autentikasi (`auth` middleware)
- Role-based access control mencegah akses tidak sah
- Password di-hash menggunakan bcrypt
- Foreign key dengan nullOnDelete untuk integritas data

## Development

Untuk menjalankan aplikasi:

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate:fresh --seed

# Run server
php artisan serve

# Compile assets (di terminal terpisah)
npm run dev
```

## Notes

- Pastikan file `.env` sudah dikonfigurasi dengan benar
- Database MySQL harus sudah dibuat sebelum menjalankan migration
- Untuk production, ubah semua password default
- Image upload untuk biaya operasional disimpan di `storage/app/public/biaya-operasional`
