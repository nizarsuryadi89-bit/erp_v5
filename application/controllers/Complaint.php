<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Complaint extends MY_Controller
{
    private $company;
    private $actor;
    private $manager;
    private $module_token;

    public function __construct()
    {
        parent::__construct();
        $this->config->load('complaint');
        $this->load->model('Complaint_model');
        $this->load->helper('complaint');
        $this->load->library(array('Complaint_policy', 'Whatsapp_export_parser', 'Complaint_processor'));
        $this->company = (int) $this->session->userdata('perusahaan_id');
        $this->actor = (int) $this->session->userdata('id');
        if ($this->session->userdata('logged_in') !== TRUE || !$this->company || !$this->actor) { show_error('Sesi tidak valid.', 401); }
        $active = $this->db->select('user_id')->get_where('users', array('user_id' => $this->actor, 'active' => 1))->row_array();
        if (!$active) { show_error('Akun tidak aktif.', 403); }
        $this->manager = !empty($this->data['is_developer']) || !empty($this->data['is_super_admin']) ||
            in_array((int) $this->session->userdata('level'), $this->config->item('complaint_manager_roles'), TRUE);
        $this->module_token = $this->session->userdata('complaint_csrf');
        if (!$this->module_token) {
            $this->module_token = bin2hex(random_bytes(32));
            $this->session->set_userdata('complaint_csrf', $this->module_token);
        }
        if (!$this->Complaint_model->ready()) {
            show_error('Modul Komplain belum dipasang. Jalankan php tools/complaint.php install di folder ERP lokal.', 503);
        }
    }

    private function view($view, $data = array())
    {
        $this->load_admin_view('admin/complaint/'.$view, array_merge(array('title' => 'TGM Complaint & Trouble Center',
            'complaint_manager' => $this->manager, 'complaint_csrf' => $this->module_token,
            'categories' => Complaint_policy::categories(), 'statuses' => Complaint_policy::statuses(),
            'priorities' => Complaint_policy::priorities()), $data));
    }

    private function mutation($manager = FALSE)
    {
        if ($this->input->method() !== 'post') { show_error('Gunakan POST.', 405); }
        if (!empty($this->data['is_readonly']) && empty($this->data['is_developer'])) { show_error('Mode baca tidak mengizinkan perubahan.', 403); }
        $token = $this->input->post('complaint_csrf');
        if (!is_string($token) || !hash_equals($this->module_token, $token)) { show_error('Sesi form tidak valid. Muat ulang halaman.', 403); }
        if ($manager && !$this->manager) { show_error('Hanya pengelola yang dapat menjalankan tindakan ini.', 403); }
    }

    private function text($name, $max = 8000, $default = '')
    {
        $value = $this->input->post($name);
        if ($value === NULL) { return $default; }
        if (!is_string($value) || !preg_match('//u', $value) || mb_strlen($value, 'UTF-8') > $max) {
            throw new InvalidArgumentException('Isian '.$name.' tidak valid atau terlalu panjang.');
        }
        return trim($value);
    }

    private function id($name)
    {
        $value = $this->input->post($name);
        if ($value === NULL || $value === '') { return NULL; }
        if (!is_string($value) || !ctype_digit($value) || strlen($value) > 10 || (int) $value < 1) {
            throw new InvalidArgumentException('ID '.$name.' tidak valid.');
        }
        return (int) $value;
    }

    private function filters()
    {
        $from = $this->input->get('from') ?: date('Y-m-01');
        $to = $this->input->get('to') ?: date('Y-m-d');
        try {
            Complaint_policy::date($from); Complaint_policy::date($to);
            if ($from > $to) { throw new InvalidArgumentException('Tanggal awal melampaui tanggal akhir.'); }
            $filters = array('from' => $from, 'to' => $to);
            foreach (array('status' => Complaint_policy::statuses(), 'priority' => Complaint_policy::priorities(), 'kategori' => Complaint_policy::categories()) as $key => $values) {
                $value = $this->input->get($key);
                if ($value && (!is_string($value) || !in_array($value, $values, TRUE))) { throw new InvalidArgumentException('Filter '.$key.' tidak valid.'); }
                $filters[$key] = $value ?: '';
            }
            $q = $this->input->get('q');
            if ($q !== NULL && (!is_string($q) || mb_strlen($q, 'UTF-8') > 100)) { throw new InvalidArgumentException('Pencarian terlalu panjang.'); }
            $filters['q'] = trim($q ?: '');
            $filters['review'] = $this->input->get('review') === '1';
            return $filters;
        } catch (InvalidArgumentException $e) { show_error(html_escape($e->getMessage()), 422); }
    }

    private function page()
    {
        $page = $this->input->get('page');
        return is_string($page) && ctype_digit($page) ? max(1, min(100000, (int) $page)) : 1;
    }

    private function error($e, $url)
    {
        if ($e instanceof InvalidArgumentException) { $message = $e->getMessage(); }
        else { $message = 'Penyimpanan gagal. Periksa log ERP atau konfigurasi database lokal.'; log_message('error', 'Complaint action failed.'); }
        $this->session->set_flashdata('complaint_error', $message);
        redirect($url);
    }

    public function index()
    {
        $filters = $this->filters();
        $this->view('index', array('filters' => $filters,
            'report' => $this->Complaint_model->report($this->company, $filters['from'], $filters['to']),
            'listing' => $this->Complaint_model->listing($this->company, $filters, $this->page())));
    }

    public function create()
    {
        $this->view('create', array('machines' => $this->Complaint_model->machines($this->company)));
    }

    public function save()
    {
        $this->mutation();
        try {
            $spks = preg_split('/[\s,;]+/', $this->text('spks', 1200), -1, PREG_SPLIT_NO_EMPTY);
            $fields = array('judul' => $this->text('judul', 191), 'pelapor' => $this->text('pelapor', 191),
                'deskripsi' => $this->text('deskripsi', 16000), 'kategori' => $this->text('kategori', 40),
                'priority' => $this->text('priority', 10), 'mesin_id' => $this->id('mesin_id'),
                'reported_at' => date('Y-m-d H:i:s'), 'spks' => $spks);
            $id = $this->Complaint_model->create($this->company, $fields, $this->actor);
            $this->_log_manual('Complaint ticket created', json_encode(array('ticket_id' => $id, 'perusahaan_id' => $this->company)));
            redirect('complaint/detail/'.$id);
        } catch (Throwable $e) { $this->error($e, 'complaint/create'); }
    }

    public function detail($id = NULL)
    {
        if (!is_string($id) || !ctype_digit($id)) { show_404(); }
        $ticket = $this->Complaint_model->ticket($this->company, $id);
        if (!$ticket) { show_404(); }
        $can_edit = ($this->manager || (int) $ticket['pic_id'] === $this->actor) && empty($this->data['is_readonly']);
        $this->view('detail', array('ticket' => $ticket, 'can_edit' => $can_edit,
            'history' => $this->Complaint_model->history($this->company, $id), 'evidence' => $this->Complaint_model->evidence($this->company, $id),
            'spks' => $this->Complaint_model->spks($this->company, $id),
            'users' => $this->Complaint_model->users($this->company), 'machines' => $this->Complaint_model->machines($this->company)));
    }

    public function update($id = NULL)
    {
        $this->mutation();
        if (!is_string($id) || !ctype_digit($id)) { show_404(); }
        try {
            $fields = array('judul' => $this->text('judul', 191), 'pelapor' => $this->text('pelapor', 191),
                'deskripsi' => $this->text('deskripsi', 16000), 'kategori' => $this->text('kategori', 40),
                'priority' => $this->text('priority', 10), 'status' => $this->text('status', 20),
                'mesin_id' => $this->id('mesin_id'), 'pic_id' => $this->id('pic_id'), 'version' => $this->id('version'),
                'resolution' => $this->text('resolution'), 'root_cause' => $this->text('root_cause'),
                'preventive_action' => $this->text('preventive_action'), 'note' => $this->text('note'),
                'needs_review' => $this->input->post('reviewed') === '1' ? 0 : 1,
                'spks' => preg_split('/[\s,;]+/', $this->text('spks', 1200), -1, PREG_SPLIT_NO_EMPTY));
            $this->Complaint_model->update_ticket($this->company, $id, $fields, $this->actor, $this->manager);
            $this->_log_manual('Complaint ticket updated', json_encode(array('ticket_id' => $id, 'perusahaan_id' => $this->company)));
            $this->session->set_flashdata('complaint_success', 'Penanganan tiket diperbarui.');
            redirect('complaint/detail/'.$id);
        } catch (Throwable $e) { $this->error($e, 'complaint/detail/'.$id); }
    }

    public function import()
    {
        $this->view('import');
    }

    private function import_input()
    {
        $conversation = $this->text('conversation', 191);
        if ($conversation === '' || strlen($conversation) > 191) { throw new InvalidArgumentException('Isi nama/ID percakapan (maksimal 191 byte).'); }
        $order = $this->text('date_order', 3, 'DMY');
        $text = $this->text('chat_text', 2097152);
        if (isset($_FILES['chat_file']) && $_FILES['chat_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['chat_file'];
            if (!is_scalar($file['error']) || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name']) ||
                $file['size'] > $this->config->item('complaint_max_import_bytes') || strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'txt') {
                throw new InvalidArgumentException('Unggah file .txt maksimal 2 MB.');
            }
            $text = file_get_contents($file['tmp_name']);
        }
        if (strlen($text) > $this->config->item('complaint_max_import_bytes')) { throw new InvalidArgumentException('Chat maksimal 2 MB.'); }
        $parsed = $this->whatsapp_export_parser->parse($text, $order, $this->config->item('complaint_max_import_messages'));
        return array('conversation' => $conversation, 'parsed' => $parsed, 'date_order' => $order);
    }

    public function preview()
    {
        $this->mutation(TRUE);
        try {
            $batch = $this->import_input();
            $this->load->library('Complaint_analyzer');
            $rows = array_slice($batch['parsed']['messages'], 0, 25);
            foreach ($rows as &$row) { $row['analysis'] = $this->complaint_analyzer->rules($row['body']); }
            unset($row);
            // Store only a hash of the preview, not the private chat in session.
            $hash = $this->import_hash($batch);
            $this->session->set_userdata('complaint_import_preview', array('hash' => $hash, 'company' => $this->company, 'expires' => time() + 1800));
            $this->view('import', array('preview' => $batch, 'preview_rows' => $rows, 'preview_hash' => $hash));
        } catch (Throwable $e) { $this->error($e, 'complaint/import'); }
    }

    public function import_save()
    {
        $this->mutation(TRUE);
        try {
            $batch = $this->import_input();
            $hash = $this->import_hash($batch);
            $stored = $this->session->userdata('complaint_import_preview');
            if (!$stored || $stored['expires'] < time() || (int) $stored['company'] !== $this->company || !hash_equals($stored['hash'], $hash)) {
                throw new InvalidArgumentException('Pratinjau berubah atau kedaluwarsa. Periksa chat kembali sebelum impor.');
            }
            $added = 0; $duplicates = 0;
            $this->db->trans_begin();
            foreach ($batch['parsed']['messages'] as $message) {
                $key = hash('sha256', 'import'."\n".$batch['conversation']."\n".$message['sent_at']."\n".$message['sender']."\n".$message['body']);
                if ($this->Complaint_model->ingest($this->company, 'import', $key, $batch['conversation'], $message['sender'], $message['sent_at'], $message['body'])) { $added++; }
                else { $duplicates++; }
            }
            if (!$this->db->trans_status()) { throw new RuntimeException('Impor gagal.'); }
            $this->db->trans_commit();
            $this->session->unset_userdata('complaint_import_preview');
            $result = $this->complaint_processor->run($this->config->item('complaint_offline_batch'), $this->company, FALSE);
            $this->_log_manual('WhatsApp complaint import', json_encode(array('perusahaan_id' => $this->company, 'added' => $added, 'duplicates' => $duplicates)));
            $this->session->set_flashdata('complaint_success', $added.' pesan baru disimpan, '.$duplicates.' duplikat dilewati, '.$result['processed'].' pesan diproses offline. Lanjutkan antrean di Kotak Masuk.');
            redirect('complaint/inbox');
        } catch (Throwable $e) { $this->db->trans_rollback(); $this->error($e, 'complaint/import'); }
    }

    public function inbox()
    {
        $this->view('inbox', array('inbox' => $this->Complaint_model->inbox($this->company, $this->page())));
    }

    private function import_hash($batch)
    {
        return hash('sha256', json_encode(array($this->company, $batch['conversation'], $batch['date_order'], $batch['parsed']['messages']), JSON_UNESCAPED_UNICODE));
    }

    public function process()
    {
        $this->mutation(TRUE);
        try {
            $result = $this->complaint_processor->run($this->config->item('complaint_offline_batch'), $this->company, FALSE);
            $this->session->set_flashdata('complaint_success', $result['processed'].' pesan diproses; '.$result['failed'].' gagal.');
            redirect('complaint/inbox');
        } catch (Throwable $e) { $this->error($e, 'complaint/inbox'); }
    }

    public function link()
    {
        $this->mutation(TRUE);
        try {
            $message = $this->id('message_id');
            if (!$message) { throw new InvalidArgumentException('Pesan tidak valid.'); }
            $id = $this->Complaint_model->link_message($this->company, $message, $this->id('ticket_id'), $this->actor);
            redirect('complaint/detail/'.$id);
        } catch (Throwable $e) { $this->error($e, 'complaint/inbox'); }
    }

    public function retry()
    {
        $this->mutation(TRUE);
        try { $this->Complaint_model->retry($this->company, $this->id('message_id')); redirect('complaint/inbox'); }
        catch (Throwable $e) { $this->error($e, 'complaint/inbox'); }
    }

    public function summary()
    {
        $filters = $this->filters();
        $this->view('summary', array('filters' => $filters,
            'report' => $this->Complaint_model->report($this->company, $filters['from'], $filters['to'])));
    }

    public function download_summary()
    {
        $filters = $this->filters();
        $report = $this->Complaint_model->report($this->company, $filters['from'], $filters['to']);
        $this->output->set_content_type('text/plain', 'UTF-8')->set_header('Content-Disposition: attachment; filename="TGM-Ringkasan-'.$filters['from'].'-'.$filters['to'].'.txt"')
            ->set_header('Cache-Control: no-store')->set_output($report['text']);
    }
}
