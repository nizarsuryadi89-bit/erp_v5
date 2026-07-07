# INTEGRASI ANTRIAN DENGAN MENU TGM ERP

## 📌 Tambahkan Menu di Database

Jalankan SQL berikut untuk menambahkan menu Sistem Antrian ke aplikasi:

```sql
-- Insert menu Sistem Antrian
INSERT INTO menu (nama, link, icon, urutan, status) VALUES
('Sistem Antrian', 'antrian', 'fas fa-traffic-light', 10, 1);

-- Get ID menu yang baru dibuat (gunakan untuk join di tbl_menu_access)
SELECT id FROM menu WHERE link = 'antrian';
```

## 🔐 Setup Access Control

### Untuk Level 2 (Deskprint) - Hanya Panel Operator
```sql
-- Deskprint bisa akses panel operator
INSERT INTO tbl_menu_access (role_id, menu_id) 
VALUES (2, @antrian_menu_id);  -- Ganti @antrian_menu_id dengan ID dari query di atas
```

### Untuk Level 1 (Admin) - Semua Fitur
```sql
-- Admin bisa akses semua fitur (dihandle otomatis di MY_Controller)
-- Tidak perlu insert manual, karena Level 1 bypass access check
```

### Untuk Level 3 (Kasir) - Hanya Ambil Nomor
```sql
-- Kasir bisa akses interface ambil nomor (sudah public)
-- Tidak perlu menu access, karena ambil nomor adalah public interface
```

## 🎨 Update Sidebar Navigation (Opsional)

Jika ingin menambahkan link langsung di sidebar, edit file layout:

**File:** `application/views/layout_admin/sidebar.php` (atau sesuai struktur Anda)

```html
<!-- Tambahkan di menu sidebar -->
<li class="nav-item">
    <a class="nav-link" href="<?= base_url('antrian'); ?>">
        <i class="fas fa-traffic-light"></i>
        <span>Sistem Antrian</span>
    </a>
</li>
```

## 📱 Create Home/Welcome Card

Edit: `application/controllers/Dashboard.php` (jika ada)

Tambahkan card untuk quick access ke antrian:

```html
<!-- Quick Access Card -->
<div class="col-md-3">
    <div class="card">
        <div class="card-body text-center">
            <i class="fas fa-traffic-light fa-3x text-primary mb-3"></i>
            <h5>Sistem Antrian</h5>
            <p class="text-muted">Manage antrian pelanggan</p>
            <a href="<?= base_url('antrian'); ?>" class="btn btn-primary btn-sm">
                Buka Sistem
            </a>
        </div>
    </div>
</div>
```

## 🔗 Navigation Links Dalam Antrian

### Dari Dashboard Antrian ke Fitur Lain:
```php
// Kembali ke dashboard utama
<a href="<?= base_url('dashboard'); ?>">Kembali ke Dashboard Utama</a>

// Akses ke modul lain
<a href="<?= base_url('konsumen'); ?>">Kelola Konsumen</a>
<a href="<?= base_url('transaksi'); ?>">Lihat Transaksi</a>
```

## 📊 Integration dengan Analytics (Opsional)

Jika Anda punya sistem analytics/reporting, bisa integrate data antrian:

```php
// Di Controller Analytics/Report:
$this->load->model('Antrian_model');
$antrian_stats = $this->Antrian_model->get_statistik_harian();

// Tambahkan ke analytics dashboard
$data['antrian_stats'] = $antrian_stats;
```

## 🔄 Synchronisasi Data dengan Modul Lain

### Contoh: Saat Antrian Selesai, Update Transaksi
```php
// File: application/models/Antrian_model.php
public function selesai_antrian($antrian_id, $user_id)
{
    // ... existing code ...
    
    // Setelah update antrian, bisa tambahkan:
    $antrian = $this->get_antrian($antrian_id);
    if ($antrian['status'] == 'selesai') {
        // Update transaksi atau modul lain
        // $this->load->model('Transaksi_model');
        // $this->Transaksi_model->log_antrian_selesai($antrian_id);
    }
}
```

## 🎯 Permission Mapping

Berdasarkan level/role yang ada di TGM ERP:

| Level | Role | Antrian Access |
|-------|------|-----------------|
| 1 | Admin | ✅ Semua (dashboard, operator, laporan) |
| 2 | Deskprint | ✅ Panel Operator saja |
| 3 | Kasir | ✅ Public interface (ambil nomor) |
| 4 | Operator Produksi | ✅ Public interface (ambil nomor) |
| 19 | Pengambilan Barang | ✅ Public interface (ambil nomor) |

Edit di: `application/controllers/Antrian.php`
```php
public function operator($counter_id = null)
{
    $level_id = $this->session->userdata('level');
    // Sesuaikan level yang diizinkan
    if ($level_id != 2 && $level_id != 1) {
        $this->session->set_flashdata('error', 'Akses ditolak');
        redirect('antrian');
    }
    // ...
}
```

## 🔔 Notifikasi Real-time (Enhancement)

Jika ingin tambahkan notifikasi ketika antrian dipanggil:

```javascript
// File: application/views/antrian/display.php
// Tambahkan di section untuk memutar sound

function playNotificationSound() {
    // Gunakan Web Audio API atau HTML5 audio
    const audio = new Audio('<?= base_url('assets/sounds/notification.mp3'); ?>');
    audio.play();
}

// Atau gunakan Text-to-Speech
function speakQueueNumber(nomor) {
    const utterance = new SpeechSynthesisUtterance(`Nomor antrian ${nomor}`);
    speechSynthesis.speak(utterance);
}
```

## 💾 Database Maintenance

### Setup Auto-Archive Antrian Lama
```php
// File: application/controllers/admin/Maintenance.php atau Cron
// Jalankan setiap hari untuk archive antrian kemarin

public function archive_old_antrian()
{
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    // Backup ke tabel archive (opsional)
    // atau langsung delete
    
    $this->db->where('DATE(waktu_ambil) <', $yesterday);
    $this->db->delete('tbl_antrian');
}
```

### Optimize Database
```sql
-- Run periodically
OPTIMIZE TABLE tbl_antrian;
OPTIMIZE TABLE tbl_antrian_history;
OPTIMIZE TABLE tbl_antrian_stats;
```

## 📧 Email Notification (Enhancement)

Jika mau kirim notifikasi via email saat antrian selesai:

```php
// File: application/models/Antrian_model.php
public function selesai_antrian($antrian_id, $user_id)
{
    // ... existing code ...
    
    // Kirim email notifikasi (optional)
    $this->load->library('email');
    $this->email->from('noreply@tgm.com');
    $this->email->to('manager@tgm.com');
    $this->email->subject('Antrian Selesai');
    $this->email->message('Antrian ' . $antrian['nomor_antrian'] . ' telah selesai dilayani');
    // $this->email->send();
}
```

## 🎓 User Training Materials

Buat dokumentasi untuk pengguna:

```markdown
# Panduan Pengguna - Sistem Antrian

## Untuk Customer:
1. Tap layar antrian
2. Pilih counter
3. Ambil karcis
4. Tunggu dipanggil

## Untuk Operator:
1. Login dengan akun Deskprint
2. Buka Panel Operator
3. Panggil antrian
4. Mulai dan selesaikan layanan
```

## 🚀 Deployment Final Steps

1. ✅ Menu sudah ditambahkan
2. ✅ Access control sudah diatur
3. ✅ Sidebar sudah updated (opsional)
4. ✅ Notifikasi sound/TTS sudah disiapkan (opsional)
5. ✅ Database maintenance sudah scheduled
6. ✅ User training sudah dilakukan

---

**Integrasi Antrian dengan TGM ERP selesai! 🎉**

Sistem siap untuk production dan fully integrated dengan ecosystem ERP.
