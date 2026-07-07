<?php
class Chat extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Chat_model');
    }

    public function index()
    {
        // Since chat is now a global widget in the footer, 
        // accessing /chat retrieves the dashboard directly.
        redirect('dashboard');
    }

    public function get_chat()
    {
        header('Content-Type: application/json; charset=utf-8');
        $chat = $this->Chat_model->get_limit(100); // Limit to 100 to prevent crash
        echo json_encode($chat);
    }

    public function send()
    {
        header('Content-Type: application/json; charset=utf-8');
        $pesan_raw = $this->input->post('pesan', true);

        // Validasi tipe data dan pesan kosong
        if (empty($pesan_raw) || !is_string($pesan_raw) || trim($pesan_raw) === '') {
            echo json_encode(['status' => 'error', 'message' => 'Pesan kosong']);
            return;
        }

        // Sanitasi XSS (Cross-Site Scripting)
        $pesan_aman = htmlspecialchars(trim($pesan_raw), ENT_QUOTES, 'UTF-8');

        $data = [
            'pesan' => $pesan_aman,
            'user_id' => $this->session->userdata('id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->Chat_model->insert($data);
        echo json_encode(['status' => 'success']);
    }

    public function load()
    {
        $last_id_client = $this->input->get('last_id', true);

        // Lightweight check: ID terbaru di DB
        $latest_id_server = $this->Chat_model->get_latest_id();

        // Jika ID client >= ID server, berarti tidak ada pesan baru
        // Kita juga cek jika last_id_client ada (bukan null/0 pertama kali)
        if ($last_id_client !== null && $latest_id_server <= $last_id_client) {
            echo "NO_CHANGE";
            return;
        }

        $data['chat'] = $this->Chat_model->get_limit(50); // Optimization: only last 50
        $this->load->view('admin/chat_list', $data);
    }

}
