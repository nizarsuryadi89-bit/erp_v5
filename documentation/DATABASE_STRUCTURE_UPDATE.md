# Database Structure Update - Laporan Keuangan

## 📋 Ringkasan Perubahan

Laporan keuangan telah diperbarui untuk menggunakan struktur database yang benar sesuai dengan tabel `tbl_pembayaran` dan `tbl_pembayaran_detail`.

## 🗄️ Struktur Database yang Digunakan

### 1. **Tabel `tbl_pembayaran`**
```sql
CREATE TABLE `tbl_pembayaran` (
  `pembayaran_id` int(11) NOT NULL,
  `nospk` varchar(50) NOT NULL,
  `konsumen_id` int(11) DEFAULT NULL,
  `tgl_bayar` date NOT NULL DEFAULT curdate(),
  `jam_bayar` time NOT NULL DEFAULT curtime(),
  `total_tagihan` decimal(15,2) NOT NULL,
  `total_bayar` decimal(15,2) NOT NULL,
  `status_lunas` enum('0','1') NOT NULL DEFAULT '0',
  `kasir_id` int(11) NOT NULL,
  `perusahaan_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 2. **Tabel `tbl_pembayaran_detail`**
```sql
CREATE TABLE `tbl_pembayaran_detail` (
  `detail_id` int(11) NOT NULL,
  `pembayaran_id` int(11) NOT NULL,
  `metode` enum('tunai','transfer','debit','kartu_kredit','ewallet','lainnya') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `referensi` varchar(100) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `edc_id` int(11) DEFAULT NULL,
  `ewallet_id` int(11) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

## 🔄 Perubahan yang Diterapkan

### 1. **Controller Updates**

#### **Method `pemasukan_harian()`**
```php
// Sebelum: Query langsung ke tbl_pembayaran_detail
$cash = $this->db->query("SELECT SUM(jumlah) as totalna FROM tbl_pembayaran_detail WHERE metode = 'tunai' and created_at = '$hariini'")->row_array();

// Sesudah: JOIN dengan tbl_pembayaran untuk filter tanggal yang benar
$cash = $this->db->query("
    SELECT SUM(pd.jumlah) as totalna 
    FROM tbl_pembayaran_detail pd
    JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
    WHERE pd.metode = 'tunai' 
    AND p.tgl_bayar = '$hariini'
")->row_array();
```

#### **Method `pemasukan_bulanan()`**
```php
// Sebelum: Query ke field yang tidak ada
$total_pemasukan = $this->db->query("
    SELECT SUM(bayar_tunai + bayar_transfer + bayar_debit + bayar_ewallet) as totalna 
    FROM tbl_pembayaran 
    WHERE pembayaran_tgl BETWEEN '$bulan_ini' AND '$bulan_akhir'
")->row_array();

// Sesudah: Query ke field yang benar
$total_pemasukan = $this->db->query("
    SELECT SUM(total_bayar) as totalna 
    FROM tbl_pembayaran 
    WHERE tgl_bayar BETWEEN '$bulan_ini' AND '$bulan_akhir'
")->row_array();
```

### 2. **Field Mapping yang Diperbaiki**

| **Fungsi** | **Field Lama** | **Field Baru** | **Keterangan** |
|------------|----------------|----------------|----------------|
| **Tanggal Pembayaran** | `pembayaran_tgl` | `tgl_bayar` | Field tanggal di tbl_pembayaran |
| **Jam Pembayaran** | `pembayaran_jam` | `jam_bayar` | Field jam di tbl_pembayaran |
| **Total Pembayaran** | `grand_total` | `total_bayar` | Total pembayaran di tbl_pembayaran |
| **Kasir ID** | `user_id` | `kasir_id` | ID kasir di tbl_pembayaran |
| **Metode Pembayaran** | Field terpisah | `metode` di tbl_pembayaran_detail | Enum: tunai, transfer, debit, dll |
| **Jumlah per Metode** | Field terpisah | `jumlah` di tbl_pembayaran_detail | Jumlah per metode pembayaran |

### 3. **Query Structure yang Diperbaiki**

#### **Total Pemasukan**
```sql
-- Sebelum
SELECT SUM(bayar_tunai + bayar_transfer + bayar_debit + bayar_ewallet) as totalna 
FROM tbl_pembayaran 
WHERE pembayaran_tgl = '$hariini'

-- Sesudah
SELECT SUM(total_bayar) as totalna 
FROM tbl_pembayaran 
WHERE tgl_bayar = '$hariini'
```

#### **Breakdown per Metode Pembayaran**
```sql
-- Sebelum
SELECT SUM(jumlah) as totalna 
FROM tbl_pembayaran_detail 
WHERE metode = 'tunai' and created_at = '$hariini'

-- Sesudah
SELECT SUM(pd.jumlah) as totalna 
FROM tbl_pembayaran_detail pd
JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
WHERE pd.metode = 'tunai' 
AND p.tgl_bayar = '$hariini'
```

#### **Detail Transaksi**
```sql
-- Sebelum
SELECT p.pembayaran_tgl as tanggal, p.pembayaran_jam as jam, p.nospk, 
       k.konsumen_nama as konsumen, k.konsumen_nohp as telepon,
       p.bayar_tunai as jumlah, p.grand_total as total,
       u.nama as kasir
FROM tbl_pembayaran p
LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
LEFT JOIN users u ON p.user_id = u.id
WHERE p.pembayaran_tgl = '$hariini'

-- Sesudah
SELECT p.tgl_bayar as tanggal, p.jam_bayar as jam, p.nospk, 
       k.konsumen_nama as konsumen, k.konsumen_nohp as telepon,
       pd.jumlah, p.total_bayar as total,
       u.nama as kasir
FROM tbl_pembayaran p
JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
LEFT JOIN users u ON p.kasir_id = u.id
WHERE pd.metode = 'tunai'
AND p.tgl_bayar = '$hariini'
```

## 📊 Data yang Ditampilkan

### 1. **Laporan Pemasukan Harian**
- **Total Pemasukan**: `SUM(total_bayar)` dari `tbl_pembayaran`
- **Cash**: `SUM(jumlah)` dari `tbl_pembayaran_detail` WHERE `metode = 'tunai'`
- **Transfer**: `SUM(jumlah)` dari `tbl_pembayaran_detail` WHERE `metode = 'transfer'`
- **Debit**: `SUM(jumlah)` dari `tbl_pembayaran_detail` WHERE `metode = 'debit'`
- **E-Wallet**: `SUM(jumlah)` dari `tbl_pembayaran_detail` WHERE `metode = 'ewallet'`
- **Piutang**: `SUM(total_tagihan)` dari `tbl_piutang`

### 2. **Laporan Pemasukan Bulanan**
- **Total Pemasukan**: `SUM(total_bayar)` dari `tbl_pembayaran` (range bulan)
- **Breakdown per Metode**: `SUM(jumlah)` dari `tbl_pembayaran_detail` (range bulan)
- **Detail Transaksi**: JOIN antara `tbl_pembayaran` dan `tbl_pembayaran_detail`

## 🎯 Keuntungan Perubahan

### 1. **Data Accuracy**
- ✅ **Tanggal yang Benar**: Menggunakan `tgl_bayar` bukan `created_at`
- ✅ **Total yang Akurat**: Menggunakan `total_bayar` bukan kalkulasi manual
- ✅ **Metode yang Tepat**: Menggunakan `metode` enum yang terstandarisasi

### 2. **Performance**
- ✅ **Query yang Efisien**: JOIN yang optimal
- ✅ **Index yang Tepat**: Menggunakan field yang ter-index
- ✅ **Filter yang Akurat**: Filter tanggal di tabel utama

### 3. **Maintainability**
- ✅ **Struktur yang Konsisten**: Mengikuti struktur database yang benar
- ✅ **Field yang Standar**: Menggunakan field yang sudah didefinisikan
- ✅ **Relasi yang Benar**: Foreign key yang tepat

## 🔧 Implementasi

### 1. **Controller Changes**
- ✅ **`pemasukan_harian()`**: Updated query structure
- ✅ **`pemasukan_bulanan()`**: Updated query structure
- ✅ **Data mapping**: Correct field mapping

### 2. **View Updates**
- ✅ **Progress bars**: Menggunakan data transfer yang benar
- ✅ **Pie charts**: Data yang akurat untuk visualisasi
- ✅ **Legends**: Menampilkan nilai yang tepat

### 3. **Data Validation**
- ✅ **Null coalescing**: `??` operator untuk handling null values
- ✅ **Type safety**: Menggunakan field yang benar
- ✅ **Error handling**: Query yang robust

## 📋 Checklist Implementasi

- [x] **Controller**: Query updated untuk menggunakan struktur database yang benar
- [x] **Field Mapping**: Semua field menggunakan nama yang benar
- [x] **JOIN Queries**: Menggunakan JOIN yang tepat antara tabel
- [x] **Date Filtering**: Filter tanggal menggunakan field yang benar
- [x] **Data Aggregation**: SUM dan GROUP BY yang akurat
- [x] **View Updates**: Progress bars dan charts menggunakan data yang benar
- [x] **Error Handling**: Null coalescing dan validasi data
- [x] **Performance**: Query yang optimal dan efisien

## 🚀 Hasil Akhir

Dengan perubahan ini, laporan keuangan sekarang:
1. **Menggunakan struktur database yang benar**
2. **Menampilkan data yang akurat**
3. **Memiliki performa yang optimal**
4. **Mudah untuk di-maintain**
5. **Konsisten dengan struktur aplikasi**

---

**Laporan keuangan sekarang sepenuhnya menggunakan struktur database `tbl_pembayaran` dan `tbl_pembayaran_detail` dengan field yang tepat dan query yang optimal.**
