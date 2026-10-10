<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Final inspection is separate from production results. History is append-only. */
class Wip_quality_model extends CI_Model
{
    private $settings;
    private $storage;

    public function __construct()
    {
        parent::__construct();
        $this->config->load('wip_quality', true, true);
        $this->settings = $this->config->item('wip_quality');
    }

    public function enabled() { return !empty($this->settings['enabled']); }

    public function installed()
    {
        if ($this->storage === null) {
            $this->storage = $this->db->query("SHOW TABLE STATUS WHERE Name IN ('tbl_wip_qc','tbl_wip_qc_inspection')")->result();
        }
        return count($this->storage) === 2;
    }

    public function assert_storage()
    {
        $installed = $this->installed();
        if (!$installed && ($this->enabled() || count($this->storage))) {
            throw new DomainException('Penyimpanan QC belum lengkap. Hubungi administrator.');
        }
        foreach ($this->storage as $table) {
            if (strtolower($table->Engine) !== 'innodb') throw new DomainException('Penyimpanan QC harus mendukung transaksi.');
        }
        return $installed;
    }

    public function can_review($actor)
    {
        return !empty($actor['admin']) || in_array((int) $actor['user_id'], array_map('intval', $this->settings['reviewer_user_ids'] ?? []), true);
    }

    private function query($sql, $binds = [])
    {
        $result = $this->db->query($sql, $binds);
        if ($result === false) throw new RuntimeException('Penyimpanan QC gagal.');
        return $result;
    }

    /** Called inside the production transaction with its header locked. */
    public function enroll($item, $actor)
    {
        if (!$this->enabled()) return;
        if (!$this->assert_storage()) throw new DomainException('Penyimpanan QC belum tersedia.');
        $exists = $this->query('SELECT * FROM tbl_wip_qc WHERE produksi_id = ? FOR UPDATE', [(int) $item->produksi_id])->row();
        if ($exists) return;
        $target = Production_workflow::number($item->qty, 'Target QC');
        if ($target <= 0) throw new DomainException('Target QC harus lebih besar dari nol.');
        $this->query("INSERT INTO tbl_wip_qc (produksi_id, target_qty, status, version, created_by, created_at, updated_at) VALUES (?, ?, 'pending', 1, ?, NOW(), NOW())", [$item->produksi_id, $target, $actor['user_id']]);
    }

    /** Existing holds remain enforced even if automatic enrollment is disabled. */
    public function assert_handover($item)
    {
        if (!$this->assert_storage()) return;
        $qc = $this->query('SELECT * FROM tbl_wip_qc WHERE produksi_id = ? FOR UPDATE', [(int) $item->produksi_id])->row();
        if ($qc && $qc->status !== 'passed') throw new DomainException('Item belum lulus QC. Buka Pemeriksaan QC sebelum serah terima PB.');
    }

    private function accessible_item($id, $actor, $lock = false)
    {
        $item = $this->query('SELECT * FROM tbl_produksi WHERE produksi_id = ?' . ($lock ? ' FOR UPDATE' : ''), [(int) $id])->row();
        if (!$item) throw new DomainException('Item produksi tidak ditemukan.');
        if (empty($actor['admin']) && Production_workflow::production_location($item) !== (int) $actor['branch']) {
            throw new DomainException('Item QC bukan cabang Anda.');
        }
        return $item;
    }

    public function inspect($id, $version, $accepted, $rejected, $note, $reprint, $request_key, $actor)
    {
        $started = false;
        try {
            if (!$this->assert_storage()) throw new DomainException('Pemeriksaan QC belum diaktifkan.');
            if (!$this->can_review($actor)) throw new DomainException('Akun belum ditugaskan sebagai pemeriksa QC.');
            $ids = Production_workflow::ids([$id]);
            if (!ctype_digit((string) $version) || (int) $version < 1) throw new DomainException('Versi QC tidak valid. Muat ulang halaman.');
            if (!preg_match('/^[a-f0-9]{32,64}$/D', (string) $request_key)) throw new DomainException('Identitas pengiriman QC tidak valid.');
            $accepted = Production_workflow::number($accepted, 'Jumlah diterima QC');
            $rejected = Production_workflow::number($rejected, 'Jumlah ditolak QC');
            if (abs($accepted - round($accepted, 4)) > 0.00000001 || abs($rejected - round($rejected, 4)) > 0.00000001) throw new DomainException('Jumlah QC maksimal 4 angka di belakang koma.');
            $note = trim((string) $note);
            if (strlen($note) > 4000) throw new DomainException('Catatan maksimal 4000 karakter.');
            if ($rejected > 0 && $note === '') throw new DomainException('Isi alasan hasil ditolak QC.');
            $reprint = $reprint ? 1 : 0;
            if ($reprint && $rejected <= 0) throw new DomainException('Permintaan reprint harus memiliki jumlah ditolak.');
            $hash = hash('sha256', json_encode([$ids[0], (int) $version, $accepted, $rejected, $note, $reprint, (int) $actor['user_id']]));
            if (!$this->db->trans_begin()) throw new RuntimeException('Transaksi QC tidak tersedia.');
            $started = true;
            // Same order as production/handover: header, then QC record.
            $item = $this->accessible_item($id, $actor, true);
            $qc = $this->query('SELECT * FROM tbl_wip_qc WHERE produksi_id = ? FOR UPDATE', [$ids[0]])->row();
            if (!$qc) throw new DomainException('Item ini belum memiliki pemeriksaan QC. Data lama tidak didaftarkan otomatis.');
            $old = $this->query('SELECT * FROM tbl_wip_qc_inspection WHERE request_key = ?', [$request_key])->row();
            if ($old) {
                if (!hash_equals($old->payload_hash, $hash)) throw new DomainException('Identitas pengiriman telah digunakan untuk hasil berbeda. Muat ulang.');
                if (!$this->db->trans_commit()) throw new RuntimeException('Transaksi QC gagal.');
                $started = false;
                return ['success' => true, 'status' => true, 'message' => 'Hasil QC sudah tercatat.', 'duplicate' => true];
            }
            if ($item->status_produksi !== 'done') throw new DomainException('QC hanya dapat dicatat setelah seluruh proses selesai dan sebelum serah terima PB.');
            if ((int) $qc->version !== (int) $version) throw new DomainException('Hasil QC telah berubah. Muat ulang sebelum menyimpan.');
            if (!in_array($qc->status, ['pending', 'hold'], true)) throw new DomainException('QC sudah lulus. Hasil yang disetujui tidak dapat ditimpa.');
            if ($qc->status === 'hold' && $note === '') throw new DomainException('Isi penjelasan tindak lanjut untuk pemeriksaan ulang.');
            if (abs($accepted + $rejected - (float) $qc->target_qty) > 0.00005) throw new DomainException('Jumlah diterima dan ditolak harus sama dengan jumlah yang diperiksa.');
            $decision = $rejected > 0 ? 'hold' : 'passed';
            $this->query('INSERT INTO tbl_wip_qc_inspection (qc_id, revision, accepted_qty, rejected_qty, decision, note, request_reprint, inspector_user_id, request_key, payload_hash, inspected_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())',
                [$qc->qc_id, $version, $accepted, $rejected, $decision, $note, $reprint, $actor['user_id'], $request_key, $hash]);
            $this->query('UPDATE tbl_wip_qc SET status = ?, version = version + 1, updated_at = NOW() WHERE qc_id = ? AND version = ?', [$decision, $qc->qc_id, $version]);
            if ($this->db->affected_rows() !== 1) throw new RuntimeException('QC berubah saat penyimpanan.');
            $audit = '[QC] ' . ($decision === 'passed' ? 'Lulus' : 'Ditahan') . '; diterima: ' . $accepted . '; ditolak: ' . $rejected
                . ($reprint ? '; permintaan reprint tercatat (belum dibuat pekerjaan baru)' : '') . ($note !== '' ? '; ' . $note : '');
            $this->query('INSERT INTO tbl_produksi_log (produksi_id, user_id, status_asal, status_tujuan, catatan) VALUES (?, ?, ?, ?, ?)', [$id, $actor['user_id'], $item->status_produksi, $item->status_produksi, $audit]);
            if (!$this->db->trans_status() || !$this->db->trans_commit()) throw new RuntimeException('Transaksi QC gagal.');
            $started = false;
            return ['success' => true, 'status' => true, 'message' => $decision === 'passed' ? 'QC lulus. Item siap diserahterimakan ke PB.' : 'Hasil QC ditahan. Catatan dan permintaan tindak lanjut tersimpan.'];
        } catch (Exception $e) {
            if ($started) $this->db->trans_rollback();
            if (!($e instanceof DomainException)) log_message('error', 'QC: ' . $e->getMessage());
            return ['success' => false, 'status' => false, 'message' => $e instanceof DomainException ? $e->getMessage() : 'Penyimpanan QC gagal. Perubahan dibatalkan.'];
        }
    }

    public function history($id, $actor)
    {
        $this->accessible_item($id, $actor);
        return $this->query('SELECT i.revision, i.accepted_qty, i.rejected_qty, i.decision, i.note, i.request_reprint, i.inspector_user_id, i.inspected_at, u.username AS inspector_name FROM tbl_wip_qc_inspection i JOIN tbl_wip_qc q ON q.qc_id = i.qc_id LEFT JOIN users u ON u.user_id = i.inspector_user_id WHERE q.produksi_id = ? ORDER BY i.revision', [(int) $id])->result();
    }

    public function queue($actor, $status, $search, $page)
    {
        if (!$this->assert_storage()) return ['rows' => [], 'total' => 0];
        $where = ['1=1']; $binds = [];
        if (empty($actor['admin'])) { $where[] = 'COALESCE(NULLIF(p.lokasi_cetak, 0), p.perusahaan_id) = ?'; $binds[] = $actor['branch']; }
        if (in_array($status, ['pending', 'hold', 'passed'], true)) { $where[] = 'q.status = ?'; $binds[] = $status; }
        elseif ($status === 'reprint') $where[] = 'EXISTS (SELECT 1 FROM tbl_wip_qc_inspection ri WHERE ri.qc_id = q.qc_id AND ri.request_reprint = 1)';
        if ($search !== '') { $where[] = 'p.nospk LIKE ?'; $binds[] = '%' . $search . '%'; }
        $from = ' FROM tbl_wip_qc q JOIN tbl_produksi p ON p.produksi_id = q.produksi_id LEFT JOIN tbl_product pr ON pr.product_id = p.product_id WHERE ' . implode(' AND ', $where);
        $total = (int) $this->query('SELECT COUNT(*) AS total' . $from, $binds)->row()->total;
        $offset = (max(1, (int) $page) - 1) * 30;
        $rows = $this->query('SELECT q.*, p.nospk, p.status_produksi, pr.product_nama' . $from . ' ORDER BY q.qc_id DESC LIMIT 30 OFFSET ' . $offset, $binds)->result();
        return ['rows' => $rows, 'total' => $total];
    }
}
