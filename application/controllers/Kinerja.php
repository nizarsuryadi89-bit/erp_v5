<?php
class Kinerja extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_activity_model');
    }

    public function index()
    {
        $data['daily_summary'] = $this->User_activity_model->get_daily_activity_summary();
        $data['skor_kinerja'] = $this->User_activity_model->get_activity_score();
        $this->load->view('admin/karyawan/kinerja_view', $data);
    }
}
