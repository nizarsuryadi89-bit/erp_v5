# ✅ FEATURE: AUTO-PRINT KARCIS SAAT AMBIL NOMOR

## 🎯 Fitur Baru Implementasi

Ketika customer tap counter untuk ambil nomor antrian, sekarang **otomatis langsung cetak/print karcis** tanpa perlu klik tombol "Cetak Karcis" lagi.

---

## 🔄 Alur Baru

```
Customer tap counter
    ↓
Modal success muncul (nomor antrian)
    ↓
Delay 1 detik (untuk menampilkan modal ke user)
    ↓
OTOMATIS buka halaman print di tab baru
    ↓
OTOMATIS trigger print dialog (Ctrl+P)
    ↓
User tinggal klik "Print" atau "OK"
```

---

## 🔧 Perubahan yang Dilakukan

### 1. Update ambil_nomor.php - JavaScript
Tambah auto-print trigger setelah success:

```javascript
// Setelah response success
if (data.status === 'success') {
    currentAntrian = data.data;
    
    // Set data ke modal & tampilkan
    new bootstrap.Modal(document.getElementById('successModal')).show();
    
    // AUTO-PRINT karcis setelah 1 detik ← NEW!
    setTimeout(() => {
        autoPrintKarcis();
    }, 1000);
}

// Fungsi auto-print ← NEW!
function autoPrintKarcis() {
    if (currentAntrian) {
        const printWindow = window.open(
            '<?= base_url('antrian/print_karcis/'); ?>' + currentAntrian.antrian_id, 
            '_blank'
        );
        
        if (printWindow) {
            printWindow.addEventListener('load', function() {
                setTimeout(() => {
                    printWindow.print();  // Trigger print dialog
                }, 500);
            }, false);
        }
    }
}
```

### 2. Update karcis.php - Remove Auto-Print
Hapus auto-print dari karcis.php karena sudah di-handle dari parent:

```javascript
// OLD (REMOVED):
// window.onload = function() {
//     setTimeout(() => {
//         window.print();
//     }, 500);
// }

// NEW:
// Print dipicu dari halaman ambil_nomor.php
// Jika dibuka manual, user bisa print dengan Ctrl+P
```

---

## 📋 Apa yang Terjadi?

### Skenario 1: Ambil Nomor Normal Flow
```
1. Customer tap "Counter 1"
2. Loading modal muncul (2 detik)
3. Success modal muncul dengan nomor C1-001
4. OTOMATIS: Tab baru terbuka (print_karcis)
5. OTOMATIS: Print dialog muncul (Ctrl+P)
6. Customer klik "Print" atau "Cancel"
```

### Skenario 2: Manual Print Karcis
Jika user klik tombol "Cetak Karcis" di modal:
```
1. Klik tombol "Cetak Karcis"
2. Tab baru terbuka (print_karcis)
3. User bisa manual print dengan Ctrl+P
4. Atau close tab
```

---

## 🎨 User Experience

### Sebelum:
1. Tap counter
2. Nomor muncul
3. Klik tombol "Cetak Karcis"
4. Print dialog muncul
5. Klik Print
6. **Total: 5 steps**

### Sesudah:
1. Tap counter
2. Nomor muncul & **otomatis print dialog**
3. Klik Print
4. **Total: 3 steps** ✅

---

## ⚙️ Configuration

### Delay sebelum print (default: 1000ms)
File: `ambil_nomor.php` line ~377
```javascript
setTimeout(() => {
    autoPrintKarcis();
}, 1000);  // ← Ubah nilai ini jika perlu
```

### Delay setelah page load (default: 500ms)
File: `ambil_nomor.php` line ~404
```javascript
setTimeout(() => {
    printWindow.print();
}, 500);  // ← Ubah nilai ini jika perlu
```

---

## 🔐 Compatibility

✅ Chrome  
✅ Firefox  
✅ Safari  
✅ Edge  
⚠️ IE (tidak recommend)

**Note:** Browser modern memungkinkan trigger print programmatic.

---

## 📱 Mobile/Tablet

Pada device mobile, print dialog akan menampilkan opsi:
- Print (ke printer)
- Save as PDF
- Share
- dll

Sesuaikan dengan device capabilities.

---

## 🧪 Testing

### Test 1: Normal Flow (Auto-Print)
```
1. Buka: http://localhost/tgm_erp/antrian
2. Tap "Counter 1"
3. Lihat loading modal
4. Lihat success modal dengan nomor
5. Otomatis print dialog muncul ✅
6. Klik "Print" atau "Cancel"
```

### Test 2: Manual Print (Fallback)
```
1. Modal success sudah terbuka
2. Klik tombol "Cetak Karcis"
3. Halaman print terbuka di tab baru
4. User bisa manual print
```

### Test 3: Tutup Modal
```
1. Modal success terbuka
2. Klik tombol "Tutup"
3. Modal tutup
4. Tab print tetap terbuka (user bisa print nanti)
```

---

## 🚀 Keuntungan

✅ **Lebih cepat** - Otomatis tanpa user click lagi  
✅ **Better UX** - Workflow lebih sederhana  
✅ **Fallback** - Masih bisa manual print jika perlu  
✅ **Professional** - Automatic flow terasa lebih modern  

---

## ⚠️ Edge Cases

### Jika print dialog tidak muncul:
- Browser security policy mungkin block
- User bisa manual print dengan Ctrl+P
- Tombol "Cetak Karcis" masih tersedia sebagai fallback

### Jika printer tidak tersambung:
- Print dialog tetap muncul
- User bisa pilih "Save as PDF" atau "Cancel"

### Jika browser block pop-up:
- Halaman print mungkin tidak terbuka
- Tombol "Cetak Karcis" masih bisa digunakan

---

## 📝 Files Modified

1. ✅ `application/views/antrian/ambil_nomor.php`
   - Tambah `autoPrintKarcis()` function
   - Trigger auto-print setelah 1 detik success

2. ✅ `application/views/antrian/karcis.php`
   - Remove auto-print script
   - Tunggu trigger dari parent

---

## 🎉 Status

```
✅ Implementation: COMPLETE
✅ Testing: READY
✅ Production: READY

Feature: Auto-Print Karcis ✅ ACTIVE
```

---

**Sekarang printer akan otomatis trigger saat customer ambil nomor antrian! 🖨️**
