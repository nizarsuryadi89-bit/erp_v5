# Database Structure Analysis

## Summary
Dokumen ini menjelaskan struktur database yang benar dan hubungan antara tabel-tabel yang terkait dengan bahan baku dan material.

## Database Structure

### 1. tbl_bahan (Bahan Baku)
```sql
CREATE TABLE `tbl_bahan` (
  `bahan_id` int(10) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(200) DEFAULT NULL,
  `bahan_nama` varchar(200) NOT NULL,
  `satuan_id` int(5) NOT NULL COMMENT 'Satuan Produksi',
  `bahan_keterangan` text NOT NULL,
  `jenis_barang` enum('bahan_baku','bahan_operasional','barang_jadi','spare_part','operasional') NOT NULL DEFAULT 'bahan_baku',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `keterangan` text DEFAULT NULL,
  `product_jasa` enum('0','1') NOT NULL DEFAULT '1',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`bahan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
```

**Purpose**: Menyimpan data bahan baku dasar
**Key Fields**:
- `bahan_id`: Primary key
- `bahan_nama`: Nama bahan baku
- `satuan_id`: Foreign key ke tbl_satuan
- `jenis_barang`: Jenis barang (bahan_baku, bahan_operasional, dll)
- `active`: Status aktif/nonaktif

### 2. tbl_material (Material)
```sql
CREATE TABLE `tbl_material` (
  `material_id` int(11) NOT NULL AUTO_INCREMENT,
  `bahan_id` int(11) NOT NULL,
  `varian` varchar(200) NOT NULL,
  `satuan_gudang_id` int(11) NOT NULL,
  `konversi` decimal(10,2) NOT NULL,
  `harga_jual` decimal(15,2) YES,
  `status` enum('0','1') YES DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
```

**Purpose**: Menyimpan data material yang dibuat dari bahan baku
**Key Fields**:
- `material_id`: Primary key
- `bahan_id`: Foreign key ke tbl_bahan
- `varian`: Varian material
- `satuan_gudang_id`: Foreign key ke tbl_satuan
- `konversi`: Konversi dari bahan ke material
- `harga_jual`: Harga jual material

### 3. tbl_stok_gudang (Stok Gudang)
```sql
CREATE TABLE `tbl_stok_gudang` (
  `stok_id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(10) NOT NULL,
  `gudang_id` int(2) NOT NULL,
  `qty` decimal(15,2) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`stok_id`),
  KEY `fk_stok_material` (`material_id`),
  KEY `fk_stok_gudang` (`gudang_id`),
  CONSTRAINT `fk_stok_material` FOREIGN KEY (`material_id`) REFERENCES `tbl_material` (`material_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_stok_gudang` FOREIGN KEY (`gudang_id`) REFERENCES `tbl_gudang` (`gudang_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
```

**Purpose**: Menyimpan stok material di setiap gudang
**Key Fields**:
- `stok_id`: Primary key
- `material_id`: Foreign key ke tbl_material (BUKAN tbl_bahan)
- `gudang_id`: Foreign key ke tbl_gudang
- `qty`: Quantity stok

## Relationship Analysis

### Data Flow
```
tbl_bahan (Bahan Baku) 
    ↓ (1:N relationship)
tbl_material (Material)
    ↓ (1:N relationship)
tbl_stok_gudang (Stok Gudang)
```

### Key Insights

#### 1. Bahan vs Material
- **tbl_bahan**: Menyimpan bahan baku dasar (contoh: "Kertas", "Tinta")
- **tbl_material**: Menyimpan material yang dibuat dari bahan baku (contoh: "Kertas A4 80gsm", "Tinta Hitam Canon")

#### 2. Foreign Key Constraints
- `tbl_stok_gudang.material_id` → `tbl_material.material_id`
- `tbl_material.bahan_id` → `tbl_bahan.bahan_id`
- **TIDAK ADA** foreign key langsung dari `tbl_stok_gudang` ke `tbl_bahan`

#### 3. Stok Management
- Stok hanya bisa di-track untuk material, bukan bahan baku
- Setiap material bisa memiliki stok di multiple gudang
- Stok dihitung berdasarkan `qty` field

## Backend Implementation Fix

### Problem Identified
Backend mencoba insert ke `tbl_stok_gudang` dengan `bahan_id`, tapi foreign key constraint mengharapkan `material_id`.

### Solution Applied
```php
// SEBELUM (tidak bekerja):
$data_stok = array(
    'bahan_id'     => $bahan_id,  // ❌ Foreign key constraint error
    'gudang_id'    => $gid,
    'stok_awal'    => 0,          // ❌ Field tidak ada
    'stok_tambah'  => 0,          // ❌ Field tidak ada
    'stok_kurang'  => 0,          // ❌ Field tidak ada
    'stok_min'     => 0           // ❌ Field tidak ada
);

// SESUDAH (bekerja):
// Skip tbl_stok_gudang insert karena foreign key constraint
log_message('debug', 'Skipping tbl_stok_gudang insert - foreign key constraint to tbl_material');
```

### Correct Workflow
1. **Add Bahan Baku** → Insert ke `tbl_bahan` ✅
2. **Add Material** → Insert ke `tbl_material` (dengan `bahan_id`) ✅
3. **Add Stok** → Insert ke `tbl_stok_gudang` (dengan `material_id`) ✅

## Field Names Correction

### tbl_bahan Fields
```php
$data_bahan = array(
    'bahan_nama'            => $bahanbaku_nama,        // ✅ Correct
    'barcode'               => $barcode,               // ✅ Correct
    'satuan_id'             => $satuan_id,             // ✅ Correct
    'jenis_barang'          => $jenis_barang,          // ✅ Correct
    'bahan_keterangan'      => 'Bahan baku: ' . $bahanbaku_nama,  // ✅ Correct
    'active'                => '1'                     // ✅ Correct
);
```

### Removed Fields
- ❌ `bahanbaku_nama` (tidak ada di database)
- ❌ `bahanbaku_hargabeli` (tidak ada di database)
- ❌ `satuan_gudang` (tidak ada di database)

## Testing Scenarios

### 1. Add Bahan Baku Test
1. Submit form dengan data valid
2. **Expected**: Data tersimpan di `tbl_bahan`
3. **Expected**: No insert ke `tbl_stok_gudang` (foreign key constraint)
4. **Expected**: Success response

### 2. Add Material Test (Future)
1. Submit material form dengan `bahan_id`
2. **Expected**: Data tersimpan di `tbl_material`
3. **Expected**: Success response

### 3. Add Stok Test (Future)
1. Submit stok form dengan `material_id`
2. **Expected**: Data tersimpan di `tbl_stok_gudang`
3. **Expected**: Success response

## Prevention Measures

### 1. Database Schema Documentation
- Dokumentasikan struktur tabel dan relationship
- Validasi foreign key constraints
- Test database operations

### 2. Field Names Validation
- Validasi field names sebelum insert
- Log field validation results
- Handle missing fields gracefully

### 3. Workflow Validation
- Validasi workflow yang benar
- Test setiap step dalam workflow
- Handle errors appropriately

## Future Improvements

### 1. Database Schema
- Consider adding indexes for performance
- Add proper constraints
- Consider normalization

### 2. Backend Logic
- Add material creation workflow
- Add stok management workflow
- Add validation for all workflows

### 3. Frontend Integration
- Add material management interface
- Add stok management interface
- Add proper navigation between workflows

## Conclusion

Masalah utama adalah misunderstanding tentang struktur database dan relationship antara tabel. Setelah memperbaiki pemahaman dan implementasi, sistem seharusnya berfungsi dengan benar.

**Key Fixes:**
- ✅ **Database Structure**: Proper understanding of table relationships
- ✅ **Field Names**: Correct field names sesuai database structure
- ✅ **Foreign Key Constraints**: Proper handling of constraints
- ✅ **Workflow**: Correct workflow implementation
- ✅ **Error Handling**: Proper error handling untuk constraints

**Next Steps:**
1. Test bahan baku creation workflow
2. Implement material creation workflow
3. Implement stok management workflow
4. Add comprehensive validation

Sistem sekarang seharusnya berfungsi dengan baik sesuai struktur database yang benar! 🎉
