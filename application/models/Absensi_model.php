<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'absensi';
    }

    public function get_all()
    {
        return $this->get_all_absensi();
    }

    public function simpan_absen($data)
    {
        if (isset($data['karyawan_id']) && isset($data['tanggal'])) {
            return $this->insert($data);
        }
        return false;
    }

    public function get_all_absensi()
    {
        $this->db->select('a.*, k.nama_lengkap, k.nik');
        $this->db->from($this->table . ' a');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left');
        $this->db->join("users u", "u.karyawan_id = k.karyawan_id", "left");
        $this->db->where("u.role_id !=", 17);
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');
        $this->db->where('k.nik NOT LIKE', 'STATION-%');
        $this->db->where('k.nik NOT LIKE', 'TERM%');
        $this->db->order_by('a.tanggal DESC, a.jam_masuk DESC');
        return $this->db->get()->result();
    }

    public function get_filtered_absensi($tanggal = '', $status = '', $karyawan_id = '')
    {
        $this->db->select('a.*, k.nama_lengkap, k.nik, p.nama_perusahaan, p.radius_meter, p.latitude as lat_perusahaan, p.longitude as long_perusahaan, u.user_id');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_perusahaan p', 'p.id_perusahaan = k.perusahaan_id', 'left');
        $this->db->join("users u", "u.karyawan_id = k.karyawan_id", "left");
        $this->db->join($this->table . ' a', 'k.karyawan_id = a.karyawan_id' . (!empty($tanggal) ? ' AND a.tanggal = "' . $tanggal . '"' : ''), 'left');
        $this->db->join('tbl_jam_kerja jk', 'jk.urutan = (DAYOFWEEK(a.tanggal) + 5) % 7 + 1', 'left');

        $this->db->where('k.status_karyawan !=', 'Tidak Aktif');
        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $this->db->group_start();
        $this->db->where('u.role_id !=', 17);
        $this->db->or_where('u.role_id IS NULL');
        $this->db->group_end();
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');
        $this->db->where('k.nik NOT LIKE', 'STATION-%');
        $this->db->where('k.nik NOT LIKE', 'TERM%');

        if (!empty($karyawan_id)) {
            $this->db->where('k.karyawan_id', $karyawan_id);
        }

        if (!empty($status)) {
            if ($status == 'Alpha') {
                $this->db->group_start();
                $this->db->where('a.status', 'Alpha');
                $this->db->or_where('a.id IS NULL');
                $this->db->group_end();
            } elseif ($status == 'Terlambat') {
                $this->db->where('a.status', 'Hadir');
                $this->db->where('a.jam_masuk > ADDTIME(jk.jam_masuk, "00:00:59")', null, false);
            } elseif ($status == 'Hadir') {
                $this->db->where('a.status', 'Hadir');
                $this->db->where('a.jam_masuk <= ADDTIME(jk.jam_masuk, "00:00:59")', null, false);
            } elseif ($status == 'Cuti') {
                $this->db->group_start();
                $this->db->where_in('a.status', ['Cuti', 'Cuti Keluarga', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Menikah']);
                $this->db->or_where('a.keterangan LIKE', '%cuti%');
                $this->db->group_end();
            } else {
                $this->db->where('a.status', $status);
            }
        }

        $this->db->order_by('a.jam_masuk ASC, k.nama_lengkap ASC');
        return $this->db->get()->result();
    }

    public function get_attendance_by_date($tanggal)
    {
        $this->db->select('a.*, k.nama_lengkap');
        $this->db->from($this->table . ' a');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left');
        $this->db->join("users u", "u.karyawan_id = k.karyawan_id", "left");
        $this->db->group_start();
        $this->db->where("u.role_id !=", 17);
        $this->db->or_where("u.role_id IS NULL");
        $this->db->group_end();
        $this->db->where("k.nama_lengkap NOT LIKE '%Terminal%'");
        $this->db->where("k.nama_lengkap NOT LIKE '%DUMMY%'");
        $this->db->where("k.nama_lengkap NOT LIKE 'DEPT.%'");
        $this->db->where("k.nama_lengkap NOT LIKE 'OPERATOR %'");
        $this->db->where("k.nik NOT LIKE 'STATION-%'");
        $this->db->where("k.nik NOT LIKE 'TERM%'");
        $this->db->where('a.tanggal', $tanggal);
        return $this->db->get()->result();
    }

    public function get_by_id($id)
    {
        $this->db->select('a.*, k.nama_lengkap, k.nik');
        $this->db->from($this->table . ' a');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left');
        $this->db->where('a.id', $id);
        return $this->db->get()->row();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($where, $data)
    {
        $this->db->where($where);
        return $this->db->update($this->table, $data);
    }

    public function update_by_id($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function check_existing_attendance($karyawan_id, $tanggal)
    {
        $this->db->where('karyawan_id', $karyawan_id);
        $this->db->where('tanggal', $tanggal);
        $this->db->where('status', 'Hadir');
        return $this->db->get($this->table)->row();
    }

    public function get_rekap_by_karyawan($karyawan_id, $bulan = '')
    {
        if (empty($bulan)) {
            $bulan = date('Y-m');
        }

        $this->db->select('a.*, k.nama_lengkap, k.nik');
        $this->db->from($this->table . ' a');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left');
        $this->db->where('a.karyawan_id', $karyawan_id);
        $this->db->where('DATE_FORMAT(a.tanggal, "%Y-%m") =', $bulan);
        $this->db->order_by('a.tanggal ASC');
        return $this->db->get()->result();
    }

    public function get_rekap_all_karyawan($start_date = '', $end_date = '', $nama = '', $jabatan = '', $divisi = '')
    {
        if (empty($start_date)) {
            $start_date = date('Y-m-01');
        }
        if (empty($end_date)) {
            $end_date = date('Y-m-t', strtotime($start_date));
        }

        $this->db->select('k.karyawan_id, k.nama_lengkap, k.nik, k.foto, k.perusahaan_id, j.nama_jabatan as jabatan, d.divisi_nama as divisi');
        $this->db->select('COUNT(CASE WHEN a.status = \'Hadir\' THEN 1 END) as total_hadir', FALSE);
        $this->db->select('COUNT(CASE WHEN a.status = \'Izin\' OR ((a.status = \'Alpha\' OR a.status = \'\' OR a.status IS NULL) AND a.keterangan LIKE \'%izin%\') THEN 1 END) as total_izin', FALSE);
        $this->db->select('COUNT(CASE WHEN a.status = \'Sakit\' OR ((a.status = \'Alpha\' OR a.status = \'\' OR a.status IS NULL) AND a.keterangan LIKE \'%sakit%\') THEN 1 END) as total_sakit', FALSE);
        $this->db->select('COUNT(CASE WHEN a.status LIKE \'%Cuti%\' OR ((a.status = \'Alpha\' OR a.status = \'\' OR a.status IS NULL) AND a.keterangan LIKE \'%cuti%\') THEN 1 END) as total_cuti', FALSE);
        $this->db->select('COUNT(CASE WHEN a.status = \'Alpha\' THEN 1 END) as total_alpha_explicit', FALSE);
        $this->db->select('COUNT(CASE WHEN a.jam_masuk > ADDTIME(jk.jam_masuk, "00:00:59") AND a.status = \'Hadir\' THEN 1 END) as total_terlambat', FALSE);
        $this->db->select('SUM(CASE WHEN a.status = \'Hadir\' AND a.jam_pulang > jk.jam_pulang THEN TIMESTAMPDIFF(MINUTE, jk.jam_pulang, a.jam_pulang) ELSE 0 END) as total_lembur_minutes', FALSE);
        $this->db->select('SUM(CASE WHEN a.status = \'Hadir\' AND a.jam_masuk > ADDTIME(jk.jam_masuk, "00:00:59") THEN TIMESTAMPDIFF(MINUTE, jk.jam_masuk, a.jam_masuk) ELSE 0 END) as total_late_minutes', FALSE);
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left');
        $this->db->join($this->table . ' a', 'k.karyawan_id = a.karyawan_id AND a.tanggal >= "' . $start_date . '" AND a.tanggal <= "' . $end_date . '"', 'left');
        $this->db->join('tbl_jam_kerja jk', 'jk.urutan = (DAYOFWEEK(a.tanggal) + 5) % 7 + 1', 'left');
        $this->db->join('users u', 'u.karyawan_id = k.karyawan_id', 'left');
        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');
        $this->db->where('k.nik NOT LIKE', 'STATION-%');
        $this->db->where('k.nik NOT LIKE', 'TERM%');

        if (!empty($nama)) {
            $this->db->like('k.nama_lengkap', $nama);
        }
        if (!empty($jabatan)) {
            $this->db->where('j.nama_jabatan', $jabatan);
        }
        if (!empty($divisi)) {
            $this->db->where('d.divisi_nama', $divisi);
        }

        $this->db->group_start();
        $this->db->where('u.role_id !=', 17);
        $this->db->or_where('u.role_id IS NULL');
        $this->db->group_end();
        $this->db->group_by('k.karyawan_id');
        $this->db->order_by('k.nama_lengkap ASC');
        $result = $this->db->get()->result();

        // Get Holidays for this range
        $holidays = $this->db->select('tanggal')
            ->where('tanggal >=', $start_date)
            ->where('tanggal <=', $end_date)
            ->get('hari_libur')
            ->result_array();

        $holiday_dates = array_column($holidays, 'tanggal');

        $workingDays = 0;
        $today = date('Y-m-d');

        $current = strtotime($start_date);
        $last = strtotime($end_date);

        while ($current <= $last) {
            $date = date('Y-m-d', $current);

            // Stop counting at today if end_date is in the future
            if ($date > $today) {
                break;
            }

            $dayOfWeek = date('N', $current); // 1 (Mon) to 7 (Sun)
            $isHoliday = in_array($date, $holiday_dates);

            if ($dayOfWeek <= 6 && !$isHoliday) { // Mon-Sat AND Not Holiday
                $workingDays++;
            }

            $current = strtotime('+1 day', $current);
        }

        // Update Alpha based on logic
        foreach ($result as $row) {
            $total_recorded = $row->total_hadir + $row->total_izin + $row->total_sakit + $row->total_cuti;
            $calculated_alpha = max(0, $workingDays - $total_recorded);
            $row->total_alpha = $calculated_alpha;
        }

        return $result;
    }

    public function get_attendance_summary($tanggal)
    {
        $this->db->select('status, COUNT(*) as total');
        $this->db->from($this->table);
        $this->db->where('tanggal', $tanggal);
        $this->db->group_by('status');
        return $this->db->get()->result();
    }

    public function get_late_employees($tanggal, $perusahaan_id = null)
    {
        $this->db->select('a.*, k.nama_lengkap, k.nik, k.foto');
        $this->db->from($this->table . ' a');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left');
        $this->db->join('users u', 'u.karyawan_id = k.karyawan_id', 'left');
        $this->db->join('tbl_jam_kerja jk', 'jk.urutan = (DAYOFWEEK(a.tanggal) + 5) % 7 + 1', 'left');
        $this->db->where('a.tanggal', $tanggal);
        $this->db->where('a.status', 'Hadir');
        $this->db->where('u.role_id !=', 17);
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');
        $this->db->where('k.nik NOT LIKE', 'STATION-%');
        $this->db->where('k.nik NOT LIKE', 'TERM%');
        $this->db->where('a.jam_masuk > ADDTIME(jk.jam_masuk, "00:00:59")', null, false);

        if ($perusahaan_id) {
            $this->db->where('k.perusahaan_id', $perusahaan_id);
        }

        $this->db->order_by('a.jam_masuk ASC');
        return $this->db->get()->result();
    }

    public function get_absent_employees($tanggal)
    {
        $this->db->select('k.karyawan_id, k.nama_lengkap, k.nik, j.nama_jabatan');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->join('users u', 'u.karyawan_id = k.karyawan_id', 'left');
        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');
        $this->db->where('k.nik NOT LIKE', 'STATION-%');
        $this->db->where('k.nik NOT LIKE', 'TERM%');
        $this->db->where('k.status_karyawan !=', 'Tidak Aktif');
        $this->db->group_start();
        $this->db->where('u.role_id !=', 17);
        $this->db->or_where('u.role_id IS NULL');
        $this->db->group_end();
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');
        $this->db->where('k.nik NOT LIKE', 'STATION-%');
        $this->db->where('k.nik NOT LIKE', 'TERM%');
        $this->db->where('k.karyawan_id NOT IN (SELECT karyawan_id FROM ' . $this->table . ' WHERE tanggal = "' . $tanggal . '")');
        $this->db->order_by('k.nama_lengkap ASC');
        return $this->db->get()->result();
    }

    public function get_monthly_report($bulan, $karyawan_id = '')
    {
        $this->db->select('a.*, k.nama_lengkap, k.nik, j.nama_jabatan');
        $this->db->from($this->table . ' a');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');
        $this->db->where('k.nik NOT LIKE', 'STATION-%');
        $this->db->where('k.nik NOT LIKE', 'TERM%');
        $this->db->where('DATE_FORMAT(a.tanggal, "%Y-%m") =', $bulan);

        if (!empty($karyawan_id)) {
            $this->db->where('a.karyawan_id', $karyawan_id);
        }

        $this->db->order_by('a.tanggal ASC, k.nama_lengkap ASC');
        return $this->db->get()->result();
    }

    public function get_attendance_stats_by_period($start_date, $end_date)
    {
        $this->db->select('DATE(a.tanggal) as tanggal');
        $this->db->select('COUNT(CASE WHEN a.status = \'Hadir\' THEN 1 END) as hadir', FALSE);
        $this->db->select('COUNT(CASE WHEN a.status = \'Izin\' OR ((a.status = \'Alpha\' OR a.status = \'\' OR a.status IS NULL) AND a.keterangan LIKE \'%izin%\') THEN 1 END) as izin', FALSE);
        $this->db->select('COUNT(CASE WHEN a.status = \'Sakit\' OR ((a.status = \'Alpha\' OR a.status = \'\' OR a.status IS NULL) AND a.keterangan LIKE \'%sakit%\') THEN 1 END) as sakit', FALSE);
        $this->db->select('COUNT(CASE WHEN a.status IN (\'Cuti\', \'Cuti Keluarga\', \'Cuti Sakit\', \'Cuti Melahirkan\', \'Cuti Menikah\') OR ((a.status = \'Alpha\' OR a.status = \'\' OR a.status IS NULL) AND a.keterangan LIKE \'%cuti%\') THEN 1 END) as cuti', FALSE);
        $this->db->select('COUNT(CASE WHEN a.status = \'Alpha\' AND (a.keterangan IS NULL OR a.keterangan NOT LIKE \'%cuti%\' AND a.keterangan NOT LIKE \'%sakit%\' AND a.keterangan NOT LIKE \'%izin%\') THEN 1 END) as alpha', FALSE);
        $this->db->from($this->table . ' a');
        $this->db->where('a.tanggal >=', $start_date);
        $this->db->where('a.tanggal <=', $end_date);
        $this->db->group_by('DATE(a.tanggal)');
        $this->db->order_by('a.tanggal ASC');
        return $this->db->get()->result();
    }

    public function get_employee_attendance_history($karyawan_id, $limit = 30)
    {
        $this->db->select('a.*, k.nama_lengkap, k.nik');
        $this->db->from($this->table . ' a');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left');
        $this->db->where('a.karyawan_id', $karyawan_id);
        $this->db->order_by('a.tanggal DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function get_today_attendance()
    {
        $today = date('Y-m-d');
        $this->db->select('a.*, k.nama_lengkap, k.nik, k.foto, j.nama_jabatan');
        $this->db->from($this->table . ' a');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->where('a.tanggal', $today);
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');
        $this->db->where('k.nik NOT LIKE', 'STATION-%');
        $this->db->where('k.nik NOT LIKE', 'TERM%');
        $this->db->order_by('a.jam_masuk ASC');
        return $this->db->get()->result();
    }

    public function get_absensi_hari_ini()
    {
        return $this->get_today_attendance();
    }

    public function get_attendance_by_employee($karyawan_id, $start_date, $end_date)
    {
        $this->db->select('a.*, k.nama_lengkap, k.nik');
        $this->db->from($this->table . ' a');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left');
        $this->db->where('a.karyawan_id', $karyawan_id);
        $this->db->where('a.tanggal >=', $start_date);
        $this->db->where('a.tanggal <=', $end_date);
        $this->db->order_by('a.tanggal ASC');
        return $this->db->get()->result();
    }

    public function get_attendance_count_by_status($tanggal, $status)
    {
        $this->db->where('tanggal', $tanggal);
        $this->db->where('status', $status);
        return $this->db->count_all_results($this->table);
    }

    public function get_total_working_hours($karyawan_id, $start_date, $end_date)
    {
        $this->db->join('tbl_jam_kerja jk', 'jk.urutan = (DAYOFWEEK(absensi.tanggal) + 5) % 7 + 1', 'left');
        $this->db->select('SUM(TIMESTAMPDIFF(MINUTE, absensi.jam_masuk, COALESCE(absensi.jam_pulang, jk.jam_pulang))) as total_minutes');
        $this->db->from($this->table);
        $this->db->where('karyawan_id', $karyawan_id);
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        $this->db->where('status', 'Hadir');
        $result = $this->db->get()->row();
        return $result ? round($result->total_minutes / 60, 2) : 0;
    }

    public function get_attendance_percentage($karyawan_id, $bulan)
    {
        // Get total working days in month
        $year = substr($bulan, 0, 4);
        $month = substr($bulan, 5, 2);
        $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Get attended days
        $this->db->where('karyawan_id', $karyawan_id);
        $this->db->where('DATE_FORMAT(tanggal, "%Y-%m") =', $bulan);
        $this->db->where('status', 'Hadir');
        $attended_days = $this->db->count_all_results($this->table);

        return $total_days > 0 ? round(($attended_days / $total_days) * 100, 2) : 0;
    }

    public function get_attendance_trend($karyawan_id, $months = 6)
    {
        $this->db->select('DATE_FORMAT(tanggal, "%Y-%m") as bulan');
        $this->db->select('COUNT(CASE WHEN status = \'Hadir\' THEN 1 END) as hadir', FALSE);
        $this->db->select('COUNT(CASE WHEN status = \'Izin\' THEN 1 END) as izin', FALSE);
        $this->db->select('COUNT(CASE WHEN status = \'Sakit\' THEN 1 END) as sakit', FALSE);
        $this->db->select('COUNT(CASE WHEN status IN (\'Cuti\', \'Cuti Keluarga\', \'Cuti Sakit\', \'Cuti Melahirkan\', \'Cuti Menikah\') THEN 1 END) as cuti', FALSE);
        $this->db->select('COUNT(CASE WHEN status = \'Alpha\' THEN 1 END) as alpha', FALSE);
        $this->db->from($this->table);
        $this->db->where('karyawan_id', $karyawan_id);
        $this->db->where('tanggal >=', date('Y-m-d', strtotime('-' . $months . ' months')));
        $this->db->group_by('DATE_FORMAT(tanggal, "%Y-%m")');
        $this->db->order_by('bulan ASC');
        return $this->db->get()->result();
    }

    public function get_bulk_attendance_for_date($tanggal)
    {
        $this->db->select('k.karyawan_id, k.nama_lengkap, k.nik, d.divisi_nama, a.id, a.jam_masuk, a.jam_pulang, a.status, a.keterangan');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left');
        $this->db->join('users u', 'u.karyawan_id = k.karyawan_id', 'left');
        $this->db->join($this->table . ' a', 'k.karyawan_id = a.karyawan_id AND a.tanggal = \'' . $tanggal . '\'', 'left');

        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');
        $this->db->where('k.nik NOT LIKE', 'STATION-%');
        $this->db->where('k.nik NOT LIKE', 'TERM%');
        $this->db->group_start();
        $this->db->where('u.role_id !=', 17);
        $this->db->or_where('u.role_id IS NULL');
        $this->db->group_end();

        $this->db->order_by('k.nama_lengkap ASC');
        return $this->db->get()->result();
    }
}
