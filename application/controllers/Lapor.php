<?php
class Lapor extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }
    public function index()
    {
        $this->output->set_status_header(404);
        $this->load->view('errors/html/error_404_custom');
    }
}

?>