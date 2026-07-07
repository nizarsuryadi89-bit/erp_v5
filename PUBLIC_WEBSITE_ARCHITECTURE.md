# Public Website Architecture for TGM Print

## Tujuan

Dokumen ini menetapkan batas akses tabel, struktur aplikasi website publik, dan strategi sinkron yang aman antara:

- `tgmprint.com` sebagai website publik
- `erp.tgmprint.com` sebagai ERP operasional

Target utamanya adalah menjaga ERP tetap ringan dan stabil, sambil tetap memungkinkan website publik membaca data yang dibutuhkan pelanggan.

## Arsitektur yang Direkomendasikan

- Aplikasi 1:
  - `tgmprint.com`
  - stack: Next.js `portal/`
  - fungsi: katalog, cek harga, tracking, self order, profil perusahaan
- Aplikasi 2:
  - `erp.tgmprint.com`
  - stack: CodeIgniter ERP
  - fungsi: login internal, transaksi, produksi, pembayaran, laporan, operasional
- Database:
  - tetap 1 database ERP
  - idealnya memakai 2 user database

## Rekomendasi User Database

- `erp_app_user`
  - dipakai ERP
  - hak akses sesuai operasional internal
- `website_public_user`
  - dipakai website publik
  - hanya `SELECT` pada tabel whitelist
  - `INSERT` terbatas hanya pada tabel intake publik bila memang dibutuhkan

Jika memungkinkan, lebih aman lagi:

- website publik tidak menulis langsung ke database
- semua write dilakukan melalui endpoint API ERP

## Tabel yang Boleh Diakses Website Publik

### Read-only utama

- `tbl_product`
  - nama produk, gambar, relasi kategori
- `tbl_harga_product`
  - harga aktif, varian, qty range
- `tbl_kategori`
  - kategori produk publik
- `tbl_subkategori`
  - opsional bila katalog nanti dibuat lebih detail
- `tbl_satuan`
  - label unit
- `tbl_side`
  - label 1 sisi / 2 sisi
- `tbl_perusahaan`
  - profil kantor pusat dan cabang

### Read-only terbatas untuk tracking

- `tbl_pemesanan`
  - hanya field yang diperlukan untuk status publik
- `tbl_pemesanan_detail`
  - item pesanan
- `tbl_pembayaran`
  - status pembayaran yang ingin ditampilkan ke customer
- `tbl_pembayaran_produk`
  - snapshot harga produk bila diperlukan
- `tbl_produksi`
  - status produksi utama
- `tbl_spk`
  - relasi pendukung tracking

### Write untuk intake publik

- `tbl_pemesanan_mandiri`
  - self order / lead order masuk
- `tbl_portal_users`
  - akun pelanggan portal
- `tbl_page_visits`
  - opsional analytics sederhana

## Tabel yang Sebaiknya Tidak Diakses Website Publik

- `users`
- `roles`
- `menu`
- `submenu`
- `tbl_karyawan`
- `tbl_absensi`
- `tbl_slip_gaji`
- `tbl_pengeluaran`
- `tbl_mutasi`
- `tbl_stok_gudang`
- `tbl_pembelian`
- `tbl_hutang`
- `tbl_piutang`
- semua tabel laporan, payroll, gudang, HR, audit, dan admin internal

## Batasan Akses yang Disarankan

### Website publik hanya boleh:

- menampilkan katalog
- menampilkan range harga
- menampilkan informasi cabang
- menampilkan tracking pesanan berdasarkan nomor SPK
- mengirim self order
- register dan login akun pelanggan

### Website publik tidak boleh:

- membuat transaksi final ERP langsung
- mengubah status produksi
- mengubah pembayaran
- mengubah master produk
- membuka data internal customer secara bebas
- menjalankan query laporan berat

## Strategi Sinkron Website Publik ↔ ERP

### Prinsip utama

- ERP adalah source of truth
- website publik adalah lapisan presentasi dan intake
- data publik harus ringan, terbatas, dan aman

### Pola sinkron yang direkomendasikan

#### 1. Read sinkron ringan

Untuk katalog, harga, dan cabang:

- website publik boleh membaca langsung dengan user database read-only
- atau membaca via API publik ERP

Untuk tracking:

- lebih aman lewat API atau query yang sangat terbatas
- jangan expose seluruh tabel transaksi

#### 2. Write melalui API ERP

Untuk:

- register
- login
- self order
- tracking request yang perlu validasi tambahan

Gunakan endpoint ERP:

- `api_portal/register`
- `api_portal/login`
- `api_portal/get_produk`
- `api_portal/submit_pesanan`

Keuntungan:

- validasi tetap terpusat
- logika bisnis tetap satu pintu
- ERP tetap mengontrol format data masuk

#### 3. Intake table, bukan transaksi final

Self order publik jangan langsung menulis ke:

- `tbl_pemesanan`
- `tbl_pembayaran`
- `tbl_produksi`

Sebaliknya, simpan dulu ke:

- `tbl_pemesanan_mandiri`

Lalu tim ERP:

- review
- validasi file/desain
- finalisasi spesifikasi
- buat transaksi resmi di ERP

Ini paling aman untuk operasional.

## Query Strategy agar ERP Tetap Aman

### Katalog dan harga

- hanya ambil kolom yang dipakai
- gunakan `WHERE hp.is_active = 1`
- batasi pagination
- hindari `SELECT *`

### Tracking

- cari berdasarkan `nospk`
- batasi hanya 1 transaksi
- tampilkan hanya kolom yang aman untuk customer

### Cabang

- filter `tbl_perusahaan.status = 1`
- cache hasilnya di aplikasi website

## Caching yang Disarankan

- kategori produk: cache 30 menit
- profil cabang: cache 30 menit
- katalog populer: cache 10 menit
- tracking: jangan cache lama, maksimal 30-60 detik bila perlu

## Rate Limiting yang Disarankan

- tracking SPK: rate limit per IP / session
- self order: rate limit per IP / nomor WhatsApp
- register/login portal: rate limit per email / IP

## Struktur Folder Aplikasi Website Publik

```text
portal/
  public/
    images/
    icons/
  src/
    app/
      (marketing)/
        page.tsx
        katalog/page.tsx
        tracking/page.tsx
        self-order/page.tsx
        cabang/page.tsx
      login/page.tsx
      register/page.tsx
      dashboard/page.tsx
      layout.tsx
      globals.css
    components/
      layout/
        header.tsx
        footer.tsx
      sections/
        hero.tsx
        category-grid.tsx
        price-checker.tsx
        tracking-box.tsx
        branch-list.tsx
      ui/
        button.tsx
        input.tsx
        badge.tsx
        card.tsx
    features/
      catalog/
        api.ts
        types.ts
        mapper.ts
      tracking/
        api.ts
        types.ts
      self-order/
        api.ts
        form-schema.ts
        types.ts
      branches/
        api.ts
        types.ts
      auth/
        api.ts
        types.ts
    lib/
      api/
        public-api.ts
      config/
        public-env.ts
      formatters/
        currency.ts
        phone.ts
      types/
        common.ts
    server/
      queries/
      guards/
      mappers/
```

## Rekomendasi Tahapan Implementasi

### Tahap 1

- website publik hanya pakai:
  - katalog
  - kategori
  - cabang
  - tracking
  - self order

### Tahap 2

- tambah dashboard customer
- tambah histori order customer portal
- tambah upload file desain

### Tahap 3

- tambah promo banner
- tambah testimoni
- tambah request quotation yang lebih detail

## Checklist Teknis Implementasi

- domain publik dipisah dari ERP
- env website publik punya `NEXT_PUBLIC_ERP_API_BASE_URL`
- API publik ERP dipisahkan dari endpoint internal
- user DB website dibatasi
- query katalog dan cabang dicache
- write publik masuk ke intake table
- tidak ada proses operasional berat di website publik

## Keputusan yang Direkomendasikan

- 2 aplikasi
- 1 database
- ERP tetap jadi pusat transaksi
- website publik hanya baca whitelist dan kirim write terbatas via API ERP

Ini adalah model yang paling seimbang antara:

- keamanan
- performa
- kemudahan pengembangan
- stabilitas operasional
