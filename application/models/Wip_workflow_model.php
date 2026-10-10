<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Transaction boundary for production operations. No writes occur when reading. */
class Wip_workflow_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Production_workflow');
        $this->load->model('Wip_quality_model', 'wip_quality');
    }

    private $storage_checked = false;

    public function assert_transactional_storage()
    {
        if ($this->storage_checked) return;
        $required = ['tbl_produksi', 'detail_produksi', 'tbl_produksi_log', 'tbl_material', 'tbl_stok_gudang', 'tbl_stok_log'];
        $rows = $this->db->query("SHOW TABLE STATUS WHERE Name IN ('tbl_produksi','detail_produksi','tbl_produksi_log','tbl_material','tbl_stok_gudang','tbl_stok_log')")->result();
        $found = [];
        foreach ($rows as $row) {
            if (strtolower((string) $row->Engine) !== 'innodb') throw new DomainException('Tabel produksi/stok belum mendukung transaksi aman. Tidak ada data produksi yang diubah.');
            $found[] = $row->Name;
        }
        if (array_diff($required, $found)) throw new DomainException('Tabel produksi/stok belum lengkap. Tidak ada data produksi yang diubah.');
        $this->storage_checked = true;
    }

    public function actor($pin = null, $operator_role = null)
    {
        $user_id = (int) ($this->session->userdata('id_user') ?: $this->session->userdata('id'));
        if (!$user_id) throw new DomainException('Sesi pengguna tidak valid.');
        $user = $this->db->get_where('users', ['user_id' => $user_id])->row();
        if (!$user) throw new DomainException('Pengguna tidak ditemukan.');
        $admin = in_array((int) $this->session->userdata('level'), [1, 17], true);
        $branch = (int) $this->session->userdata('perusahaan_id');
        $employee = null;
        if ($pin !== null && $pin !== '') {
            $this->db->where('pin_produksi', $pin);
            if (!$admin) $this->db->where('perusahaan_id', $branch);
            $employee = $this->db->get('tbl_karyawan')->row();
            if (!$employee) throw new DomainException('PIN operator tidak valid untuk cabang Anda.');
            // A PIN establishes the employee, not an authorization to change any operation.
            $pin_user = $this->db->get_where('users', ['karyawan_id' => $employee->karyawan_id])->row();
            $employee_role = $pin_user && !empty($pin_user->operator_id)
                ? (int) $pin_user->operator_id : (int) ($employee->divisi_id ?? 0);
            if (!$admin && $operator_role !== null && $employee_role !== (int) $operator_role) {
                throw new DomainException('PIN bukan milik operator pada divisi pekerjaan ini.');
            }
        } elseif (!empty($user->karyawan_id)) {
            $employee = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $user->karyawan_id])->row();
        }
        if (!$admin && $operator_role === null) {
            $own_employee = !empty($user->karyawan_id) ? $this->db->get_where('tbl_karyawan', ['karyawan_id' => $user->karyawan_id])->row() : null;
            $candidate = !empty($user->operator_id) ? (int) $user->operator_id : (int) ($own_employee->divisi_id ?? 0);
            if ($candidate && $this->db->get_where('tbl_operator', ['operator_id' => $candidate])->row()) $operator_role = $candidate;
        }
        return ['user_id' => $user_id, 'employee_id' => $employee ? (int) $employee->karyawan_id : null,
            'branch' => $branch, 'admin' => $admin, 'operator_role' => $operator_role];
    }

    public function assert_access($item, $actor)
    {
        if (!$item) throw new DomainException('Item produksi tidak ditemukan.');
        $branch = (int) (($item->lokasi_cetak ?? 0) ?: $item->perusahaan_id);
        if (!$actor['admin'] && (!$actor['branch'] || $branch !== $actor['branch'])) {
            throw new DomainException('Akses ditolak: lokasi produksi bukan cabang Anda.');
        }
    }

    /** Caller owns the customer-handover transaction and applies its header/photo updates. */
    public function customer_handover_items($ids, $actor, $nospk = null)
    {
        $this->assert_transactional_storage();
        $this->wip_quality->assert_storage();
        $items = [];
        foreach (Production_workflow::ids($ids) as $id) {
            $item = $this->db->query('SELECT * FROM tbl_produksi WHERE produksi_id = ? FOR UPDATE', [$id])->row();
            $this->assert_access($item, $actor);
            if ($nospk !== null && (string) $item->nospk !== (string) $nospk) throw new DomainException('Item bukan milik SPK yang dipilih.');
            if ($item->status_produksi !== 'on_pb') throw new DomainException('Item harus diserahterimakan ke PB dahulu sebelum diberikan kepada konsumen.');
            $this->wip_quality->assert_handover($item);
            $items[] = $item;
        }
        return $items;
    }

    private function assert_operator($step, $actor)
    {
        if ($actor['admin']) return;
        if ($actor['operator_role'] === null) throw new DomainException('Akun belum ditugaskan ke divisi operator produksi.');
        $role = (int) ($step->routing_operator_id ?? 0);
        if (!$role || $role !== (int) $actor['operator_role']) {
            throw new DomainException('Akses ditolak: tahap ini ditugaskan ke divisi operator lain.');
        }
    }

    public function steps($item, $lock = false)
    {
        $suffix = $lock ? ' FOR UPDATE' : '';
        $steps = $this->db->query('SELECT * FROM detail_produksi WHERE produksi_id = ? '
            . 'ORDER BY routing_urutan, detail_produksi_id' . $suffix, [(int) $item->produksi_id])->result();
        $routes = $this->db->where('product_id', $item->product_id)
            ->group_start()->where('harga_id', null)->or_where('harga_id', $item->harga_id)->group_end()
            ->order_by('routing_urutan')->order_by('routing_id')->get('routing')->result();
        foreach ($steps as $step) {
            $matched = null;
            foreach ($routes as $route) {
                if ((int) $step->routing_id > 0 && (int) $route->routing_id === (int) $step->routing_id) {
                    $matched = $route; break;
                }
                if (!(int) $step->routing_id && (int) $route->routing_urutan === (int) $step->routing_urutan) {
                    $matched = $route;
                }
            }
            $step->routing_operator_id = $matched ? $matched->operator_id : null;
            $step->routing_name = $matched ? $matched->routing_name : '';
            $step->is_finishing = $matched ? (int) ($matched->is_finishing ?? 0) : 0;
            $step->bom_line_id = $matched ? ($matched->bom_line_id ?? null) : null;
            $step->workflow_version = Production_workflow::version($step);
        }
        return $steps;
    }

    private function initialize_steps($item)
    {
        $routes = $this->db->where('product_id', $item->product_id)
            ->group_start()->where('harga_id', null)->or_where('harga_id', $item->harga_id)->group_end()
            ->order_by('routing_urutan')->order_by('routing_id')->get('routing')->result();
        if (!$routes) throw new DomainException('Routing belum tersedia. Lengkapi master routing sebelum mulai.');
        foreach ($routes as $route) {
            $this->write('detail_produksi', ['produksi_id' => $item->produksi_id,
                'routing_id' => $route->routing_id, 'routing_urutan' => $route->routing_urutan,
                'operator_id' => $route->operator_id, 'qty_input' => $item->qty, 'status_produksi' => '0'], null);
        }
    }

    private function write($table, $data, $where)
    {
        if ($where === null) $ok = $this->db->insert($table, $data);
        else $ok = $this->db->where($where)->update($table, $data);
        if (!$ok) throw new RuntimeException('Penyimpanan produksi gagal.');
    }

    private function log($item, $to, $actor, $note)
    {
        $this->write('tbl_produksi_log', ['produksi_id' => $item->produksi_id,
            'user_id' => $actor['user_id'], 'status_asal' => $item->status_produksi,
            'status_tujuan' => $to, 'catatan' => $note], null);
    }

    private function finishing_open($item)
    {
        if (empty($item->finishing_id)) return false;
        return !in_array(strtolower((string) ($item->status_finishing ?? '')), ['done', 'selesai', 'finished', 'on_pb'], true);
    }

    /** Callback posts actual material use and returns fields for the selected operation. */
    public function execute($ids, $action, $actor, $expected = [], $versions = [], $callback = null, $material_ids = [])
    {
        $started = false;
        try {
            $ids = Production_workflow::ids($ids);
            if (!in_array($action, ['start', 'complete', 'cancel', 'handover', 'report'], true)) {
                throw new DomainException('Aksi produksi tidak valid.');
            }
            $this->assert_transactional_storage();
            $this->wip_quality->assert_storage();
            if (!$this->db->trans_begin()) throw new RuntimeException('Transaksi database tidak tersedia.');
            $started = true;
            $items = [];
            // Stable lock order prevents two overlapping batches from locking in opposite order.
            foreach ($ids as $id) {
                $item = $this->db->query('SELECT * FROM tbl_produksi WHERE produksi_id = ? FOR UPDATE', [$id])->row();
                $this->assert_access($item, $actor);
                $items[$id] = $item;
            }
            // Lock all declared materials in one order before any stock callback runs.
            if ($material_ids) {
                foreach (Production_workflow::ids($material_ids) as $material_id) {
                    $material = $this->db->query('SELECT material_id FROM tbl_material WHERE material_id = ? FOR UPDATE', [$material_id])->row();
                    if (!$material) throw new DomainException('Material yang dipilih tidak ditemukan.');
                }
            }
            $processed = [];
            foreach ($items as $id => $item) {
                $steps = $this->steps($item, true);
                if ($action === 'handover') {
                    if ((string) $item->status_produksi === 'on_pb') continue; // Safe retry; no extra log/timestamp.
                    if ((string) $item->status_produksi !== 'done' || $this->finishing_open($item)) {
                        throw new DomainException('Item belum selesai atau finishing masih terbuka.');
                    }
                    // Empty routing on historical completed records is not fabricated or rewritten.
                    if ($steps && !Production_workflow::all_completed($steps)) {
                        throw new DomainException('Riwayat tahap belum tuntas. Tinjau item sebelum serah terima PB.');
                    }
                    if ($steps && $actor['operator_role'] !== null) $this->assert_operator(end($steps), $actor);
                    $this->wip_quality->assert_handover($item);
                    $data = ['status_produksi' => 'on_pb', 'tgl_kirim_pb' => date('Y-m-d H:i:s'),
                        'update_time' => date('Y-m-d H:i:s')];
                    $this->write('tbl_produksi', $data, ['produksi_id' => $id]);
                    $this->log($item, 'on_pb', $actor, '[PB] Serah terima ke PB; riwayat operasi dipertahankan');
                    $processed[] = $id;
                    continue;
                }
                if ($action === 'report') {
                    if (!$actor['admin'] && $actor['operator_role'] !== null) {
                        $assigned = false;
                        foreach ($steps as $assigned_step) {
                            if ((int) $assigned_step->routing_operator_id === (int) $actor['operator_role']) $assigned = true;
                        }
                        if (!$assigned) throw new DomainException('Laporan hanya dapat dibuat untuk pekerjaan divisi Anda.');
                    }
                    if ($callback) $callback($item, null, $actor);
                    $processed[] = $id;
                    continue;
                }
                if (!Production_workflow::active_header($item->status_produksi)) {
                    throw new DomainException('Item sudah selesai/diserahkan atau status tidak dapat diproses.');
                }
                $order = $this->db->get_where('tbl_pemesanan', ['nospk' => $item->nospk])->row();
                if (!$order || $order->status_pembayaran !== 'paid') {
                    throw new DomainException('SPK belum divalidasi pembayarannya.');
                }
                if (!$steps && $action === 'start') {
                    $this->initialize_steps($item);
                    $steps = $this->steps($item, true);
                }
                if (!$steps) throw new DomainException('Tahap belum tersedia. Mulai produksi dahulu.');
                if ($action !== 'start' && empty($expected[$id])) {
                    throw new DomainException('Identitas tahap wajib diisi. Muat ulang halaman produksi.');
                }
                $step = Production_workflow::current_step($steps, $action, $expected[$id] ?? null);
                $this->assert_operator($step, $actor);
                if (!empty($versions[$id]) && !hash_equals($step->workflow_version, (string) $versions[$id])) {
                    throw new DomainException('Hasil tahap telah berubah. Muat ulang sebelum menyimpan.');
                }
                $fields = $callback ? $callback($item, $step, $actor) : [];
                $fields = is_array($fields) ? $fields : [];
                $note = $fields['_note'] ?? '';
                unset($fields['_note']);
                $now = date('Y-m-d H:i:s');
                $header = ['status_produksi' => 'proses', 'update_time' => $now];
                if (empty($item->tgl_mulai)) $header['tgl_mulai'] = $now;
                if ($action === 'start') {
                    $fields['status_produksi'] = '1';
                    $fields['waktu_mulai'] = ($step->waktu_mulai ?? null) ?: $now;
                    // Starting is authorized by the signed-in account and audited with its user_id.
                    // Keep the existing worker if this account is not linked to an employee.
                    if ($actor['employee_id']) $fields['operator_id'] = $actor['employee_id'];
                } elseif ($action === 'cancel') {
                    // Cancel only an unused start; keep employee/machine/time evidence.
                    foreach (['qty_output', 'qty_missing', 'qty_bahan_pakai', 'qty_bahan_rusak'] as $field) {
                        if ((float) ($step->$field ?? 0) > 0) {
                            throw new DomainException('Tahap sudah memiliki hasil/pemakaian bahan; pembatalan mulai ditolak.');
                        }
                    }
                    $fields['status_produksi'] = '0';
                    $note = 'Pembatalan mulai; operator #' . $step->operator_id . ', mesin #' . ($step->mesin_id ?? 0)
                        . ', waktu mulai ' . ($step->waktu_mulai ?? '-') . ' tetap tersimpan';
                } else {
                    $failed = !empty($fields['_failed']);
                    unset($fields['_failed']);
                    if (!array_key_exists('qty_output', $fields)) {
                        throw new DomainException('Isi jumlah hasil baik aktual.');
                    }
                    $qty = Production_workflow::number($fields['qty_output'], 'Hasil baik');
                    $missing = Production_workflow::number($fields['qty_missing'] ?? 0, 'Hasil rusak');
                    $fields['qty_output'] = (float) ($step->qty_output ?? 0) + $qty;
                    $fields['qty_missing'] = (float) ($step->qty_missing ?? 0) + $missing;
                    $partial = $failed || $fields['qty_output'] + 0.000001 < (float) $item->qty;
                    if ($partial && empty($versions[$id])) {
                        throw new DomainException('Muat ulang halaman untuk mencatat hasil parsial/rusak dengan aman.');
                    }
                    $fields['status_produksi'] = $partial ? '1' : '2';
                    if (!$partial) $fields['waktu_selesai'] = $now;
                    $fields['operator_id'] = $actor['employee_id'] ?: $step->operator_id;
                    $step->status_produksi = $fields['status_produksi'];
                    if (!$partial && !empty($step->is_finishing) && !empty($item->finishing_id) && property_exists($item, 'status_finishing')) {
                        $header['status_finishing'] = 'done';
                        $item->status_finishing = 'done';
                    }
                    if (Production_workflow::all_completed($steps)) {
                        $header['status_produksi'] = $this->finishing_open($item) ? 'finishing' : 'done';
                        if ($header['status_produksi'] === 'done') $header['tgl_selesai'] = $now;
                    }
                    $note = ($partial ? 'Hasil parsial/rijek; tahap tetap terbuka' : 'Tahap selesai')
                        . '; baik: ' . $qty . '; rusak: ' . $missing . ($note ? '; ' . $note : '');
                }
                // Append event evidence; never replace historical notes. The nonce also separates
                // cancel/resume cycles and zero-output failed attempts for optimistic retry checks.
                $entry = $note ?: 'Produksi dimulai';
                $fields['catatan'] = trim((string) ($step->catatan ?? '') . "\n" . $now . ' | ' . $entry
                    . (!empty($fields['catatan']) ? ' | ' . $fields['catatan'] : '') . ' [event:' . bin2hex(random_bytes(8)) . ']');
                $this->write('detail_produksi', $fields, ['detail_produksi_id' => $step->detail_produksi_id]);
                if ($header['status_produksi'] === 'done') $this->wip_quality->enroll($item, $actor);
                $this->write('tbl_produksi', $header, ['produksi_id' => $id]);
                $this->log($item, $header['status_produksi'], $actor,
                    'Tahap #' . $step->detail_produksi_id . ': ' . ($note ?: 'Produksi dimulai'));
                $processed[] = $id;
            }
            if (!$this->db->trans_status() || !$this->db->trans_commit()) {
                throw new RuntimeException('Transaksi produksi gagal. Tidak ada hasil yang disimpan.');
            }
            $started = false;
            return ['success' => true, 'status' => true, 'message' => count($processed) . ' item berhasil diproses.',
                'processed_ids' => $processed, 'count' => count($processed)];
        } catch (Exception $e) {
            if ($started) $this->db->trans_rollback();
            if (!($e instanceof DomainException)) log_message('error', 'WIP workflow: ' . $e->getMessage());
            return ['success' => false, 'status' => false, 'message' => $e instanceof DomainException
                ? $e->getMessage() : 'Transaksi gagal. Perubahan dibatalkan; periksa log aplikasi.'];
        }
    }
}
