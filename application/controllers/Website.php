<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Website extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model('M_kiosk');

    }

    public function index()
    {
        $this->load->view('website/portal', $this->build_landing_data());

    }

    public function portal()
    {
        $this->load->view('website/portal', $this->build_landing_data());
    }


    public function product()
    {

        $data = array(
            'product' => $this->M_master->getDataProduct()->result(),
        );

        $this->load->view('website/head', $data);
        // $this->load->view('website/header', $data);
        $this->load->view('website/product', $data);
        $this->load->view('website/footer', $data);
    }

    public function penggunaan()
    {

        $data = array(
            'product' => $this->M_master->getDataProduct()->result(),
        );

        $this->load->view('website/head', $data);
        // $this->load->view('website/header', $data);
        $this->load->view('website/cara_penggunaan', $data);
        $this->load->view('website/footer', $data);
    }

    public function cekpesanan()
    {

        $data = array(
            'product' => $this->M_master->getDataProduct()->result(),
        );

        if ($this->input->post('nospk', true)) {
            $nospk = $this->input->post('nospk', true);
            $data = array(
                'pembayaran' => $this->M_transaksi->getcekpesanan($nospk)->row_array(),
                'detailProduk' => $this->M_transaksi->getDetailPesan($nospk)->result(),
                'detailBayar' => $this->M_transaksi->getDetailPembayaran($nospk)->result(),
                'cekflow' => $this->db->get_where("tbl_produksi", array('nospk' => $nospk))->row_array(),
            );
        }

        $this->load->view('website/head', $data);
        $this->load->view('website/header', $data);
        $this->load->view('website/cek_pesanan', $data);
        $this->load->view('website/footer', $data);
    }

    public function detail_product($kode)
    {

        $data = array(
            'getproduct' => $this->M_master->getProduct($kode)->row_array(),

        );

        $this->load->view('website/head', $data);
        $this->load->view('website/header', $data);
        $this->load->view('website/single_product', $data);
        $this->load->view('website/footer', $data);
    }

    public function submit_self_order()
    {
        $harga_id = (int) $this->input->post('harga_id', true);
        $qty = (float) $this->input->post('qty', true);
        $nama = trim((string) $this->input->post('nama_lengkap', true));
        $no_hp = trim((string) $this->input->post('no_hp', true));
        $catatan = trim((string) $this->input->post('catatan', true));

        if ($harga_id <= 0 || $qty <= 0 || $nama === '' || $no_hp === '') {
            $this->session->set_flashdata('landing_error', 'Mohon lengkapi produk, qty, nama, dan nomor WhatsApp terlebih dahulu.');
            redirect(base_url('website#self-order'));
            return;
        }

        $variant = $this->db
            ->select('hp.harga_id, hp.product_id, hp.harga_1, p.product_nama')
            ->from('tbl_harga_product hp')
            ->join('tbl_product p', 'p.product_id = hp.product_id')
            ->where('hp.harga_id', $harga_id)
            ->where('hp.is_active', '1')
            ->get()
            ->row();

        if (!$variant) {
            $this->session->set_flashdata('landing_error', 'Produk yang dipilih tidak tersedia atau sudah tidak aktif.');
            redirect(base_url('website#self-order'));
            return;
        }

        $this->db->where('konsumen_nohp', $no_hp);
        $konsumen = $this->db->get('tbl_konsumen')->row();

        if ($konsumen) {
            $konsumen_id = $konsumen->konsumen_id;
            if ($konsumen->konsumen_nama !== $nama) {
                $this->db->where('konsumen_id', $konsumen_id)->update('tbl_konsumen', ['konsumen_nama' => $nama]);
            }
        } else {
            $this->db->insert('tbl_konsumen', array(
                'konsumen_nama' => $nama,
                'konsumen_nohp' => $no_hp,
                'konsumen_alamat' => '-',
                'status' => '1'
            ));
            $konsumen_id = $this->db->insert_id();
        }

        $total_harga = $qty * (float) $variant->harga_1;

        $ref = $this->M_kiosk->save_batch_order(array(
            array(
                'konsumen_id' => $konsumen_id,
                'konsumen_nama' => $nama,
                'no_wa' => $no_hp,
                'perusahaan_id' => 1,
                'product_id' => $variant->product_id,
                'harga_id' => $variant->harga_id,
                'qty' => $qty,
                'panjang' => 0,
                'lebar' => 0,
                'total_harga' => $total_harga,
                'catatan' => $catatan,
                'status' => 'submitted'
            )
        ));

        if ($ref) {
            $this->session->set_flashdata('landing_success', 'Self order berhasil dikirim. Nomor referensi Anda: ' . $ref);
        } else {
            $this->session->set_flashdata('landing_error', 'Pesanan gagal disimpan. Silakan coba lagi.');
        }

        redirect(base_url('website#self-order'));
    }

    private function build_landing_data()
    {
        $price_keyword = trim((string) $this->input->get('price', true));
        $tracking_keyword = trim((string) $this->input->get('spk', true));
        $selected_category = (int) $this->input->get('category', true);

        $data = array(
            'perusahaan' => $this->db->get('tbl_config')->row_array(),
            'total_konsumen' => $this->M_master->tampilData('tbl_konsumen', 'konsumen_nama')->num_rows(),
            'total_product' => $this->M_master->getDataProduct()->num_rows(),
            'categories' => $this->M_master->get_public_categories(),
            'selected_category' => $selected_category,
            'featured_prices' => $this->M_master->search_public_prices('', 6),
            'order_options' => $this->M_master->get_public_order_options(250),
            'price_keyword' => $price_keyword,
            'tracking_keyword' => $tracking_keyword,
            'price_results' => array(),
            'tracking' => null,
            'tracking_found' => null,
        );

        $data['company_branches'] = $this->db
            ->where('status', '1')
            ->order_by('id_perusahaan', 'ASC')
            ->get('tbl_perusahaan')
            ->result_array();

        $data['primary_branch'] = !empty($data['company_branches']) ? $data['company_branches'][0] : null;

        if ($price_keyword !== '') {
            $data['price_results'] = $this->M_master->search_public_prices($price_keyword, 12);
        }

        if ($tracking_keyword !== '') {
            $tracking = $this->build_tracking_data($tracking_keyword);
            $data['tracking'] = $tracking;
            $data['tracking_found'] = !empty($tracking['pembayaran']);
            $data['tracking_keyword'] = $tracking['searched_spk'];
        }

        return $data;
    }

    private function build_tracking_data($nospk)
    {
        $searched_spk = strtoupper(trim((string) $nospk));
        $pembayaran = $this->M_transaksi->getcekpesanan($searched_spk)->row();

        if (!$pembayaran) {
            return array(
                'searched_spk' => $searched_spk,
                'pembayaran' => null,
                'detailProduk' => array(),
                'cekflow' => null,
                'progress_label' => 'SPK tidak ditemukan',
                'progress_percent' => 0,
                'status_badge' => 'Tidak ditemukan',
                'status_class' => 'danger',
            );
        }

        $detailProduk = $this->M_transaksi->getDetailPesan($searched_spk)->result();
        $cekflow = $this->M_transaksi->getcekflow($searched_spk)->row_array();

        foreach ($detailProduk as &$item) {
            if (!empty($item->metadata)) {
                $meta = json_decode($item->metadata, true);
                if (isset($meta['unit_price'])) {
                    $item->unit_price = $meta['unit_price'];
                }
            }
        }
        unset($item);

        $status_produksi = isset($cekflow['status_produksi']) ? (string) $cekflow['status_produksi'] : '0';
        $progress_label = 'Menunggu diproses';
        $progress_percent = 20;
        $status_badge = 'Pending';
        $status_class = 'warning';

        if ($status_produksi === '1') {
            $progress_label = 'Sedang diproses oleh tim produksi';
            $progress_percent = 65;
            $status_badge = 'Diproses';
            $status_class = 'primary';
        } elseif ($status_produksi === '2') {
            $progress_label = 'Pesanan siap diambil atau dikirim';
            $progress_percent = 100;
            $status_badge = 'Selesai';
            $status_class = 'success';
        }

        return array(
            'searched_spk' => $searched_spk,
            'pembayaran' => $pembayaran,
            'detailProduk' => $detailProduk,
            'cekflow' => $cekflow,
            'progress_label' => $progress_label,
            'progress_percent' => $progress_percent,
            'status_badge' => $status_badge,
            'status_class' => $status_class,
        );
    }
}
?>
