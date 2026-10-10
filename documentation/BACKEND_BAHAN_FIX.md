# Backend Bahan Input Fix

## Summary
Dokumen ini menjelaskan perbaikan backend untuk masalah input bahan baku yang tidak bekerja karena ketidakcocokan field names antara frontend dan database.

## Issues Identified

### 1. Field Names Mismatch
**Problem**: Field names di backend tidak sesuai dengan struktur database.

**Symptoms**:
- Data tidak tersimpan ke database
- Error pada backend
- Response error dari server

**Root Cause**:
- Backend menggunakan `bahanbaku_nama` sedangkan database menggunakan `bahan_nama`
- Backend menggunakan `satuan_gudang` sedangkan frontend mengirim `satuan_id`
- Field `bahanbaku_hargabeli` tidak ada di database

## Database Structure Analysis

### tbl_bahan Structure
```sql
DESCRIBE tbl_bahan;
```

**Actual Fields**:
- `bahan_id` (int, primary key, auto_increment)
- `barcode` (varchar(200))
- `bahan_nama` (varchar(200), NOT NULL)
- `satuan_id` (int(5), NOT NULL)
- `bahan_keterangan` (text, NOT NULL)
- `jenis_barang` (enum)
- `updated_at` (timestamp)
- `keterangan` (text)
- `product_jasa` (enum('0','1'))
- `active` (enum('0','1'))

**Missing Fields**:
- `bahanbaku_nama` (tidak ada, seharusnya `bahan_nama`)
- `bahanbaku_hargabeli` (tidak ada)
- `satuan_gudang` (tidak ada, seharusnya `satuan_id`)

## Fixes Applied

### 1. Field Names Correction
**Before**:
```php
$bahanbaku_nama = trim($this->input->post('bahanbaku_nama'));
$satuan_gudang = $this->input->post('satuan_gudang');
$bahanbaku_hargabeli = $this->input->post('bahanbaku_hargabeli');

$data_bahan = array(
    'bahanbaku_nama' => $bahanbaku_nama,
    'satuan_gudang' => $satuan_gudang,
    'bahanbaku_hargabeli' => $bahanbaku_hargabeli,
    'jenis_barang' => $jenis_barang
);
```

**After**:
```php
$bahanbaku_nama = trim($this->input->post('bahanbaku_nama'));
$satuan_id = $this->input->post('satuan_id');

$data_bahan = array(
    'bahan_nama' => $bahanbaku_nama,
    'satuan_id' => $satuan_id,
    'jenis_barang' => $jenis_barang,
    'bahan_keterangan' => 'Bahan baku: ' . $bahanbaku_nama,
    'active' => '1'
);
```

### 2. Validation Fix
**Before**:
```php
if(empty($bahanbaku_nama) || empty($satuan_gudang) || empty($jenis_barang)) {
    $response = array(
        'status' => 'error',
        'pesan' => 'Nama bahan, satuan gudang, dan jenis barang harus diisi!'
    );
}
```

**After**:
```php
if(empty($bahanbaku_nama) || empty($satuan_id) || empty($jenis_barang)) {
    $response = array(
        'status' => 'error',
        'pesan' => 'Nama bahan, satuan gudang, dan jenis barang harus diisi!',
        'debug_info' => [
            'bahanbaku_nama' => $bahanbaku_nama,
            'satuan_id' => $satuan_id,
            'jenis_barang' => $jenis_barang
        ]
    );
}
```

### 3. Debug Logging Added
```php
// Debug: Log received data
log_message('debug', 'actionAddBahanBaku - Received data: ' . json_encode([
    'bahanbaku_nama' => $bahanbaku_nama,
    'barcode' => $barcode,
    'satuan_id' => $satuan_id,
    'jenis_barang' => $jenis_barang
]));
```

### 4. Error Handling Enhancement
**Before**:
```php
} catch (Exception $e) {
    $response = array(
        'status' => 'error',
        'pesan' => 'Gagal menambahkan bahan baku: ' . $e->getMessage()
    );
}
```

**After**:
```php
} catch (Exception $e) {
    $response = array(
        'status' => 'error',
        'pesan' => 'Gagal menambahkan bahan baku: ' . $e->getMessage(),
        'debug_info' => [
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine()
        ]
    );
}
```

### 5. Barcode Generation Fix
**Before**:
```php
// Generate barcode image
$barcodeDir = FCPATH . 'assets/uploads/barcode/';
if (!is_dir($barcodeDir)) {
    mkdir($barcodeDir, 0777, true);
}
$barcodeFile = $barcodeDir . $barcode . '.png';

// Gunakan Picqer Barcode Generator
if (!class_exists('\Picqer\Barcode\BarcodeGeneratorPNG')) {
    require_once(APPPATH . '../vendor/autoload.php');
}
$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
$barcodeImage = $generator->getBarcode($barcode, $generator::TYPE_CODE_128);
write_file($barcodeFile, $barcodeImage);
```

**After**:
```php
// Generate barcode image jika barcode ada
if (!empty($barcode)) {
    $barcodeDir = FCPATH . 'assets/uploads/barcode/';
    if (!is_dir($barcodeDir)) {
        mkdir($barcodeDir, 0777, true);
    }
    $barcodeFile = $barcodeDir . $barcode . '.png';

    // Gunakan Picqer Barcode Generator jika tersedia
    if (class_exists('\Picqer\Barcode\BarcodeGeneratorPNG')) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodeImage = $generator->getBarcode($barcode, $generator::TYPE_CODE_128);
        write_file($barcodeFile, $barcodeImage);
    }
}
```

### 6. Stok Gudang Fix
**Before**:
```php
$data_stok = array(
    'bahan_id' => $bahan_id,
    'gudang_id' => $gid,
    'stok_awal' => 0,
    'stok_tambah' => 0,
    'stok_kurang' => 0,
    'stok_min' => 0,
    'gudang_id' => $gid, // Duplicate field
);
```

**After**:
```php
$data_stok = array(
    'bahan_id' => $bahan_id,
    'gudang_id' => $gid,
    'stok_awal' => 0,
    'stok_tambah' => 0,
    'stok_kurang' => 0,
    'stok_min' => 0
);
```

## Data Flow Correction

### 1. Frontend to Backend
```javascript
// Frontend sends
{
    'bahanbaku_nama': 'Kertas A4',
    'barcode': '123456789',
    'satuan_id': '1',
    'jenis_barang': 'bahan_baku'
}
```

### 2. Backend Processing
```php
// Backend receives and processes
$bahanbaku_nama = trim($this->input->post('bahanbaku_nama'));
$barcode = trim($this->input->post('barcode'));
$satuan_id = $this->input->post('satuan_id');
$jenis_barang = $this->input->post('jenis_barang');

// Backend saves to database
$data_bahan = array(
    'bahan_nama' => $bahanbaku_nama,        // Maps to database field
    'barcode' => $barcode,
    'satuan_id' => $satuan_id,              // Maps to database field
    'jenis_barang' => $jenis_barang,
    'bahan_keterangan' => 'Bahan baku: ' . $bahanbaku_nama,
    'active' => '1'
);
```

### 3. Database Storage
```sql
INSERT INTO tbl_bahan (
    bahan_nama, 
    barcode, 
    satuan_id, 
    jenis_barang, 
    bahan_keterangan, 
    active
) VALUES (
    'Kertas A4',
    '123456789',
    1,
    'bahan_baku',
    'Bahan baku: Kertas A4',
    '1'
);
```

## Testing Steps

### 1. Backend Validation Test
1. Submit form dengan data valid
2. **Expected**: Data tersimpan ke database
3. **Expected**: Response success dengan debug info

### 2. Error Handling Test
1. Submit form dengan data invalid
2. **Expected**: Error response dengan debug info
3. **Expected**: Log error di server

### 3. Database Integration Test
1. Submit form dengan data valid
2. **Expected**: Data tersimpan di tbl_bahan
3. **Expected**: Stok gudang terbuat di tbl_stok_gudang

### 4. Barcode Generation Test
1. Submit form dengan barcode
2. **Expected**: Barcode image ter-generate
3. **Expected**: File tersimpan di assets/uploads/barcode/

## Debug Information

### 1. Server Logs
```bash
# Check CodeIgniter logs
tail -f application/logs/log-YYYY-MM-DD.php
```

### 2. Database Logs
```sql
-- Check recent inserts
SELECT * FROM tbl_bahan ORDER BY bahan_id DESC LIMIT 5;

-- Check stok gudang
SELECT * FROM tbl_stok_gudang WHERE bahan_id = (SELECT MAX(bahan_id) FROM tbl_bahan);
```

### 3. Response Debug
```javascript
// Frontend can access debug info
.then(data => {
    if (data.debug_info) {
        console.log('Debug info:', data.debug_info);
    }
});
```

## Prevention Measures

### 1. Field Names Documentation
- Dokumentasikan field names yang diharapkan
- Gunakan consistent naming convention
- Validasi field names di backend

### 2. Database Schema Validation
- Validasi struktur database sebelum insert
- Gunakan `list_fields()` untuk cek field yang ada
- Filter data sesuai field yang tersedia

### 3. Error Handling
- Implement proper error handling
- Log errors untuk debugging
- Return debug info untuk troubleshooting

## Future Improvements

### 1. Database Schema
- Standardize field names across tables
- Add proper foreign key constraints
- Add indexes for performance

### 2. Backend Validation
- Add more comprehensive validation
- Implement custom validation rules
- Add validation feedback

### 3. Error Recovery
- Add retry mechanism
- Implement rollback functionality
- Add data recovery options

## Conclusion

Masalah utama adalah ketidakcocokan field names antara frontend, backend, dan database. Setelah memperbaiki field names dan menambahkan debug logging, sistem input bahan seharusnya berfungsi dengan baik.

**Key Fixes:**
- ✅ **Field Names**: Sesuai dengan struktur database
- ✅ **Data Validation**: Proper validation dengan debug info
- ✅ **Error Handling**: Enhanced error handling dan logging
- ✅ **Database Integration**: Proper data mapping dan storage
- ✅ **Debug Support**: Comprehensive debug information

**Next Steps:**
1. Test semua backend functionality
2. Monitor server logs
3. Implement additional validation
4. Add performance optimizations

Backend sekarang seharusnya berfungsi dengan baik dan memberikan response yang informatif! 🎉
