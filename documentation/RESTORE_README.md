# Database Restore Application

Aplikasi restore database yang memungkinkan restore per tabel atau full database dengan fitur keamanan dan analisis backup.

## 🚀 Fitur Utama

### 1. **Restore Modes**
- **Full Database Restore**: Restore seluruh database
- **Per Table Restore**: Restore tabel tertentu saja

### 2. **File Format Support**
- **SQL Files**: Langsung eksekusi
- **ZIP Files**: Extract SQL dari archive
- **GZIP Files**: Decompress dan eksekusi

### 3. **Safety Features**
- **Pre-restore Backup**: Otomatis backup sebelum restore
- **Error Handling**: Penanganan error yang robust
- **Transaction Support**: Rollback jika gagal

### 4. **User Interface**
- **Dashboard Overview**: Statistik backup dan tabel
- **File Management**: List semua file backup tersedia
- **Interactive Analysis**: Analisis file backup
- **Table Selection**: Pilih tabel untuk restore

## 📁 File Structure

```
application/
├── controllers/
│   └── Restore.php              # Main restore controller
├── views/admin/restore/
│   ├── index.php                # Main restore dashboard
│   └── analyze.php              # Table analysis and selection
├── helpers/
│   └── restore_helper.php       # Helper functions
└── config/
    └── autoload.php             # Autoload configuration
```

## 🔧 Installation & Setup

### 1. **File Placement**
Pastikan semua file ditempatkan di lokasi yang benar:
- Controller: `application/controllers/Restore.php`
- Views: `application/views/admin/restore/`
- Helper: `application/helpers/restore_helper.php`

### 2. **Autoload Configuration**
Helper sudah ditambahkan ke autoload di `application/config/autoload.php`:
```php
$autoload['helper'] = array('url', 'file', 'form', 'log', 'costum', 'tgl_indo', 'terbilang', 'umur_helper', 'text', 'security', 'rupiah', 'restore');
```

### 3. **Directory Permissions**
Pastikan direktori backup memiliki permission yang benar:
```bash
chmod 755 uploads/backup/
chmod 755 uploads/temp/
```

## 🎯 Cara Penggunaan

### 1. **Akses Restore**
```
URL: /restore
```

### 2. **Pilih File Backup**
- Klik "Analisis" pada file backup yang diinginkan
- Sistem akan menganalisis tabel yang tersedia

### 3. **Konfigurasi Restore**
- Pilih mode: Full Database atau Per Tabel
- Pilih tabel yang akan di-restore
- Aktifkan backup sebelum restore (direkomendasikan)

### 4. **Eksekusi Restore**
- Konfirmasi pengaturan
- Klik "Mulai Restore"
- Sistem akan mengeksekusi restore

## 🛡️ Keamanan

### **Access Control**
- Hanya admin (level = 1) yang bisa akses
- Validasi file backup
- Sanitasi input

### **Error Handling**
- Log semua error ke file log
- Rollback otomatis jika gagal
- Pre-restore backup untuk safety

### **File Validation**
- Validasi ekstensi file
- Validasi konten SQL
- Validasi struktur backup

## 🔍 Troubleshooting

### **Common Issues**

#### 1. **"No backup file found"**
- Pastikan file backup ada di `uploads/backup/`
- Periksa permission direktori
- Pastikan ekstensi file didukung (.sql, .zip, .gz)

#### 2. **"Error analyzing backup"**
- Periksa format file backup
- Pastikan file tidak corrupt
- Cek log error di `application/logs/`

#### 3. **"Restore failed"**
- Periksa koneksi database
- Pastikan user database memiliki permission
- Cek log error untuk detail

#### 4. **"No SQL content found"**
- Pastikan file backup berisi SQL yang valid
- Periksa format file (ZIP/GZIP)
- Coba extract manual untuk verifikasi

### **Testing**
Gunakan endpoint test untuk debugging:
```
URL: /restore/test
```

## 📋 API Endpoints

### **Main Endpoints**
- `GET /restore` - Main dashboard
- `GET /restore/analyze/{filename}` - Analyze backup file
- `POST /restore/process` - Process restore request

### **AJAX Endpoints**
- `POST /restore/preview_backup` - Preview backup content
- `POST /restore/get_table_structure` - Get table structure

### **Test Endpoints**
- `GET /restore/test` - Test restore functionality

## 🔧 Configuration

### **Backup Directory**
```php
$this->backupDirectory = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR;
```

### **Supported File Types**
```php
array('zip', 'gz', 'gzip', 'sql')
```

### **Max File Size**
Default: Tidak ada batasan (tergantung PHP settings)

## 📊 Monitoring

### **Log Files**
- Error logs: `application/logs/`
- Restore logs: Included in error logs

### **Backup Files**
- Location: `uploads/backup/`
- Naming: `backup_YYYYMMDD_HHMMSS.sql`
- Pre-restore: `pre_restore_backup_YYYYMMDD_HHMMSS.sql`

## 🚨 Important Notes

### **Before Restore**
1. **Always create backup** sebelum restore
2. **Test on development** environment first
3. **Verify backup file** integrity
4. **Check database permissions**

### **During Restore**
1. **Don't interrupt** the process
2. **Monitor logs** for errors
3. **Keep backup** files safe

### **After Restore**
1. **Verify data** integrity
2. **Test application** functionality
3. **Update documentation** if needed

## 🔄 Maintenance

### **Regular Tasks**
- Clean old backup files
- Monitor disk space
- Update backup strategy
- Test restore procedures

### **Backup Strategy**
- Daily backups for critical data
- Weekly full backups
- Monthly archive backups
- Test restore monthly

## 📞 Support

Jika mengalami masalah:
1. Periksa log error
2. Test dengan file backup kecil
3. Verifikasi konfigurasi database
4. Hubungi administrator sistem

---

**Version**: 1.0  
**Last Updated**: January 2025  
**Compatibility**: CodeIgniter 3.x, PHP 7.4+
