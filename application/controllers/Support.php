<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Support extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_master');
        $this->load->helper('whatsapp');
        // Pastikan user login sudah dihandle di MY_Controller
    }

    public function index()
    {
        $user_id = $this->session->userdata('id');
        $is_developer = ($this->session->userdata('username') === 'nizarsuryadi');
        $level = $this->session->userdata('level');

        // Check if Super Admin or Developer
        $is_admin = false;
        $role_check = $this->db->get_where('tbl_divisi', ['divisi_id' => $level])->row();
        if (($role_check && $role_check->is_super_admin == 1) || $is_developer) {
            $is_admin = true;
        }

        if ($is_admin) {
            // Admin sees all tickets
            $this->db->select('t.*, u.username, k.nama_lengkap');
            $this->db->from('tbl_support_tickets t');
            $this->db->join('users u', 'u.user_id = t.user_id', 'left');
            $this->db->join('tbl_karyawan k', 'k.karyawan_id = u.karyawan_id', 'left');
            $this->db->order_by('t.created_at', 'DESC');
            $data['tickets'] = $this->db->get()->result();
            $data['is_admin'] = true;
        } else {
            // User sees their own tickets
            $this->db->where('user_id', $user_id);
            $this->db->order_by('created_at', 'DESC');
            $data['tickets'] = $this->db->get('tbl_support_tickets')->result();
            $data['is_admin'] = false;
        }

        $data['title'] = 'Support Ticket System';
        $this->load_admin_view('admin/support/index', $data);
    }

    public function create()
    {
        $data['title'] = 'Create New Ticket / Report';
        $data['modules'] = $this->db->where('status', '1')->order_by('nama_menu', 'ASC')->get('menu')->result();
        $this->load_admin_view('admin/support/create', $data);
    }

    public function store()
    {
        $ticket_number = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $data = [
            'ticket_number' => $ticket_number,
            'user_id' => $this->session->userdata('id'),
            'subject' => $this->input->post('subject', true),
            'module_name' => $this->input->post('module_name', true),
            'description' => $this->input->post('description', true),
            'category' => $this->input->post('category', true),
            'priority' => $this->input->post('priority', true),
            'status' => 'OPEN'
        ];

        // Handle File Upload if any
        if (!empty($_FILES['attachment']['name'])) {
            $path = FCPATH . 'assets/uploads/support/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|zip|xls|xlsx|csv';
            $config['encrypt_name'] = TRUE;
            $config['max_size'] = 10240; // 10MB

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('attachment')) {
                $upload_data = $this->upload->data();
                $data['attachment'] = $upload_data['file_name'];
            } else {
                // Log Error if needed
                log_message('error', 'Support Ticket Upload Error: ' . $this->upload->display_errors());
            }
        }

        $this->db->insert('tbl_support_tickets', $data);

        // WhatsApp Notification
        $this->_send_support_notification('NEW_TICKET', $data);

        $this->session->set_flashdata('success', 'Ticket successfully created. Our developers will review it soon.');
        redirect('support');
    }

    public function detail($id)
    {
        $this->db->select('t.*, u.username, k.nama_lengkap');
        $this->db->from('tbl_support_tickets t');
        $this->db->join('users u', 'u.user_id = t.user_id', 'left');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = u.karyawan_id', 'left');
        $this->db->where('t.id', $id);
        $ticket = $this->db->get()->row();

        if (!$ticket) {
            redirect('support');
        }

        // Check Access
        $user_id = $this->session->userdata('id');
        $level = $this->session->userdata('level');
        $is_developer = ($this->session->userdata('username') === 'nizarsuryadi');

        $is_admin = false;
        $role_check = $this->db->get_where('tbl_divisi', ['divisi_id' => $level])->row();
        if (($role_check && $role_check->is_super_admin == 1) || $is_developer) {
            $is_admin = true;
        }

        if (!$is_admin && $ticket->user_id != $user_id) {
            $this->session->set_flashdata('error', 'Unauthorized Access');
            redirect('support');
        }

        // Get Replies
        $this->db->select('r.*, u.username, k.nama_lengkap, d.divisi_nama as role_name');
        $this->db->from('tbl_support_replies r');
        $this->db->join('users u', 'u.user_id = r.user_id', 'left');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = u.karyawan_id', 'left');
        $this->db->join('tbl_divisi d', 'd.divisi_id = u.role_id', 'left'); // In this system, role_id in users table refers to divisi_id
        $this->db->where('r.ticket_id', $id);
        $this->db->order_by('r.created_at', 'ASC');
        $replies = $this->db->get()->result();

        $data['title'] = $ticket->ticket_number . ' - ' . $ticket->subject;
        $data['ticket'] = $ticket;
        $data['replies'] = $replies;
        $data['is_admin'] = $is_admin;

        $this->load_admin_view('admin/support/detail', $data);
    }

    public function reply($id)
    {
        $ticket = $this->db->get_where('tbl_support_tickets', ['id' => $id])->row();
        if (!$ticket)
            redirect('support');

        $data = [
            'ticket_id' => $id,
            'user_id' => $this->session->userdata('id'),
            'message' => $this->input->post('message', true)
        ];

        // Handle File Upload
        if (!empty($_FILES['attachment']['name'])) {
            $path = FCPATH . 'assets/uploads/support/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|zip|xls|xlsx|csv';
            $config['encrypt_name'] = TRUE;
            $config['max_size'] = 10240; // 10MB

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('attachment')) {
                $upload_data = $this->upload->data();
                $data['attachment'] = $upload_data['file_name'];
            }
        }

        if ($data['message']) {
            $this->db->insert('tbl_support_replies', $data);

            // WhatsApp Notification for Reply
            $this->_send_support_notification('REPLY', $data);
        }

        // Update Ticket status or notes if admin
        $is_developer = ($this->session->userdata('username') === 'nizarsuryadi');
        $level = $this->session->userdata('level');
        $is_admin = false;
        $role_check = $this->db->get_where('tbl_divisi', ['divisi_id' => $level])->row();
        if (($role_check && $role_check->is_super_admin == 1) || $is_developer) {
            $is_admin = true;
        }

        if ($is_admin) {
            $update = [];
            if ($this->input->post('status', true)) {
                $update['status'] = $this->input->post('status', true);
            }
            if ($this->input->post('developer_notes', true)) {
                $update['developer_notes'] = $this->input->post('developer_notes', true);
            }
            if (!empty($update)) {
                $this->db->where('id', $id)->update('tbl_support_tickets', $update);
            }
        } else {
            // If user replies, set status back to OPEN or IN-PROGRESS if it was PENDING
            if ($ticket->status == 'PENDING') {
                $this->db->where('id', $id)->update('tbl_support_tickets', ['status' => 'IN-PROGRESS']);
            }
        }

        $this->session->set_flashdata('success', 'Reply posted successfully.');
        redirect('support/detail/' . $id);
    }

    // Helper for DB Setup
    public function setup_db()
    {
        // Only Developer/Admin can run setup
        $is_developer = ($this->session->userdata('username') === 'nizarsuryadi');
        if (!$is_developer)
            die("Access Restricted");

        $queries = [
            "CREATE TABLE IF NOT EXISTS `tbl_support_tickets` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `ticket_number` varchar(20) NOT NULL,
                `user_id` int(11) NOT NULL,
                `subject` varchar(255) NOT NULL,
                `module_name` varchar(100) DEFAULT NULL,
                `description` text NOT NULL,
                `category` enum('BUG','FEATURE','SUPPORT','OTHER') NOT NULL DEFAULT 'SUPPORT',
                `priority` enum('LOW','MEDIUM','HIGH','URGENT') NOT NULL DEFAULT 'MEDIUM',
                `status` enum('OPEN','PENDING','IN-PROGRESS','RESOLVED','CLOSED') NOT NULL DEFAULT 'OPEN',
                `developer_notes` text,
                `attachment` varchar(255) DEFAULT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `ticket_number` (`ticket_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            "CREATE TABLE IF NOT EXISTS `tbl_support_replies` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `ticket_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `message` text NOT NULL,
                `attachment` varchar(255) DEFAULT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `ticket_id` (`ticket_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
        ];

        foreach ($queries as $query) {
            $this->db->query($query);
        }

        echo "Database tables created successfully. <a href='" . base_url('support') . "'>Go to Support System</a>";
    }

    private function _send_support_notification($type, $payload)
    {
        $username = ucfirst($this->session->userdata('username'));
        $datetime = date('d/m/Y H:i');
        $target_email = 'nizarsuryadi89@gmail.com';

        // 1. Get Setting for Branding/WA
        $setting = $this->db->get_where('tbl_wa_setting', ['id' => 1])->row();

        $wa_message = "";
        $email_subject = "";
        $email_body = "";

        if ($type == 'NEW_TICKET') {
            // WA Content
            $wa_message = "🔔 *SUPPORT TICKET BARU*\n\n";
            $wa_message .= "Ticket: #{$payload['ticket_number']}\n";
            $wa_message .= "Dari: {$username}\n";
            $wa_message .= "Modul: {$payload['module_name']}\n";
            $wa_message .= "Subject: {$payload['subject']}\n";
            $wa_message .= "Prioritas: *{$payload['priority']}*\n";
            $wa_message .= "Waktu: {$datetime}\n\n";
            $wa_message .= "Silakan cek di dashboard ERP.";

            // Email Content
            $email_subject = "[SUPPORT] Ticket Baru: #{$payload['ticket_number']} - {$payload['subject']}";
            $email_body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #0d6efd;'>Notifikasi Support Ticket</h2>
                    <p>Halo Developer, ada ticket baru yang memerlukan review Anda.</p>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr><td style='width: 120px; font-weight: bold;'>Nomor Ticket</td><td>: #{$payload['ticket_number']}</td></tr>
                        <tr><td style='font-weight: bold;'>Pengirim</td><td>: {$username}</td></tr>
                        <tr><td style='font-weight: bold;'>Modul Terkait</td><td>: {$payload['module_name']}</td></tr>
                        <tr><td style='font-weight: bold;'>Subject</td><td>: {$payload['subject']}</td></tr>
                        <tr><td style='font-weight: bold;'>Prioritas</td><td>: <b>{$payload['priority']}</b></td></tr>
                    </table>
                    <div style='margin-top: 15px; padding: 10px; background: #f8f9fa; border-left: 4px solid #0d6efd;'>
                        <strong>Deskripsi:</strong><br>
                        " . nl2br($payload['description']) . "
                    </div>
                    <p style='margin-top: 20px;'>
                        <a href='" . base_url('support') . "' style='background: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Buka Dashboard Support</a>
                    </p>
                </div>
            ";

        } else if ($type == 'REPLY') {
            $is_developer = ($this->session->userdata('username') === 'nizarsuryadi');
            $level = $this->session->userdata('level');
            $role_check = $this->db->get_where('tbl_divisi', ['divisi_id' => $level])->row();
            $is_admin = (($role_check && $role_check->is_super_admin == 1) || $is_developer);

            if ($is_admin)
                return;

            $ticket = $this->db->get_where('tbl_support_tickets', ['id' => $payload['ticket_id']])->row();

            // WA Content
            $wa_message = "💬 *BALASAN SUPPORT TICKET*\n\n";
            $wa_message .= "Ticket: #{$ticket->ticket_number}\n";
            $wa_message .= "Dari: {$username}\n";
            $wa_message .= "Pesan: \"" . substr($payload['message'], 0, 50) . "...\"\n";
            $wa_message .= "Waktu: {$datetime}\n\n";
            $wa_message .= "Segera respon di dashboard ERP.";

            // Email Content
            $email_subject = "[SUPPORT] Balasan User: #{$ticket->ticket_number}";
            $email_body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #198754;'>Balasan Ticket Baru</h2>
                    <p>User <b>{$username}</b> telah membalas ticket <b>#{$ticket->ticket_number}</b>.</p>
                    <div style='margin-top: 15px; padding: 10px; background: #f8f9fa; border-left: 4px solid #198754;'>
                        <strong>Isi Pesan:</strong><br>
                        " . nl2br($payload['message']) . "
                    </div>
                    <p style='margin-top: 20px;'>
                        <a href='" . base_url('support/detail/' . $ticket->id) . "' style='background: #198754; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Balas di ERP</a>
                    </p>
                </div>
            ";
        }

        // --- Proses Kirim WA ---
        if ($setting && !empty($setting->manager_number) && !empty($wa_message) && function_exists('send_wa')) {
            send_wa($setting->manager_number, $wa_message);
        }

        // --- Proses Kirim Email ---
        if (!empty($email_subject)) {
            $this->load->library('email');

            // Konfigurasi SMTP Gmail
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'ssl://smtp.googlemail.com';
            $config['smtp_user'] = 'nizarsuryadi89@gmail.com'; // Email Anda sebagai pengirim
            $config['smtp_pass'] = 'hhfo vcvh xaih ummg';   // 16 digit App Password (BUKAN password login)
            $config['smtp_port'] = 465;
            $config['smtp_timeout'] = 30;
            $config['mailtype'] = 'html';
            $config['charset'] = 'utf-8';
            $config['newline'] = "\r\n";
            $config['wordwrap'] = TRUE;

            $this->email->initialize($config);
            $this->email->from($config['smtp_user'], 'ERP Support System');
            $this->email->to($target_email);
            $this->email->subject($email_subject);
            $this->email->message($email_body);

            // Hapus '@' jika ingin melihat eror pengiriman (untuk debug)
            if (!$this->email->send()) {
                log_message('error', $this->email->print_debugger());
            }
        }
    }
}
