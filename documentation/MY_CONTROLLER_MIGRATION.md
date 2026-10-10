# MY_Controller Migration - Penjualan_manual

## Overview
Controller `Penjualan_manual` telah dimigrasikan untuk mengikuti aturan core `MY_Controller` yang menyediakan fungsionalitas umum dan data konfigurasi untuk semua controller.

## Perubahan yang Dilakukan

### 1. Controller Changes (`application/controllers/Penjualan_manual.php`)

#### **Before (Old Way):**
```php
class Penjualan_manual extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Penjualan_manual_model', 'penjualan');
        $this->load->library('form_validation');
        $this->load->model('M_master');
    }

    public function index() {
        $controller = $this->uri->segment(1);
        $userid = $this->session->userdata('id');
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        
        // Get company config
        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan'=> $perusahaan_id))->row_array(),
            'hakakses'   => $this->db->get_where('tbl_divisi', array('divisi_id'=> $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $controller,
            'function' => $this->uri->segment(2),
            'title' => 'Transaksi Penjualan Manual',
            'konsumen' => $this->penjualan->get_konsumen(),
            'produk' => $this->penjualan->get_produk(),
            'metode_pembayaran' => $this->penjualan->get_metode_pembayaran(),
            'rekening' => $this->penjualan->get_rekening(),
            'ewallet' => $this->penjualan->get_ewallet(),
            'edc' => $this->penjualan->get_edc(),
            
            // Company and user info
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            'menu' => $this->db->get('menu')->result(),
            'submenu' => $this->M_master->getmenu($controller)->result(),
        );
        
        $this->load_admin_view('admin/transaksi/manual', $data);
    }
}
```

#### **After (New Way):**
```php
class Penjualan_manual extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Penjualan_manual_model', 'penjualan');
        $this->load->library('form_validation');
    }

    public function index() {
        // Get data from model
        $data = array(
            'title' => 'Transaksi Penjualan Manual',
            'konsumen' => $this->penjualan->get_konsumen(),
            'produk' => $this->penjualan->get_produk(),
            'metode_pembayaran' => $this->penjualan->get_metode_pembayaran(),
            'rekening' => $this->penjualan->get_rekening(),
            'ewallet' => $this->penjualan->get_ewallet(),
            'edc' => $this->penjualan->get_edc(),
        );
        
        // Load admin view with common data automatically included
        $this->load_admin_view('admin/transaksi/manual', $data);
    }
}
```

### 2. Model Corrections (`application/models/Penjualan_manual_model.php`)

#### **Field Names Fixed (Based on Actual Database Structure):**

**tbl_konsumen:**
```php
// Konsumen - menggunakan field names yang benar sesuai struktur tabel
public function get_konsumen() {
    $this->db->where('status', '1');                    // bukan 'active'
    $this->db->order_by('konsumen_nama', 'ASC');        // bukan 'nama_konsumen'
    return $this->db->get('tbl_konsumen')->result();
}
```

**tbl_product:**
```php
// Produk - menggunakan field names yang benar sesuai struktur tabel
public function get_produk() {
    $this->db->where('status', '1');                    // bukan 'active'
    $this->db->order_by('product_nama', 'ASC');         // bukan 'produk_nama'
    return $this->db->get('tbl_product')->result();     // bukan 'tbl_produk'
}

public function get_produk_by_id($produk_id) {
    return $this->db->get_where('tbl_product', ['product_id' => $produk_id])->row(); // bukan 'produk_id'
}
```

**get_transaksi_terakhir:**
```php
public function get_transaksi_terakhir($limit = 10) {
    $this->db->select('p.*, k.konsumen_nama, pr.product_nama, pb.total_tagihan');
    $this->db->from('tbl_pemesanan p');
    $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id');
    $this->db->join('tbl_pemesanan_detail pd', 'pd.nospk = p.nospk');
    $this->db->join('tbl_product pr', 'pr.product_id = pd.produk_id');  // menggunakan tbl_product
    $this->db->join('tbl_pembayaran pb', 'pb.nospk = p.nospk', 'left');
    $this->db->where('p.id_jenis_transaksi', 2); // manual transactions
    $this->db->order_by('p.created_at', 'DESC');
    $this->db->limit($limit);
    return $this->db->get()->result();
}
```

**get_harga_id:**
```php
private function get_harga_id($produk_id) {
    // Get default harga_id for produk
    $harga = $this->db->get_where('tbl_harga_product', [
        'product_id' => $produk_id,  // bukan 'produk_id'
        'active' => '1'
    ])->row();
    
    return $harga ? $harga->harga_id : 1; // default to 1 if not found
}
```

### 3. View Corrections (`application/views/admin/transaksi/manual.php`)

#### **Field Names Fixed:**
```php
// Konsumen dropdown
<?php foreach($konsumen as $k): ?>
    <option value="<?= $k->konsumen_id ?>"><?= $k->konsumen_nama ?> - <?= $k->konsumen_nohp ?></option>
<?php endforeach; ?>

// Produk dropdown
<?php foreach($produk as $p): ?>
    <option value="<?= $p->product_id ?>"><?= $p->product_nama ?></option>
<?php endforeach; ?>

// Recent transactions table
<td><?= $t->konsumen_nama ?></td>
<td><?= $t->product_nama ?></td>
```

## Database Structure Reference

### **tbl_konsumen:**
```sql
CREATE TABLE `tbl_konsumen` (
  `konsumen_id` int(11) NOT NULL,
  `konsumen_nama` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `konsumen_alamat` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `konsumen_nohp` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `konsumen_email` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `update_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('0','1') NOT NULL DEFAULT '0'
)
```

### **tbl_product:**
```sql
CREATE TABLE `tbl_product` (
  `product_id` int(11) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `subkategori_id` int(11) NOT NULL,
  `satuan_id` int(11) NOT NULL,
  `tipe_id` int(11) NOT NULL,
  `side_id` int(1) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `barcode_product` varchar(20) DEFAULT NULL,
  `product_nama` varchar(200) NOT NULL,
  `product_deskripsi` text NOT NULL,
  `product_img` varchar(100) DEFAULT 'official.png',
  `detail_id` int(2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
)
```

## Benefits dari MY_Controller

### 1. **DRY Principle (Don't Repeat Yourself)**
- Tidak perlu mengulang kode konfigurasi di setiap controller
- Data umum tersedia secara otomatis

### 2. **Consistency**
- Semua controller memiliki data dasar yang sama
- Layout admin yang konsisten

### 3. **Maintainability**
- Perubahan data umum hanya perlu dilakukan di satu tempat
- Lebih mudah untuk maintenance

### 4. **Security**
- Pengecekan autentikasi otomatis
- Validasi session user

### 5. **Efficiency**
- Model umum dimuat sekali
- Performa yang lebih baik

## Data yang Tersedia Otomatis

### **Configuration Data (`$this->conf`):**
```php
$this->conf['perusahaan']    // Data perusahaan
$this->conf['hakakses']      // Data hak akses user
$this->conf['userid']        // Data user yang login
```

### **Common Data (`$this->data`):**
```php
$this->data['controller']    // Nama controller saat ini
$this->data['function']      // Nama function saat ini
$this->data['perusahaan']    // Nama perusahaan
$this->data['hakakses']      // Nama divisi/role user
$this->data['logo']          // Logo perusahaan
$this->data['username']      // Username yang login
$this->data['level']         // Data level user
$this->data['menu']          // Data menu
$this->data['foto']          // Foto karyawan
```

## Methods yang Tersedia

### **`load_admin_view($content_view, $additional_data = array())`**
```php
// Load view dengan layout admin lengkap
$this->load_admin_view('admin/transaksi/manual', $data);
```

### **`get_config()`**
```php
// Mendapatkan data konfigurasi
$config = $this->get_config();
```

### **`get_common_data()`**
```php
// Mendapatkan data umum
$common_data = $this->get_common_data();
```

## Authentication & Security

### **Automatic Authentication Check:**
- Setiap controller yang extend `MY_Controller` otomatis mengecek login
- Jika tidak login, redirect ke halaman auth
- Tidak perlu menulis kode pengecekan login manual

### **Session Management:**
- Session user otomatis tersedia
- Data user otomatis dimuat
- Hak akses otomatis dicek

## Migration Checklist

### ✅ **Completed:**
- [x] Extend `MY_Controller` instead of `CI_Controller`
- [x] Remove redundant configuration loading
- [x] Remove redundant model loading (`M_master`)
- [x] Use `load_admin_view()` method
- [x] Fix field names in model and view (based on actual DB structure)
- [x] Maintain all existing functionality

### 🔄 **Maintained:**
- [x] All validation rules
- [x] AJAX handling
- [x] Database transactions
- [x] Error handling
- [x] SweetAlert2 integration
- [x] Select2 integration

## Field Names Mapping

### **Konsumen:**
- `nama_konsumen` → `konsumen_nama`
- `nohp` → `konsumen_nohp`
- `active` → `status`

### **Product:**
- `produk_nama` → `product_nama`
- `produk_id` → `product_id`
- `tbl_produk` → `tbl_product`
- `active` → `status`

## Testing

### **Test Cases:**
1. **Authentication**: Pastikan hanya user yang login yang bisa akses
2. **Form Validation**: Semua validasi masih berfungsi
3. **Database Operations**: Transaksi tersimpan dengan benar
4. **UI/UX**: Tampilan tetap konsisten
5. **AJAX**: Response JSON tetap berfungsi
6. **Error Handling**: Error handling tetap berfungsi
7. **Field Names**: Semua field names sesuai dengan struktur database

## Notes

- Controller sekarang lebih clean dan maintainable
- Tidak ada perubahan fungsionalitas
- Semua fitur tetap berfungsi seperti sebelumnya
- Performa lebih baik karena tidak ada duplikasi kode
- Konsistensi dengan controller lain di sistem
- Field names telah disesuaikan dengan struktur database yang sebenarnya

## Support

Untuk bantuan teknis atau pertanyaan tentang migrasi MY_Controller, silakan hubungi tim development TGM ERP.
