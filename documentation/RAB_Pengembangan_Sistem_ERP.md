# Proposal & Rencana Anggaran Biaya (RAB)

## Pengembangan Sistem ERP Terintegrasi (TGM Print)

**Tanggal:** 26 Januari 2026
**Perihal:** Estimasi Biaya Pengembangan Sistem Custom ERP
**Platform:** Web Based (CodeIgniter 3) & Mobile Web (PWA/Responsive)

---

### I. Ringkasan Eksekutif

Dokumen ini berisi rincian estimasi biaya untuk pengembangan sistem Enterprise Resource Planning (ERP) yang disesuaikan khusus untuk kebutuhan manufaktur dan operasional bisnis. Sistem ini mencakup integrasi penuh mulai dari manajemen gudang, produksi (manufaktur), penjualan, keuangan, hingga manajemen SDM (absensi mobile & payroll).

### II. Rincian Biaya Pengembangan per Modul

#### 1. Core System & Keamanan

*Pondasi sistem, manajemen user, hak akses (RBAC), dan keamanan data.*

* Instalasi & Konfigurasi Framework (CI3)
* Desain Database & Optimasi Relasional
* Sistem Login & Autentikasi Multi-level (Owner, Admin, Produksi, Keuangan, dll)
* User Activity Log & Security Hardening
* **Estimasi: Rp 8.000.000**

#### 2. Modul Master Data Center

*Manajemen data induk yang menjadi referensi seluruh transaksi.*

* Master Produk (Single/Composite)
* Master Kategori, Satuan, Tipe, & Konversi
* Master Kontak (Supplier, Customer, Karyawan)
* Manajemen Multi-Lokasi / Cabang
* **Estimasi: Rp 5.000.000**

#### 3. Modul Manufaktur & Produksi (Kompleks)

*Inti dari operasional produksi dengan perhitungan HPP presisi.*

* Konfigurasi Bill of Materials (BOM) Multi-level
* Manajemen Routing / Alur Kerja Produksi per Produk
* Kalkulasi Estimasi HPP (Bahan + Tenaga Kerja + Overhead) secara Real-time
* Surat Perintah Kerja (SPK) & Tracking Progress Produksi
* **Estimasi: Rp 25.000.000**

#### 4. Modul Inventory & Warehouse Management

*Kontrol stok presisi dan efisiensi gudang.*

* Manajemen Stok Masuk (Receiving) & Keluar (Delivery)
* Transfer Stok Antar Gudang
* Stock Opname & Adjustments
* **Fitur Cetak & Scan Barcode System**
* Kartu Stok & History Pergerakan Barang
* **Estimasi: Rp 15.000.000**

#### 5. Modul Transaksi (Pembelian & Penjualan)

*Siklus bisnis dari hulu ke hilir.*

* Purchase Order (PO) ke Supplier & Hutang Dagang
* Sales Order (SO), Invoicing, & Piutang Dagang
* POS (Point of Sales) untuk kasir toko (jika ada)
* Integrasi status pembayaran dengan Keuangan
* **Estimasi: Rp 12.000.000**

#### 6. Modul Keuangan & Akuntansi Sederhana

*Tracking kesehatan finansial perusahaan.*

* Pencatatan Kas Masuk & Keluar (Expenses)
* Jurnal Umum Otomatis dari Transaksi
* Laporan Laba Rugi (Profit & Loss)
* Laporan Neraca (Balance Sheet) & Arus Kas
* Manajemen Pajak Bulanan
* **Estimasi: Rp 18.000.000**

#### 7. Modul HRM & Absensi Mobile (ESS)

*Manajemen SDM modern dengan fitur mobile.*

* **Mobile App Interface** untuk Karyawan
* Absensi Selfie dengan Validasi GPS (Geofencing) & Anti-Fake GPS
* Pengajuan Cuti, Izin, & Lembur Online (Approval System)
* Manajemen Shift & Jadwal Kerja
* Rekapitulasi Kehadiran untuk Payroll
* **Estimasi: Rp 20.000.000**

#### 8. Executive Dashboard & Reporting

*Business Intelligence untuk pengambilan keputusan.*

* Dashboard Real-time (Sales, Production, Stock Alerts)
* Laporan Analisa Bisnis (Top Products, Cost Analysis)
* Export Data (PDF/Excel)
* **Estimasi: Rp 10.000.000**

---

### III. Jasa Tambahan & Infrastruktur

| Item | Keterangan | Biaya Estimasi |
| :--- | :--- | :--- |
| **UI/UX Design Premium** | Penerapan desain modern, responsive, dan user-friendly (seperti yang sudah diterapkan). | Rp 10.000.000 |
| **Testing & QA** | UAT (User Acceptance Testing), Bug Fixing, Security Stress Test. | Rp 7.000.000 |
| **Deployment & Server Setup** | Instalasi di VPS/Cloud, konfigurasi SSL, Cron Jobs, Backup Automation. | Rp 3.000.000 |
| **Training & Dokumentasi** | Pelatihan user admin & staff, serta buku manual penggunaan sistem. | Rp 5.000.000 |

---

### IV. Rekapitulasi Total Biaya

1. Total Biaya Pengembangan Modul: **Rp 113.000.000**
2. Total Jasa Tambahan: **Rp 25.000.000**

**GRAND TOTAL ESTIMASI: Rp 138.000.000,-**
*(Terbilang: Seratus Tiga Puluh Delapan Juta Rupiah)*

---

### V. Estimasi Waktu Pengerjaan

* **Fase 1: Analisa & Desain** (2 Minggu)
* **Fase 2: Core, Master Data, Inventory** (4 Minggu)
* **Fase 3: Transaksi & Manufaktur** (5 Minggu)
* **Fase 4: Keuangan & HRM Mobile** (4 Minggu)
* **Fase 5: Reporting, Integrasi, QC** (3 Minggu)
* **Fase 6: Deployment & Training** (2 Minggu)

**Total Waktu Estimasi: +/- 5 Bulan (20 Minggu)**

### VI. Catatan

* Harga di atas adalah estimasi untuk pembangunan sistem dari nol atau refactoring besar-besaran dengan standar kualitas *enterprise*.
* Biaya server (VPS) dan domain dibayarkan terpisah oleh klien (Estimasi Rp 2-3jt/tahun).
* Garansi maintenance (bug fix) biasanya diberikan selama 3-6 bulan setelah serah terima.

---
*Dibuat secara otomatis oleh Asisten AI untuk keperluan perencanaan TGM Print.*
