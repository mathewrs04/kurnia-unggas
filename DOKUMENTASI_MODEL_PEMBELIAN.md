# Dokumentasi Model Pembelian dan Relasi

## Model yang Telah Dibuat

### 1. Pembelian Model
**Path:** `app/Models/Pembelian.php`

**Fillable Fields:**
- `tanggal_pembelian` (date)
- `kode_pembelian` (string, unique)
- `status` (enum: 'belum bayar', 'sudah bayar')
- `peternak_id` (foreign key)

**Relasi:**
- `peternak()` - belongsTo Peternak
- `pembelianDetails()` - hasMany PembelianDetail

**Method:**
- `generateKodePembelian()` - Generate kode pembelian otomatis (PBL-YYYYMMDD-0001)
- `scopeStatus($status)` - Filter berdasarkan status
- `scopeTanggal($tanggal)` - Filter berdasarkan tanggal

---

### 2. PembelianDetail Model
**Path:** `app/Models/PembelianDetail.php`

**Fillable Fields:**
- `pembelian_id` (foreign key)
- `batch_pembelian_id` (foreign key)
- `timbangan_id` (foreign key)
- `delivery_order_id` (foreign key, nullable)
- `harga_beli_per_kg` (integer)
- `subtotal` (integer)
- `susut_kg` (float, nullable)

**Relasi:**
- `pembelian()` - belongsTo Pembelian
- `batchPembelian()` - belongsTo BatchPembelian
- `timbangan()` - belongsTo Timbangan
- `deliveryOrder()` - belongsTo DeliveryOrder

---

### 3. Timbangan Model
**Path:** `app/Models/Timbangan.php`

**Fillable Fields:**
- `jenis` (enum)
- `tanggal` (date)
- `total_jumlah_ekor` (integer)
- `total_berat` (float)
- `nama_karyawan` (string, nullable)

**Relasi:**
- `keranjangs()` - hasMany Keranjang
- `pembelianDetails()` - hasMany PembelianDetail

---

### 4. Keranjang Model
**Path:** `app/Models/Keranjang.php`

**Fillable Fields:**
- `timbangan_id` (foreign key)
- `jumlah_ekor` (integer)
- `berat_ayam` (float)

**Relasi:**
- `timbangan()` - belongsTo Timbangan

---

### 5. BatchPembelian Model (Updated)
**Path:** `app/Models/BatchPembelian.php`

**Fillable Fields:**
- `kode_batch` (string)
- `harga_beli_per_kg` (integer)
- `stok_ekor` (integer)
- `stok_ekor_minimal` (integer)
- `stok_kg` (float)

**Relasi:**
- `pembelianDetails()` - hasMany PembelianDetail

**Method:**
- `kodeBatch()` - Generate kode batch otomatis

---

### 6. DeliveryOrder Model (Updated)
**Path:** `app/Models/DeliveryOrder.php`

**Fillable Fields:**
- `kode_do` (string)
- `tanggal` (date)
- `jumlah_ekor` (integer)
- `berat_total` (float)

**Relasi:**
- `pembelianDetails()` - hasMany PembelianDetail

---

### 7. Peternak Model (Updated)
**Path:** `app/Models/Peternak.php`

**Fillable Fields:**
- `pemasok_id` (foreign key)
- `nama` (string)
- `alamat` (text)
- `no_telp` (string)

**Relasi:**
- `pemasok()` - belongsTo Pemasok
- `pembelians()` - hasMany Pembelian

---

## Controller: PembelianController

**Path:** `app/Http/Controllers/PembelianController.php`

### Methods:

#### 1. `index()`
- Menampilkan daftar semua pembelian
- Dengan eager loading: peternak, pembelianDetails.timbangan

#### 2. `create()`
- Menampilkan form tambah pembelian
- Generate kode pembelian otomatis
- Load data batch pembelian dan delivery order

#### 3. `store(Request $request)`
- Validasi input form
- Menggunakan Database Transaction
- Proses penyimpanan:
  1. Hitung total jumlah ekor dan berat dari keranjang
  2. Simpan data timbangan (jenis otomatis: "timbangan data pembelian")
  3. Simpan data keranjang per baris
  4. Simpan data pembelian
  5. Simpan detail pembelian
  6. Update stok batch pembelian (stok_ekor dan stok_kg)
- Redirect ke index dengan notifikasi SweetAlert

#### 4. `show($id)`
- Menampilkan detail pembelian
- Dengan eager loading semua relasi

#### 5. `destroy($id)`
- Hapus pembelian
- Menggunakan Database Transaction
- Proses penghapusan:
  1. Update stok batch pembelian (kurangi)
  2. Hapus data keranjang
  3. Hapus data timbangan
  4. Hapus pembelian (cascade delete pembelian details)

#### 6. `getBatchPembelian()` (AJAX)
- Return JSON data batch pembelian

#### 7. `getDeliveryOrder()` (AJAX)
- Return JSON data delivery order

---

## Routes

**Path:** `routes/web.php`

```php
// AJAX Routes
Route::prefix('get-data')->as('get-data.')->group(function () {
    Route::get('peternak', [PeternakController::class, 'getData'])->name('peternak');
    Route::get('batch-pembelian', [PembelianController::class, 'getBatchPembelian'])->name('batch-pembelian');
    Route::get('delivery-order', [PembelianController::class, 'getDeliveryOrder'])->name('delivery-order');
});

// Pembelian Routes
Route::prefix('pembelian')->as('pembelian.')->controller(PembelianController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}/show', 'show')->name('show');
    Route::delete('/{id}/destroy', 'destroy')->name('destroy');
});
```

---

## Diagram Relasi

```
Peternak
  └── hasMany → Pembelian
                  ├── belongsTo → Peternak
                  └── hasMany → PembelianDetail
                                  ├── belongsTo → Pembelian
                                  ├── belongsTo → BatchPembelian
                                  ├── belongsTo → Timbangan
                                  │                 ├── hasMany → Keranjang
                                  │                 └── hasMany → PembelianDetail
                                  └── belongsTo → DeliveryOrder (nullable)

BatchPembelian
  └── hasMany → PembelianDetail

DeliveryOrder
  └── hasMany → PembelianDetail

Timbangan
  ├── hasMany → Keranjang
  └── hasMany → PembelianDetail
```

---

## Alur Proses Pembelian

1. **User membuka form create:**
   - Controller generate kode pembelian otomatis
   - Load data batch pembelian dan delivery order
   - Tampilkan form

2. **User mengisi form:**
   - Data pembelian (peternak, tanggal, kode, status)
   - Data timbangan (tanggal, nama karyawan)
   - Data keranjang (dapat menambah/hapus baris)
   - Detail pembelian (batch, DO, harga, susut)
   - Subtotal dihitung otomatis: (Total Berat - Susut) × Harga per Kg

3. **User submit form:**
   - Controller validasi input
   - Mulai database transaction
   - Simpan timbangan dengan jenis otomatis "timbangan data pembelian"
   - Simpan semua keranjang yang diinput
   - Simpan pembelian
   - Simpan detail pembelian
   - Update stok batch pembelian
   - Commit transaction
   - Redirect dengan notifikasi sukses

4. **Jika error:**
   - Rollback semua perubahan
   - Tampilkan pesan error
   - Return ke form dengan input lama

---

## Fitur JavaScript pada Form

1. **Select2** untuk dropdown dengan pencarian
2. **Dynamic rows** untuk input keranjang
3. **Auto-calculate** total jumlah ekor dan berat
4. **Auto-calculate** subtotal
5. **Validasi** minimal 1 keranjang
6. **Update nomor urut** otomatis setelah hapus/tambah
7. **Disable tombol hapus** jika hanya ada 1 keranjang
8. **Real-time update** saat input berubah

---

## Catatan Penting

- Jenis timbangan otomatis: **"timbangan data pembelian"**
- Kode pembelian format: **PBL-YYYYMMDD-0001**
- Stok batch pembelian otomatis terupdate saat pembelian disimpan
- Menggunakan **Database Transaction** untuk data integrity
- Menggunakan **SweetAlert** untuk notifikasi
- Semua validasi dilakukan di server-side (controller)
