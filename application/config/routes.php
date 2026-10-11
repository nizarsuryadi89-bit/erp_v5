<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'auth';
$route['404_override'] = 'Lapor';
$route['error'] = 'Lapor';
$route['translate_uri_dashes'] = FALSE;
// $route['bahan']                                 = FALSE;

// Sistem Gudang

$route['warehouse/gudang'] = 'warehouse';
$route['warehouse/bahan'] = 'warehouse/materials_jasa';
$route['warehouse/produk-bahan/(:any)'] = 'warehouse/product_bahan_detail/$1';
$route['warehouse/buat-produk/(:any)'] = 'manufaktur';
$route['warehouse/lihat-produk/(:any)'] = 'manufaktur/skala-harga/$1';

$route['warehouse/hapus-bahan/(:any)'] = 'warehouse/actionDeleteBahanLegacy/$1';
$route['warehouse/product'] = 'manufaktur';
$route['product'] = 'manufaktur';
$route['product/(:any)'] = 'manufaktur/$1';
$route['inventori'] = 'manufaktur';
$route['inventori/detail_product/(:any)'] = 'manufaktur/skala-harga/$1';
$route['inventori/detail/(:any)'] = 'manufaktur/detail/$1';
$route['inventori/(:any)'] = 'manufaktur/$1';
// $route['warehouse/detail-product/(:any)']          = 'product/detail/$id';
$route['warehouse/formAdd/(:any)'] = 'manufaktur';
$route['warehouse/pembelian-barang'] = 'warehouse/DaftarPembelian';
$route['warehouse/pengiriman-barang'] = 'warehouse/DaftarPengambilan';
$route['warehouse/tambah-pengiriman'] = 'warehouse/PengambilanBahan';
$route['warehouse/laporan-stok'] = 'warehouse/gudang_bahan';
$route['warehouse/laporan-barang-masuk'] = 'warehouse/laporan_barang_masuk';
$route['warehouse/laporan-barang-keluar'] = 'warehouse/laporan_barang_keluar';
$route['warehouse/stok-opname'] = 'warehouse/opname';
$route['gudang/stokopname'] = 'warehouse/opname';
$route['inventori/stokopname'] = 'warehouse/opname';
$route['warehouse/barang-masuk'] = 'warehouse/barang_masuk';
$route['warehouse/barang-keluar'] = 'warehouse/barang_keluar';
$route['warehouse/cetak-barcode-semua'] = 'warehouse/cetak_barcode_semua'; // Cetak semua barcode bahan
$route['warehouse/cetak-barcode/(:any)'] = 'warehouse/cetak_barcode/$1'; // Cetak barcode bahan tertentu



// Sistem Deskprint
$route['deskprint'] = 'deskprint';
$route['deskprint/(:any)'] = 'deskprint/$1';

// Sistem Kasir
$route['transaksi/daftar-bahan'] = 'warehouse/materials_jasa';
$route['transaksi/daftar-product'] = 'manufaktur';
$route['transaksi/daftar-konsumen'] = 'konsumen';
$route['transaksi/daftar-transaksi'] = 'transaksi/transaksi_sudahbayar';
$route['transaksi/search_konsumen'] = 'Transaksi/search_konsumen';



$route['transaksi/daftar-produksi'] = 'wip/produksi';
$route['transaksi/daftar-produksi-op'] = 'wip';
$route['transaksi/daftar-pengambilan-barang'] = 'transaksi/pbarang';
$route['dashboard/halaman-utama'] = 'dashboard/board';

//Website
$route['cek-pesanan'] = 'website/cekpesanan';
$route['website/self-order'] = 'website/submit_self_order';

// Karyawan
$route['humanresource/'] = 'karyawan/index';
$route['karyawan/lihat-akun/(:any)'] = 'karyawan/lihat_akun/$id';
$route['karyawan/edit-akun/(:any)'] = 'karyawan/edit_akun/$id';
$route['karyawan/delete-akun/(:any)'] = 'karyawan/edit_akun/$id';
$route['karyawan/lihat-karyawan/(:any)'] = 'karyawan/lihat_karyawan/$id';
$route['karyawan/edit-karyawan/(:any)'] = 'karyawan/edit_karyawan/$id';
$route['karyawan/delete-karyawan/(:any)'] = 'karyawan/delete_karyawan/$id';
$route['karyawan/tambah-karyawan'] = 'karyawan/tambah_karyawan';
$route['karyawan/daftar-karyawan'] = 'karyawan/karyawan';
$route['karyawan/daftar-akun'] = 'karyawan/akun';
$route['karyawan/daftar-jabatan-divisi'] = 'karyawan/jabatan';
$route['karyawan/tambah-jabatan'] = 'karyawan/tambah_jabatan';
$route['karyawan/penilaian-kinerja'] = 'karyawan/penilaian_kinerja';
$route['karyawan/absensi-lembur'] = 'karyawan/absen_lembur';
$route['karyawan/absensi-dan-cuti'] = 'karyawan/absen_karyawan';
$route['konsumen/search_ajax'] = 'konsumen/search_ajax';


// Manufactur
$route['manufaktur/get_subkategori'] = 'manufaktur/get_subkategori';
$route['manufaktur/get_product_detail/(:any)'] = 'manufaktur/get_product_detail/$1';
$route['manufaktur/update_product_ajax'] = 'manufaktur/update_product_ajax';
$route['manufaktur/rename_product_ajax'] = 'manufaktur/rename_product_ajax';

$route['manufaktur/routing/(:any)'] = 'manufaktur/routing/$1';
$route['manufaktur/bom/(:any)'] = 'manufaktur/bom/$1';
$route['manufaktur/skala-harga/(:any)'] = 'manufaktur/skalaharga/$1';
$route['manufaktur/edit-product/(:any)'] = 'manufaktur/edit_product/$1';
$route['manufaktur/copy-product/(:any)'] = 'manufaktur/copy_product/$1';
$route['manufaktur/produksi'] = 'wip/produksi';
$route['produksi'] = 'wip/produksi';
$route['produksi/(:any)'] = 'wip/$1';

$route['konsumen/aksi-simpan'] = 'konsumen/actionAdd';
$route['konsumen/aksi-delete/(:any)'] = 'konsumen/action_delete';
$route['konsumen/aktivitas-konsumen'] = 'konsumen/aktivitas_konsumen';

$route['mesin/aksi-simpan'] = 'mesin/actionAdd';
$route['mesin/aksi-delete/(:any)'] = 'mesin/action_delete';

//keuangan
$route['laporan/akun-piutang'] = 'keuangan/piutang';
$route['laporan/akun-hutang'] = 'keuangan/hutang';
$route['laporan/laporan-periode-keuangan'] = 'keuangan/laporan_periode_keuangan';
$route['laporan/laporan-keuangan-harian'] = 'keuangan/transaksi_harian';
$route['laporan/laporan-keuangan-bulanan'] = 'keuangan/transaksi_bulanan';


$route['laporan/laporan-cash-harian'] = 'keuangan/transaksi_cash_harian';
$route['laporan/laporan-transfer-harian'] = 'keuangan/transaksi_transfer_harian';
$route['laporan/laporan-edc-harian'] = 'keuangan/transaksi_edc_harian';
$route['laporan/laporan-ewallet-harian'] = 'keuangan/transaksi_ewallet_harian';
$route['laporan/buku-besar'] = 'keuangan/legger';
// Pengeluaran
$route['pengeluaran'] = 'laporan/pengeluaran';
$route['laporan/pengeluaran-harian'] = 'keuangan/pengeluaran_harian';
$route['laporan/pengeluaran-bulanan'] = 'keuangan/pengeluaran_bulanan';


//bahan
$route['bahan'] = 'warehouse/materials_jasa';
$route['bahan/actionAdd'] = 'warehouse/actionAddBahanLegacy';
$route['bahan/actionUpdate'] = 'warehouse/actionUpdateBahanLegacy';
$route['bahan/updateBarcodeOnly'] = 'warehouse/updateBarcodeBahanLegacy';
$route['bahan/edit-bahan/(:any)'] = 'warehouse/materials_jasa';



// Owner Report Token - Public Routes (NO Auth Required)
$route['owner-report/(:any)'] = 'OwnerReport/view/$1';
$route['owner-report'] = 'OwnerReport/index';

// Custom route untuk Owner
$route['owner'] = 'Owner/dashboard/TGM2026';
