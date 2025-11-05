# Summary Pembuatan Model Pembelian & Controller

## ✅ Yang Sudah Dibuat:

### 1. Model Files (7 Files Updated/Created)

#### ✅ `app/Models/Pembelian.php` - UPDATED
- Fillable fields: tanggal_pembelian, kode_pembelian, status, peternak_id
- Relasi: belongsTo(Peternak), hasMany(PembelianDetail)
- Method: generateKodePembelian() untuk auto-generate kode
- Scope: status(), tanggal()

#### ✅ `app/Models/PembelianDetail.php` - CREATED NEW
- Fillable fields: pembelian_id, batch_pembelian_id, timbangan_id, delivery_order_id, harga_beli_per_kg, subtotal, susut_kg
- Relasi: belongsTo(Pembelian, BatchPembelian, Timbangan, DeliveryOrder)

#### ✅ `app/Models/Timbangan.php` - UPDATED
- Fillable fields: jenis, tanggal, total_jumlah_ekor, total_berat, nama_karyawan
- Relasi: hasMany(Keranjang, PembelianDetail)

#### ✅ `app/Models/Keranjang.php` - UPDATED
- Fillable fields: timbangan_id, jumlah_ekor, berat_ayam
- Relasi: belongsTo(Timbangan)

#### ✅ `app/Models/BatchPembelian.php` - UPDATED
- Added relasi: hasMany(PembelianDetail)
- Added casts untuk type conversion

#### ✅ `app/Models/DeliveryOrder.php` - UPDATED
- Added fillable fields
- Added relasi: hasMany(PembelianDetail)
- Added casts untuk type conversion

#### ✅ `app/Models/Peternak.php` - UPDATED
- Added relasi: hasMany(Pembelian)

---

### 2. Controller File

#### ✅ `app/Http/Controllers/PembelianController.php` - UPDATED LENGKAP

**Methods yang sudah dibuat:**

1. **`create()`**
   - Generate kode pembelian otomatis
   - Load batch pembelian dan delivery order
   - Return view dengan data

2. **`store(Request $request)`**
   - Validasi lengkap semua input
   - Database transaction untuk data integrity
   - Flow penyimpanan:
     - Hitung total dari keranjang
     - Simpan timbangan (jenis otomatis: "timbangan data pembelian")
     - Simpan keranjang (multiple rows)
     - Simpan pembelian
     - Simpan detail pembelian
     - Update stok batch pembelian
   - Notifikasi SweetAlert
   - Error handling dengan rollback

3. **`index()`** - Updated dengan eager loading

4. **`show($id)`** - Lengkap dengan relasi

5. **`destroy($id)`** - Dengan rollback stok dan transaction

6. **`getBatchPembelian()`** - AJAX method

7. **`getDeliveryOrder()`** - AJAX method

---

### 3. Routes File

#### ✅ `routes/web.php` - UPDATED
- Added routes untuk AJAX:
  - `get-data.batch-pembelian`
  - `get-data.delivery-order`

---

### 4. View File

#### ✅ `resources/views/pembelian/create.blade.php` - UPDATED
- Added field status pembelian
- Auto-fill kode pembelian dari controller
- Load batch pembelian dari server
- Load delivery order dari server
- JavaScript lengkap untuk dynamic form

---

### 5. Documentation File

#### ✅ `DOKUMENTASI_MODEL_PEMBELIAN.md` - CREATED
- Dokumentasi lengkap semua model dan relasi
- Penjelasan controller methods
- Diagram relasi
- Alur proses pembelian
- Fitur-fitur JavaScript

---

## 📋 Relasi Antar Model:

```
Pembelian (1) ──── (Many) PembelianDetail
    │                        │
    │                        ├── BatchPembelian
    │                        ├── Timbangan ──── Keranjang (Many)
    │                        └── DeliveryOrder (Optional)
    │
    └── Peternak
```

---

## 🔧 Fitur yang Sudah Diimplementasi:

### Form Create Pembelian:
✅ Input data pembelian (peternak, tanggal, kode, status)
✅ Kode pembelian auto-generate
✅ Timbangan otomatis jenis "timbangan data pembelian"
✅ Input keranjang dinamis (tambah/hapus)
✅ Auto-calculate total ekor dan berat
✅ Input detail pembelian (batch, DO, harga, susut)
✅ Auto-calculate subtotal
✅ Select2 untuk semua dropdown
✅ Validasi client-side dan server-side

### Controller Store:
✅ Validasi lengkap dengan pesan kustom
✅ Database transaction
✅ Auto-save timbangan dengan jenis tertentu
✅ Save multiple keranjang
✅ Save pembelian dan detail
✅ Update stok batch pembelian otomatis
✅ SweetAlert notification
✅ Error handling dengan rollback

### Model Relationships:
✅ Semua relasi sudah terdefinisi
✅ Casts untuk type conversion
✅ Helper methods (generateKodePembelian, kodeBatch)
✅ Query scopes

---

## 🎯 Cara Menggunakan:

### 1. Akses Form Create:
```
URL: /pembelian/create
Route: route('pembelian.create')
```

### 2. Submit Form akan:
- Validasi semua input
- Simpan timbangan dengan jenis otomatis
- Simpan semua keranjang
- Simpan pembelian
- Simpan detail pembelian
- Update stok batch
- Redirect ke index dengan notifikasi

### 3. Data yang Tersimpan:
- **pembelians table**: 1 record
- **pembelian_details table**: 1 record
- **timbangans table**: 1 record (jenis: "timbangan data pembelian")
- **keranjangs table**: N records (sesuai jumlah input)
- **batch_pembelians table**: stok terupdate otomatis

---

## 📝 Contoh Penggunaan di View:

```php
// Di Controller
$pembelian = Pembelian::with([
    'peternak',
    'pembelianDetails.batchPembelian',
    'pembelianDetails.timbangan.keranjangs',
    'pembelianDetails.deliveryOrder'
])->find($id);

// Di View
{{ $pembelian->kode_pembelian }}
{{ $pembelian->peternak->nama }}
{{ $pembelian->pembelianDetails->first()->timbangan->total_berat }}
@foreach($pembelian->pembelianDetails->first()->timbangan->keranjangs as $keranjang)
    {{ $keranjang->jumlah_ekor }}
@endforeach
```

---

## ⚠️ Catatan Penting:

1. **Jenis Timbangan**: Otomatis diset "timbangan data pembelian" (readonly di form)
2. **Kode Pembelian**: Format PBL-YYYYMMDD-0001 (auto-generate per hari)
3. **Subtotal**: Dihitung dari (Total Berat - Susut) × Harga per Kg
4. **Stok Batch**: Otomatis bertambah saat pembelian disimpan
5. **Transaction**: Semua operasi dalam 1 transaction, jika error akan rollback
6. **Minimal Keranjang**: Harus ada minimal 1 keranjang
7. **Delivery Order**: Optional (boleh kosong)

---

## 🚀 Siap Digunakan!

Semua model, relasi, controller, dan view sudah lengkap dan siap digunakan.
Form pembelian dapat menangani:
- Multiple keranjang input
- Auto-calculate total dan subtotal
- Validasi lengkap
- Update stok otomatis
- Error handling
- Notifikasi user-friendly

---

## 📚 File Dokumentasi:

Untuk detail lengkap, lihat: `DOKUMENTASI_MODEL_PEMBELIAN.md`
