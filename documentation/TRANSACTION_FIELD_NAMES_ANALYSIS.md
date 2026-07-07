# Transaction Field Names Analysis

## Summary
Dokumen ini menjelaskan inkonsistensi field names di tabel transaksi dan bagaimana menanganinya dalam fungsi delete bahan baku.

## Database Structure Analysis

### Transaction Tables Field Names

#### 1. tbl_pembelian_detail
```sql
CREATE TABLE `tbl_pembelian_detail` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `pembelian_id` int(11) YES,
  `material_id` int(11) YES,  -- Foreign key ke tbl_material
  `qty` decimal(10,2) YES,
  `harga_beli` decimal(15,2) YES,
  `subtotal` decimal(15,2) YES,
  PRIMARY KEY (`detail_id`)
)
```

**Field**: `material_id` (foreign key ke `tbl_material`)
**Relationship**: `tbl_pembelian_detail.material_id` → `tbl_material.material_id`

#### 2. tbl_pengambilan_detail
```sql
CREATE TABLE `tbl_pengambilan_detail` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL,  -- Foreign key ke tbl_material
  `pengambilan_qty` int(11) NOT NULL,
  `pengambilan_id` int(11) NOT NULL,
  `kondisi` enum('Baik','Rusak') NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `gudang_id` int(1) NOT NULL
)
```

**Field**: `material_id` (foreign key ke `tbl_material`)
**Relationship**: `tbl_pengambilan_detail.material_id` → `tbl_material.material_id`

#### 3. tbl_return_pembelian_detail
```sql
CREATE TABLE `tbl_return_pembelian_detail` (
  `return_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,  -- Foreign key ke tbl_material
  `qty_return` decimal(10,2) NOT NULL,
  `harga_return` decimal(15,2) NOT NULL,
  `alasan_return` text NOT NULL,
  PRIMARY KEY (`return_detail_id`)
)
```

**Field**: `material_id` (foreign key ke `tbl_material`)
**Relationship**: `tbl_return_pembelian_detail.material_id` → `tbl_material.material_id`

## Field Names Inconsistency

### Problem Identified
Ada inkonsistensi dalam penamaan field foreign key di tabel transaksi:

1. **tbl_pembelian_detail**: Menggunakan `material_id` (referensi ke `tbl_material`)
2. **tbl_pengambilan_detail**: Menggunakan `material_id` (referensi ke `tbl_material`)
3. **tbl_return_pembelian_detail**: Menggunakan `material_id` (referensi ke `tbl_material`)

### Data Flow Analysis
```
tbl_bahan (Bahan Baku)
    ↓ (1:N relationship)
tbl_material (Material)
    ↓ (1:N relationship)
tbl_pembelian_detail (Pembelian) -- menggunakan material_id

tbl_bahan (Bahan Baku)
    ↓ (1:N relationship)
tbl_material (Material)
    ↓ (1:N relationship)
tbl_pengambilan_detail (Pengambilan) -- menggunakan material_id

tbl_bahan (Bahan Baku)
    ↓ (1:N relationship)
tbl_material (Material)
    ↓ (1:N relationship)
tbl_return_pembelian_detail (Return) -- menggunakan material_id
```

## Backend Implementation Fix

### Problem
Backend mencoba menggunakan field names yang konsisten (`bahan_id`) untuk semua tabel transaksi, padahal struktur database menggunakan field names yang berbeda.

### Solution Applied

#### 1. Pembelian Detail Check
```php
// tbl_pembelian_detail menggunakan material_id, jadi cek material terkait dulu
if($this->db->table_exists('tbl_pembelian_detail')) {
    // Cek apakah ada material dari bahan ini yang digunakan dalam pembelian
    $material_ids = $this->db->select('material_id')
        ->where('bahan_id', $id)
        ->get('tbl_material')
        ->result_array();
    
    if (!empty($material_ids)) {
        $material_id_list = array_column($material_ids, 'material_id');
        $pembelian_count = $this->db->where_in('material_id', $material_id_list)
            ->count_all_results('tbl_pembelian_detail');
    }
}
```

#### 2. Pengambilan Detail Check
```php
// tbl_pengambilan_detail menggunakan material_id, jadi cek material terkait dulu
if($this->db->table_exists('tbl_pengambilan_detail')) {
    // Cek apakah ada material dari bahan ini yang digunakan dalam pengambilan
    $material_ids = $this->db->select('material_id')
        ->where('bahan_id', $id)
        ->get('tbl_material')
        ->result_array();
    
    if (!empty($material_ids)) {
        $material_id_list = array_column($material_ids, 'material_id');
        $pengambilan_count = $this->db->where_in('material_id', $material_id_list)
            ->count_all_results('tbl_pengambilan_detail');
    }
}
```

#### 3. Return Detail Check
```php
// tbl_return_pembelian_detail menggunakan material_id, jadi cek material terkait dulu
if($this->db->table_exists('tbl_return_pembelian_detail')) {
    // Cek apakah ada material dari bahan ini yang digunakan dalam return
    $material_ids = $this->db->select('material_id')
        ->where('bahan_id', $id)
        ->get('tbl_material')
        ->result_array();
    
    if (!empty($material_ids)) {
        $material_id_list = array_column($material_ids, 'material_id');
        $return_count = $this->db->where_in('material_id', $material_id_list)
            ->count_all_results('tbl_return_pembelian_detail');
    }
}
```

#### 4. Enhanced Error Reporting
```php
log_message('debug', 'actionDeleteBahanBaku - Transaction counts: pembelian=' . $pembelian_count . ', pengambilan=' . $pengambilan_count . ', return=' . $return_count);

if($pembelian_count > 0 || $pengambilan_count > 0 || $return_count > 0) {
    $error_details = [];
    if($pembelian_count > 0) $error_details[] = 'Pembelian: ' . $pembelian_count . ' transaksi';
    if($pengambilan_count > 0) $error_details[] = 'Pengambilan: ' . $pengambilan_count . ' transaksi';
    if($return_count > 0) $error_details[] = 'Return: ' . $return_count . ' transaksi';
    
    throw new Exception('Bahan baku tidak dapat dihapus karena masih memiliki transaksi terkait: ' . implode(', ', $error_details));
}
```

## Database Schema Recommendations

### 1. Standardize Field Names
Untuk konsistensi, sebaiknya semua tabel transaksi menggunakan field names yang sama:

#### Option A: Use material_id everywhere
```sql
-- Update tbl_pengambilan_detail
ALTER TABLE tbl_pengambilan_detail 
CHANGE COLUMN bahanbaku_id material_id int(11) NOT NULL;

-- Update tbl_return_pembelian_detail
ALTER TABLE tbl_return_pembelian_detail 
CHANGE COLUMN bahanbaku_id material_id int(11) NOT NULL;
```

#### Option B: Use bahan_id everywhere
```sql
-- Update tbl_pembelian_detail
ALTER TABLE tbl_pembelian_detail 
CHANGE COLUMN material_id bahan_id int(11) NOT NULL;
```

### 2. Update Foreign Key Constraints
Setelah standardisasi field names, update foreign key constraints:

```sql
-- Add foreign key constraints
ALTER TABLE tbl_pengambilan_detail 
ADD CONSTRAINT fk_pengambilan_bahan 
FOREIGN KEY (bahan_id) REFERENCES tbl_bahan(bahan_id);

ALTER TABLE tbl_return_pembelian_detail 
ADD CONSTRAINT fk_return_bahan 
FOREIGN KEY (bahan_id) REFERENCES tbl_bahan(bahan_id);
```

## Testing Scenarios

### 1. Bahan with Material and Pembelian
1. Create bahan baku
2. Create material from bahan baku
3. Create pembelian using material
4. Try to delete bahan baku
5. **Expected**: Error about pembelian transactions

### 2. Bahan with Pengambilan
1. Create bahan baku
2. Create pengambilan for bahan baku
3. Try to delete bahan baku
4. **Expected**: Error about pengambilan transactions

### 3. Bahan with Return
1. Create bahan baku
2. Create return for bahan baku
3. Try to delete bahan baku
4. **Expected**: Error about return transactions

### 4. Bahan without Transactions
1. Create bahan baku
2. Try to delete bahan baku
3. **Expected**: Success delete

## Debug Information

### 1. Check Transaction Counts
```sql
-- Check pembelian transactions
SELECT COUNT(*) as pembelian_count 
FROM tbl_pembelian_detail pd 
JOIN tbl_material m ON pd.material_id = m.material_id 
WHERE m.bahan_id = [bahan_id];

-- Check pengambilan transactions
SELECT COUNT(*) as pengambilan_count 
FROM tbl_pengambilan_detail pd 
JOIN tbl_material m ON pd.material_id = m.material_id 
WHERE m.bahan_id = [bahan_id];

-- Check return transactions
SELECT COUNT(*) as return_count 
FROM tbl_return_pembelian_detail rd 
JOIN tbl_material m ON rd.material_id = m.material_id 
WHERE m.bahan_id = [bahan_id];
```

### 2. Check Related Materials
```sql
-- Check materials for bahan
SELECT material_id, varian 
FROM tbl_material 
WHERE bahan_id = [bahan_id];
```

### 3. Server Logs
```bash
# Check delete operation logs
tail -f application/logs/log-YYYY-MM-DD.php | grep actionDeleteBahanBaku
```

## Prevention Measures

### 1. Database Schema Documentation
- Dokumentasikan field names dan relationships
- Maintain consistent naming conventions
- Update documentation when schema changes

### 2. Validation Functions
- Create helper functions untuk check dependencies
- Implement consistent validation logic
- Add comprehensive error reporting

### 3. Migration Scripts
- Create migration scripts untuk standardisasi
- Test migrations thoroughly
- Backup data before migrations

## Future Improvements

### 1. Schema Standardization
- Standardize field names across all transaction tables
- Update foreign key constraints
- Add proper indexes

### 2. Data Integrity
- Implement proper cascade delete options
- Add soft delete functionality
- Maintain audit trails

### 3. Performance Optimization
- Add proper indexes for foreign keys
- Optimize query performance
- Implement caching where appropriate

## Conclusion

Masalah utama adalah inkonsistensi field names di tabel transaksi. Setelah memperbaiki implementasi untuk menangani inkonsistensi ini, fungsi delete bahan baku seharusnya berfungsi dengan benar.

**Key Fixes:**
- ✅ **Field Names Handling**: Proper handling of inconsistent field names
- ✅ **Transaction Validation**: Correct validation for all transaction types
- ✅ **Error Reporting**: Detailed error messages with transaction counts
- ✅ **Debug Support**: Comprehensive logging for troubleshooting

**Next Steps:**
1. Test semua transaction scenarios
2. Consider schema standardization
3. Implement proper foreign key constraints
4. Add performance optimizations

Sistem sekarang dapat menangani inkonsistensi field names dengan baik! 🎉
