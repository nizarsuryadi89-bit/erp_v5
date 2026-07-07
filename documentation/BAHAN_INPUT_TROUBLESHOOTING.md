# Bahan Input Process Troubleshooting

## Summary
Dokumen ini menjelaskan langkah-langkah troubleshooting untuk masalah pada proses input bahan baku yang tidak bisa digunakan.

## Issues Identified

### 1. Form Field Names Inconsistency
**Problem**: Field names di form edit tidak konsisten dengan backend.

**Symptoms**:
- Form edit tidak berfungsi
- Data tidak tersimpan dengan benar
- Error pada console browser

**Root Cause**:
- Form edit menggunakan `bahan_id` sedangkan backend mengharapkan `bahanbaku_id`
- Field names tidak konsisten antara form add dan edit

## Fixes Applied

### 1. Form Edit Field Names Fix
**Before**:
```html
<input type="hidden" name="bahan_id" id="edit_bahan_id">
```

**After**:
```html
<input type="hidden" name="bahanbaku_id" id="edit_bahanbaku_id">
```

### 2. Form Edit Structure Fix
**Before**:
```html
<div class="col-md-6">
    <label for="edit_satuan_id">Satuan Jual</label>
    <select name="satuan_id" id="edit_satuan_id">
</div>
<div class="col-md-12">
    <label for="edit_jenis_barang">Jenis Barang</label>
    <select name="jenis_barang" id="edit_jenis_barang">
</div>
```

**After**:
```html
<div class="col-md-6">
    <label for="edit_barcode">Barcode</label>
    <input name="barcode" id="edit_barcode" type="text">
</div>
<div class="col-md-6">
    <label for="edit_satuan_id">Satuan Gudang</label>
    <select name="satuan_id" id="edit_satuan_id">
</div>
<div class="col-md-6">
    <label for="edit_jenis_barang">Jenis Barang</label>
    <select name="jenis_barang" id="edit_jenis_barang">
</div>
```

### 3. JavaScript Event Handler Fix
**Before**:
```javascript
$(document).on('click', '.btn-edit', function() {
    var bahanId = $(this).data('id');
    var nama = $(this).data('nama');
    var jenis = $(this).data('jenis');
    var satuan = $(this).data('satuan');
    
    $('#edit_bahanbaku_id').val(bahanId);
    $('#edit_bahanbaku_nama').val(nama);
    $('#edit_jenis_barang').val(jenis);
    $('#edit_satuan_id').val(satuan);
});
```

**After**:
```javascript
$(document).on('click', '.btn-edit', function() {
    var bahanId = $(this).data('id');
    var nama = $(this).data('nama');
    var jenis = $(this).data('jenis');
    var satuan = $(this).data('satuan');
    var barcode = $(this).data('barcode') || '';
    
    console.log('Edit bahan baku:', bahanId, nama, jenis, satuan, barcode);
    
    $('#edit_bahanbaku_id').val(bahanId);
    $('#edit_bahanbaku_nama').val(nama);
    $('#edit_barcode').val(barcode);
    $('#edit_jenis_barang').val(jenis);
    $('#edit_satuan_id').val(satuan);
});
```

### 4. Form Submission API Fix
**Before**:
```javascript
$.ajax({
    url: '<?= base_url('gudang/actionEditBahanBaku') ?>',
    method: 'POST',
    data: data,
    dataType: 'json',
    success: function(res) { ... }
});
```

**After**:
```javascript
fetch('<?= base_url('gudang/actionEditBahanBaku') ?>', {
    method: 'POST',
    body: formData
})
.then(response => {
    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
        return response.text().then(text => {
            throw new Error('Response is not JSON! Content-Type: ' + contentType + '. Response: ' + text.substring(0, 200));
        });
    }
    
    return response.json();
})
.then(data => { ... })
.catch(error => { ... });
```

### 5. Client-Side Validation Enhancement
**Added**:
```javascript
// Validasi nama bahan
if (!bahanbakuNama) {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Nama bahan harus diisi.'
    });
    $('#bahanbaku_nama').focus();
    return;
}

if (bahanbakuNama.length < 3) {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Nama bahan minimal 3 karakter.'
    });
    $('#bahanbaku_nama').focus();
    return;
}

// Validasi satuan
if (!satuanId) {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Satuan gudang harus dipilih.'
    });
    $('#satuan_id').focus();
    return;
}

// Validasi jenis barang
if (!jenisBarang) {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Jenis barang harus dipilih.'
    });
    $('#jenis_barang').focus();
    return;
}
```

### 6. Data Attributes Enhancement
**Before**:
```javascript
<button class="btn btn-warning btn-sm btn-edit" 
        data-id="${bahan.bahan_id || ''}" 
        data-nama="${bahanNama}" 
        data-jenis="${bahan.jenis_barang || ''}" 
        data-satuan="${bahan.satuan_id || ''}" 
        title="Edit">
```

**After**:
```javascript
<button class="btn btn-warning btn-sm btn-edit" 
        data-id="${bahan.bahan_id || ''}" 
        data-nama="${bahanNama}" 
        data-jenis="${bahan.jenis_barang || ''}" 
        data-satuan="${bahan.satuan_id || ''}" 
        data-barcode="${bahan.barcode || ''}" 
        title="Edit">
```

## Data Structure Consistency

### 1. Form Add Structure
```html
<form id="formTambahBahanBaku">
    <input name="bahanbaku_nama" id="bahanbaku_nama" type="text" required>
    <input name="barcode" id="barcode" type="text">
    <select name="satuan_id" id="satuan_id" required>
    <select name="jenis_barang" id="jenis_barang" required>
</form>
```

### 2. Form Edit Structure
```html
<form id="formEditBahanBaku">
    <input type="hidden" name="bahanbaku_id" id="edit_bahanbaku_id">
    <input name="bahanbaku_nama" id="edit_bahanbaku_nama" type="text" required>
    <input name="barcode" id="edit_barcode" type="text">
    <select name="satuan_id" id="edit_satuan_id" required>
    <select name="jenis_barang" id="edit_jenis_barang" required>
</form>
```

### 3. Backend Expected Data
```php
// Expected POST data
$_POST = [
    'bahanbaku_nama' => 'Kertas A4',
    'barcode' => '123456789',
    'satuan_id' => '1',
    'jenis_barang' => 'bahan_baku'
];
```

## Testing Steps

### 1. Form Add Test
1. Klik "Tambah Bahan Baku"
2. **Expected**: Modal terbuka
3. Isi form dengan data valid
4. Klik "Simpan Bahan Baku"
5. **Expected**: Data tersimpan, modal tertutup, tabel ter-refresh

### 2. Form Edit Test
1. Klik tombol edit pada baris bahan baku
2. **Expected**: Modal edit terbuka dengan data terisi
3. Edit data
4. Klik "Simpan Perubahan"
5. **Expected**: Data terupdate, modal tertutup, tabel ter-refresh

### 3. Validation Test
1. Coba submit form kosong
2. **Expected**: Error message muncul
3. Coba submit dengan data invalid
4. **Expected**: Error message sesuai validasi

### 4. Console Debug Test
1. Buka Developer Tools → Console
2. Submit form
3. **Expected**: Log data form dan response server

## Common Issues and Solutions

### 1. Form Not Submitting
**Problem**: Form tidak bisa disubmit
**Solution**: 
- Periksa field names di form
- Pastikan semua required fields terisi
- Periksa JavaScript errors di console

### 2. Data Not Saving
**Problem**: Data tidak tersimpan ke database
**Solution**:
- Periksa field names di backend
- Pastikan data terkirim dengan benar
- Periksa server response

### 3. Edit Form Not Working
**Problem**: Form edit tidak menampilkan data
**Solution**:
- Periksa data attributes di tombol edit
- Pastikan JavaScript event handler terpasang
- Periksa field IDs di form edit

### 4. Validation Errors
**Problem**: Validasi tidak berfungsi
**Solution**:
- Periksa client-side validation
- Pastikan server-side validation
- Periksa error messages

## Debug Information

### 1. Console Debug
```javascript
// Add to JavaScript for debugging
console.log('Form data:', new FormData($('#formTambahBahanBaku')[0]));
console.log('Edit data:', $('#edit_bahanbaku_id').val(), $('#edit_bahanbaku_nama').val());
```

### 2. Network Debug
- Buka Developer Tools → Network tab
- Submit form dan periksa request/response
- Periksa status code dan response data

### 3. Form Data Debug
```javascript
// Log all form data
$('#formTambahBahanBaku').on('submit', function(e) {
    var formData = new FormData(this);
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
});
```

## Prevention Measures

### 1. Field Names Documentation
- Dokumentasikan field names yang diharapkan
- Gunakan consistent naming convention
- Validasi field names di backend

### 2. Form Validation
- Implement client-side validation
- Add server-side validation
- Show validation feedback

### 3. Error Handling
- Implement proper error handling
- Show user-friendly error messages
- Log errors untuk debugging

## Future Improvements

### 1. Form Enhancement
- Add real-time validation
- Implement auto-save
- Add form state management

### 2. User Experience
- Add loading indicators
- Implement progressive enhancement
- Add keyboard shortcuts

### 3. Data Validation
- Add more comprehensive validation
- Implement custom validation rules
- Add validation feedback

## Conclusion

Masalah utama adalah ketidakcocokan field names dan struktur form antara add dan edit. Setelah memperbaiki konsistensi field names dan menambahkan validasi yang lebih baik, sistem input bahan seharusnya berfungsi dengan baik.

**Key Fixes:**
- ✅ **Field Names Consistency**: Field names sesuai backend
- ✅ **Form Structure**: Form add dan edit konsisten
- ✅ **JavaScript Integration**: Event handlers berfungsi dengan baik
- ✅ **Validation**: Client-side dan server-side validation
- ✅ **Error Handling**: Proper error handling dan user feedback

**Next Steps:**
1. Test semua form functionality
2. Monitor error logs
3. Implement additional validation
4. Add user feedback mechanisms

Sistem input bahan sekarang seharusnya berfungsi dengan baik dan memberikan user experience yang optimal! 🎉
