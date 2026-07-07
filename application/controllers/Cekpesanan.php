<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cekpesanan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->load->helper('security');
        // Proteksi Brute Force untuk PIN (Hanya POST request)
        if ($this->input->method() === 'post') {
            $last = $this->session->userdata('last_cekpesanan_post');
            if ($last && (time() - $last) < 3) {
                $this->session->set_flashdata('error_pin', 'Terlalu banyak percobaan. Harap tunggu beberapa detik sebelum mencoba lagi.');
                redirect(current_url());
                exit;
            }
            $this->session->set_userdata('last_cekpesanan_post', time());
        }

        // Batasi GET request agar tidak dispam (Flood control ringan)
        if ($this->input->method() === 'get') {
            $last_get = $this->session->userdata('last_cekpesanan_get');
            if ($last_get && (time() - $last_get) < 1) {
                // Beri jeda 1 detik secara diam-diam tanpa memunculkan error putih
                sleep(1);
            }
            $this->session->set_userdata('last_cekpesanan_get', time());
        }
    }

    private function _resolve_and_verify_spk($nospk)
    {
        if ($nospk == null) {
            redirect(base_url());
            exit;
        }

        // Tembalkan fallback untuk link WhatsApp lawas (yang terenkripsi decrypt_spk)
        $nospk_plain = urldecode($nospk);
        if (function_exists('decrypt_spk')) {
            $decrypted = decrypt_spk($nospk_plain);
            if ($decrypted && preg_match('/^[A-Za-z0-9\-]{1,30}$/', $decrypted)) {
                $nospk = $decrypted;
            } else {
                $nospk = $nospk_plain;
            }
        } else {
            $nospk = $nospk_plain;
        }

        if (!preg_match('/^[A-Za-z0-9\-]{1,30}$/', $nospk)) {
            show_404();
            exit;
        }

        $pembayaran_data = $this->M_transaksi->getcekpesanan($nospk)->row();

        // Mencegah FATAL ERROR (Trying to get property of non-object) jika SPK tidak ada
        if (!$pembayaran_data) {
            $this->session->set_flashdata('error', 'Pesanan tidak ditemukan.');
            redirect(base_url());
            exit;
        }

        // Phone verification removed as per request – no OTP/PIN needed.


        return [$nospk, $pembayaran_data];
    }

    public function spk($nospk = null)
    {
        list($nospk, $pembayaran_data) = $this->_resolve_and_verify_spk($nospk);

        // Ambil data perusahaan berdasarkan lokasi pesanan dibuat
        $perusahaan_id = isset($pembayaran_data->perusahaan_id) ? $pembayaran_data->perusahaan_id : 1;
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row_array();

        // Pass data to view
        $data = [
            'function' => 'Cek Pesanan',
            'title' => 'Cek Pesanan - Online',
            'body_class' => 'hold-transition layout-top-nav bg-light',
            'nospk' => $nospk,
            'perusahaan' => $perusahaan,
            'pembayaran' => $pembayaran_data,
            'detailProduk' => (function () use ($nospk, $pembayaran_data) {
                $this->load->model('M_pembayaran');
                $is_paid = !empty($pembayaran_data->bayar_nospk);

                $items = [];
                if ($is_paid) {
                    $items = $this->M_pembayaran->get_pembayaran_produk($nospk);
                }

                if (empty($items)) {
                    $items = $this->M_transaksi->getDetailPesan($nospk)->result();
                }

                foreach ($items as &$item) {
                    if (empty($item->metadata) && !empty($item->product_metadata)) {
                        $item->metadata = $item->product_metadata;
                    } else if (!empty($item->metadata) && !empty($item->product_metadata)) {
                        $meta_arr = json_decode($item->metadata, true);
                        $prod_meta = json_decode($item->product_metadata, true);
                        if (is_array($meta_arr) && is_array($prod_meta)) {
                            $item->metadata = json_encode(array_merge($prod_meta, $meta_arr));
                        }
                    }

                    // Normalize metadata and prefer snapshot price when available
                    $meta = json_decode($item->metadata, true) ?: [];
                    if (isset($item->snapshot_harga) && is_numeric($item->snapshot_harga) && $item->snapshot_harga > 0) {
                        $item->unit_price = $item->snapshot_harga;
                        $meta['unit_price'] = $item->snapshot_harga;

                        if (isset($item->snapshot_subtotal) && is_numeric($item->snapshot_subtotal) && $item->snapshot_subtotal > 0) {
                            $item->subtotal = $item->snapshot_subtotal;
                            $meta['subtotal'] = $item->snapshot_subtotal;
                        }

                        $item->metadata = json_encode($meta);
                    } else if (isset($meta['unit_price'])) {
                        $item->unit_price = $meta['unit_price'];
                    }
                }
                return $items;
            })(),
            'detailBayar' => $this->M_transaksi->getDetailPembayaran($nospk)->result(),
            'cekflow' => $this->M_transaksi->getcekflow($nospk)->row_array(),
            'rekening' => $this->db->get('tbl_rekening')->result(),
        ];

        $this->load->view('layout_admin/head', $data);
        $this->load->view('pesanan/cekpesanan', $data);
        $this->load->view('layout_admin/footer');
    }

    function cetak_invoice($nospk = null)
    {
        list($nospk, $pembayaran_data) = $this->_resolve_and_verify_spk($nospk);

        // Ambil data perusahaan berdasarkan lokasi pesanan dibuat
        $perusahaan_id = isset($pembayaran_data->perusahaan_id) ? $pembayaran_data->perusahaan_id : 1;
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row_array();

        // Pass data to view
        $data = [
            'function' => 'Cek Pesanan',
            'title' => 'Cek Pesanan - Online',
            'nospk' => $nospk,
            'perusahaan' => $perusahaan,
            'pembayaran' => $pembayaran_data,
            'detailProduk' => (function () use ($nospk, $pembayaran_data) {
                $this->load->model('M_pembayaran');
                $is_paid = !empty($pembayaran_data->bayar_nospk);

                $items = [];
                if ($is_paid) {
                    $items = $this->M_pembayaran->get_pembayaran_produk($nospk);
                }

                if (empty($items)) {
                    $items = $this->M_transaksi->getDetailPesan($nospk)->result();
                }

                foreach ($items as &$item) {
                    if (empty($item->metadata) && !empty($item->product_metadata)) {
                        $item->metadata = $item->product_metadata;
                    } else if (!empty($item->metadata) && !empty($item->product_metadata)) {
                        $meta_arr = json_decode($item->metadata, true);
                        $prod_meta = json_decode($item->product_metadata, true);
                        if (is_array($meta_arr) && is_array($prod_meta)) {
                            $item->metadata = json_encode(array_merge($prod_meta, $meta_arr));
                        }
                    }

                    $meta = json_decode($item->metadata, true) ?: [];

                    if (isset($item->snapshot_harga) && is_numeric($item->snapshot_harga) && $item->snapshot_harga > 0) {
                        $item->unit_price = $item->snapshot_harga;
                        $meta['unit_price'] = $item->snapshot_harga;

                        if (isset($item->snapshot_subtotal) && is_numeric($item->snapshot_subtotal) && $item->snapshot_subtotal > 0) {
                            $item->subtotal = $item->snapshot_subtotal;
                            $meta['subtotal'] = $item->snapshot_subtotal;
                        }

                        $item->metadata = json_encode($meta);
                    } else if (isset($meta['unit_price'])) {
                        $item->unit_price = $meta['unit_price'];
                    }
                }
                return $items;
            })(),
            'detailBayar' => $this->M_transaksi->getDetailPembayaran($nospk)->result(),
            'cekflow' => $this->M_transaksi->getcekflow($nospk)->row_array(),
            'rekening' => $this->db->get('tbl_rekening')->result(),
        ];

        $this->load->view('layout_admin/head', $data);
        $this->load->view('pesanan/cetak_invoice', $data);
        $this->load->view('layout_admin/footer');
    }
}
?>