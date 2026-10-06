<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'libraries/Complaint_policy.php';

class Complaint_model extends CI_Model
{
    public function ready()
    {
        foreach (array('tbl_complaint', 'tbl_wa_complaint_messages', 'tbl_complaint_spk', 'tbl_complaint_history') as $table) {
            if (!$this->db->table_exists($table)) { return FALSE; }
        }
        return TRUE;
    }

    public function users($company)
    {
        // Use the employee's branch, as ERP v5 does at login. Some DB versions
        // store the fallback branch as users.perusahaan_id1.
        $column = $this->db->field_exists('perusahaan_id1', 'users') ? 'perusahaan_id1' : 'perusahaan_id';
        return $this->db->select('u.user_id, u.username, k.nama_lengkap')->from('users u')
            ->join('tbl_karyawan k', 'k.karyawan_id = u.karyawan_id', 'left')
            ->where('u.active', 1)->where('COALESCE(NULLIF(k.perusahaan_id, 0), u.'.$column.') = '.(int) $company, NULL, FALSE)
            ->order_by('u.username', 'ASC')->get()->result_array();
    }

    public function machines($company)
    {
        return $this->db->select('mesin_id, mesin_nama')->where('perusahaan_id', $company)
            ->order_by('mesin_nama', 'ASC')->get('tbl_mesin')->result_array();
    }

    public function ticket($company, $id, $lock = FALSE)
    {
        if ($lock) {
            return $this->db->query('SELECT * FROM tbl_complaint WHERE perusahaan_id = ? AND complaint_id = ? FOR UPDATE',
                array($company, $id))->row_array();
        }
        return $this->db->get_where('tbl_complaint', array('perusahaan_id' => $company, 'complaint_id' => $id))->row_array();
    }

    public function history($company, $id)
    {
        return $this->db->select('h.*, u.username')->from('tbl_complaint_history h')
            ->join('users u', 'u.user_id = h.actor_id', 'left')->where('h.perusahaan_id', $company)
            ->where('h.complaint_id', $id)->order_by('h.id', 'ASC')->get()->result_array();
    }

    public function evidence($company, $id)
    {
        return $this->db->where('perusahaan_id', $company)->where('ticket_id', $id)
            ->order_by('sent_at', 'ASC')->get('tbl_wa_complaint_messages')->result_array();
    }

    public function spks($company, $id)
    {
        return $this->db->select('nospk')->get_where('tbl_complaint_spk',
            array('perusahaan_id' => $company, 'complaint_id' => $id))->result_array();
    }

    private function scope($company, $filters)
    {
        $this->db->where('c.perusahaan_id', $company);
        $this->db->where('c.reported_at >=', $filters['from'].' 00:00:00');
        $this->db->where('c.reported_at <', date('Y-m-d', strtotime($filters['to'].' +1 day')).' 00:00:00');
        foreach (array('status', 'priority', 'kategori') as $field) {
            if (!empty($filters[$field])) { $this->db->where('c.'.$field, $filters[$field]); }
        }
        if (!empty($filters['review'])) { $this->db->where('c.needs_review', 1); }
        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $this->db->group_start()->like('c.ticket_no', $q)->or_like('c.judul', $q)->or_like('c.pelapor', $q)
                ->or_where('EXISTS (SELECT 1 FROM tbl_complaint_spk cs WHERE cs.complaint_id = c.complaint_id AND cs.perusahaan_id = '.(int) $company.' AND cs.nospk LIKE '.$this->db->escape('%'.$this->db->escape_like_str($q).'%')." ESCAPE '!')", NULL, FALSE)
                ->group_end();
        }
    }

    public function listing($company, $filters, $page)
    {
        $this->scope($company, $filters);
        $total = $this->db->from('tbl_complaint c')->count_all_results();
        $this->scope($company, $filters);
        $rows = $this->db->select('c.*, u.username AS pic_name')->from('tbl_complaint c')
            ->join('users u', 'u.user_id = c.pic_id', 'left')->order_by('c.reported_at', 'DESC')
            ->order_by('c.complaint_id', 'DESC')->limit(25, ($page - 1) * 25)->get()->result_array();
        return array('total' => $total, 'rows' => $rows, 'page' => $page, 'pages' => max(1, (int) ceil($total / 25)));
    }

    public function report($company, $from, $to)
    {
        $filters = array('from' => $from, 'to' => $to);
        $this->scope($company, $filters);
        $counts = $this->db->select('c.status, COUNT(*) AS total')->from('tbl_complaint c')
            ->group_by('c.status')->get()->result_array();
        $this->scope($company, $filters);
        $cats = $this->db->select('c.kategori, COUNT(*) AS total')->from('tbl_complaint c')
            ->group_by('c.kategori')->order_by('total', 'DESC')->get()->result_array();
        $this->scope($company, $filters);
        $critical = $this->db->where('c.priority', 'CRITICAL')->from('tbl_complaint c')->count_all_results();
        $this->scope($company, $filters);
        $unassigned = $this->db->where('c.pic_id IS NULL', NULL, FALSE)->where_not_in('c.status', array('RESOLVED', 'CLOSED'))
            ->from('tbl_complaint c')->count_all_results();
        $this->scope($company, $filters);
        $review = $this->db->where('c.needs_review', 1)->from('tbl_complaint c')->count_all_results();
        $this->scope($company, $filters);
        $affected = $this->db->select('COUNT(DISTINCT cs.nospk) AS total', FALSE)->from('tbl_complaint c')
            ->join('tbl_complaint_spk cs', 'cs.complaint_id = c.complaint_id AND cs.perusahaan_id = c.perusahaan_id')
            ->get()->row_array();
        $this->scope($company, $filters);
        $machines = $this->db->select('m.mesin_nama, COUNT(*) AS total')->from('tbl_complaint c')
            ->join('tbl_mesin m', 'm.mesin_id = c.mesin_id AND m.perusahaan_id = c.perusahaan_id')
            ->group_by('m.mesin_id, m.mesin_nama')->order_by('total', 'DESC')->limit(10)->get()->result_array();
        $this->scope($company, $filters);
        $pending = $this->db->select('c.ticket_no, c.judul, c.priority, c.status, u.username AS pic_name')->from('tbl_complaint c')
            ->join('users u', 'u.user_id = c.pic_id', 'left')->where_not_in('c.status', array('RESOLVED', 'CLOSED'))
            ->order_by("FIELD(c.priority, 'CRITICAL', 'HIGH', 'MEDIUM', 'LOW')", '', FALSE)
            ->order_by('c.reported_at', 'ASC')->limit(10)->get()->result_array();
        $status = array_fill_keys(Complaint_policy::statuses(), 0);
        foreach ($counts as $count) { $status[$count['status']] = (int) $count['total']; }
        $total = array_sum($status);
        $text = 'Ringkasan TGM Print — '.$from.' s.d. '.$to."\n";
        $text .= 'Terdapat '.$total.' tiket yang dilaporkan pada periode ini. Status saat laporan dibuat: '.
            $status['OPEN'].' terbuka, '.$status['ASSIGNED'].' ditugaskan, '.$status['ON_PROGRESS'].' dalam proses, '.
            $status['RESOLVED'].' terselesaikan, dan '.$status['CLOSED'].' ditutup. ';
        $text .= $unassigned.' tiket aktif belum memiliki PIC; '.$review.' tiket memerlukan tinjauan. ';
        if ($cats) { $text .= 'Kategori terbanyak: '.$cats[0]['kategori'].' ('.$cats[0]['total'].' tiket). '; }
        $text .= 'Ada '.$critical.' tiket berprioritas kritis dan '.(int) $affected['total'].' SPK terkait. SPK terkait belum tentu mengalami keterlambatan.';
        if ($pending) {
            $text .= "\n\nMaksimal 10 tiket aktif untuk tindak lanjut:\n";
            foreach ($pending as $p) {
                $text .= '- '.$p['ticket_no'].' ['.$p['priority'].'] '.$p['judul'].' — PIC: '.($p['pic_name'] ?: 'Belum ditetapkan')."\n";
            }
        }
        return array('total' => $total, 'status' => $status, 'categories' => $cats, 'critical' => $critical,
            'unassigned' => $unassigned, 'review' => $review, 'affected_spks' => (int) $affected['total'],
            'machines' => $machines, 'pending' => $pending, 'text' => trim($text));
    }

    public function inbox($company, $page)
    {
        $total = $this->db->where('perusahaan_id', $company)->count_all_results('tbl_wa_complaint_messages');
        $rows = $this->db->select('m.*, c.ticket_no')->from('tbl_wa_complaint_messages m')
            ->join('tbl_complaint c', 'c.complaint_id = m.ticket_id AND c.perusahaan_id = m.perusahaan_id', 'left')
            ->where('m.perusahaan_id', $company)->order_by('m.id', 'DESC')->limit(25, ($page - 1) * 25)->get()->result_array();
        $pending = $this->db->where('perusahaan_id', $company)->where('state', 'pending')->count_all_results('tbl_wa_complaint_messages');
        return array('total' => $total, 'rows' => $rows, 'pending' => $pending, 'page' => $page, 'pages' => max(1, (int) ceil($total / 25)));
    }

    public function ingest($company, $source, $key, $conversation, $sender, $sent_at, $body)
    {
        foreach (array($conversation, $sender, $body, $key, $sent_at) as $value) {
            if (!is_string($value) || trim($value) === '' || !preg_match('//u', $value)) {
                throw new InvalidArgumentException('Data pesan wajib berupa teks UTF-8 dan tidak boleh kosong.');
            }
        }
        if ($company < 1 || !in_array($source, array('import', 'webhook'), TRUE) || strlen($key) !== 64 ||
            strlen($conversation) > 191 || strlen($sender) > 191 || strlen($body) > 16000) {
            throw new InvalidArgumentException('Data pesan melampaui batas atau sumber tidak valid.');
        }
        $d = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $sent_at);
        if (!$d || $d->format('Y-m-d H:i:s') !== $sent_at) { throw new InvalidArgumentException('Waktu pesan tidak valid.'); }
        $ok = $this->db->query('INSERT INTO tbl_wa_complaint_messages (perusahaan_id, source, external_key, conversation, sender, sent_at, body, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE id = id',
            array($company, $source, $key, $conversation, $sender, $sent_at, $body, date('Y-m-d H:i:s')));
        if (!$ok) { throw new RuntimeException('Pesan gagal disimpan.'); }
        return $this->db->affected_rows() === 1;
    }

    public function claim($company = NULL)
    {
        $expired_scope = $company !== NULL ? ' AND perusahaan_id = ?' : '';
        $cutoff = date('Y-m-d H:i:s', time() - 300);
        $expired_params = $company !== NULL ? array($cutoff, $company) : array($cutoff);
        $this->db->query("UPDATE tbl_wa_complaint_messages SET state = 'failed', lock_token = NULL, locked_at = NULL, analysis_error = 'Batas pemrosesan tercapai; coba ulang.' WHERE state = 'processing' AND attempts >= 3 AND locked_at < ?".$expired_scope, $expired_params);
        $this->db->trans_begin();
        $params = array($cutoff);
        $scope = $company !== NULL ? ' AND perusahaan_id = ?' : '';
        if ($company !== NULL) { $params[] = $company; }
        $row = $this->db->query("SELECT * FROM tbl_wa_complaint_messages WHERE attempts < 3 AND (state = 'pending' OR (state = 'processing' AND locked_at < ?))".$scope.' ORDER BY id ASC LIMIT 1 FOR UPDATE', $params)->row_array();
        if (!$row) { $this->db->trans_commit(); return NULL; }
        $token = bin2hex(random_bytes(16));
        $this->db->where('id', $row['id'])->update('tbl_wa_complaint_messages', array('state' => 'processing',
            'locked_at' => date('Y-m-d H:i:s'), 'lock_token' => $token, 'attempts' => $row['attempts'] + 1));
        $this->finish_transaction();
        $row['lock_token'] = $token;
        return $row;
    }

    public function complete_analysis($message, $analysis)
    {
        $this->db->trans_begin();
        try {
            $row = $this->db->query('SELECT * FROM tbl_wa_complaint_messages WHERE id = ? AND perusahaan_id = ? AND lock_token = ? AND state = ? FOR UPDATE',
                array($message['id'], $message['perusahaan_id'], $message['lock_token'], 'processing'))->row_array();
            if (!$row) { $this->db->trans_rollback(); return FALSE; }
            $ticket_id = $row['ticket_id'];
            if (!$ticket_id && in_array($analysis['classification'], array('complaint', 'trouble'), TRUE)) {
                $ticket_id = $this->insert_ticket($row['perusahaan_id'], array(
                    'pelapor' => $row['sender'], 'judul' => $analysis['title'], 'deskripsi' => $row['body'],
                    'ai_summary' => $analysis['summary'], 'kategori' => $analysis['category'],
                    'priority' => $analysis['priority'], 'analysis_method' => $analysis['method'],
                    'wa_message_id' => $row['id'], 'reported_at' => $row['sent_at']), NULL);
                $this->replace_spks($row['perusahaan_id'], $ticket_id, $analysis['spks'], FALSE);
            }
            $this->db->where('id', $row['id'])->update('tbl_wa_complaint_messages', array('state' => 'analyzed',
                'classification' => $analysis['classification'], 'category' => $analysis['category'],
                'priority' => $analysis['priority'], 'summary' => $analysis['summary'],
                'analysis_method' => $analysis['method'], 'analysis_error' => $analysis['error'],
                'ticket_id' => $ticket_id, 'locked_at' => NULL, 'lock_token' => NULL));
            $this->finish_transaction();
            return TRUE;
        } catch (Throwable $e) { $this->db->trans_rollback(); throw $e; }
    }

    public function fail_analysis($message)
    {
        $this->db->where('id', $message['id'])->where('lock_token', $message['lock_token'])->where('perusahaan_id', $message['perusahaan_id'])
            ->set('state', "IF(attempts >= 3, 'failed', 'pending')", FALSE)
            ->update('tbl_wa_complaint_messages', array('locked_at' => NULL, 'lock_token' => NULL,
                'analysis_error' => 'Pemrosesan gagal; coba ulang dari Kotak Masuk.'));
    }

    public function retry($company, $id)
    {
        $this->db->where('perusahaan_id', $company)->where('id', $id)->where('state', 'failed')
            ->update('tbl_wa_complaint_messages', array('state' => 'pending', 'attempts' => 0, 'analysis_error' => NULL));
    }

    public function create($company, $fields, $actor)
    {
        $this->db->trans_begin();
        try {
            $this->validate_fields($company, $fields);
            $id = $this->insert_ticket($company, $fields, $actor);
            $this->replace_spks($company, $id, $fields['spks'], TRUE);
            $this->finish_transaction();
            return $id;
        } catch (Throwable $e) { $this->db->trans_rollback(); throw $e; }
    }

    private function insert_ticket($company, $fields, $actor)
    {
        $now = date('Y-m-d H:i:s');
        $data = array('ticket_no' => 'TGM-'.$company.'-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(6))),
            'perusahaan_id' => $company, 'wa_message_id' => isset($fields['wa_message_id']) ? $fields['wa_message_id'] : NULL,
            'reported_at' => $fields['reported_at'], 'pelapor' => $fields['pelapor'], 'judul' => $fields['judul'],
            'deskripsi' => $fields['deskripsi'], 'ai_summary' => isset($fields['ai_summary']) ? $fields['ai_summary'] : NULL,
            'analysis_method' => isset($fields['analysis_method']) ? $fields['analysis_method'] : 'manual',
            'needs_review' => isset($fields['wa_message_id']) ? 1 : 0,
            'kategori' => $fields['kategori'], 'priority' => $fields['priority'],
            'mesin_id' => !empty($fields['mesin_id']) ? $fields['mesin_id'] : NULL,
            'created_by' => $actor, 'created_at' => $now, 'updated_at' => $now);
        $this->db->insert('tbl_complaint', $data);
        $id = $this->db->insert_id();
        $this->event($company, $id, $actor, 'CREATED', NULL, 'OPEN', 'Tiket dibuat dari '.($data['wa_message_id'] ? 'pesan WhatsApp; perlu tinjauan.' : 'laporan manual.'));
        return $id;
    }

    private function validate_fields($company, $fields)
    {
        foreach (array('judul' => 191, 'pelapor' => 191, 'deskripsi' => 16000) as $key => $max) {
            if (!isset($fields[$key]) || !is_string($fields[$key]) || trim($fields[$key]) === '' || mb_strlen($fields[$key], 'UTF-8') > $max) {
                throw new InvalidArgumentException('Isi '.$key.' dengan benar (maksimal '.$max.' karakter).');
            }
        }
        if (!in_array($fields['kategori'], Complaint_policy::categories(), TRUE) || !in_array($fields['priority'], Complaint_policy::priorities(), TRUE)) {
            throw new InvalidArgumentException('Kategori/prioritas tidak valid.');
        }
        if (!empty($fields['mesin_id']) && !$this->db->get_where('tbl_mesin', array('mesin_id' => $fields['mesin_id'], 'perusahaan_id' => $company))->row_array()) {
            throw new InvalidArgumentException('Mesin harus berasal dari cabang aktif.');
        }
    }

    private function replace_spks($company, $id, $spks, $strict)
    {
        if (!is_array($spks) || count($spks) > 20) { throw new InvalidArgumentException('Maksimal 20 SPK terkait.'); }
        $valid = array();
        foreach (array_unique($spks) as $spk) {
            if (!is_string($spk) || strlen($spk) > 50) { throw new InvalidArgumentException('Nomor SPK tidak valid.'); }
            $found = $this->db->get_where('tbl_pemesanan', array('perusahaan_id' => $company, 'nospk' => $spk))->row_array();
            if ($found) { $valid[] = $spk; }
            elseif ($strict) { throw new InvalidArgumentException('SPK '.$spk.' tidak ditemukan di cabang aktif.'); }
        }
        $this->db->where('perusahaan_id', $company)->where('complaint_id', $id)->delete('tbl_complaint_spk');
        foreach ($valid as $spk) {
            $this->db->insert('tbl_complaint_spk', array('complaint_id' => $id, 'perusahaan_id' => $company, 'nospk' => $spk));
        }
    }

    public function update_ticket($company, $id, $fields, $actor, $manager)
    {
        $this->db->trans_begin();
        try {
            $old = $this->ticket($company, $id, TRUE);
            if (!$old) { throw new InvalidArgumentException('Tiket tidak ditemukan.'); }
            if (!$manager && (int) $old['pic_id'] !== (int) $actor) { throw new InvalidArgumentException('Hanya PIC atau pengelola yang dapat memperbarui tiket.'); }
            if ((int) $fields['version'] !== (int) $old['version']) { throw new InvalidArgumentException('Tiket telah diperbarui pengguna lain. Muat ulang halaman.'); }
            $this->validate_fields($company, $fields);
            if (!$manager && (string) $fields['pic_id'] !== (string) $old['pic_id']) { throw new InvalidArgumentException('Hanya pengelola yang dapat mengganti PIC.'); }
            if (!empty($fields['pic_id'])) {
                $ids = array_map('intval', array_column($this->users($company), 'user_id'));
                if (!in_array((int) $fields['pic_id'], $ids, TRUE)) { throw new InvalidArgumentException('PIC harus pengguna aktif di cabang ini.'); }
            }
            Complaint_policy::transition($old['status'], $fields['status'], $fields['pic_id'], $fields['resolution'], $manager);
            if ($fields['status'] === 'OPEN' && !empty($fields['pic_id'])) { throw new InvalidArgumentException('Status OPEN belum memiliki PIC; pilih ASSIGNED.'); }
            foreach (array('resolution', 'root_cause', 'preventive_action', 'note') as $key) {
                if (!is_string($fields[$key]) || mb_strlen($fields[$key], 'UTF-8') > 8000) { throw new InvalidArgumentException('Catatan terlalu panjang.'); }
            }
            $data = array_intersect_key($fields, array_flip(array('judul', 'pelapor', 'deskripsi', 'kategori', 'priority', 'status', 'resolution', 'root_cause', 'preventive_action', 'needs_review')));
            $data['pic_id'] = $fields['pic_id'] ?: NULL;
            $data['mesin_id'] = $fields['mesin_id'] ?: NULL;
            $data['updated_at'] = date('Y-m-d H:i:s');
            $data['version'] = $old['version'] + 1;
            if ($fields['pic_id'] && (string) $fields['pic_id'] !== (string) $old['pic_id']) { $data['assigned_at'] = $data['updated_at']; }
            if ($fields['status'] === 'RESOLVED' && $old['status'] !== 'RESOLVED') { $data['resolved_at'] = $data['updated_at']; }
            if ($fields['status'] === 'CLOSED' && $old['status'] !== 'CLOSED') { $data['closed_at'] = $data['updated_at']; }
            if (in_array($fields['status'], array('OPEN', 'ASSIGNED', 'ON_PROGRESS'), TRUE)) { $data['resolved_at'] = NULL; $data['closed_at'] = NULL; }
            $this->db->where('perusahaan_id', $company)->where('complaint_id', $id)->update('tbl_complaint', $data);
            $this->replace_spks($company, $id, $fields['spks'], TRUE);
            $changes = array();
            foreach (array('judul', 'kategori', 'priority', 'pic_id', 'mesin_id', 'needs_review') as $key) {
                if ((string) $old[$key] !== (string) $data[$key]) { $changes[] = $key.': '.($old[$key] === NULL ? '-' : $old[$key]).' → '.($data[$key] === NULL ? '-' : $data[$key]); }
            }
            $note = trim($fields['note']);
            if ($changes) { $note = implode('; ', $changes).($note !== '' ? "\n".$note : ''); }
            $this->event($company, $id, $actor, 'UPDATED', $old['status'], $data['status'], $note ?: 'Data penanganan diperbarui.');
            $this->finish_transaction();
        } catch (Throwable $e) { $this->db->trans_rollback(); throw $e; }
    }

    public function link_message($company, $message_id, $target, $actor)
    {
        $this->db->trans_begin();
        try {
            $message = $this->db->query('SELECT * FROM tbl_wa_complaint_messages WHERE perusahaan_id = ? AND id = ? FOR UPDATE', array($company, $message_id))->row_array();
            if (!$message || $message['ticket_id'] || $message['state'] === 'processing') {
                throw new InvalidArgumentException('Pesan tidak tersedia untuk ditautkan.');
            }
            if ($target) {
                $ticket = $this->ticket($company, $target, TRUE);
                if (!$ticket) { throw new InvalidArgumentException('Tiket tujuan tidak ditemukan di cabang aktif.'); }
                $id = $ticket['complaint_id'];
            } else {
                $this->load->library('Complaint_analyzer');
                $a = $this->complaint_analyzer->rules($message['body']);
                $id = $this->insert_ticket($company, array('pelapor' => $message['sender'], 'judul' => $a['title'],
                    'deskripsi' => $message['body'], 'kategori' => $a['category'], 'priority' => $a['priority'],
                    'ai_summary' => $a['summary'], 'analysis_method' => 'manual', 'wa_message_id' => $message['id'],
                    'reported_at' => $message['sent_at']), $actor);
                $this->replace_spks($company, $id, $a['spks'], FALSE);
            }
            $this->db->where('id', $message['id'])->where('perusahaan_id', $company)->update('tbl_wa_complaint_messages',
                array('ticket_id' => $id, 'state' => 'analyzed', 'locked_at' => NULL, 'lock_token' => NULL));
            $this->event($company, $id, $actor, 'MESSAGE_LINKED', NULL, NULL, 'Pesan WhatsApp #'.$message['id'].' ditautkan sebagai bukti/follow-up.');
            $this->finish_transaction();
            return $id;
        } catch (Throwable $e) { $this->db->trans_rollback(); throw $e; }
    }

    private function event($company, $id, $actor, $event, $old, $new, $note)
    {
        $this->db->insert('tbl_complaint_history', array('perusahaan_id' => $company, 'complaint_id' => $id,
            'actor_id' => $actor, 'event' => $event, 'old_status' => $old, 'new_status' => $new,
            'note' => $note, 'created_at' => date('Y-m-d H:i:s')));
    }

    private function finish_transaction()
    {
        if (!$this->db->trans_status()) { $this->db->trans_rollback(); throw new RuntimeException('Penyimpanan gagal; perubahan dibatalkan.'); }
        $this->db->trans_commit();
    }
}
