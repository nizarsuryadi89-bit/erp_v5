# Debug Guide - Transaksi Penjualan Manual

## Masalah yang Ditemukan dan Perbaikan

### 1. **Field Names Inconsistency**
**Masalah:** Field names tidak konsisten antara tabel dan kode
**Perbaikan:**
- `tbl_pemesanan_detail`: menggunakan `produk_id` bukan `product_id`
- `tbl_product`: menggunakan `product_id` dan `product_nama`
- `tbl_konsumen`: menggunakan `konsumen_nama` dan `konsumen_nohp`

### 2. **Database Transaction Error Handling**
**Masalah:** Tidak ada error handling yang detail
**Perbaikan:**
- Menambahkan try-catch block
- Logging error untuk setiap insert operation
- Rollback otomatis jika terjadi error

### 3. **Timestamp Fields**
**Masalah:** Beberapa tabel mungkin tidak memiliki field `created_at` dan `updated_at`
**Perbaikan:**
- Menghapus field timestamp yang mungkin tidak ada
- Mengandalkan default timestamp dari database

## Cara Debug

### 1. **Cek Error Log**
```bash
# Cek file log CodeIgniter
tail -f application/logs/log-YYYY-MM-DD.php
```

### 2. **Cek Database Error**
```sql
-- Cek struktur tabel
DESCRIBE tbl_pemesanan;
DESCRIBE tbl_pemesanan_detail;
DESCRIBE tbl_pembayaran;
DESCRIBE tbl_pembayaran_detail;
DESCRIBE tbl_piutang;
```

### 3. **Test Query Manual**
```sql
-- Test insert manual untuk setiap tabel
INSERT INTO tbl_pemesanan (nospk, konsumen_id, deskprint_id, id_jenis_transaksi, id_pengiriman, perusahaan_id, status_pembayaran) 
VALUES ('TEST-001', 1, 0, 2, 0, 1, 'paid');
```

## Kemungkinan Penyebab Error

### 1. **Foreign Key Constraint**
- `konsumen_id` tidak ada di `tbl_konsumen`
- `produk_id` tidak ada di `tbl_product`
- `perusahaan_id` tidak ada di `tbl_perusahaan`

### 2. **Field Not Found**
- Field `created_at` atau `updated_at` tidak ada
- Field `status_pembayaran` tidak ada
- Field `status_lunas` tidak ada

### 3. **Data Type Mismatch**
- `total_tagihan` bukan DECIMAL
- `qty` bukan INT
- `status` bukan ENUM

### 4. **Session Data**
- `perusahaan_id` tidak ada di session
- `kasir_id` tidak ada di session

## Langkah Troubleshooting

### 1. **Cek Session Data**
```php
// Tambahkan di controller sebelum simpan
var_dump($this->session->userdata());
```

### 2. **Cek Input Data**
```php
// Tambahkan di model
log_message('debug', 'Input data: ' . json_encode($post));
```

### 3. **Cek Database Connection**
```php
// Test koneksi database
if ($this->db->simple_query('SELECT 1')) {
    echo "Database connection OK";
} else {
    echo "Database connection failed";
}
```

### 4. **Cek Table Structure**
```sql
-- Cek struktur semua tabel yang terkait
SHOW CREATE TABLE tbl_pemesanan;
SHOW CREATE TABLE tbl_pemesanan_detail;
SHOW CREATE TABLE tbl_pembayaran;
SHOW CREATE TABLE tbl_pembayaran_detail;
SHOW CREATE TABLE tbl_piutang;
```

## Perbaikan yang Telah Dilakukan

### 1. **Error Handling**
```php
try {
    // Database operations
} catch (Exception $e) {
    $this->db->trans_rollback();
    log_message('error', 'Exception: ' . $e->getMessage());
    throw $e;
}
```

### 2. **Field Names Correction**
```php
// tbl_pemesanan_detail
'produk_id' => $post['produk_id'] // bukan 'product_id'

// tbl_product
'product_id' => $produk_id // untuk join
'product_nama' => $product_name // untuk display
```

### 3. **Remove Timestamp Fields**
```php
// Menghapus created_at dan updated_at yang mungkin tidak ada
$pemesanan = [
    'nospk' => $nospk,
    'konsumen_id' => $post['konsumen_id'],
    // ... other fields
    // 'created_at' => date('Y-m-d H:i:s'), // removed
    // 'updated_at' => date('Y-m-d H:i:s')  // removed
];
```

### 4. **Debug Logging**
```php
// Log setiap insert operation
$result = $this->db->insert('table_name', $data);
if (!$result) {
    $error = $this->db->error();
    log_message('error', 'Failed to insert: ' . json_encode($error));
    throw new Exception('Insert failed: ' . $error['message']);
}
```

## Testing Steps

### 1. **Test Basic Insert**
```php
// Test insert sederhana
$test_data = [
    'nospk' => 'TEST-001',
    'konsumen_id' => 1,
    'deskprint_id' => 0,
    'id_jenis_transaksi' => 2,
    'id_pengiriman' => 0,
    'perusahaan_id' => 1,
    'status_pembayaran' => 'paid'
];

$result = $this->db->insert('tbl_pemesanan', $test_data);
var_dump($result);
```

### 2. **Test Transaction**
```php
$this->db->trans_start();
// ... insert operations
$this->db->trans_complete();

if ($this->db->trans_status()) {
    echo "Transaction successful";
} else {
    echo "Transaction failed";
    $error = $this->db->error();
    var_dump($error);
}
```

### 3. **Test Data Validation**
```php
// Validasi data sebelum insert
if (empty($post['konsumen_id'])) {
    throw new Exception('Konsumen ID is required');
}

if (empty($post['produk_id'])) {
    throw new Exception('Produk ID is required');
}

// Cek apakah data exists
$konsumen = $this->db->get_where('tbl_konsumen', ['konsumen_id' => $post['konsumen_id']])->row();
if (!$konsumen) {
    throw new Exception('Konsumen not found');
}

$produk = $this->db->get_where('tbl_product', ['product_id' => $post['produk_id']])->row();
if (!$produk) {
    throw new Exception('Produk not found');
}
```

## Next Steps

1. **Cek error log** untuk melihat error yang spesifik
2. **Test dengan data minimal** untuk mengisolasi masalah
3. **Cek struktur database** untuk memastikan field names benar
4. **Validasi session data** untuk memastikan data tersedia
5. **Test step by step** setiap insert operation

## Support

Jika masih mengalami masalah, silakan:
1. Cek error log di `application/logs/`
2. Cek struktur database dengan `DESCRIBE table_name`
3. Test dengan data minimal
4. Hubungi tim development untuk bantuan lebih lanjut
