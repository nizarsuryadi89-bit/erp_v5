<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deskprint extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_transaksi');
        $this->load->model('M_master');
    }

    public function index()
    {
        $data = $this->data; // From MY_Controller
        $data['title'] = 'Deskprint Order Center';
        $data['kategori'] = $this->M_master->tampilData('tbl_kategori', 'kategori_urutan')->result();

        // Stats for Deskprint
        $userid = $this->session->userdata('id');
        $jml_transaksi = $this->M_transaksi->jml_transaksi($userid)->row_array();
        $data['jml_order_saya'] = $jml_transaksi['jml'] ?? 0;

        $this->load_admin_view('admin/deskprint/index', $data);
    }

    public function createspk()
    {
        // Proxy to Transaksi/createspk
        redirect('transaksi/createspk');
    }

    public function transaksi_deskprint()
    {
        // Proxy to Transaksi/transaksi_deskprint
        redirect('transaksi/transaksi_deskprint');
    }
}
