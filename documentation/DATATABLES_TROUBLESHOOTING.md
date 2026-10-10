# DataTables Troubleshooting Guide

## 🔧 Error: "Incorrect column count"

### **Penyebab Error:**
Error ini terjadi ketika ada perbedaan jumlah kolom antara header (`<thead>`) dan body (`<tbody>`) pada tabel DataTables.

### **Solusi yang Diterapkan:**

#### 1. **Perbaikan Controller**
```php
// Sebelum: Variabel $transactions tidak didefinisikan
$data = array(
    'transactions' => $transactions // ❌ Undefined variable
);

// Sesudah: Menambahkan definisi lengkap untuk $transactions
$transactions = array();

// Cash transactions
$transactions['cash'] = $this->db->query("
    SELECT p.pembayaran_tgl as tanggal, p.pembayaran_jam as jam, p.nospk, 
           k.konsumen_nama as konsumen, k.konsumen_nohp as telepon,
           p.bayar_tunai as jumlah, p.grand_total as total,
           u.nama as kasir
    FROM tbl_pembayaran p
    LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
    LEFT JOIN users u ON p.user_id = u.id
    WHERE p.pembayaran_tgl BETWEEN '$bulan_ini' AND '$bulan_akhir'
    AND p.bayar_tunai > 0
    ORDER BY p.pembayaran_tgl DESC, p.pembayaran_jam DESC
")->result_array();
```

#### 2. **Perbaikan JavaScript Initialization**
```javascript
// Sebelum: Inisialisasi semua tabel sekaligus
$('#table-cash, #table-transfer, #table-debit, #table-ewallet, #table-piutang').DataTable({
    // configuration
});

// Sesudah: Inisialisasi individual dengan pengecekan
const tableIds = ['table-cash', 'table-transfer', 'table-debit', 'table-ewallet', 'table-piutang'];

tableIds.forEach(function(tableId) {
    if ($('#' + tableId).length) {
        $('#' + tableId).DataTable({
            // configuration
        });
    }
});
```

#### 3. **Struktur Tabel yang Konsisten**
```html
<!-- Header: 8 kolom -->
<thead class="table-dark">
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>No SPK</th>
        <th>Konsumen</th>
        <th>Telepon</th>
        <th>Jumlah Cash</th>
        <th>Total Transaksi</th>
        <th>Kasir</th>
    </tr>
</thead>

<!-- Body: 8 kolom -->
<tbody>
    <?php if (!empty($transactions['cash'])): ?>
        <?php foreach ($transactions['cash'] as $i => $trx): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= date('d/m/Y H:i', strtotime($trx['tanggal'] . ' ' . $trx['jam'])) ?></td>
            <td><?= $trx['nospk'] ?></td>
            <td><?= $trx['konsumen'] ?></td>
            <td><?= $trx['telepon'] ?></td>
            <td class="text-end fw-bold">Rp <?= number_format($trx['jumlah']) ?></td>
            <td class="text-end">Rp <?= number_format($trx['total']) ?></td>
            <td><?= $trx['kasir'] ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- No data row: colspan="8" untuk 8 kolom -->
        <tr>
            <td colspan="8" class="text-center text-muted py-4">
                <i class="fa fa-inbox fa-3x mb-3"></i>
                <br>Belum ada transaksi cash bulan ini
            </td>
        </tr>
    <?php endif; ?>
</tbody>
```

## 🔍 **Cara Debugging:**

### 1. **Periksa Struktur HTML**
```javascript
// Di browser console
console.log('Table headers:', $('#table-cash thead tr').first().find('th').length);
console.log('Table body rows:', $('#table-cash tbody tr').length);
$('#table-cash tbody tr').each(function(index) {
    console.log('Row ' + index + ' columns:', $(this).find('td').length);
});
```

### 2. **Periksa Data PHP**
```php
// Di controller, sebelum load view
echo "<pre>";
print_r($transactions);
echo "</pre>";
die();
```

### 3. **Periksa Query Results**
```php
// Debug query results
$cash_transactions = $this->db->query("SELECT ...")->result_array();
echo "<pre>";
print_r($cash_transactions);
echo "</pre>";
die();
```

## 🛠️ **Best Practices:**

### 1. **Validasi Data Sebelum Render**
```php
<?php if (!empty($transactions['cash'])): ?>
    <!-- Render data rows -->
<?php else: ?>
    <!-- Render no data row -->
<?php endif; ?>
```

### 2. **Gunakan Null Coalescing Operator**
```php
<td><?= $trx['konsumen'] ?? '-' ?></td>
<td><?= $trx['telepon'] ?? '-' ?></td>
```

### 3. **Inisialisasi DataTables dengan Pengecekan**
```javascript
if ($('#table-id').length) {
    $('#table-id').DataTable({
        // configuration
    });
}
```

### 4. **Struktur Tabel yang Konsisten**
```html
<!-- Pastikan jumlah kolom sama di semua bagian -->
<thead>
    <tr>
        <th>Col1</th>
        <th>Col2</th>
        <th>Col3</th>
        <!-- ... -->
    </tr>
</thead>
<tbody>
    <tr>
        <td>Data1</td>
        <td>Data2</td>
        <td>Data3</td>
        <!-- ... -->
    </tr>
    <!-- No data row -->
    <tr>
        <td colspan="3">No data</td>
    </tr>
</tbody>
```

## 🚨 **Common Issues:**

### 1. **Undefined Variables**
```php
// ❌ Error
$data['transactions'] = $transactions; // $transactions not defined

// ✅ Correct
$transactions = array();
$transactions['cash'] = $this->db->query("...")->result_array();
$data['transactions'] = $transactions;
```

### 2. **Empty Arrays**
```php
// ❌ Error: Empty array without proper handling
$transactions = array();

// ✅ Correct: Initialize with empty arrays
$transactions = array(
    'cash' => array(),
    'transfer' => array(),
    'debit' => array(),
    'ewallet' => array(),
    'piutang' => array()
);
```

### 3. **Column Mismatch**
```html
<!-- ❌ Error: Different column counts -->
<thead><tr><th>Col1</th><th>Col2</th></tr></thead>
<tbody><tr><td>Data1</td><td>Data2</td><td>Data3</td></tr></tbody>

<!-- ✅ Correct: Same column counts -->
<thead><tr><th>Col1</th><th>Col2</th><th>Col3</th></tr></thead>
<tbody><tr><td>Data1</td><td>Data2</td><td>Data3</td></tr></tbody>
```

## 📋 **Checklist Debugging:**

- [ ] **Controller**: Variabel `$transactions` didefinisikan
- [ ] **Query**: Query menghasilkan data yang benar
- [ ] **View**: Struktur tabel konsisten (header = body)
- [ ] **Data**: Array tidak kosong atau ditangani dengan benar
- [ ] **JavaScript**: DataTables diinisialisasi dengan pengecekan
- [ ] **Console**: Tidak ada error JavaScript
- [ ] **Network**: Semua file CSS/JS ter-load dengan benar

## 🔧 **Quick Fixes:**

### 1. **Temporary Disable DataTables**
```javascript
// Comment out DataTables initialization temporarily
// $('#table-cash').DataTable({...});
```

### 2. **Add Error Handling**
```javascript
try {
    $('#table-cash').DataTable({
        // configuration
    });
} catch (error) {
    console.error('DataTables error:', error);
}
```

### 3. **Check Table Structure**
```javascript
// Add this before DataTables initialization
console.log('Table structure check:');
console.log('Headers:', $('#table-cash thead th').length);
console.log('First row:', $('#table-cash tbody tr:first td').length);
```

---

**Dengan menerapkan solusi di atas, error "Incorrect column count" seharusnya teratasi dan DataTables akan berfungsi dengan normal.**
