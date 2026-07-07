<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hr extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Absensi_model');
        $this->load->model('M_laporan');
    }

    private function _apply_karyawan_filter($alias = '')
    {
        $prefix = $alias ? $alias . '.' : '';
        $this->db->where($prefix . "nama_lengkap NOT LIKE '%Terminal%'");
        $this->db->where($prefix . "nama_lengkap NOT LIKE '%DUMMY%'");
        $this->db->where($prefix . "nama_lengkap NOT LIKE 'DEPT.%'");
        $this->db->where($prefix . "nama_lengkap NOT LIKE 'OPERATOR %'");
        $this->db->where($prefix . "nik NOT LIKE 'STATION-%'");
        $this->db->where($prefix . "nik NOT LIKE 'TERM%'");
    }

    public function index()
    {
        $role_id = (int) $this->session->userdata('level');
        $is_developer = ($this->session->userdata('username') === 'nizarsuryadi');

        $data = array_merge($this->data, $this->_prepare_hrd_data(null)); // Load HR Dats
        $data = array_merge($data, $this->_prepare_finance_data(null)); // Load Finance Data

        $data['title'] = 'HR Command Center' . ($is_developer ? ' [PREVIEW]' : '');
        $data['is_preview'] = $is_developer;
        $data['quick_access'] = [];

        $this->load_admin_view('admin/dashboard/hrd_summary', $data);
        return;
    }

    private function _prepare_hrd_data($pid = null)
    {
        $harian = date('Y-m-d');
        $data = [];

        // 1. Employee Stats
        if ($pid) {
            $this->db->where('perusahaan_id', $pid);
        }
        $this->_apply_karyawan_filter();
        $data['total_karyawan'] = $this->db->count_all_results('tbl_karyawan');

        // 2. Attendance Stats for today
        $this->db->from('absensi');
        if ($pid) {
            $this->db->join('tbl_karyawan', 'absensi.karyawan_id = tbl_karyawan.karyawan_id');
            $this->db->where('tbl_karyawan.perusahaan_id', $pid);
        }
        $this->db->where('absensi.tanggal', $harian);

        $base_q = clone $this->db;
        $data['absensi_stats'] = [
            'hadir' => (clone $base_q)->where('status', 'Hadir')->count_all_results(),
            'izin' => (clone $base_q)->where_in('status', ['Izin', 'Sakit', 'Cuti'])->count_all_results(),
            'alpha' => (clone $base_q)->where('status', 'Alpha')->count_all_results(),
            'telat' => (clone $base_q)->where('status', 'Late')->count_all_results(),
        ];

        $data['late_employees_today'] = $this->Absensi_model->get_late_employees($harian, $pid);

        $total_today = array_sum($data['absensi_stats']);
        $data['attendance_health'] = ($total_today > 0) ? round(($data['absensi_stats']['hadir'] / $total_today) * 100, 1) : 0;

        // Gender Distribution
        $this->db->select('jenis_kelamin, COUNT(*) as count');
        $this->db->from('tbl_karyawan');
        if ($pid)
            $this->db->where('perusahaan_id', $pid);
        $this->_apply_karyawan_filter();
        $this->db->group_by('jenis_kelamin');
        $data['gender_stats'] = $this->db->get()->result();

        // Division Distribution
        $this->db->select('d.divisi_nama, COUNT(k.karyawan_id) as count');
        $this->db->from('tbl_divisi d');
        $this->db->join('tbl_karyawan k', 'd.divisi_id = k.divisi_id', 'left');
        if ($pid)
            $this->db->where('k.perusahaan_id', $pid);
        $this->_apply_karyawan_filter('k');
        $this->db->group_by('d.divisi_id');
        $this->db->having('count > 0');
        $this->db->order_by('count', 'DESC');
        $data['division_stats'] = $this->db->get()->result();

        // Upcoming Birthdays
        $this->db->select('nama_lengkap, tanggal_lahir, foto, nik');
        $this->db->from('tbl_karyawan');
        if ($pid)
            $this->db->where('perusahaan_id', $pid);
        $this->_apply_karyawan_filter();
        $this->db->where("DATE_FORMAT(tanggal_lahir, '%m-%d') BETWEEN DATE_FORMAT(CURDATE(), '%m-%d') AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 14 DAY), '%m-%d')");
        $this->db->order_by("DATE_FORMAT(tanggal_lahir, '%m-%d') ASC", '', FALSE);
        $this->db->limit(5);
        $data['birthdays'] = $this->db->get()->result();

        // Upcoming Anniversaries
        $this->db->select('nama_lengkap, tanggal_masuk, foto');
        $this->db->from('tbl_karyawan');
        if ($pid)
            $this->db->where('perusahaan_id', $pid);
        $this->_apply_karyawan_filter();
        $this->db->where("MONTH(tanggal_masuk) = MONTH(CURDATE())");
        $this->db->where("DAY(tanggal_masuk) >= DAY(CURDATE())");
        $this->db->order_by("DAY(tanggal_masuk) ASC", '', FALSE);
        $this->db->limit(5);
        $data['anniversaries'] = $this->db->get()->result();

        // Recent Employees
        $this->db->select('k.*, j.nama_jabatan, d.divisi_nama, p.nama_perusahaan as branch_name');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left');
        $this->db->join('tbl_perusahaan p', 'k.perusahaan_id = p.id_perusahaan', 'left');
        if ($pid) {
            $this->db->where('k.perusahaan_id', $pid);
        }
        $this->_apply_karyawan_filter('k');
        $this->db->order_by('k.created_at', 'DESC');
        $this->db->limit(10);
        $data['recent_employees'] = $this->db->get()->result();

        // 5.b Semua Karyawan (Untuk Mini Table Preview)
        $this->db->select('k.*, j.nama_jabatan, d.divisi_nama, p.nama_perusahaan as branch_name');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left');
        $this->db->join('tbl_perusahaan p', 'k.perusahaan_id = p.id_perusahaan', 'left');
        if ($pid) {
            $this->db->where('k.perusahaan_id', $pid);
        }
        // Exclude Owner or Superadmin from the list if necessary, but typically all employees are shown
        $this->_apply_karyawan_filter('k');
        $this->db->order_by('k.nama_lengkap', 'ASC');
        $data['semua_karyawan'] = $this->db->get()->result();

        // Attendance Feed
        $this->db->select('a.*, k.nama_lengkap, k.foto, k.nik');
        $this->db->from('absensi a');
        $this->db->join('tbl_karyawan k', 'a.karyawan_id = k.karyawan_id');
        if ($pid) {
            $this->db->where('k.perusahaan_id', $pid);
        }
        $this->db->where('a.tanggal', $harian);
        $this->db->order_by('a.id', 'DESC');
        $this->db->limit(10);
        $data['attendance_feed'] = $this->db->get()->result();

        // Audit Logs
        $this->db->select('al.*, u.username');
        $this->db->from('user_activity_log al');
        $this->db->join('users u', 'al.user_id = u.user_id', 'left');
        $this->db->order_by('al.timestamp', 'DESC');
        $this->db->limit(10);
        $data['recent_logs'] = $this->db->get()->result();

        // Employee Status Stats (Tetap, Kontrak, Magang)
        $this->db->select('status_karyawan, COUNT(*) as count');
        $this->db->from('tbl_karyawan');
        if ($pid)
            $this->db->where('perusahaan_id', $pid);
        $this->db->where("nik NOT LIKE 'STATION%'");
        $this->db->group_by('status_karyawan');
        $data['status_stats'] = $this->db->get()->result();

        // Expiring Contracts (Exact calculation using akhir_kontrak within 30 days)
        $this->db->select('tbl_karyawan.karyawan_id, tbl_karyawan.nama_lengkap, tbl_karyawan.akhir_kontrak, tbl_karyawan.status_karyawan, tbl_karyawan.foto, tbl_karyawan.nik, tbl_karyawan.tanggal_masuk, pk.id as pengajuan_id, pk.durasi_perpanjangan, pk.status as pengajuan_status, pk.tanggal_pengajuan');
        $this->db->from('tbl_karyawan');
        $this->db->join('tbl_pengajuan_kontrak pk', "pk.karyawan_id = tbl_karyawan.karyawan_id AND pk.status = 'Pending'", 'left', FALSE);
        if ($pid)
            $this->db->where('tbl_karyawan.perusahaan_id', $pid);
        $this->db->where_in('tbl_karyawan.status_karyawan', ['Kontrak', 'Magang']);
        $this->db->where('tbl_karyawan.akhir_kontrak IS NOT NULL');
        // Include contracts expiring in the next 30 days OR already expired (overdue) OR have pending requests
        $this->db->where("(tbl_karyawan.akhir_kontrak <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) OR pk.id IS NOT NULL)");
        $this->_apply_karyawan_filter('tbl_karyawan');
        $this->db->order_by("tbl_karyawan.akhir_kontrak ASC");
        $data['expiring_contracts'] = $this->db->get()->result();

        // Pending Approvals
        $this->db->where('status', 'Pending');
        if ($pid) {
            $this->db->join('tbl_karyawan', 'tbl_pengajuan_izin.karyawan_id = tbl_karyawan.karyawan_id');
            $this->db->where('tbl_karyawan.perusahaan_id', $pid);
        }
        $data['pending_izin_count'] = $this->db->count_all_results('tbl_pengajuan_izin');

        $this->db->where('status_pembayaran', 'pending');
        if ($pid) {
            $this->db->join('tbl_karyawan', 'tbl_slip_gaji.karyawan_id = tbl_karyawan.karyawan_id');
            $this->db->where('tbl_karyawan.perusahaan_id', $pid);
        }
        $data['pending_payroll_count'] = $this->db->count_all_results('tbl_slip_gaji');

        $this->db->where('status', 'Pending');
        if ($pid) {
            $this->db->join('tbl_karyawan', 'tbl_pengajuan_kontrak.karyawan_id = tbl_karyawan.karyawan_id');
            $this->db->where('tbl_karyawan.perusahaan_id', $pid);
        }
        $data['pending_kontrak_count'] = $this->db->count_all_results('tbl_pengajuan_kontrak');

        return $data;
    }

    private function _prepare_finance_data($selected_branch = null)
    {
        $hariini = date('Y-m-d');
        $bulanini = date('Y-m');
        $data = [];

        // Pemasukan Harian
        $data['pemasukan_hariini'] = $this->_get_consolidated_income($hariini, $selected_branch);

        // Pemasukan Bulanan
        $data['pemasukan_bulanini'] = $this->M_laporan->pemasukan_bulanan($selected_branch, $bulanini)->row()->total_pemasukan_bulanan ?? 0;

        // Pengeluaran Harian
        $data['pengeluaran_hariini'] = $this->M_laporan->pengeluaran($selected_branch)->row()->pengeluaran ?? 0;

        // Pengeluaran Bulanan
        $data['pengeluaran_bulanini'] = $this->M_laporan->pengeluaran_bulanan($selected_branch, $bulanini)->row()->pengeluaran_bulanan ?? 0;

        // Hutang
        $data['total_hutang'] = $this->db->select('SUM(sisa_hutang) as total')->from('tbl_hutang')->get()->row()->total ?? 0;

        return $data;
    }

    private function _get_consolidated_income($date, $branch_id = null)
    {
        $this->db->select('SUM(total_bayar) as total');
        $this->db->from('tbl_pembayaran');
        $this->db->where('DATE(tgl_bayar)', $date);
        if ($branch_id) {
            $this->db->where('perusahaan_id', $branch_id);
        }
        return $this->db->get()->row()->total ?? 0;
    }
    
    public function approve_kontrak()
    {
        $pengajuan_id = $this->input->post('pengajuan_id');
        if (!$pengajuan_id) {
            echo json_encode(['success' => false, 'message' => 'ID pengajuan tidak valid']);
            return;
        }

        $pengajuan = $this->db->get_where('tbl_pengajuan_kontrak', ['id' => $pengajuan_id])->row();
        if (!$pengajuan) {
            echo json_encode(['success' => false, 'message' => 'Data pengajuan tidak ditemukan']);
            return;
        }

        $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $pengajuan->karyawan_id])->row();
        if (!$karyawan) {
            echo json_encode(['success' => false, 'message' => 'Data karyawan tidak ditemukan']);
            return;
        }

        $akhir_kontrak_baru = date('Y-m-d', strtotime("+" . $pengajuan->durasi_perpanjangan . " months", strtotime($karyawan->akhir_kontrak)));

        $this->db->trans_start();

        // Update pengajuan
        $this->db->where('id', $pengajuan_id);
        $this->db->update('tbl_pengajuan_kontrak', [
            'status' => 'Approved',
            'responded_at' => date('Y-m-d H:i:s'),
            'responded_by' => $this->session->userdata('user_id')
        ]);

        // Update karyawan
        $this->db->where('karyawan_id', $pengajuan->karyawan_id);
        $this->db->update('tbl_karyawan', [
            'akhir_kontrak' => $akhir_kontrak_baru
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal memproses pengajuan']);
        } else {
            if (method_exists($this, 'log_activity')) {
                $this->log_activity($this->session->userdata('user_id'), 'Approve_Kontrak', 'HR', $pengajuan_id);
            }
            echo json_encode(['success' => true, 'message' => 'Kontrak berhasil diperpanjang sampai ' . date('d M Y', strtotime($akhir_kontrak_baru))]);
        }
    }
}
