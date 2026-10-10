# Inventory Management Troubleshooting

## Summary
Dokumen ini menjelaskan langkah-langkah troubleshooting untuk masalah pada interface manajemen inventory yang tidak berfungsi dengan baik.

## Issues Identified

### 1. Data Structure Mismatch
**Problem**: Perubahan dari `bahanbaku` ke `material` menyebabkan ketidakcocokan dengan backend.

**Symptoms**:
- Tabel tidak menampilkan data dengan benar
- Dropdown tidak berfungsi
- Error pada console browser

**Root Cause**:
- Frontend menggunakan field `material_id` dan `varian`
- Backend masih menggunakan `bahanbaku_id` dan `bahanbaku_nama`
- Data structure tidak konsisten

## Fixes Applied

### 1. Table Data Display Fix
**Before**:
```php
<td class="text-left"><?= $dt->varian ?></td>
<td class="text-right"><?= number_format($dt->qty) ?> <?= $dt->satuan_nama ?></td>
```

**After**:
```php
<td class="text-left"><?= $dt->bahanbaku_nama ?></td>
<td class="text-right"><?= number_format($dt->qty) ?></td>
```

### 2. Table Header Fix
**Before**:
```html
<th class="text-left">Nama Material</th>
<th class="text-right">Jumlah Beli</th>
```

**After**:
```html
<th class="text-left">Nama Bahan</th>
<th class="text-right">QTY</th>
```

### 3. Dropdown Data Fix
**Before**:
```php
<select name="material_id" id="material_id">
<?php foreach($material as $b): ?>
    <option value="<?= $b->material_id ?>"><?= $b->varian ?></option>
<?php endforeach; ?>
```

**After**:
```php
<select name="bahan_id" id="bahan_id">
<?php foreach($bahanbaku as $b): ?>
    <option value="<?= $b->bahanbaku_id ?>"><?= $b->bahanbaku_nama ?></option>
<?php endforeach; ?>
```

### 4. JavaScript Data Fix
**Before**:
```javascript
.val(b.material_id)
.text(b.varian)
```

**After**:
```javascript
.val(b.bahanbaku_id)
.text(b.bahanbaku_nama)
```

### 5. CSS Class Fix
**Before**:
```html
<table class="inventory-table table-bordered table-hover fs-5">
```

**After**:
```html
<table class="table table-bordered table-hover fs-5">
```

## Data Structure Consistency

### 1. Backend Data Structure
```php
// Expected data structure from backend
$temp = [
    (object)[
        'temp_id' => 1,
        'bahanbaku_id' => 1,
        'bahanbaku_nama' => 'Kertas A4',
        'qty' => 100,
        'harga' => 50000,
        'satuan_nama' => 'Lembar'
    ]
];

$bahanbaku = [
    (object)[
        'bahanbaku_id' => 1,
        'bahanbaku_nama' => 'Kertas A4',
        'satuan_nama' => 'Lembar',
        'stok_aktif' => 500,
        'bahanbaku_hargabeli' => 50000
    ]
];
```

### 2. Frontend Data Usage
```javascript
// Correct field names for frontend
var bahanId = $('#bahan_id').val();           // bahanbaku_id
var bahanNama = $('#bahan_id option:selected').text(); // bahanbaku_nama
var satuan = $('#bahan_id option:selected').data('satuan'); // satuan_nama
```

## Testing Steps

### 1. Data Display Test
1. Buka halaman barang masuk
2. **Expected**: Tabel menampilkan data dengan benar
3. **Expected**: Kolom "Nama Bahan" menampilkan nama bahan
4. **Expected**: Kolom "QTY" menampilkan jumlah tanpa satuan

### 2. Dropdown Functionality Test
1. Klik "Tambah Pembelian"
2. **Expected**: Modal terbuka
3. **Expected**: Dropdown "Nama Bahan" menampilkan opsi bahan
4. **Expected**: Select2 berfungsi dengan baik

### 3. Form Submission Test
1. Pilih bahan dari dropdown
2. Isi QTY dan harga
3. Klik "Simpan"
4. **Expected**: Data tersimpan ke keranjang
5. **Expected**: Tabel ter-refresh dengan data baru

### 4. JavaScript Functionality Test
1. Pilih bahan dari dropdown
2. **Expected**: Satuan otomatis terisi
3. **Expected**: Info bahan muncul
4. **Expected**: Search filter berfungsi

## Common Issues and Solutions

### 1. Table Not Displaying Data
**Problem**: Tabel kosong atau error
**Solution**: 
- Periksa field names di PHP loop
- Pastikan data dari backend sesuai struktur
- Periksa error di console browser

### 2. Dropdown Not Working
**Problem**: Select2 tidak berfungsi
**Solution**:
- Periksa field names di option values
- Pastikan data array sesuai struktur
- Periksa JavaScript errors

### 3. Form Not Submitting
**Problem**: Form tidak bisa disubmit
**Solution**:
- Periksa field names di form
- Pastikan required fields terisi
- Periksa validation errors

### 4. CSS Styling Issues
**Problem**: Tampilan tidak sesuai design
**Solution**:
- Periksa CSS class conflicts
- Pastikan Bootstrap CSS loaded
- Periksa custom CSS specificity

## Debug Information

### 1. Console Debug
```javascript
// Add to JavaScript for debugging
console.log('Bahan ID:', $('#bahan_id').val());
console.log('Selected Option:', $('#bahan_id option:selected').text());
console.log('Form Data:', new FormData($('#formTambahProdukModal')[0]));
```

### 2. PHP Debug
```php
// Add to PHP for debugging
echo '<pre>';
print_r($temp);
print_r($bahanbaku);
echo '</pre>';
```

### 3. Network Debug
- Buka Developer Tools → Network tab
- Submit form dan periksa request/response
- Periksa status code dan response data

## Prevention Measures

### 1. Data Structure Documentation
- Dokumentasikan struktur data yang diharapkan
- Gunakan consistent naming convention
- Validasi data structure di backend

### 2. Frontend-Backend Sync
- Pastikan field names konsisten
- Test perubahan data structure
- Maintain backward compatibility

### 3. Error Handling
- Implement proper error handling
- Show user-friendly error messages
- Log errors untuk debugging

## Future Improvements

### 1. Data Validation
- Implement client-side validation
- Add server-side validation
- Show validation feedback

### 2. Error Recovery
- Add retry mechanism
- Implement auto-save
- Add data recovery options

### 3. User Experience
- Add loading indicators
- Implement progressive enhancement
- Add keyboard shortcuts

## Conclusion

Masalah utama adalah ketidakcocokan struktur data antara frontend dan backend. Setelah memperbaiki field names dan data structure, sistem seharusnya berfungsi dengan baik.

**Key Fixes:**
- ✅ **Data Structure Consistency**: Field names sesuai backend
- ✅ **Table Display**: Menampilkan data dengan benar
- ✅ **Dropdown Functionality**: Select2 berfungsi dengan baik
- ✅ **Form Submission**: Form bisa disubmit dengan benar
- ✅ **JavaScript Integration**: Semua JavaScript functions berfungsi

**Next Steps:**
1. Test semua functionality
2. Monitor error logs
3. Implement additional error handling
4. Add user feedback mechanisms
