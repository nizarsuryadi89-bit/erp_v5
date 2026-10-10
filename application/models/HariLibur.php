<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HariLibur extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Check login session
        if (!$this->session->userdata('email')) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Please Login!</div>');
            redirect('auth');
        }
        $this->load->model('M_master');
    }

    public function index()
    {
        $userid = $this->session->userdata('id');
        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Data Hari Libur',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            'menu' => $this->db->get('menu')->result(),
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        // Fetch holidays order by date desc
        $this->db->order_by('tanggal', 'DESC');
        $data['hari_libur'] = $this->db->get('hari_libur')->result();

        $this->load->view('layout_admin/head', $data);
        $this->load->view('layout_admin/top_header', $data);
        $this->load->view('layout_admin/sidebar', $data);
        $this->load->view('admin/harilibur/index', $data);
        $this->load->view('layout_admin/footer');
    }

    public function store()
    {
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required|is_unique[hari_libur.tanggal]', [
            'is_unique' => 'Tanggal ini sudah terdaftar sebagai hari libur!'
        ]);
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('harilibur');
        } else {
            $data = [
                'tanggal' => $this->input->post('tanggal'),
                'keterangan' => $this->input->post('keterangan'),
            ];
            $this->db->insert('hari_libur', $data);
            $this->session->set_flashdata('success', 'Data hari libur berhasil ditambahkan!');
            redirect('harilibur');
        }
    }

    public function update()
    {
        $id = $this->input->post('id');
        $original_date = $this->input->post('original_tanggal');
        $new_date = $this->input->post('tanggal');

        $is_unique = '';
        if ($original_date != $new_date) {
            $is_unique = '|is_unique[hari_libur.tanggal]';
        }

        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required' . $is_unique);
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('harilibur');
        } else {
            $data = [
                'tanggal' => $new_date,
                'keterangan' => $this->input->post('keterangan'),
            ];
            $this->db->where('id', $id);
            $this->db->update('hari_libur', $data);
            $this->session->set_flashdata('success', 'Data hari libur berhasil diperbarui!');
            redirect('harilibur');
        }
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('hari_libur');
        $this->session->set_flashdata('success', 'Data hari libur berhasil dihapus!');
        redirect('harilibur');
    }
}
