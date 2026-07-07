# Implementasi Metode Pembayaran untuk Pengeluaran

## 📋 Ringkasan Implementasi

Sistem pengeluaran telah diperbarui untuk mendukung berbagai metode pembayaran seperti tunai, transfer bank, debit, kartu kredit, e-wallet, dan lainnya. Fitur ini memungkinkan tracking yang lebih detail terhadap cara pembayaran pengeluaran dilakukan.

## 🗄️ Struktur Database

### Modifikasi Tabel `tbl_pengeluaran`

```sql
-- Kolom baru yang ditambahkan:
ALTER TABLE `tbl_pengeluaran` 
ADD COLUMN `metode_bayar` ENUM('tunai','transfer','debit','kartu_kredit','ewallet','lainnya') NULL AFTER `pengeluaran_penerima`;

ALTER TABLE `tbl_pengeluaran` 
ADD COLUMN `rekening_tujuan` VARCHAR(255) NULL AFTER `metode_bayar`;

ALTER TABLE `tbl_pengeluaran` 
ADD COLUMN `ewallet_id` INT(11) NULL AFTER `rekening_tujuan`;

ALTER TABLE `tbl_pengeluaran` 
ADD COLUMN `edc_id` INT(2) NULL AFTER `ewallet_id`;
```

### Foreign Key Constraints

```sql
-- Foreign key untuk e-wallet
ALTER TABLE `tbl_pengeluaran` 
ADD CONSTRAINT `fk_pengeluaran_ewallet` 
FOREIGN KEY (`ewallet_id`) REFERENCES `tbl_ewallet`(`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Foreign key untuk mesin EDC
ALTER TABLE `tbl_pengeluaran` 
ADD CONSTRAINT `fk_pengeluaran_edc` 
FOREIGN KEY (`edc_id`) REFERENCES `tbl_mesin_edc`(`edc_id`) 
ON DELETE SET NULL ON UPDATE CASCADE;
```

### Indexes untuk Performance

```sql
CREATE INDEX `idx_pengeluaran_metode_bayar` ON `tbl_pengeluaran`(`metode_bayar`);
CREATE INDEX `idx_pengeluaran_ewallet` ON `tbl_pengeluaran`(`ewallet_id`);
CREATE INDEX `idx_pengeluaran_edc` ON `tbl_pengeluaran`(`edc_id`);
```

## 🔧 Modifikasi Controller

### Controller `Laporan.php` - Method `pengeluaran()`

**Perubahan:**
- Menambahkan data e-wallet dan EDC untuk dropdown
- Data ini akan digunakan dalam form tambah pengeluaran

```php
$data = array(
    // ... existing data ...
    'ewallet_list'  => $this->db->get('tbl_ewallet')->result(),
    'edc_list'      => $this->db->get('tbl_mesin_edc')->result(),
);
```

### Controller `Laporan.php` - Method `add_pengeluaran()`

**Perubahan:**
- Menambahkan validasi metode pembayaran
- Menangani data spesifik untuk setiap metode pembayaran
- Menyimpan data ke database dengan field baru

```php
public function add_pengeluaran()
{
    $metode_bayar = $this->input->post('metode_bayar');
    $rekening_tujuan = trim($this->input->post('rekening_tujuan'));
    $ewallet_id = $this->input->post('ewallet_id');
    $edc_id = $this->input->post('edc_id');
    
    // Validasi metode pembayaran
    if (!$metode_bayar) {
        $this->session->set_flashdata('error', 'Metode pembayaran harus dipilih');
        redirect('laporan/pengeluaran');
        return;
    }
    
    // Validasi spesifik untuk setiap metode
    if ($metode_bayar == 'transfer' && !$rekening_tujuan) {
        $this->session->set_flashdata('error', 'Isi rekening tujuan untuk pembayaran transfer');
        redirect('laporan/pengeluaran');
        return;
    }
    
    if ($metode_bayar == 'ewallet' && !$ewallet_id) {
        $this->session->set_flashdata('error', 'Pilih e-wallet untuk pembayaran e-wallet');
        redirect('laporan/pengeluaran');
        return;
    }
    
    if (($metode_bayar == 'debit' || $metode_bayar == 'kartu_kredit') && !$edc_id) {
        $this->session->set_flashdata('error', 'Pilih mesin EDC untuk pembayaran kartu');
        redirect('laporan/pengeluaran');
        return;
    }
    
    $add = array(
        // ... existing fields ...
        'metode_bayar' => $metode_bayar,
        'rekening_tujuan' => $rekening_tujuan ?: null,
        'ewallet_id' => $ewallet_id ?: null,
        'edc_id' => $edc_id ?: null,
    );
    
    $success = $this->db->insert('tbl_pengeluaran', $add);
}
```

## 🎨 Modifikasi View

### View `application/views/admin/laporan/keuangan/rekap_pengeluaran.php`

**Perubahan Form:**
- Menambahkan dropdown metode pembayaran
- Menambahkan field detail untuk setiap metode pembayaran
- Field detail muncul secara dinamis berdasarkan pilihan metode

**Perubahan Tabel:**
- Menambahkan kolom "Metode Bayar"
- Menampilkan badge dengan icon dan warna yang berbeda untuk setiap metode
- Menyesuaikan colspan untuk pesan "tidak ada data"

## 📱 Metode Pembayaran yang Didukung

### 1. **Tunai**
- **Icon:** `fa-money-bill`
- **Warna:** `bg-success` (hijau)
- **Detail:** Tidak memerlukan field tambahan

### 2. **Transfer Bank**
- **Icon:** `fa-university`
- **Warna:** `bg-primary` (biru)
- **Detail:** Input text untuk rekening tujuan (manual)

### 3. **Debit**
- **Icon:** `fa-credit-card`
- **Warna:** `bg-info` (biru muda)
- **Detail:** Dropdown mesin EDC dari `tbl_mesin_edc`

### 4. **Kartu Kredit**
- **Icon:** `fa-credit-card`
- **Warna:** `bg-warning` (kuning)
- **Detail:** Dropdown mesin EDC dari `tbl_mesin_edc`

### 5. **E-Wallet**
- **Icon:** `fa-mobile`
- **Warna:** `bg-secondary` (abu-abu)
- **Detail:** Dropdown e-wallet dari `tbl_ewallet`

### 6. **Lainnya**
- **Icon:** `fa-question`
- **Warna:** `bg-dark` (hitam)
- **Detail:** Tidak memerlukan field tambahan

## 🔄 Alur Kerja

### 1. Tambah Pengeluaran
```
1. User klik "Tambah Pengeluaran"
2. Modal form muncul
3. User pilih metode pembayaran
4. Field detail muncul sesuai metode yang dipilih
5. User isi data dan submit
6. Sistem validasi dan simpan ke database
```

### 2. Validasi Form
```
- Metode pembayaran wajib dipilih
- Transfer Bank: rekening_tujuan wajib diisi
- E-Wallet: ewallet_id wajib diisi
- Debit/Kartu Kredit: edc_id wajib diisi
```

### 3. Tampilan Data
```
- Tabel menampilkan kolom "Metode Bayar"
- Setiap metode ditampilkan dengan badge berwarna
- Icon yang sesuai untuk setiap metode
```

## 🎯 Fitur Utama

### ✅ Fitur yang Sudah Diimplementasikan:
1. **Multiple Payment Methods** - 6 metode pembayaran yang berbeda
2. **Dynamic Form Fields** - Field detail muncul sesuai metode
3. **Form Validation** - Validasi client-side dan server-side
4. **Visual Indicators** - Badge dengan icon dan warna yang berbeda
5. **Database Integration** - Foreign key constraints dan indexes
6. **Responsive Design** - Form yang responsive untuk mobile

### 🔮 Fitur yang Bisa Ditambahkan:
1. **Payment History** - Riwayat pembayaran per metode
2. **Analytics** - Analisis pengeluaran berdasarkan metode pembayaran
3. **Reports** - Laporan pengeluaran per metode pembayaran
4. **Export** - Export data dengan detail metode pembayaran
5. **Filtering** - Filter pengeluaran berdasarkan metode pembayaran

## 🚀 Cara Penggunaan

### 1. Jalankan SQL Script
```bash
# Jalankan script untuk menambah kolom
mysql -u username -p database_name < add_payment_methods_to_pengeluaran.sql
```

### 2. Akses Halaman Pengeluaran
```
URL: /laporan/pengeluaran
```

### 3. Tambah Pengeluaran
```
1. Klik tombol "Tambah Pengeluaran"
2. Pilih metode pembayaran
3. Isi field detail sesuai metode
4. Submit form
```

## 📝 Catatan Penting

1. **Backup Database** - Selalu backup database sebelum menjalankan script
2. **Existing Data** - Data lama akan otomatis di-set sebagai 'tunai'
3. **Foreign Keys** - Pastikan tabel referensi sudah ada sebelum menjalankan script
4. **Validation** - Form validation mencegah data tidak valid
5. **Performance** - Indexes sudah ditambahkan untuk optimasi query

## 🔧 Troubleshooting

### Error: "Foreign key constraint fails"
- Pastikan tabel `tbl_ewallet`, `tbl_mesin_edc` sudah ada
- Pastikan data referensi tidak dihapus

### Error: "Column already exists"
- Kolom mungkin sudah ada, skip bagian ALTER TABLE yang error

### Form tidak menampilkan field detail
- Pastikan JavaScript berjalan dengan benar
- Cek console browser untuk error JavaScript

---

**Implementasi ini memberikan tracking yang lebih detail terhadap metode pembayaran pengeluaran dan meningkatkan akurasi laporan keuangan.**
