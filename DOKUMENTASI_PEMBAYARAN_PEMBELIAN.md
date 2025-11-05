# Dokumentasi Fitur Pembayaran Pembelian

## 📋 Overview

Fitur pembayaran pembelian memungkinkan user untuk:
1. Membuat pembelian dengan status otomatis "Belum Bayar"
2. Melakukan pembayaran melalui modal di halaman index
3. Memasukkan harga per kg dan nominal pembayaran
4. Otomatis update status menjadi "Sudah Bayar"

---

## 🔄 Alur Proses

### 1. **Membuat Pembelian (Create)**

**Status:** Otomatis "Belum Bayar"

**Data yang Disimpan:**
- Data Pembelian: peternak, tanggal, kode, status (otomatis: belum bayar)
- Data Timbangan: jenis (otomatis: timbangan data pembelian), tanggal, nama karyawan, total ekor, total berat
- Data Keranjang: jumlah ekor dan berat per keranjang (multiple rows)
- Detail Pembelian: batch, DO (opsional), susut, **harga_beli_per_kg = 0**, **subtotal = 0**

**Catatan:**
- Harga beli per kg dan subtotal diset 0 saat create
- Akan diisi saat proses pembayaran

---

### 2. **Proses Pembayaran (Bayar)**

**Tombol Bayar:** Hanya muncul untuk pembelian dengan status "Belum Bayar"

**Modal Pembayaran:**
- Menampilkan total berat
- Menampilkan susut kg
- Menghitung berat bersih otomatis
- Input harga per kg (required)
- Auto-calculate total yang harus dibayar
- Input nominal pembayaran (required)
- Auto-calculate kembalian
- Input tanggal pembayaran (default: hari ini)
- Input catatan (opsional)

**Validasi:**
- Nominal pembayaran harus >= total yang harus dibayar
- Harga per kg harus > 0
- Semua field required harus diisi

**Proses di Backend:**
1. Validasi input
2. Cek status pembelian (harus belum bayar)
3. Hitung subtotal: (Total Berat - Susut) × Harga per Kg
4. Update `harga_beli_per_kg` di tabel `pembelian_details`
5. Update `subtotal` di tabel `pembelian_details`
6. Update `status` menjadi "sudah bayar" di tabel `pembelians`
7. Tampilkan notifikasi dengan kembalian

---

## 📝 Perubahan File

### 1. **Controller: PembelianController.php**

#### Method Baru: `bayar(Request $request, $id)`

```php
public function bayar(Request $request, $id)
{
    // Validasi input
    // Cek status pembelian
    // Hitung subtotal
    // Update harga_beli_per_kg dan subtotal
    // Update status menjadi sudah bayar
    // Return dengan notifikasi
}
```

**Input:**
- `harga_per_kg` (required, integer, min:0)
- `nominal_bayar` (required, integer, min:0)
- `tanggal_bayar` (required, date)
- `catatan_bayar` (nullable, string)

**Output:**
- Success: Redirect ke index dengan notifikasi kembalian
- Error: Redirect back dengan pesan error

#### Method `store()` - Updated

**Perubahan:**
- Validasi `harga_beli_per_kg` dan `subtotal` dihapus
- Saat create, kedua field diset 0
- Status otomatis "belum bayar"

---

### 2. **View: create.blade.php**

**Perubahan:**
- Field `status` otomatis "Belum Bayar" (hidden input)
- Field `harga_beli_per_kg` dihapus
- Field `subtotal` dihapus
- JavaScript untuk hitung subtotal dihapus
- Tambah info: "Harga dan subtotal akan diisi saat pembayaran"

---

### 3. **View: index.blade.php**

**Perubahan:**

#### Tabel:
- Tambah badge untuk status (Warning: Belum Bayar, Success: Sudah Bayar)
- Tombol "Bayar" hanya muncul untuk status "Belum Bayar"
- Format tanggal dd/mm/yyyy

#### Modal Pembayaran:
- Form lengkap dengan semua field
- Auto-calculate total dan kembalian
- Validasi real-time
- Alert info kode pembelian

#### JavaScript:
- Function `setBayarData()` untuk set data modal
- Auto-calculate total saat harga per kg diinput
- Auto-calculate kembalian saat nominal bayar diinput
- Validasi sebelum submit
- Reset form saat modal ditutup

---

### 4. **Routes: web.php**

**Route Baru:**
```php
Route::put('/{id}/bayar', 'bayar')->name('bayar');
```

**Full Path:** `PUT /pembelian/{id}/bayar`

---

## 💾 Database Schema

### Tabel: `pembelians`
- `status` → enum('belum bayar', 'sudah bayar')
- Default saat create: 'belum bayar'
- Update saat bayar: 'sudah bayar'

### Tabel: `pembelian_details`
- `harga_beli_per_kg` → integer
- `subtotal` → integer
- Default saat create: 0
- Update saat bayar: nilai sebenarnya

---

## 🎯 Contoh Penggunaan

### 1. **Membuat Pembelian:**

```
User mengisi form:
- Peternak: PT. ABC
- Tanggal: 2025-01-15
- Kode: PBL-20250115-0001
- Status: Belum Bayar (otomatis)
- Batch: BATCH-00001
- Susut: 5 kg
- Keranjang:
  * Keranjang 1: 100 ekor, 150 kg
  * Keranjang 2: 50 ekor, 75 kg

Tersimpan:
- pembelians: status = 'belum bayar'
- pembelian_details: harga_beli_per_kg = 0, subtotal = 0
```

### 2. **Proses Pembayaran:**

```
User klik tombol "Bayar" di index

Modal muncul dengan data:
- Total Berat: 225 kg
- Susut: 5 kg
- Berat Bersih: 220 kg

User input:
- Harga per Kg: Rp 35.000
- Total Harus Bayar: Rp 7.700.000 (otomatis)
- Nominal Bayar: Rp 8.000.000
- Kembalian: Rp 300.000 (otomatis)
- Tanggal Bayar: 2025-01-15
- Catatan: Pembayaran cash

Update Database:
- pembelians: status = 'sudah bayar'
- pembelian_details: 
  * harga_beli_per_kg = 35000
  * subtotal = 7700000
```

---

## ✅ Validasi

### Client-Side (JavaScript):
1. Nominal bayar harus >= total harus bayar
2. Total harus bayar harus > 0 (harga per kg harus diisi)
3. Form tidak bisa submit jika validasi gagal
4. Alert SweetAlert untuk pesan error

### Server-Side (Controller):
1. Harga per kg required, integer, min 0
2. Nominal bayar required, integer, min 0
3. Tanggal bayar required, format date
4. Status harus 'belum bayar'
5. Nominal bayar harus >= subtotal
6. Database transaction untuk data integrity

---

## 🔒 Keamanan

1. **Authorization:** Hanya user yang login dapat akses
2. **Validation:** Semua input divalidasi di server
3. **Transaction:** Menggunakan DB transaction
4. **Status Check:** Cek status sebelum bayar (tidak bisa bayar 2x)
5. **Error Handling:** Try-catch dengan rollback

---

## 🎨 UI/UX Features

1. **Badge Status:**
   - Warning (kuning): Belum Bayar
   - Success (hijau): Sudah Bayar

2. **Tombol Bayar:**
   - Icon money-bill
   - Warna success (hijau)
   - Hanya muncul untuk status belum bayar

3. **Modal:**
   - Header hijau dengan icon
   - Alert info kode pembelian
   - Field read-only untuk data tetap
   - Field input besar untuk nominal
   - Warna berbeda untuk total dan kembalian

4. **Auto-Calculate:**
   - Total = Berat Bersih × Harga per Kg
   - Kembalian = Nominal Bayar - Total
   - Format rupiah dengan separator

5. **Validasi Visual:**
   - Kembalian merah jika kurang
   - Kembalian biru jika cukup
   - Required field dengan tanda (*)

---

## 📊 Status Badge Colors

```blade
@if($item->status == 'belum bayar')
    <span class="badge badge-warning">Belum Bayar</span>
@else
    <span class="badge badge-success">Sudah Bayar</span>
@endif
```

---

## 🚀 Testing

### Test Case 1: Create Pembelian
- Status otomatis 'belum bayar' ✓
- Harga dan subtotal = 0 ✓
- Data tersimpan dengan benar ✓

### Test Case 2: Proses Pembayaran
- Modal muncul dengan data yang benar ✓
- Auto-calculate berfungsi ✓
- Validasi berfungsi ✓
- Database terupdate ✓
- Notifikasi muncul ✓

### Test Case 3: Validasi
- Nominal kurang ditolak ✓
- Harga kosong ditolak ✓
- Status sudah bayar tidak bisa bayar lagi ✓

---

## 📞 Support

Untuk pertanyaan atau issue, silakan hubungi developer.

---

**Last Updated:** 2025-01-15
**Version:** 1.0
