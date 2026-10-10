<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Aset extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Authentication is already handled globally by MY_Controller::_check_auth()
    }

    public function index()
    {
        $query_bahan = $this->M_master->getDataBahan();
        $data = array(
            'controller'    => 'aset',
            'function'      => $this->uri->segment(2) ?? 'index',
            'title'         => 'Daftar Bahan Baku',
            'content'       => 'workflow/index',
            'table'         => 'tbl_kategori',
            'tampilData'    => $query_bahan->result(),
            'total_rows'    => $query_bahan->num_rows(),
            'workFlow'      => $this->M_master->tampilData('tbl_workflow', 'alur_urutan')->result(),
        );

        $this->load->view('layout_admin/head', $data);
        $this->load->view('layout_admin/header');
        $this->load->view('layout_admin/left_sidebar', $data);
        $this->load->view('admin/aset/index', $data);
        $this->load->view('layout_admin/footer');
    }

    public function kategori()
    {
        $table = 'tbl_kategori_assets';
        $query_kategori = $this->db->get($table);
        $data = array(
            'controller'    => 'aset',
            'function'      => $this->uri->segment(2) ?? 'kategori',
            'title'         => 'Daftar Kategori Assets',
            'content'       => 'workflow/index',
            'table'         => 'tbl_kategori',
            'tampilData'    => $query_kategori->result(),
            'total_rows'    => $query_kategori->num_rows(),
            'workFlow'      => $this->M_master->tampilData('tbl_workflow', 'alur_urutan')->result(),
        );

        $this->load->view('layout_admin/head', $data);
        $this->load->view('layout_admin/header');
        $this->load->view('layout_admin/left_sidebar', $data);
        $this->load->view('admin/assets/kategori', $data);
        $this->load->view('layout_admin/footer');
    }
}