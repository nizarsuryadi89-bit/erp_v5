<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Karyawan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Payroll_model');
        $this->load->model('M_master');
        $this->load->model('Absensi_model');
        $this->load->model('Penggajian_model');
        $this->load->library('form_validation'); // Load validation library
        $this->load->library('fungsi');
        $this->_init_dokumen_table();
        $this->_init_gaji_manual_table();
        $this->_init_payroll_fields();
        $this->_init_perusahaan_payroll_fields();
    }

    private function _get_payroll_range($bulan, $perusahaan_id = '')
    {
        return $this->Payroll_model->_get_payroll_range($bulan, $perusahaan_id);
    }

    private function _init_payroll_fields()
    {
        $fields = $this->db->list_fields('tbl_karyawan');
        if (!in_array('gaji_pokok', $fields)) {
            $this->db->query("ALTER TABLE tbl_karyawan ADD COLUMN gaji_pokok DECIMAL(15,2) DEFAULT 0 AFTER status_karyawan");
        }
        if (!in_array('nominal_lembur', $fields)) {
            $this->db->query("ALTER TABLE tbl_karyawan ADD COLUMN nominal_lembur DECIMAL(15,2) DEFAULT 0 AFTER gaji_pokok");
        }
    }

    private function _init_perusahaan_payroll_fields()
    {
        $fields = $this->db->list_fields('tbl_perusahaan');
        if (!in_array('payroll_start_day', $fields)) {
            $this->db->query("ALTER TABLE tbl_perusahaan ADD COLUMN payroll_start_day INT DEFAULT 26");
        }
        if (!in_array('payroll_end_day', $fields)) {
            $this->db->query("ALTER TABLE tbl_perusahaan ADD COLUMN payroll_end_day INT DEFAULT 25");
        }
    }


    private function _init_gaji_manual_table()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS tbl_gaji_manual (
            id INT AUTO_INCREMENT PRIMARY KEY,
            karyawan_id INT NOT NULL,
            bulan VARCHAR(7) NOT NULL,
            gaji_pokok DECIMAL(15,2),
            tunjangan_jabatan DECIMAL(15,2),
            tunjangan_transportasi DECIMAL(15,2),
            bonus_kinerja DECIMAL(15,2),
            bonus_kehadiran DECIMAL(15,2),
            uang_lembur DECIMAL(15,2),
            potongan_alpha DECIMAL(15,2),
            kasbon DECIMAL(15,2) DEFAULT 0,
            total_gaji DECIMAL(15,2),
            keterangan TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY (karyawan_id, bulan)
        )");

        $fields = $this->db->list_fields('tbl_gaji_manual');
        $new_cols = [
            'kasbon' => 'DECIMAL(15,2) DEFAULT 0',
            'biaya_makan_lembur' => 'DECIMAL(15,2) DEFAULT 0',
            'reward' => 'DECIMAL(15,2) DEFAULT 0',
            'bpjs' => 'DECIMAL(15,2) DEFAULT 0',
            'tunjangan_extra' => 'DECIMAL(15,2) DEFAULT 0',
            'tunjangan_kinerja_manual' => 'DECIMAL(15,2) DEFAULT 0',
            'potongan_terlambat' => 'DECIMAL(15,2) DEFAULT 0',
            'pinjaman' => 'DECIMAL(15,2) DEFAULT 0'
        ];

        foreach ($new_cols as $col => $type) {
            if (!in_array($col, $fields)) {
                $this->db->query("ALTER TABLE tbl_gaji_manual ADD COLUMN $col $type");
            }
        }
    }

    private function _init_dokumen_table()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS tbl_karyawan_dokumen (
            id INT AUTO_INCREMENT PRIMARY KEY,
            karyawan_id INT NOT NULL,
            jenis_dokumen VARCHAR(50) NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(karyawan_id)
        )");
    }

    private function get_karyawan_data($jabatan_id = null)
    {
        $this->db->select('*');
        $this->db->from('tbl_karyawan');
        if ($jabatan_id !== null) {
            $this->db->where('jabatan_id', $jabatan_id);
        }
        return $this->db->get()->result();
    }

    private function get_karyawan_count($jabatan_id = null, $jenis_kelamin = null, $perusahaan_id = null)
    {
        $this->db->from('tbl_karyawan');
        $this->db->where("nik NOT LIKE 'STATION%'");
        $this->db->where("nama_lengkap NOT LIKE '%Terminal%'");
        $this->db->where("nama_lengkap NOT LIKE '%DTF%'");
        $this->db->where("nama_lengkap NOT LIKE 'OPERATOR %'");
        if ($perusahaan_id !== null) {
            if ($perusahaan_id === 'cabang') {
                $this->db->where('perusahaan_id !=', 1);
            } else {
                $this->db->where('perusahaan_id', $perusahaan_id);
            }
        }

        if ($jabatan_id !== null) {
            $this->db->where('divisi_id', $jabatan_id);
        }
        if ($jenis_kelamin !== null) {
            $this->db->where('jenis_kelamin', $jenis_kelamin);
        }
        return $this->db->count_all_results();
    }

    private function get_karyawan_by_divisi()
    {
        $this->db->select('d.divisi_id, d.divisi_nama, COUNT(k.karyawan_id) as jumlah_karyawan');
        $this->db->from('tbl_divisi d');
        $this->db->join('tbl_karyawan k', "d.divisi_id = k.divisi_id AND k.nik NOT LIKE 'STATION%'", 'left');
        $this->db->group_by('d.divisi_id, d.divisi_nama');
        $this->db->order_by('d.divisi_id', 'asc');
        return $this->db->get()->result();
    }

    private function _get_karyawan_stats()
    {
        // Total Karyawan (Excluding Owner/Directors and Dummy Accounts)
        $this->db->where("nik NOT LIKE 'STATION%'");
        $this->db->where("nik NOT LIKE 'TERM%'");
        $this->db->where('nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('nama_lengkap NOT LIKE', '%OPERATOR%');
        $this->db->where('nama_lengkap NOT LIKE', '%DEPT%');
        $this->db->where('nama_lengkap NOT LIKE', '%DTG%');
        $this->db->where('nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('nama_lengkap NOT LIKE', '%WORK CENTER%');
        $this->db->where('nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('nama_lengkap NOT LIKE', '%A3+%');
        $this->db->where('divisi_id !=', 8);
        $this->db->where_not_in('jabatan_id', [1, 2, 3]);
        if (!empty($this->session->userdata('perusahaan_id')) && $this->session->userdata('perusahaan_id') != 1) {
            $this->db->where('perusahaan_id', $this->session->userdata('perusahaan_id'));
        }
        $all = $this->db->get('tbl_karyawan')->result();

        $male = 0;
        $female = 0;
        $active = 0;

        foreach ($all as $row) {
            if ($row->jenis_kelamin == 'L' || $row->jenis_kelamin == 'Laki-laki')
                $male++;
            if ($row->jenis_kelamin == 'P' || $row->jenis_kelamin == 'Perempuan')
                $female++;
            if ($row->status_karyawan == 'Aktif' || $row->status_karyawan == '1')
                $active++;
        }

        return [
            'total' => count($all),
            'male' => $male,
            'female' => $female,
            'active' => $active
        ];
    }

    private function get_karyawan_by_divisi_perusahaan($perusahaan_id)
    {
        $this->db->select('d.divisi_id, d.divisi_nama, COUNT(k.karyawan_id) as jumlah_karyawan');
        $this->db->from('tbl_divisi d');
        $this->db->join('tbl_karyawan k', "d.divisi_id = k.divisi_id AND k.nik NOT LIKE 'STATION%'", 'left');
        $this->db->where('k.perusahaan_id', $perusahaan_id);
        $this->db->group_by('d.divisi_id, d.divisi_nama');
        $this->db->order_by('d.divisi_id', 'asc');
        return $this->db->get()->result();
    }

    private function _prepare_view_data($title, $extra_data = [])
    {
        $userid = $this->session->userdata('id');
        $controller = $this->uri->segment(1);
        $function = $this->uri->segment(2);

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $controller,
            'function' => $function,
            'title' => $title,
            'logo' => $conf['perusahaan']['logo'] ?? '',
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            'submenu' => $this->M_master->getmenu($controller)->result(),
        );

        return array_merge($data, $extra_data);
    }

    public function index()
    {
        $extra_data = array(
            'total_karyawan' => $this->get_karyawan_count(),
            'total_akun' => $this->db->join('tbl_karyawan k', 'a.karyawan_id = k.karyawan_id', 'left')->get('users a')->num_rows(),

            // Dynamic Company Stats
            'company_stats' => array_map(function ($company) {
                return (object) [
                    'id_perusahaan' => $company->id_perusahaan,
                    'nama_perusahaan' => $company->nama_perusahaan,
                    'total_karyawan' => $this->get_karyawan_count(null, null, $company->id_perusahaan)
                ];
            }, $this->db->get('tbl_perusahaan')->result()),

            'total_deskprint' => $this->get_karyawan_count('2'),
            'total_kasir' => $this->get_karyawan_count('3'),
            'total_operator' => $this->get_karyawan_count('8'),
            'total_owner' => $this->get_karyawan_count('17'),
            'total_admin' => $this->get_karyawan_count('1'),
            'karyawan_by_divisi' => $this->get_karyawan_by_divisi(),
            'karyawan_perusahaan_2' => $this->get_karyawan_by_divisi_perusahaan(2),
            'karyawan_perusahaan_3' => $this->get_karyawan_by_divisi_perusahaan(3),
        );

        $data = $this->_prepare_view_data('Dashboard Human Resourse', $extra_data);
        $this->load_admin_view('admin/karyawan/index', $data);
    }

    public function karyawan($gender = null, $divisi = null, $jabatan = null, $status = null)
    {
        // Data will be loaded via AJAX for better performance
        $karyawan = [];

        $extra_data = array(
            'table' => 'tbl_karyawan',
            'karyawan' => $karyawan,
            'jml' => 0, // Will be updated by AJAX
            'initial_gender' => $gender,
            'initial_divisi' => $divisi,
            'initial_jabatan' => $jabatan,
            'initial_status' => $status,
            'total_karyawan' => $this->get_karyawan_count(),
            'total_akun' => $this->db->join('tbl_karyawan k', 'a.karyawan_id = k.karyawan_id', 'left')->get('users a')->num_rows(),

            // Dynamic Company Stats
            'company_stats' => array_map(function ($company) {
                return (object) [
                    'id_perusahaan' => $company->id_perusahaan,
                    'nama_perusahaan' => $company->nama_perusahaan,
                    'total_karyawan' => $this->get_karyawan_count(null, null, $company->id_perusahaan)
                ];
            }, $this->db->get('tbl_perusahaan')->result()),

            'all_jabatan' => $this->db->get('tbl_jabatan')->result(),
            'all_divisi' => $this->db->get('tbl_divisi')->result(),
            'all_perusahaan' => $this->db->get('tbl_perusahaan')->result(),
        );

        $title = 'Daftar Karyawan';
        if ($gender == 'L')
            $title .= ' Laki-laki';
        if ($gender == 'P')
            $title .= ' Perempuan';

        $data = $this->_prepare_view_data($title, $extra_data);
        $this->load_admin_view('admin/karyawan/karyawan', $data);
    }

    public function karyawan_lakilaki()
    {
        $this->karyawan('L');
    }

    public function karyawan_perempuan()
    {
        $this->karyawan('P');
    }

    public function karyawan_deskprint()
    {
        $deskprint_divisi = $this->db->get_where('tbl_divisi', ['divisi_nama' => 'deskprint'])->row();
        $deskprint_id = $deskprint_divisi ? $deskprint_divisi->divisi_id : 0;
        $this->karyawan(null, $deskprint_id);
    }

    public function operator()
    {
        $divisi = $this->db->get_where('tbl_divisi', ['divisi_nama' => 'operator'])->row();
        $id = $divisi ? $divisi->divisi_id : 0;
        $this->karyawan(null, $id);
    }

    public function admin()
    {
        $divisi = $this->db->get_where('tbl_divisi', ['divisi_nama' => 'admin'])->row();
        $id = $divisi ? $divisi->divisi_id : 0;
        $this->karyawan(null, $id);
    }

    public function gudang()
    {
        $divisi = $this->db->get_where('tbl_divisi', ['divisi_nama' => 'gudang'])->row();
        $id = $divisi ? $divisi->divisi_id : 0;
        $this->karyawan(null, $id);
    }

    public function kasir()
    {
        $divisi = $this->db->get_where('tbl_divisi', ['divisi_nama' => 'kasir'])->row();
        $id = $divisi ? $divisi->divisi_id : 0;
        $this->karyawan(null, $id);
    }

    public function penilaian_kinerja()
    {
        // Get filter parameters
        $bulan = $this->input->get('bulan', true) ?: date('Y-m');
        $karyawan_id = $this->input->get('karyawan_id', true) ?: '';
        $jabatan_id = $this->input->get('jabatan_id', true) ?: '';

        $range = $this->_get_payroll_range($bulan);

        $extra_data = array(
            'performance_data' => $this->get_performance_data($bulan, $karyawan_id, $jabatan_id, $range['start'], $range['end']),
            'stats' => $this->get_performance_stats($bulan, $karyawan_id, $jabatan_id),
            'karyawan_list' => $this->db->where('divisi_id !=', 8)->where_not_in('jabatan_id', [1, 2, 3])->where('nama_lengkap NOT LIKE', '%Terminal%')->where('nama_lengkap NOT LIKE', '%OPERATOR%')->where('nama_lengkap NOT LIKE', '%DEPT%')->where('nama_lengkap NOT LIKE', '%DTG%')->where('nama_lengkap NOT LIKE', '%DTF%')->where('nama_lengkap NOT LIKE', '%WORK CENTER%')->where('nama_lengkap NOT LIKE', '%LARGE FORMAT%')->where('nama_lengkap NOT LIKE', '%A3+%')->where("nik NOT LIKE", 'STATION%')->where("nik NOT LIKE", 'TERM%')->get('tbl_karyawan')->result(),
            'jabatan_list' => $this->db->get('tbl_jabatan')->result(),
            'bulan' => $bulan,
            'karyawan_id' => $karyawan_id,
            'jabatan_id' => $jabatan_id,
            'perusahaan' => ($this->session->userdata('perusahaan_id') == 1 || $this->session->userdata('level') == 1 || $this->session->userdata('level') == 21) ? 'Semua Perusahaan' : $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $this->session->userdata('perusahaan_id')])->row('nama_perusahaan'),
        );

        $data = $this->_prepare_view_data('Penilaian Kinerja Karyawan', $extra_data);
        $this->load_admin_view('admin/karyawan/penilaian_kinerja', $data);
    }

    /**
     * Get performance data for employees (Attendance-Based)
     */
    private function get_performance_data($bulan, $karyawan_id = '', $jabatan_id = '', $start_date = '', $end_date = '')
    {
        return $this->Payroll_model->get_performance_data($bulan, $karyawan_id, $jabatan_id, $start_date, $end_date);
    }

    /**
     * Get aggregated performance statistics
     */
    private function get_performance_stats($bulan, $karyawan_id = '', $jabatan_id = '')
    {
        $range = $this->_get_payroll_range($bulan);
        $performance_data = $this->get_performance_data($bulan, $karyawan_id, $jabatan_id, $range['start'], $range['end']);

        $total_employees = count($performance_data);
        $total_hadir = 0;
        $total_telat = 0;
        $total_alpha = 0;
        $total_sakit = 0;
        $total_izin = 0;
        $total_score = 0;

        foreach ($performance_data as $perf) {
            $total_hadir += $perf->total_hadir;
            $total_telat += $perf->total_telat;
            $total_alpha += $perf->total_alpha;
            $total_sakit += $perf->total_sakit;
            $total_izin += $perf->total_izin;
            $total_score += $perf->skor_kinerja;
        }

        $avg_score = $total_employees > 0 ? $total_score / $total_employees : 0;

        return array(
            'total_employees' => $total_employees,
            'total_hadir' => $total_hadir,
            'total_telat' => $total_telat,
            'total_alpha' => $total_alpha,
            'total_sakit' => $total_sakit,
            'total_izin' => $total_izin,
            'avg_score' => round($avg_score, 2)
        );
    }

    private function get_working_days($bulan, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $start_date = $bulan . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
        }

        // Get Holidays
        $this->db->select('tanggal');
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        $holidays = $this->db->get('hari_libur')->result_array();
        $holiday_dates = array_column($holidays, 'tanggal');

        $workingDays = 0;
        $today = date('Y-m-d');

        $current = strtotime($start_date);
        $last = strtotime($end_date);

        while ($current <= $last) {
            $date = date('Y-m-d', $current);
            if ($date > $today) {
                break;
            }

            $dayOfWeek = date('N', $current); // 1 (Mon) to 7 (Sun)
            if ($dayOfWeek <= 6 && !in_array($date, $holiday_dates)) { // Mon-Sat, not holiday
                $workingDays++;
            }
            $current = strtotime('+1 day', $current);
        }
        return $workingDays;
        $range = $this->_get_payroll_range($bulan);
        $start_date = $range['start'];
        $end_date = $range['end'];

        // Total active employees this month
        $this->db->select('COUNT(DISTINCT k.karyawan_id) as total_karyawan_aktif');
        $this->db->from('absensi');
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        $this->db->where('status', 'Hadir');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = absensi.karyawan_id');
        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        if ($this->session->userdata('logged_in')) {
            if (!empty($this->session->userdata('perusahaan_id')) && $this->session->userdata('perusahaan_id') != 1) {
                // Already joined above with 'tbl_karyawan k'
                $this->db->where('k.perusahaan_id', $this->session->userdata('perusahaan_id'));
            }
        }
        $total_active = $this->db->get()->row()->total_karyawan_aktif;

        // Total Late
        $this->db->select('COUNT(*) as total_telat');
        $this->db->from('absensi');
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        $this->db->where('status', 'Hadir');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = absensi.karyawan_id');
        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $this->db->where('jam_masuk >', '08:00:00');
        if ($this->session->userdata('logged_in')) {
            if (!empty($this->session->userdata('perusahaan_id')) && $this->session->userdata('perusahaan_id') != 1) {
                $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = absensi.karyawan_id');
                $this->db->where('tbl_karyawan.perusahaan_id', $this->session->userdata('perusahaan_id'));
            }
        }
        $total_late = $this->db->get()->row()->total_telat;

        // Average Discipline Score and Top Score
        $performance_data = $this->get_performance_data($bulan, '', '', $start_date, $end_date);
        $total_score = 0;
        $max_score = 0;

        foreach ($performance_data as $row) {
            $total_score += $row->skor_kinerja;
            if ($row->skor_kinerja > $max_score) {
                $max_score = $row->skor_kinerja;
            }
        }

        $avg_score = count($performance_data) > 0 ? round($total_score / count($performance_data), 1) : 0;

        return array(
            'total_karyawan_aktif' => $total_active,
            'total_telat' => $total_late,
            'rata_rata_skor' => $avg_score,
            'top_score' => $max_score
        );
    }

    /**
     * Get detailed activity for specific employee
     */
    public function detail_kinerja($karyawan_id)
    {
        $bulan = $this->input->get('bulan', true) ?: date('Y-m');

        $extra_data = array(
            'karyawan' => $this->db->select('k.*, j.nama_jabatan, o.operator_nama as divisi_nama, p.nama_perusahaan')
                ->from('tbl_karyawan k')
                ->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left')
                ->join('tbl_operator o', 'o.operator_id = k.divisi_id', 'left')
                ->join('tbl_perusahaan p', 'p.id_perusahaan = k.perusahaan_id', 'left')
                ->where('k.karyawan_id', $karyawan_id)
                ->get()->row(),
            'attendance_history' => $this->db->select('*')
                ->from('absensi')
                ->where('karyawan_id', $karyawan_id)
                ->where('DATE_FORMAT(tanggal, "%Y-%m") =', $bulan)
                ->order_by('tanggal', 'ASC')
                ->get()->result(),
            'bulan' => $bulan,
        );

        $data = $this->_prepare_view_data('Detail Kinerja Karyawan', $extra_data);
        $this->load_admin_view('admin/karyawan/detail_kinerja', $data);
    }

    /**
     * Export performance data to Excel
     */
    public function export_kinerja_excel()
    {
        $bulan = $this->input->get('bulan', true) ? $this->input->get('bulan', true) : date('Y-m');
        $karyawan_id = $this->input->get('karyawan_id', true) ? $this->input->get('karyawan_id', true) : '';
        $jabatan_id = $this->input->get('jabatan_id', true) ? $this->input->get('jabatan_id', true) : '';

        $range = $this->_get_payroll_range($bulan);
        $performance_data = $this->get_performance_data($bulan, $karyawan_id, $jabatan_id, $range['start'], $range['end']);

        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="laporan_kinerja_' . $bulan . '.xls"');

        $this->load->view('admin/karyawan/export_kinerja_excel', array(
            'performance_data' => $performance_data,
            'bulan' => $bulan
        ));
    }

    /**
     * Print performance report for specific employee
     */
    public function cetak_kinerja($karyawan_id)
    {
        $bulan = $this->input->get('bulan', true) ?: date('Y-m');
        $this->load->model('Absensi_model');

        $perf_data = $this->get_performance_data($bulan, $karyawan_id);

        if (empty($perf_data)) {
            show_404();
        }

        // Fetch company logo and details
        $perf = $perf_data[0];
        $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $karyawan_id])->row();
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $karyawan->perusahaan_id])->row();

        $extra_data = array(
            'perf' => $perf,
            'bulan' => $bulan,
            'perusahaan' => $perusahaan
        );

        $this->load->view('admin/karyawan/cetak_laporan_kinerja', $extra_data);
    }

    /**
     * Salary calculation application
     */
    /**
     * Laporan Payroll Bulanan (Alias/Redirect to Penggajian)
     */
    public function laporan_payroll_bulanan()
    {
        redirect('karyawan/penggajian');
    }

    public function update_payroll_range()
    {
        $perusahaan_id = $this->input->post('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $start_day = (int) $this->input->post('start_day', true);
        $end_day = (int) $this->input->post('end_day', true);
        
        if (!$perusahaan_id || !$start_day || !$end_day) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap!']);
            return;
        }
        
        $this->db->where('id_perusahaan', $perusahaan_id);
        $this->db->update('tbl_perusahaan', [
            'payroll_start_day' => $start_day,
            'payroll_end_day' => $end_day
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Periode penggajian berhasil diupdate!']);
    }

    public function penggajian()
    {
        // Get filter parameters
        $bulan = $this->input->get('bulan', true) ?: date('Y-m');
        $karyawan_id = $this->input->get('karyawan_id', true) ?: '';
        $jabatan_id = $this->input->get('jabatan_id', true) ?: '';
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: '';

        // Exclude Owner and Directors from list
        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $karyawan_list = $this->M_master->get_karyawan()->result();

        $salary_data = $this->get_salary_data($bulan, $karyawan_id, $jabatan_id, $perusahaan_id);

        if (empty($karyawan_id) && empty($jabatan_id)) {
            $total_employees = count($salary_data);
            $total_salary = 0; $total_overtime = 0; $total_meal = 0;
            $total_performance_bonus = 0; $total_attendance_bonus = 0; $total_alpha_penalty = 0;
            foreach ($salary_data as $salary) {
                $total_salary += ($salary['total_gaji'] ?? 0);
                $total_overtime += ($salary['uang_lembur'] ?? 0);
                $total_meal += ($salary['biaya_makan_lembur'] ?? 0);
                $total_performance_bonus += ($salary['tunjangan_kinerja_manual'] ?? 0);
                $total_attendance_bonus += ($salary['reward'] ?? 0);
                $total_alpha_penalty += ($salary['potongan_alpha'] ?? 0);
            }
            $avg_salary = $total_employees > 0 ? $total_salary / $total_employees : 0;
            $salary_stats = array(
                'total_employees' => $total_employees,
                'total_salary' => $total_salary,
                'avg_salary' => round($avg_salary, 0),
                'total_overtime' => $total_overtime,
                'total_meal' => $total_meal,
                'total_performance_bonus' => $total_performance_bonus,
                'total_attendance_bonus' => $total_attendance_bonus,
                'total_alpha_penalty' => $total_alpha_penalty
            );
        } else {
            $salary_stats = $this->get_salary_stats($bulan, $perusahaan_id);
        }

        $extra_data = array(
            'salary_data' => $salary_data,
            'salary_stats' => $salary_stats,
            'karyawan_list' => $karyawan_list,
            'jabatan_list' => $this->db->get('tbl_jabatan')->result(),
            'perusahaan_list' => $this->db->get('tbl_perusahaan')->result(),
            'bulan' => $bulan,
            'karyawan_id' => $karyawan_id,
            'jabatan_id' => $jabatan_id,
            'perusahaan_id' => $perusahaan_id,
            'payroll_range' => $this->_get_payroll_range($bulan),
        );

        $data = $this->_prepare_view_data('Sistem Penggajian Karyawan', $extra_data);
        $this->load_admin_view('admin/karyawan/penggajian', $data);
    }

    /**
     * Get comprehensive salary data including overtime and performance
     */
    private function get_salary_data($bulan, $karyawan_id = '', $jabatan_id = '', $perusahaan_id = '')
    {
        return $this->Payroll_model->get_salary_data($bulan, $karyawan_id, $jabatan_id, $perusahaan_id);
    }

    /**
     * Get salary statistics
     */
    private function get_salary_stats($bulan, $perusahaan_id = '')
    {
        $salary_data = $this->get_salary_data($bulan, '', '', $perusahaan_id);

        $total_employees = count($salary_data);
        $total_salary = 0;
        $total_overtime = 0;
        $total_meal = 0;
        $total_performance_bonus = 0;
        $total_attendance_bonus = 0;
        $total_alpha_penalty = 0;

        foreach ($salary_data as $salary) {
            $total_salary += ($salary['total_gaji'] ?? 0);
            $total_overtime += ($salary['uang_lembur'] ?? 0);
            $total_meal += ($salary['biaya_makan_lembur'] ?? 0);
            $total_performance_bonus += ($salary['tunjangan_kinerja_manual'] ?? 0);
            $total_attendance_bonus += ($salary['reward'] ?? 0);
            $total_alpha_penalty += ($salary['potongan_alpha'] ?? 0);
        }

        $avg_salary = $total_employees > 0 ? $total_salary / $total_employees : 0;

        return array(
            'total_employees' => $total_employees,
            'total_salary' => $total_salary,
            'avg_salary' => round($avg_salary, 0),
            'total_overtime' => $total_overtime,
            'total_meal' => $total_meal,
            'total_performance_bonus' => $total_performance_bonus,
            'total_attendance_bonus' => $total_attendance_bonus,
            'total_alpha_penalty' => $total_alpha_penalty
        );
    }

    /**
     * Get detailed salary for specific employee
     */
    public function detail_penggajian($karyawan_id)
    {
        $bulan = $this->input->get('bulan', true) ?: date('Y-m');

        $range = $this->_get_payroll_range($bulan);

        $extra_data = array(
            'karyawan' => $this->db->select('k.*, j.nama_jabatan, j.gaji_pokok as default_gaji, j.tunjangan_jabatan as default_tunjangan_jabatan, k.tunjangan_jabatan as individual_tunjangan_jabatan, d.operator_nama as divisi_nama, p.nama_perusahaan')
                ->from('tbl_karyawan k')
                ->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left')
                ->join('tbl_operator d', 'd.operator_id = k.divisi_id', 'left')
                ->join('tbl_perusahaan p', 'p.id_perusahaan = k.perusahaan_id', 'left')
                ->where('k.karyawan_id', $karyawan_id)
                ->get()->row(),
            'overtime_details' => $this->db->where('karyawan_id', $karyawan_id)
                ->where('status', 'disetujui')
                ->where('tanggal >=', $range['start'])
                ->where('tanggal <=', $range['end'])
                ->order_by('tanggal', 'ASC')
                ->get('tbl_lembur')->result(),
            'performance' => $this->get_performance_data($bulan, $karyawan_id, '', $range['start'], $range['end'])[0] ?? null,
            'attendance_details' => $this->db->where('karyawan_id', $karyawan_id)
                ->where('tanggal >=', $range['start'])
                ->where('tanggal <=', $range['end'])
                ->order_by('tanggal', 'ASC')
                ->get('absensi')->result(),
            'bulan' => $bulan,
            'payroll_range' => $range,
        );

        $data = $this->_prepare_view_data('Detail Penggajian Karyawan', $extra_data);
        $this->load_admin_view('admin/karyawan/detail_penggajian', $data);
    }

    /**
     * Export salary data to Excel
     */
    public function export_penggajian_excel()
    {
        $bulan = $this->input->get('bulan', true) ? $this->input->get('bulan', true) : date('Y-m');
        $karyawan_id = $this->input->get('karyawan_id', true) ? $this->input->get('karyawan_id', true) : '';
        $jabatan_id = $this->input->get('jabatan_id', true) ? $this->input->get('jabatan_id', true) : '';

        $salary_data = $this->get_salary_data($bulan, $karyawan_id, $jabatan_id);

        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="laporan_penggajian_' . $bulan . '.xls"');

        $this->load->view('admin/karyawan/export_penggajian_excel', array(
            'salary_data' => $salary_data,
            'bulan' => $bulan
        ));
    }


    public function absen_lembur()
    {
        $bulan = $this->input->get('bulan', true) ?: date('Y-m');
        $range = $this->_get_payroll_range($bulan);
        $start_date = $range['start'];
        $end_date = $range['end'];

        $tanggal = $this->input->get('tanggal', true) ?: date('Y-m-d');
        $day_num = date('N', strtotime($tanggal));
        $jk_rule = $this->db->get_where('tbl_jam_kerja', ['urutan' => $day_num])->row();
        $w_start_time_str = ($day_num == 6) ? $this->_get_lembur_config('w_start_sat', '15:00:00') : $this->_get_lembur_config('w_start_weekday', '16:00:00');
        $w_end_time_str = ($day_num == 6) ? $this->_get_lembur_config('w_end_sat', '16:00:00') : $this->_get_lembur_config('w_end_weekday', '18:00:00');

        // 1. Fetch all active employees
        // FIX: Filter konsisten dengan rekap bulanan & penggajian (exclude divisi owner & direksi)
        $all_employees = $this->db->select('karyawan_id, nama_lengkap, nik, foto, nominal_lembur')
            ->from('tbl_karyawan')
            ->where('nama_lengkap NOT LIKE', '%Terminal%')
            ->where('nama_lengkap NOT LIKE', '%OPERATOR%')
            ->where('nama_lengkap NOT LIKE', '%DEPT%')
            ->where('nama_lengkap NOT LIKE', '%DTG%')
            ->where('nama_lengkap NOT LIKE', '%DTF%')
            ->where('nama_lengkap NOT LIKE', '%WORK CENTER%')
            ->where('nama_lengkap NOT LIKE', '%LARGE FORMAT%')
            ->where('nama_lengkap NOT LIKE', '%A3+%')
            ->where("nik NOT LIKE", 'STATION%')
            ->where("nik NOT LIKE", 'TERM%')
            ->where('divisi_id !=', 8)
            ->where_not_in('jabatan_id', [1, 2, 3]);

        if (!empty($this->session->userdata('perusahaan_id')) && $this->session->userdata('perusahaan_id') != 1) {
            $this->db->where('perusahaan_id', $this->session->userdata('perusahaan_id'));
        }

        $all_employees = $this->db->get()->result();

        // 2. Fetch SPLs for the selected date - grouped by employee
        $this->db->where('tanggal', $tanggal);
        $spl_data = $this->db->get('tbl_lembur')->result();
        $spl_grouped = [];
        foreach ($spl_data as $s) {
            $spl_grouped[$s->karyawan_id][] = $s;
        }

        // 3. Fetch Attendance for the selected date
        $this->db->where('tanggal', $tanggal);
        $absensi_data = $this->db->get('absensi')->result();
        $abs_map = [];
        foreach ($absensi_data as $a) {
            $abs_map[$a->karyawan_id] = $a;
        }

        $final_list = [];

        foreach ($all_employees as $emp) {
            $k_id = $emp->karyawan_id;
            $day_spls = isset($spl_grouped[$k_id]) ? $spl_grouped[$k_id] : [];
            $att_day = isset($abs_map[$k_id]) ? $abs_map[$k_id] : null;

            // Calculate Wajib for this day using exclusively auto-wajib logic
            $hitung_wajib = $this->_hitung_jam_lembur($k_id, '00:00', '00:00', $tanggal, $att_day);
            $total_jam_wajib = $hitung_wajib['raw_wajib'];
            $jam_mulai_wajib = $hitung_wajib['wajib_start'];
            $jam_selesai_wajib = $hitung_wajib['wajib_end'];

            $total_jam_pagi = 0;
            $jam_mulai_pagi = '00:00';
            $jam_selesai_pagi = '00:00';
            $total_jam_kerjaan = 0;
            $jam_mulai_kerjaan = '00:00';
            $jam_selesai_kerjaan = '00:00';
            $last_spl = null;

            $list_kegiatan = [];
            $all_pagi_ranges = [];
            $all_kerjaan_ranges = [];
            $all_wajib_ranges = [];

            if ($total_jam_wajib > 0) {
                $all_wajib_ranges[] = $jam_mulai_wajib . '-' . $jam_selesai_wajib;
            }

            // Calculate Pagi & Kerjaan untuk setiap SPL di hari ini
            foreach ($day_spls as $spl) {
                // Selalu catat kegiatan & last_spl untuk keperluan display/status
                if ($spl->kegiatan && !in_array($spl->kegiatan, $list_kegiatan)) {
                    $list_kegiatan[] = $spl->kegiatan;
                }
                $last_spl = $spl;

                // FIX: Hanya hitung JAM untuk SPL yang sudah DISETUJUI
                // Ini menyinkronkan tampilan harian dengan rekap bulanan & penggajian
                if ($spl->status != 'disetujui')
                    continue;

                $hitung = $this->_hitung_jam_lembur($k_id, $spl->jam_mulai, $spl->jam_selesai, $tanggal, $att_day);

                $total_jam_pagi += $hitung['raw_pagi'];
                $total_jam_kerjaan += $hitung['raw_kerjaan'];

                if ($hitung['raw_wajib'] > 0) {
                    $all_wajib_ranges[] = substr($hitung['wajib_start'], 0, 5) . '-' . substr($hitung['wajib_end'], 0, 5);

                    // Jika sudah ada Wajib (otomatis), wajib dari SPL masuk ke Kerjaan
                    if ($total_jam_wajib > 0) {
                        $total_jam_kerjaan += $hitung['raw_wajib'];
                    } else {
                        $total_jam_wajib = $hitung['raw_wajib'];
                        $jam_mulai_wajib = $hitung['wajib_start'];
                        $jam_selesai_wajib = $hitung['wajib_end'];
                    }
                }

                if ($hitung['raw_pagi'] > 0) {
                    $all_pagi_ranges[] = substr($hitung['pagi_start'], 0, 5) . '-' . substr($hitung['pagi_end'], 0, 5);
                    if ($jam_mulai_pagi == '00:00' || strtotime($hitung['pagi_start']) < strtotime($jam_mulai_pagi)) {
                        $jam_mulai_pagi = $hitung['pagi_start'];
                    }
                    if ($jam_selesai_pagi == '00:00' || strtotime($hitung['pagi_end']) > strtotime($jam_selesai_pagi)) {
                        $jam_selesai_pagi = $hitung['pagi_end'];
                    }
                }

                if ($hitung['raw_kerjaan'] > 0) {
                    $all_kerjaan_ranges[] = substr($hitung['kerjaan_start'], 0, 5) . '-' . substr($hitung['kerjaan_end'], 0, 5);
                    if ($jam_mulai_kerjaan == '00:00' || strtotime($hitung['kerjaan_start']) < strtotime($jam_mulai_kerjaan)) {
                        $jam_mulai_kerjaan = $hitung['kerjaan_start'];
                    }
                    if ($jam_selesai_kerjaan == '00:00' || strtotime($hitung['kerjaan_end']) > strtotime($jam_selesai_kerjaan)) {
                        $jam_selesai_kerjaan = $hitung['kerjaan_end'];
                    }
                }
            }

            $total_efektif = max(0, $total_jam_pagi + $total_jam_wajib + $total_jam_kerjaan - $hitung_wajib['raw_potongan']);

            // Calculate Daily Meal Allowance
            $biaya_makan = 0;
            if ($day_num <= 6) {
                $check_times = [];
                foreach ($day_spls as $spl) {
                    // FIX: Biaya makan hanya dari SPL disetujui agar sinkron dengan payroll
                    if ($spl->status == 'disetujui' && $spl->jam_selesai && $spl->jam_selesai != '00:00:00') {
                        $check_times[] = substr($spl->jam_selesai, 0, 5);
                    }
                }
                if ($att_day && $att_day->jam_pulang && $att_day->jam_pulang != '00:00:00') {
                    $check_times[] = substr($att_day->jam_pulang, 0, 5);
                }

                if (!empty($check_times)) {
                    $max_time = max($check_times);
                    if ($max_time >= "23:00")
                        $biaya_makan = $this->_get_lembur_config('makan_23_00', 25000);
                    elseif ($max_time >= "21:00")
                        $biaya_makan = $this->_get_lembur_config('makan_21_00', 20000);
                    elseif ($max_time >= "20:00")
                        $biaya_makan = $this->_get_lembur_config('makan_20_00', 15000);
                    elseif ($max_time >= "19:00")
                        $biaya_makan = $this->_get_lembur_config('makan_19_00', 10000);
                }
            }

            // Construct row object for view
            $row = new stdClass();
            $row->id = $last_spl ? $last_spl->id : null;
            $row->karyawan_id = $k_id;
            $row->nama_lengkap = $emp->nama_lengkap;
            $row->nik = $emp->nik;
            $row->foto = $emp->foto;
            $row->tanggal = $tanggal;

            // For general range display
            $row->jam_mulai = '00:00';
            $row->jam_selesai = '00:00';
            if (!empty($day_spls)) {
                $min_s = '23:59';
                $max_e = '00:00';
                foreach ($day_spls as $ds) {
                    if ($ds->jam_mulai && $ds->jam_mulai < $min_s)
                        $min_s = substr($ds->jam_mulai, 0, 5);
                    if ($ds->jam_selesai && $ds->jam_selesai > $max_e)
                        $max_e = substr($ds->jam_selesai, 0, 5);
                }
                $row->jam_mulai = $min_s;
                $row->jam_selesai = $max_e;
            }

            // Consolidated status logic: If any part is waiting, show waiting.
            $final_status = ($total_efektif > 0 ? 'hadir' : 'tidak lembur');
            if (!empty($day_spls)) {
                $has_waiting = false;
                $has_rejected = false;
                $approved_count = 0;
                foreach ($day_spls as $s) {
                    if ($s->status == 'menunggu')
                        $has_waiting = true;
                    elseif ($s->status == 'ditolak')
                        $has_rejected = true;
                    elseif ($s->status == 'disetujui')
                        $approved_count++;
                }

                if ($has_waiting)
                    $final_status = 'menunggu';
                elseif ($has_rejected)
                    $final_status = 'ditolak';
                elseif ($approved_count > 0)
                    $final_status = 'disetujui';
            }
            $row->status = $final_status;

            // Join activities
            if (empty($list_kegiatan) && $total_jam_wajib > 0) {
                $row->kegiatan = 'Lembur Wajib/Otomatis';
            } else {
                $row->kegiatan = implode(', ', $list_kegiatan) ?: '-';
            }

            $row->foto_kegiatan = $last_spl ? $last_spl->foto_kegiatan : null;
            $row->lokasi = $last_spl ? (isset($last_spl->lokasi) ? $last_spl->lokasi : null) : null;
            $row->jam_masuk = $att_day ? $att_day->jam_masuk : '00:00:00';
            $row->jam_pulang = $att_day ? $att_day->jam_pulang : '00:00:00';

            $row->jam_efektif = round($total_efektif, 1);
            $row->jam_pagi = round($total_jam_pagi, 1);
            $row->jam_mulai_pagi = $jam_mulai_pagi;
            $row->jam_selesai_pagi = $jam_selesai_pagi;
            $row->all_pagi_ranges = $all_pagi_ranges;

            $row->jam_wajib = round($total_jam_wajib, 1);
            $row->jam_mulai_wajib = $jam_mulai_wajib;
            $row->jam_selesai_wajib = $jam_selesai_wajib;
            $row->all_wajib_ranges = $all_wajib_ranges;

            $row->jam_kerjaan = round($total_jam_kerjaan, 1);
            $row->jam_mulai_kerjaan = $jam_mulai_kerjaan;
            $row->jam_selesai_kerjaan = $jam_selesai_kerjaan;
            $row->all_kerjaan_ranges = $all_kerjaan_ranges;
            $row->nominal_rate = $emp->nominal_lembur ?: 0;
            $row->total_lembur = $total_efektif * $row->nominal_rate;
            $row->biaya_makan = $biaya_makan;

            $breakdown_list = [];
            if ($total_jam_pagi > 0)
                $breakdown_list[] = "Pagi: " . round($total_jam_pagi, 1) . "j";
            if ($total_jam_wajib > 0)
                $breakdown_list[] = "Wajib: " . round($total_jam_wajib, 1) . "j";
            if ($total_jam_kerjaan > 0)
                $breakdown_list[] = "Kerjaan: " . round($total_jam_kerjaan, 1) . "j";
            $row->breakdown_html = implode('<br>', $breakdown_list) ?: 'Total: ' . $row->jam_efektif . ' jam';

            // Hanya tampilkan karyawan yang mengajukan lembur (punya SPL)
            if (!empty($day_spls)) {
                $final_list[] = $row;
            }
        }

        // Sort: show lembur first, then by name
        usort($final_list, function ($a, $b) {
            if ($a->jam_efektif != $b->jam_efektif)
                return $b->jam_efektif <=> $a->jam_efektif;
            return strcmp($a->nama_lengkap, $b->nama_lengkap);
        });

        $raw_configs = $this->db->get('tbl_lembur_config')->result();
        $conf_map = [];
        foreach ($raw_configs as $c)
            $conf_map[$c->config_key] = $c->config_value;

        $extra_data = array(
            'karyawan' => $final_list,
            'jml' => count(array_filter($final_list, function ($x) {
                return $x->jam_efektif > 0;
            })),
            'all_karyawan' => $this->M_master->get_lembur()->result(),
            'listkaryawan' => $this->M_master->get_karyawan()->result(),
            'configs' => $raw_configs,
            'conf' => $conf_map,
            'tanggal' => $tanggal
        );

        $data = $this->_prepare_view_data('Daftar Rekap Lembur', $extra_data);
        $this->load_admin_view('admin/karyawan/absen_lembur', $data);
    }

    public function rekap_lembur()
    {
        $tanggal = $this->input->get('tanggal', true) ?: date('Y-m-d');
        $day_num = date('N', strtotime($tanggal));
        $jk_rule = $this->db->get_where('tbl_jam_kerja', ['urutan' => $day_num])->row();
        $w_start_time_str = ($day_num == 6) ? $this->_get_lembur_config('w_start_sat', '15:00:00') : $this->_get_lembur_config('w_start_weekday', '16:00:00');
        $w_end_time_str = ($day_num == 6) ? $this->_get_lembur_config('w_end_sat', '16:00:00') : $this->_get_lembur_config('w_end_weekday', '18:00:00');

        // 1. Fetch all active employees
        $all_employees = $this->db->select('karyawan_id, nama_lengkap, nik, foto, nominal_lembur')
            ->from('tbl_karyawan')
            ->where('nama_lengkap NOT LIKE', '%Terminal%')
            ->where('nama_lengkap NOT LIKE', '%OPERATOR%')
            ->where('nama_lengkap NOT LIKE', '%DEPT%')
            ->where('nama_lengkap NOT LIKE', '%DTG%')
            ->where('nama_lengkap NOT LIKE', '%DTF%')
            ->where('nama_lengkap NOT LIKE', '%WORK CENTER%')
            ->where('nama_lengkap NOT LIKE', '%LARGE FORMAT%')
            ->where('nama_lengkap NOT LIKE', '%A3+%')
            ->where("nik NOT LIKE", 'STATION%')
            ->where("nik NOT LIKE", 'TERM%');

        if (!empty($this->session->userdata('perusahaan_id')) && $this->session->userdata('perusahaan_id') != 1) {
            $this->db->where('perusahaan_id', $this->session->userdata('perusahaan_id'));
        }

        $all_employees = $this->db->get()->result();

        // 2. Fetch SPLs for the selected date - Grouped
        $spl_data = $this->db->get_where('tbl_lembur', ['tanggal' => $tanggal])->result();
        $spl_grouped = [];
        foreach ($spl_data as $s) {
            $spl_grouped[$s->karyawan_id][] = $s;
        }

        // 3. Fetch Attendance
        $absensi_data = $this->db->get_where('absensi', ['tanggal' => $tanggal])->result();
        $abs_map = [];
        foreach ($absensi_data as $a) {
            $abs_map[$a->karyawan_id] = $a;
        }

        $final_list = [];

        foreach ($all_employees as $emp) {
            $k_id = $emp->karyawan_id;
            $spls = isset($spl_grouped[$k_id]) ? $spl_grouped[$k_id] : [];
            
            // Hanya tampilkan karyawan yang mengajukan lembur
            if (empty($spls)) {
                continue;
            }
            
            $att = isset($abs_map[$k_id]) ? $abs_map[$k_id] : null;

            $total_jam_pagi = 0;
            $total_jam_wajib = 0;
            $total_jam_kerjaan = 0;
            $last_spl = null;
            $jam_mulai_kerjaan = '00:00';
            $jam_selesai_kerjaan = '00:00';
            $jam_mulai_pagi = '00:00';
            $jam_selesai_pagi = '00:00';

            // Wajib calculation
            $hitung_wajib = $this->_hitung_jam_lembur($k_id, '00:00', '00:00', $tanggal, $att);
            $total_jam_wajib = $hitung_wajib['raw_wajib'];
            $jam_mulai_wajib = $hitung_wajib['wajib_start'];
            $jam_selesai_wajib = $hitung_wajib['wajib_end'];

            // Pagi & Task calculation across all SPLs
            foreach ($spls as $spl) {
                $hitung = $this->_hitung_jam_lembur($k_id, $spl->jam_mulai, $spl->jam_selesai, $tanggal, $att);
                $total_jam_pagi += $hitung['raw_pagi'];
                $total_jam_kerjaan += $hitung['raw_kerjaan'];

                // Capture Pagi Tap Times
                if ($hitung['raw_pagi'] > 0) {
                    if ($jam_mulai_pagi == '00:00' || strtotime($spl->jam_mulai) < strtotime($jam_mulai_pagi)) {
                        $jam_mulai_pagi = substr($spl->jam_mulai, 0, 5);
                    }
                    if ($jam_selesai_pagi == '00:00' || strtotime($spl->jam_selesai) > strtotime($jam_selesai_pagi)) {
                        $jam_selesai_pagi = substr($spl->jam_selesai, 0, 5);
                    }
                }

                // Wajib from SPL
                if ($hitung['raw_wajib'] > 0) {
                    if ($total_jam_wajib == 0) {
                        $total_jam_wajib = $hitung['raw_wajib'];
                        $jam_mulai_wajib = $hitung['wajib_start'];
                        $jam_selesai_wajib = $hitung['wajib_end'];
                    } else {
                        // Sync: SPL Wajib becomes Kerjaan extra if Wajib auto exists
                        $total_jam_kerjaan += $hitung['raw_wajib'];
                    }
                }

                if ($hitung['raw_kerjaan'] > 0) {
                    if ($jam_mulai_kerjaan == '00:00' || strtotime($hitung['kerjaan_start']) < strtotime($jam_mulai_kerjaan)) {
                        $jam_mulai_kerjaan = $hitung['kerjaan_start'];
                    }
                    if ($jam_selesai_kerjaan == '00:00' || strtotime($hitung['kerjaan_end']) > strtotime($jam_selesai_kerjaan)) {
                        $jam_selesai_kerjaan = $hitung['kerjaan_end'];
                    }
                }
                $last_spl = $spl;
            }

            // Calculate Daily Meal Allowance
            $biaya_makan = 0;
            $day_num_daily = date('N', strtotime($tanggal));
            if ($day_num_daily <= 6) {
                $check_times = [];
                foreach ($spls as $spl) {
                    if ($spl->jam_selesai && $spl->jam_selesai != '00:00:00') {
                        $check_times[] = substr($spl->jam_selesai, 0, 5);
                    }
                }
                if ($att && $att->jam_pulang && $att->jam_pulang != '00:00:00') {
                    $check_times[] = substr($att->jam_pulang, 0, 5);
                }

                if (!empty($check_times)) {
                    $max_time = max($check_times);
                    if ($max_time >= "23:00")
                        $biaya_makan = $this->_get_lembur_config('makan_23_00', 25000);
                    elseif ($max_time >= "21:00")
                        $biaya_makan = $this->_get_lembur_config('makan_21_00', 20000);
                    elseif ($max_time >= "20:00")
                        $biaya_makan = $this->_get_lembur_config('makan_20_00', 15000);
                    elseif ($max_time >= "19:00")
                        $biaya_makan = $this->_get_lembur_config('makan_19_00', 10000);
                }
            }

            $total_efektif = $total_jam_pagi + $total_jam_wajib + $total_jam_kerjaan;

            $row = new stdClass();
            $row->id = $last_spl ? $last_spl->id : null;
            $row->karyawan_id = $k_id;
            $row->nama_lengkap = $emp->nama_lengkap;
            $row->nik = $emp->nik;
            $row->foto = $emp->foto;
            $row->tanggal = $tanggal;
            $row->jam_masuk = $att ? $att->jam_masuk : '-';
            $row->jam_pulang = $att ? $att->jam_pulang : '-';
            $row->jam_mulai = count($spls) > 0 ? $spls[0]->jam_mulai : '00:00';
            $row->jam_selesai = count($spls) > 0 ? $spls[count($spls) - 1]->jam_selesai : ($att ? date('H:i', strtotime($att->jam_pulang)) : '00:00');
            $row->status = $last_spl ? $last_spl->status : ($total_efektif > 0 ? 'hadir' : 'tidak lembur');
            $row->kegiatan = count($spls) > 0 ? $spls[0]->kegiatan : ($total_efektif > 0 ? 'Lembur Wajib/Otomatis' : '-');
            $row->foto_kegiatan = count($spls) > 0 ? $spls[0]->foto_kegiatan : null;
            $row->lokasi = count($spls) > 0 ? (isset($spls[0]->lokasi) ? $spls[0]->lokasi : null) : null;

            $row->jam_efektif = round($total_efektif, 1);
            $row->jam_pagi = round($total_jam_pagi, 1);
            $row->jam_wajib = round($total_jam_wajib, 1);
            $row->jam_kerjaan = round($total_jam_kerjaan, 1);
            $row->biaya_makan = $biaya_makan;
            $row->jam_mulai_pagi = $jam_mulai_pagi;
            $row->jam_selesai_pagi = $jam_selesai_pagi;
            $row->jam_mulai_kerjaan = $jam_mulai_kerjaan;
            $row->jam_selesai_kerjaan = $jam_selesai_kerjaan;
            $row->jam_mulai_wajib = $hitung_wajib['wajib_start'];
            $row->jam_selesai_wajib = $hitung_wajib['wajib_end'];
            $row->nominal_rate = $emp->nominal_lembur ?: 0;
            $row->total_lembur = $row->jam_efektif * $row->nominal_rate;

            // Breakdown HTML
            $breakdown_list = [];

            if ($total_jam_pagi > 0) {
                $p_start = ($row->jam_mulai_pagi != '00:00') ? $row->jam_mulai_pagi : date('H:i', max(strtotime($row->jam_mulai), strtotime("06:00")));
                $p_end = ($row->jam_selesai_pagi != '00:00') ? $row->jam_selesai_pagi : date('H:i', min(strtotime($row->jam_selesai), strtotime("08:00")));
                $breakdown_list[] = "- " . $p_start . " s.d " . $p_end . " (" . round($total_jam_pagi, 1) . "jam)";
            }
            if ($total_jam_wajib > 0 || $total_jam_kerjaan > 0) {
                $m_start = strtotime("$tanggal $w_start_time_str");
                $a_start = date('H:i', max(strtotime($row->jam_mulai), $m_start));
                $a_end = date('H:i', ($att && strtotime($att->jam_pulang) > 0) ? strtotime($att->jam_pulang) : strtotime($row->jam_selesai));

                $combined = $total_jam_wajib + $total_jam_kerjaan;
                if ($combined > 0) {
                    $breakdown_list[] = "- " . $a_start . " s.d " . $a_end . " (" . $combined . "jam)";
                }
            }
            $row->breakdown_html = implode('<br>', $breakdown_list);

            $final_list[] = $row;
        }

        usort($final_list, function ($a, $b) {
            if ($a->jam_efektif != $b->jam_efektif)
                return $b->jam_efektif <=> $a->jam_efektif;
            return strcmp($a->nama_lengkap, $b->nama_lengkap);
        });

        $extra_data = array(
            'karyawan' => $final_list,
            'jml' => count(array_filter($final_list, function ($x) {
                return $x->jam_efektif > 0;
            })),
            'all_karyawan' => $this->M_master->get_lembur()->result(),
            'listkaryawan' => $this->M_master->get_karyawan()->result(),
            'tanggal' => $tanggal
        );

        $data = $this->_prepare_view_data('Daftar Rekap Lembur', $extra_data);
        $this->load_admin_view('admin/karyawan/absen_lembur', $data);
    }

    public function rekap_lembur_bulanan()
    {
        // Use Payroll Period Range
        $bulan_get = $this->input->get('bulan', true) ?: date('m');
        $tahun_get = $this->input->get('tahun', true) ?: date('Y');
        $bulan_full = $tahun_get . '-' . str_pad($bulan_get, 2, '0', STR_PAD_LEFT);

        $range = $this->_get_payroll_range($bulan_full);
        $start_date = $range['start'];
        $end_date = $range['end'];

        // 1. Fetch Absensi (Primary Source) - Joined with Karyawan
        $this->db->select('a.*, k.nama_lengkap, k.nik, k.foto, k.nominal_lembur');
        $this->db->from('absensi a');
        $this->db->join('tbl_karyawan k', 'a.karyawan_id = k.karyawan_id');
        $this->db->where('a.tanggal >=', $start_date);
        $this->db->where('a.tanggal <=', $end_date);
        $this->db->where('a.status', 'Hadir');

        // SYNC FILTER: Exclude Owners/Directors & Terminals
        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');

        if (!empty($this->session->userdata('perusahaan_id')) && $this->session->userdata('perusahaan_id') != 1) {
            $this->db->where('k.perusahaan_id', $this->session->userdata('perusahaan_id'));
        }

        $this->db->order_by('k.nama_lengkap', 'ASC');
        $this->db->order_by('a.tanggal', 'ASC');
        $raw_absensi = $this->db->get()->result();


        // 2. Fetch SPLs for the period - Grouped (Only Approved for consistency with Payroll)
        $this->db->select('a.nama_lengkap, b.*');
        $this->db->from('tbl_karyawan a');
        $this->db->join('tbl_lembur b', 'a.karyawan_id = b.karyawan_id');
        $this->db->where('b.status', 'disetujui');
        $this->db->where('b.tanggal >=', $start_date);
        $this->db->where('b.tanggal <=', $end_date);
        $lembur_data = $this->db->get()->result();

        $spl_grouped = [];
        foreach ($lembur_data as $l) {
            $spl_grouped[$l->karyawan_id][$l->tanggal][] = $l;
        }

        // 2.5 Fetch Manual Override Data from tbl_gaji_manual
        $manual_data = $this->db->get_where('tbl_gaji_manual', ['bulan' => $bulan_full])->result();
        $manual_map = [];
        foreach ($manual_data as $m) {
            $manual_map[$m->karyawan_id] = $m;
        }

        // Define all dates in period
        $period_dates = [];
        $currDate = strtotime($start_date);
        $lastDate = strtotime($end_date);
        while ($currDate <= $lastDate) {
            $period_dates[] = date('Y-m-d', $currDate);
            $currDate = strtotime('+1 day', $currDate);
        }

        $rekap_karyawan = [];

        // 3. Process Logic — bangun rekap_karyawan dari absensi Hadir
        foreach ($raw_absensi as $abs_row) {
            $k_id = $abs_row->karyawan_id;

            if (!isset($rekap_karyawan[$k_id])) {
                $rekap_karyawan[$k_id] = (object) [
                    'karyawan_id' => $k_id,
                    'nama_lengkap' => $abs_row->nama_lengkap,
                    'nik' => $abs_row->nik,
                    'foto' => $abs_row->foto,
                    'nominal_rate' => $abs_row->nominal_lembur > 0 ? $abs_row->nominal_lembur : 10000,
                    'jam_pagi' => 0,
                    'act_jam_pagi' => 0,
                    'jam_wajib' => 0,
                    'act_jam_wajib' => 0,
                    'jam_kerjaan' => 0,
                    'act_jam_kerjaan' => 0,
                    'jam_potongan' => 0,
                    'total_uang_makan' => 0,
                    'total_jam_efektif' => 0,
                    'total_act_jam_efektif' => 0,
                    'total_rupiah' => 0,
                    'jml_hari' => 0,
                    'details' => []
                ];
            }
        }

        // FIX: Tambahkan karyawan yang punya SPL approved tapi tidak ada di raw_absensi
        // (misal: karyawan lembur tanpa tap absensi, atau status absensi bukan 'Hadir')
        foreach ($lembur_data as $l) {
            $k_id = $l->karyawan_id;
            if (!isset($rekap_karyawan[$k_id])) {
                $kary = $this->db->select('karyawan_id, nama_lengkap, nik, foto, nominal_lembur')
                    ->where('karyawan_id', $k_id)
                    ->get('tbl_karyawan')->row();
                if ($kary) {
                    $rekap_karyawan[$k_id] = (object) [
                        'karyawan_id' => $k_id,
                        'nama_lengkap' => $kary->nama_lengkap,
                        'nik' => $kary->nik,
                        'foto' => $kary->foto,
                        'nominal_rate' => $kary->nominal_lembur > 0 ? $kary->nominal_lembur : 10000,
                        'jam_pagi' => 0,
                        'act_jam_pagi' => 0,
                        'jam_wajib' => 0,
                        'act_jam_wajib' => 0,
                        'jam_kerjaan' => 0,
                        'act_jam_kerjaan' => 0,
                        'jam_potongan' => 0,
                        'total_uang_makan' => 0,
                        'total_jam_efektif' => 0,
                        'total_act_jam_efektif' => 0,
                        'total_rupiah' => 0,
                        'jml_hari' => 0,
                        'details' => []
                    ];
                }
            }
        }

        // We should iterate over employees, but the current UI is based on Present days.
        // To handle multiple SPLs correctly while keeping the detailed list, we map attendance.
        $abs_map = [];
        foreach ($raw_absensi as $abs_row) {
            $abs_map[$abs_row->karyawan_id][$abs_row->tanggal] = $abs_row;
        }

        foreach ($rekap_karyawan as $k_id => $emp) {
            $emp_spls = isset($spl_grouped[$k_id]) ? $spl_grouped[$k_id] : [];
            $emp_atts = isset($abs_map[$k_id]) ? $abs_map[$k_id] : [];

            // Collect all relevant dates (days with attendance OR days with SPL)
            $relevant_dates = array_unique(array_merge(array_keys($emp_spls), array_keys($emp_atts)));
            sort($relevant_dates);

            foreach ($relevant_dates as $tgl) {
                if ($tgl < $start_date || $tgl > $end_date)
                    continue;

                $row_att = isset($emp_atts[$tgl]) ? $emp_atts[$tgl] : null;
                $day_spls = isset($emp_spls[$tgl]) ? $emp_spls[$tgl] : [];

                // 1. Calculate Auto-Wajib (once per day) via logic
                $res_wajib = $this->_hitung_jam_lembur($k_id, '00:00', '00:00', $tgl, $row_att);
                $day_wajib = (float) $res_wajib['raw_wajib'];
                $act_day_wajib = (float) $res_wajib['breakdown']['act_wajib'];
                $wajib_start_res = $res_wajib['wajib_start'];
                $wajib_end_res = $res_wajib['wajib_end'];

                $day_pagi = 0;
                $act_day_pagi = 0;
                $day_kerjaan = 0;
                $act_day_kerjaan = 0;
                $list_kegiatan_m = [];
                $last_spl = null;

                foreach ($day_spls as $s) {
                    // Selalu catat kegiatan dan posisi last_spl
                    if ($s->kegiatan && !in_array($s->kegiatan, $list_kegiatan_m)) {
                        $list_kegiatan_m[] = $s->kegiatan;
                    }
                    $last_spl = $s;

                    // Deteksi SPL tipe auto-wajib (jam_mulai & jam_selesai = 00:00)
                    // Jam-nya SUDAH dihitung via auto-wajib call sebelum loop ini
                    // Jangan panggil _hitung_jam_lembur lagi — akan double count!
                    $jam_m_norm = substr($s->jam_mulai ?? '00:00', 0, 5);
                    $jam_s_norm = substr($s->jam_selesai ?? '00:00', 0, 5);

                    if ($jam_m_norm === '00:00' && $jam_s_norm === '00:00') {
                        // Kasus khusus: auto-wajib gagal (jam_pulang tidak tercatat = 0)
                        // Gunakan fallback config agar karyawan tidak dirugikan
                        if ($day_wajib == 0) {
                            $hari_db_s = (date('N', strtotime($tgl)) == 6) ? 'Sabtu' : 'Senin-Jumat';
                            $wajib_cfg = $this->db
                                ->order_by('total_jam', 'DESC')
                                ->get_where('tbl_lembur_jam_config', ['hari' => $hari_db_s, 'is_wajib' => 1])
                                ->row();
                            if ($wajib_cfg) {
                                $day_wajib = (float) $wajib_cfg->total_jam;
                                $act_day_wajib = (strtotime($wajib_cfg->jam_selesai) - strtotime($wajib_cfg->jam_mulai)) / 3600;
                                $wajib_start_res = substr($wajib_cfg->jam_mulai, 0, 5);
                                $wajib_end_res = substr($wajib_cfg->jam_selesai, 0, 5);
                            }
                        }
                        continue; // ← skip _hitung_jam_lembur; tidak ada proses lagi untuk SPL ini
                    }

                    // SPL dengan jam spesifik (pagi, kerjaan, atau wajib tertentu)
                    $hitung = $this->_hitung_jam_lembur($k_id, $s->jam_mulai, $s->jam_selesai, $tgl, $row_att);

                    // Pagi from SPL (Accumulate)
                    if ($hitung['raw_pagi'] > 0) {
                        $day_pagi += (float) $hitung['raw_pagi'];
                        $act_day_pagi += (float) $hitung['breakdown']['act_pagi'];
                    }

                    // Kerjaan from SPL (Accumulate)
                    if ($hitung['raw_kerjaan'] > 0) {
                        $day_kerjaan += (float) $hitung['raw_kerjaan'];
                        $act_day_kerjaan += (float) $hitung['breakdown']['act_kerjaan'];
                    }

                    // Wajib dari SPL dengan jam spesifik
                    if ($hitung['raw_wajib'] > 0) {
                        if ($day_wajib == 0) {
                            $day_wajib = (float) $hitung['raw_wajib'];
                            $act_day_wajib = (float) $hitung['breakdown']['act_wajib'];
                            $wajib_start_res = $hitung['wajib_start'];
                            $wajib_end_res = $hitung['wajib_end'];
                        } else {
                            // Wajib sudah ada → SPL wajib tambahan jadi kerjaan
                            $day_kerjaan += (float) $hitung['raw_wajib'];
                            $act_day_kerjaan += (float) $hitung['breakdown']['act_wajib'];
                        }
                    }
                }


                $day_total = $day_pagi + $day_wajib + $day_kerjaan;

                if ($day_total > 0) {
                    // Start/End for the whole day SPLs
                    $min_start_spl = '00:00';
                    $max_end_spl = '00:00';
                    if (!empty($day_spls)) {
                        $m_s = '23:59';
                        $m_e = '00:00';
                        foreach ($day_spls as $ds) {
                            if ($ds->jam_mulai && $ds->jam_mulai < $m_s)
                                $m_s = substr($ds->jam_mulai, 0, 5);
                            if ($ds->jam_selesai && $ds->jam_selesai > $m_e)
                                $m_e = substr($ds->jam_selesai, 0, 5);
                        }
                        $min_start_spl = $m_s;
                        $max_end_spl = $m_e;
                    }

                    $emp->jam_pagi += $day_pagi;
                    $emp->act_jam_pagi += $act_day_pagi;
                    $emp->jam_wajib += $day_wajib;
                    $emp->act_jam_wajib += $act_day_wajib;
                    $emp->jam_kerjaan += $day_kerjaan;
                    $emp->act_jam_kerjaan += $act_day_kerjaan;
                    $emp->jam_potongan += $res_wajib['raw_potongan'];


                    // Monthly Meal Calculation logic
                    $day_num_m = date('N', strtotime($tgl));
                    $per_day_max_meal = 0;
                    if ($day_num_m <= 6) {
                        $daily_latest_out_ts = 0;
                        foreach ($day_spls as $spl) {
                            if ($spl->jam_selesai != '00:00:00') {
                                $daily_latest_out_ts = max($daily_latest_out_ts, strtotime($spl->jam_selesai));
                            }
                        }
                        if ($row_att && $row_att->jam_pulang != '00:00:00') {
                            $daily_latest_out_ts = max($daily_latest_out_ts, strtotime($row_att->jam_pulang));
                        }

                        if ($daily_latest_out_ts > 0) {
                            $out_time_m = date('H:i', $daily_latest_out_ts);
                            if ($out_time_m >= "23:00")
                                $per_day_max_meal = $this->_get_lembur_config('makan_23_00', 25000);
                            elseif ($out_time_m >= "21:00")
                                $per_day_max_meal = $this->_get_lembur_config('makan_21_00', 20000);
                            elseif ($out_time_m >= "20:00")
                                $per_day_max_meal = $this->_get_lembur_config('makan_20_00', 15000);
                            elseif ($out_time_m >= "19:00")
                                $per_day_max_meal = $this->_get_lembur_config('makan_19_00', 10000);
                        }
                    }
                    $emp->total_uang_makan += $per_day_max_meal;

                    $emp->total_jam_efektif += $day_total;
                    $emp->total_act_jam_efektif += ($act_day_pagi + $act_day_wajib + $act_day_kerjaan);
                    $emp->total_rupiah += ($day_total * $emp->nominal_rate);
                    $emp->jml_hari++;

                    // Final kegiatan string for detail
                    $keg_str = implode(', ', $list_kegiatan_m);
                    if (empty($keg_str) && $day_wajib > 0)
                        $keg_str = 'Lembur Wajib';

                    $emp->details[] = (object) [
                        'tanggal' => $tgl,
                        'jam_masuk' => $row_att ? $row_att->jam_masuk : '00:00:00',
                        'jam_pulang' => $row_att ? $row_att->jam_pulang : '00:00:00',
                        'jam_mulai_spl' => $min_start_spl,
                        'jam_selesai_spl' => $max_end_spl,
                        'pagi' => round($day_pagi, 1),
                        'wajib' => round($day_wajib, 1),
                        'kerjaan' => round($day_kerjaan, 1),
                        'wajib_start' => $wajib_start_res,
                        'wajib_end' => $wajib_end_res,
                        'total_efektif' => round($day_total, 1),
                        'act_total_efektif' => round($act_day_pagi + $act_day_wajib + $act_day_kerjaan, 1),
                        'rupiah' => $day_total * $emp->nominal_rate,

                        'keterangan' => $keg_str ?: '-',
                        'foto_kegiatan' => $last_spl ? $last_spl->foto_kegiatan : null
                    ];
                }
            }
            // Round totals after summary
            $emp->jam_pagi = round($emp->jam_pagi, 1);
            $emp->jam_wajib = round($emp->jam_wajib, 1);
            $emp->jam_kerjaan = round($emp->jam_kerjaan, 1);
            $emp->jam_potongan = round($emp->jam_potongan, 1);
            $emp->total_jam_efektif = round($emp->total_jam_efektif, 1);
            $emp->total_act_jam_efektif = round($emp->total_act_jam_efektif, 1);


            // Attach Manual Overrides
            $emp->manual_uang_lembur = isset($manual_map[$k_id]) ? $manual_map[$k_id]->uang_lembur : null;
            $emp->manual_biaya_makan = isset($manual_map[$k_id]) ? $manual_map[$k_id]->biaya_makan_lembur : null;
            $emp->manual_keterangan = isset($manual_map[$k_id]) ? $manual_map[$k_id]->keterangan : '';
        }

        // Sort by Name (Already sorted by query, but array keys might need indexed reset if views iterate strictly)
        // actually usort is safer if keys are IDs
        usort($rekap_karyawan, function ($a, $b) {
            return strcmp($a->nama_lengkap, $b->nama_lengkap);
        });

        $extra_data = array(
            'karyawan' => $rekap_karyawan,
            'jml' => count($rekap_karyawan),
            'total_karyawan' => count($rekap_karyawan),
            'bulan' => $bulan_get,
            'tahun' => $tahun_get
        );

        $data = $this->_prepare_view_data('Rekap Lembur Bulanan', $extra_data);
        $this->load_admin_view('admin/karyawan/rekap_lembur_bulanan', $data);
    }

    private function _get_lembur_config($key, $default = null)
    {
        static $configs = null;
        if ($configs === null) {
            $configs = [];
            $data = $this->db->get('tbl_lembur_config')->result();
            foreach ($data as $row) {
                $configs[$row->config_key] = $row->config_value;
            }
        }
        return isset($configs[$key]) ? $configs[$key] : $default;
    }

    private function _hitung_jam_lembur($karyawan_id, $jam_mulai, $jam_selesai, $tanggal, $absensi_data = null)
    {
        return $this->Payroll_model->_hitung_jam_lembur($karyawan_id, $jam_mulai, $jam_selesai, $tanggal, $absensi_data);
    }


    function update_status_ajax()
    {
        $id = (int) $this->input->post('id', true);
        $karyawan_id = (int) $this->input->post('karyawan_id', true);
        $tanggal = $this->input->post('tanggal', true);
        $status = $this->input->post('status', true);

        if ($karyawan_id && $tanggal) {
            $this->db->where('karyawan_id', $karyawan_id);
            $this->db->where('tanggal', $tanggal);
        } else {
            $this->db->where('id', $id);
        }

        $this->db->update('tbl_lembur', ['status' => $status]);

        echo json_encode(['success' => true]);
    }

    public function toggle_transport_status()
    {
        $karyawan_id = (int) $this->input->post('karyawan_id', true);
        $status = (int) $this->input->post('status', true);

        $this->db->where('karyawan_id', $karyawan_id);
        $update = $this->db->update('tbl_karyawan', ['transport_aktif' => $status]);

        if ($update) {
            echo json_encode(['success' => true, 'message' => 'Status tunjangan transport berhasil diperbarui']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui database']);
        }
    }

    public function pengajuan_lembur()
    {
        $karyawan_id = $this->session->userdata('karyawan_id');
        $extra_data = array(
            'karyawan' => $this->M_master->karyawan_lembur()->result(),
            'jml' => $this->M_master->karyawan_lembur()->num_rows(),
            'listkaryawan' => $this->M_master->get_karyawan()->result(),
            'karyawan_terpilih' => $this->db->get_where('tbl_karyawan', array('karyawan_id' => $karyawan_id))->row_array(),
        );

        $data = $this->_prepare_view_data('Sistem Absensi Lembur', $extra_data);
        $this->load_admin_view('admin/karyawan/pengajuan_lembur', $data);
    }

    public function absen_karyawan()
    {
        $tanggal = $this->input->get('tanggal', true) ?: date('Y-m-d');
        $bulan_berjalan = date('Y-m-01'); // Ensure full date for DB filter

        $extra_data = array(
            'absensi' => $this->Absensi_model->get_attendance_by_date($tanggal),
            'rekap_bulanan' => $this->Absensi_model->get_rekap_all_karyawan($bulan_berjalan),
            'tanggal' => $tanggal,
            'bulan' => date('Y-m')
        );

        $data = $this->_prepare_view_data('Sistem Monitoring Absensi', $extra_data);
        $this->load_admin_view('admin/karyawan/absen_karyawan', $data);
    }

    public function aksi_simpan_lembur()
    {
        $karyawan_id = (int) $this->input->post('karyawan_id', true);
        $tanggal = $this->input->post('tanggal', true);
        $jam_mulai = $this->input->post('jam_mulai', true);
        $jam_selesai = $this->input->post('jam_selesai', true);
        $kegiatan = $this->input->post('kegiatan', true);

        $data = [
            'karyawan_id' => $karyawan_id,
            'tanggal' => $tanggal,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'kegiatan' => $kegiatan,
            'status' => 'disetujui' // Auto-approve if HR adds it manually
        ];

        $this->db->insert('tbl_lembur', $data);
        $this->session->set_flashdata('success', 'Data lembur berhasil ditambahkan.');
        redirect('karyawan/absen_lembur');
    }

    public function get_lembur_detail_ajax()
    {
        $karyawan_id = (int) $this->input->post('karyawan_id', true);
        $tanggal = $this->input->post('tanggal', true);

        $this->db->where('karyawan_id', $karyawan_id);
        $this->db->where('tanggal', $tanggal);
        $this->db->order_by('jam_mulai', 'ASC');
        $data = $this->db->get('tbl_lembur')->result();

        echo json_encode(['success' => true, 'data' => $data]);
    }

    public function update_lembur_time_ajax()
    {
        $id = (int) $this->input->post('id', true);
        $jam_mulai = $this->input->post('jam_mulai', true);
        $jam_selesai = $this->input->post('jam_selesai', true);

        $this->db->where('id', $id);
        $this->db->update('tbl_lembur', [
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai
        ]);

        echo json_encode(['success' => true]);
    }

    public function hapus_lembur_ajax()
    {
        $id = (int) $this->input->post('id', true);
        $this->db->where('id', $id);
        $this->db->delete('tbl_lembur');

        echo json_encode(['success' => true]);
    }



    public function jabatan()
    {
        $extra_data = array(
            'jabatan' => $this->db->get('tbl_jabatan')->result(),
            'jml' => $this->db->get('tbl_jabatan')->num_rows(),
            'table' => 'tbl_karyawan', // Keep for compatibility if view uses it
        );

        $data = $this->_prepare_view_data('Daftar Jabatan', $extra_data);
        $this->load_admin_view('admin/karyawan/jabatan', $data);
    }

    public function tambah_karyawan()
    {
        // Initialize empty object for a new employee
        $karyawan = (object) [
            'karyawan_id' => '',
            'nik' => '',
            'nama_lengkap' => '',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => '',
            'tanggal_lahir' => '',
            'alamat' => '',
            'telepon' => '',
            'email' => '',
            'tanggal_masuk' => date('Y-m-d'),
            'status_karyawan' => 'Tetap',
            'jabatan_id' => '',
            'divisi_id' => '',
            'perusahaan_id' => $this->session->userdata('perusahaan_id'),
        ];

        $extra_data = array(
            'karyawan' => $karyawan,
            'jabatan' => $this->M_master->get_all_jabatan(),
            'divisi' => $this->db->get('tbl_divisi')->result(),
            'perusahaan' => $this->db->get('tbl_perusahaan')->result(),
        );

        $data = $this->_prepare_view_data('Tambah Data Karyawan', $extra_data);
        $this->load_admin_view('admin/karyawan/tambah_karyawan', $data);
    }

    public function tambah_jabatan()
    {
        $data = $this->_prepare_view_data('Update Alur Kerja');
        $this->load_admin_view('admin/karyawan/tambah_jabatan', $data);
    }

    public function aksi_simpan_jabatan()
    {
        $data = [
            'nama_jabatan' => $this->input->post('nama_jabatan', true),
            'gaji_pokok' => $this->input->post('gaji_pokok', true),
            'tunjangan_jabatan' => $this->input->post('tunjangan_jabatan', true),
            'tunjangan_transportasi' => $this->input->post('tunjangan_transportasi', true),
            'keterangan' => $this->input->post('deskripsi', true),
        ];

        if ($this->db->insert('tbl_jabatan', $data)) {
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
            redirect('karyawan/daftar-jabatan-divisi');
        }
    }

    public function simpan_konsumen()
    {
        $data = array(
            'konsumen_nama' => $this->input->post('konsumen_nama', true),
            'konsumen_alamat' => $this->input->post('konsumen_alamat', true),
            'konsumen_nohp' => $this->input->post('konsumen_nohp', true),
            'konsumen_email' => $this->input->post('konsumen_email', true),
            'password' => md5($this->input->post('konsumen_nohp', true)),
            'member' => '0',
            'avatar' => 'male.png',
            'perusahaan_id' => $this->session->userdata('perusahaan_id')
        );

        if ($this->M_master->addData('tbl_konsumen', $data)) {
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
            redirect('transaksi/add/1');
        }
    }

    public function delete_karyawan($id)
    {
        // 1. Dapatkan data karyawan sebelum dihapus (untuk referensi file fisik)
        $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $id])->row();
        if (!$karyawan) {
            $this->session->set_flashdata('error', 'Data karyawan tidak ditemukan.');
            redirect('karyawan/karyawan');
            return;
        }

        // 2. Cari Akun User yang terhubung
        $user = $this->db->get_where('users', ['karyawan_id' => $id])->row();

        // 3. Cari Daftar Dokumen yang terhubung
        $documents = $this->db->get_where('tbl_karyawan_dokumen', ['karyawan_id' => $id])->result();

        // Mulai Transaksi Database
        $this->db->trans_start();

        // A. Hapus data akun dan log aktivitas (jika ada)
        if ($user) {
            // Hapus log aktivitas user
            $this->db->where('user_id', $user->user_id);
            $this->db->delete('user_activity_log');

            // Hapus roles user (jika menggunakan tabel junction)
            $this->db->where('user_id', $user->user_id);
            $this->db->delete('user_roles');

            // Hapus akun user
            $this->db->where('user_id', $user->user_id);
            $this->db->delete('users');
        }

        // B. Hapus data absensi
        $this->db->where('karyawan_id', $id);
        $this->db->delete('absensi');

        // C. Hapus data lembur
        $this->db->where('karyawan_id', $id);
        $this->db->delete('tbl_lembur');

        // D. Hapus data pengajuan izin/sakit
        $this->db->where('karyawan_id', $id);
        $this->db->delete('tbl_pengajuan_izin');

        // E. Hapus data penggajian manual/kasbon
        $this->db->where('karyawan_id', $id);
        $this->db->delete('tbl_gaji_manual');

        // F. Hapus data dokumen (record database)
        $this->db->where('karyawan_id', $id);
        $this->db->delete('tbl_karyawan_dokumen');

        // G. Hapus data utama karyawan
        $this->db->where('karyawan_id', $id);
        $this->db->delete('tbl_karyawan');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal menghapus data karyawan secara menyeluruh.');
        } else {
            // Hapus File Fisik Jika Transaksi Berhasil

            // 1. Hapus Foto Profil
            if ($karyawan->foto && file_exists('./assets/images/karyawan/' . $karyawan->foto)) {
                unlink('./assets/images/karyawan/' . $karyawan->foto);
            }

            // 2. Hapus Semua Dokumen (KTP, KK, CV, dll)
            foreach ($documents as $doc) {
                if ($doc->file_path && file_exists('./assets/documents/karyawan/' . $doc->file_path)) {
                    unlink('./assets/documents/karyawan/' . $doc->file_path);
                }
            }

            $this->session->set_flashdata('success', 'Seluruh data karyawan (Akun, Absen, Gaji, Dokumen) berhasil dihapus permanen.');
        }

        redirect('karyawan/karyawan');
    }

    public function simpan_karyawan()
    {
        // Validation Rules
        $this->form_validation->set_rules('nip', 'NIK', 'required|is_unique[tbl_karyawan.nik]');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|is_unique[tbl_karyawan.email]');
        $this->form_validation->set_rules('telepon', 'Telepon', 'required');
        $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
        $this->form_validation->set_rules('tanggal_masuk', 'Tanggal Masuk', 'required');
        $this->form_validation->set_rules('jabatan_id', 'Jabatan', 'required');
        $this->form_validation->set_rules('divisi_id', 'Divisi', 'required');
        $this->form_validation->set_rules('perusahaan_id', 'Perusahaan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('karyawan/tambah_karyawan');
            return;
        }

        $table = 'tbl_karyawan';
        $status_karyawan = $this->input->post('status_karyawan', true);
        $tanggal_masuk = $this->input->post('tanggal_masuk', true);
        $akhir_kontrak = $this->input->post('akhir_kontrak', true);

        if (($status_karyawan == 'Kontrak' || $status_karyawan == 'Magang') && empty($akhir_kontrak)) {
            $akhir_kontrak = date('Y-m-d', strtotime($tanggal_masuk . ' + 2 years'));
        }

        $data = [
            'nik' => $this->input->post('nip', true),
            'nama_lengkap' => $this->input->post('nama_lengkap', true),
            'jenis_kelamin' => $this->input->post('jenis_kelamin', true),
            'tempat_lahir' => $this->input->post('tempat_lahir', true),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', true),
            'alamat' => $this->input->post('alamat', true),
            'telepon' => $this->input->post('telepon', true),
            'email' => $this->input->post('email', true),
            'tanggal_masuk' => $tanggal_masuk,
            'status_karyawan' => $status_karyawan,
            'akhir_kontrak' => ($status_karyawan == 'Kontrak' || $status_karyawan == 'Magang') ? $akhir_kontrak : NULL,
            'jenis_gaji' => $this->input->post('jenis_gaji', true) ?: 'Bulanan',
            'jabatan_id' => $this->input->post('jabatan_id', true),
            'divisi_id' => $this->input->post('divisi_id', true),
            'perusahaan_id' => $this->input->post('perusahaan_id', true),
            'pin_produksi' => $this->input->post('pin_produksi', true),
            'transport_aktif' => $this->input->post('transport_aktif', true) ?? 0,
        ];

        // PIN Uniqueness Check
        if (!empty($data['pin_produksi'])) {
            $exists = $this->db->get_where('tbl_karyawan', [
                'pin_produksi' => $data['pin_produksi'],
                'perusahaan_id' => $data['perusahaan_id']
            ])->row();
            if ($exists) {
                $this->session->set_flashdata('error', 'PIN Produksi sudah digunakan oleh ' . $exists->nama_lengkap . ' di cabang ini!');
                redirect('karyawan/tambah_karyawan');
                return;
            }
        }

        $insert = $this->db->insert($table, $data);
        if ($insert) {
            $karyawan_id = $this->db->insert_id();

            // Handle Document Uploads
            $dokumen_types = [
                'dokumen_ktp' => 'ktp',
                'dokumen_kk' => 'kk',
                'dokumen_npwp' => 'npwp',
                'dokumen_ijazah' => 'ijazah',
                'dokumen_cv' => 'cv',
                'dokumen_kontrak' => 'kontrak',
                'dokumen_pernyataan' => 'pernyataan',
                'dokumen_form_data' => 'form_data'
            ];

            $config['upload_path'] = './assets/documents/karyawan/';
            $config['allowed_types'] = 'pdf|jpg|jpeg|png';
            $config['max_size'] = 5048; // 5MB
            $config['encrypt_name'] = TRUE; // Encrypt name for security

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            foreach ($dokumen_types as $input_name => $db_type) {
                if (!empty($_FILES[$input_name]['name'])) {
                    // Initialize or clear previous upload data
                    $this->upload->initialize($config);

                    if ($this->upload->do_upload($input_name)) {
                        $upload_data = $this->upload->data();
                        $doc_data = [
                            'karyawan_id' => $karyawan_id,
                            'jenis_dokumen' => $db_type,
                            'file_path' => $upload_data['file_name']
                        ];
                        $this->db->insert('tbl_karyawan_dokumen', $doc_data);
                    }
                }
            }

            $this->session->set_flashdata('success', 'Data karyawan dan dokumen berhasil disimpan!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data karyawan!');
        }
        redirect('karyawan/karyawan');
    }


    public function simpan_karyawan_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        // Validation
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('nik', 'NIK', 'required|is_unique[tbl_karyawan.nik]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|is_unique[tbl_karyawan.email]');
        $this->form_validation->set_rules('telepon', 'Telepon', 'required');
        $this->form_validation->set_rules('jabatan_id', 'Jabatan', 'required');
        $this->form_validation->set_rules('divisi_id', 'Divisi', 'required');
        $this->form_validation->set_rules('perusahaan_id', 'Perusahaan', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => false, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'nama_lengkap' => $this->input->post('nama_lengkap', true),
            'nik' => $this->input->post('nik', true),
            'jenis_kelamin' => $this->input->post('jenis_kelamin', true),
            'tempat_lahir' => $this->input->post('tempat_lahir', true),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', true),
            'alamat' => $this->input->post('alamat', true),
            'telepon' => $this->input->post('telepon', true),
            'email' => $this->input->post('email', true),
            'tanggal_masuk' => $this->input->post('tanggal_masuk', true),
            'status_karyawan' => $this->input->post('status_karyawan', true),
            'jabatan_id' => (int) $this->input->post('jabatan_id', true),
            'divisi_id' => (int) $this->input->post('divisi_id', true),
            'perusahaan_id' => (int) $this->input->post('perusahaan_id', true),
            'pin_produksi' => $this->input->post('pin_produksi', true),
        ];

        // Check PIN Uniqueness within same branch
        if (!empty($data['pin_produksi'])) {
            $exists = $this->db->get_where('tbl_karyawan', [
                'pin_produksi' => $data['pin_produksi'],
                'perusahaan_id' => $data['perusahaan_id']
            ])->row();
            if ($exists) {
                echo json_encode(['status' => false, 'message' => 'PIN Produksi sudah digunakan oleh ' . $exists->nama_lengkap . ' di cabang ini!']);
                return;
            }
        }

        // Handle File Upload (Foto Profil)
        if (!empty($_FILES['foto']['name'])) {
            $upload_path = FCPATH . 'assets/images/karyawan/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config = [];
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|webp|gif|JPG|JPEG|PNG|WEBP|GIF';
            $config['max_size'] = 10240; // 10MB
            $config['file_name'] = 'karyawan_' . time() . '_' . uniqid();

            $this->upload->initialize($config);

            if ($this->upload->do_upload('foto')) {
                $upload_data = $this->upload->data();
                $data['foto'] = $upload_data['file_name'];
            } else {
                echo json_encode(['status' => false, 'message' => 'Gagal upload foto: ' . strip_tags($this->upload->display_errors())]);
                return;
            }
        }

        if ($this->db->insert('tbl_karyawan', $data)) {
            $karyawan_id = $this->db->insert_id();

            // Handle Document Uploads
            $dokumen_types = [
                'dokumen_ktp' => 'ktp',
                'dokumen_kk' => 'kk',
                'dokumen_npwp' => 'npwp',
                'dokumen_ijazah' => 'ijazah',
                'dokumen_cv' => 'cv',
                'dokumen_kontrak' => 'kontrak',
                'dokumen_pernyataan' => 'pernyataan',
                'dokumen_form_data' => 'form_data'
            ];

            $doc_upload_path = FCPATH . 'assets/documents/karyawan/';
            if (!is_dir($doc_upload_path)) {
                mkdir($doc_upload_path, 0777, true);
            }

            $doc_config = [];
            $doc_config['upload_path'] = $doc_upload_path;
            $doc_config['allowed_types'] = 'pdf|jpg|jpeg|png|doc|docx|PDF|JPG|JPEG|PNG|DOC|DOCX';
            $doc_config['max_size'] = 10240; // 10MB
            $doc_config['encrypt_name'] = TRUE;

            $this->upload->initialize($doc_config);

            $upload_errors = [];

            foreach ($dokumen_types as $input_name => $db_type) {
                if (!empty($_FILES[$input_name]['name'])) {
                    $this->upload->initialize($doc_config);
                    if ($this->upload->do_upload($input_name)) {
                        $upload_data = $this->upload->data();
                        $doc_data = [
                            'karyawan_id' => $karyawan_id,
                            'jenis_dokumen' => $db_type,
                            'file_path' => $upload_data['file_name']
                        ];
                        $this->db->insert('tbl_karyawan_dokumen', $doc_data);
                    } else {
                        $upload_errors[] = "Gagal mengunggah $db_type: " . strip_tags($this->upload->display_errors());
                    }
                }
            }

            $response = [
                'status' => true,
                'message' => 'Data karyawan berhasil disimpan',
                'stats' => $this->_get_karyawan_stats() // Ensure this helper exists or inline it
            ];
            if (!empty($upload_errors)) {
                $response['message'] .= '. Namun ada beberapa dokumen gagal: ' . implode(', ', $upload_errors);
            }
            echo json_encode($response);
        } else {
            echo json_encode(['status' => false, 'message' => 'Gagal menyimpan ke database']);
        }
    }
    public function get_karyawan_json($id)
    {
        // Check if it's an AJAX request
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->db->select('k.*, j.nama_jabatan, d.divisi_nama, p.nama_perusahaan');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left');
        $this->db->join('tbl_perusahaan p', 'k.perusahaan_id = p.id_perusahaan', 'left');
        $this->db->where('k.karyawan_id', $id);
        $karyawan = $this->db->get()->row();

        if ($karyawan) {
            // Get documents
            $this->db->where('karyawan_id', $id);
            $documents = $this->db->get('tbl_karyawan_dokumen')->result();
            $karyawan->documents = $documents;

            echo json_encode(['success' => true, 'data' => $karyawan]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data not found']);
        }
    }

    public function leger_hrd()
    {
        $bulan = $this->input->get('bulan', true) ?: date('Y-m');
        $nama = $this->input->get('nama', true) ?: '';
        $divisi = $this->input->get('divisi', true) ?: '';
        $jabatan = $this->input->get('jabatan', true) ?: '';

        // Gunakan Payroll_model agar angka 100% konsisten dengan Slip Gaji
        $this->load->model('Payroll_model');
        $salary_data = $this->Payroll_model->get_salary_data($bulan);

        // Filter data berdasarkan input user (karena get_salary_data mengembalikan semua)
        $filtered_data = [];
        foreach ($salary_data as $s) {
            $match = true;
            if ($nama && stripos($s['nama_lengkap'], $nama) === false)
                $match = false;
            if ($divisi && $s['divisi_nama'] != $divisi)
                $match = false;
            if ($jabatan && $s['nama_jabatan'] != $jabatan)
                $match = false;

            if ($match) {
                // Ambil data foto (Payroll_model mungkin tidak mengembalikannya)
                $emp = $this->db->select('foto, nik')->get_where('tbl_karyawan', ['karyawan_id' => $s['karyawan_id']])->row();
                $s['foto'] = $emp->foto ?? '';
                $s['nik'] = $emp->nik ?? '';

                // Data KPI sudah dihitung otomatis oleh Payroll_model di field 'skor_kinerja'
                $s['kpi_score'] = $s['skor_kinerja'] ?? 0;

                // Tentukan Predikat berdasarkan skor (sama dengan logika rekap bulanan)
                if ($s['kpi_score'] >= 95)
                    $s['kpi_predikat'] = 'ISTIMEWA';
                elseif ($s['kpi_score'] >= 85)
                    $s['kpi_predikat'] = 'SANGAT BAIK';
                elseif ($s['kpi_score'] >= 75)
                    $s['kpi_predikat'] = 'BAIK';
                elseif ($s['kpi_score'] >= 65)
                    $s['kpi_predikat'] = 'CUKUP';
                else
                    $s['kpi_predikat'] = 'KURANG';

                $filtered_data[] = (object) $s;
            }
        }

        $extra_data = [
            'rekap_data' => $filtered_data,
            'bulan' => $bulan,
            'nama' => $nama,
            'divisi' => $divisi,
            'jabatan' => $jabatan,
            'jabatan_list' => $this->db->get('tbl_jabatan')->result(),
            'divisi_list' => $this->db->get('tbl_divisi')->result(),
        ];

        $data = $this->_prepare_view_data('Master Leger HRD', $extra_data);
        $this->load_admin_view('admin/karyawan/leger', $data);
    }

    public function edit_karyawan($id)
    {
        // Ambil data karyawan
        $karyawan = $this->M_master->get_by_id($id);
        if (!$karyawan) {
            show_404();
            return;
        }

        $extra_data = array(
            'karyawan' => $karyawan,
            'jabatan' => $this->M_master->get_all_jabatan(),
            'divisi' => $this->db->get('tbl_divisi')->result(),
            'perusahaan' => $this->db->get('tbl_perusahaan')->result(),
        );

        $data = $this->_prepare_view_data('Edit Data Karyawan', $extra_data);
        $this->load_admin_view('admin/karyawan/edit_karyawan', $data);
    }

    public function edit_akun($id)
    {
        $extra_data = array(
            'getlist' => $this->db->get_where('users', array('user_id' => $id))->row_array(),
            'karyawan' => $this->M_master->get_karyawan()->result(),
            'perusahaan' => $this->db->get('tbl_perusahaan')->result(),
        );

        $data = $this->_prepare_view_data('Update Akun', $extra_data);
        $this->load_admin_view('admin/karyawan/edit_akun', $data);
    }

    public function aksi_edit_akun()
    {
        $controller = $this->uri->segment(1);
        $table = 'users';

        $where = array(
            'user_id' => $this->input->post('user_id', true),
        );


        $id = $this->input->post('user_id', true);
        $password = md5($this->input->post('password', true));

        $data = array(
            'username' => $this->input->post('username', true),
            'password' => $password,
            'email' => $this->input->post('email', true),
            'karyawan_id' => $this->input->post('karyawan_id', true),
            'perusahaan_id' => $this->input->post('perusahaan_id', true),

            'submenu' => $this->M_master->getmenu($controller)->result(),
        );


        $update = $this->M_master->updateData($table, $where, $data);


        if ($update == true) {
            $this->session->set_flashdata('success', 'Data Berhasil Diupdate');
            redirect('karyawan/daftar-akun');
        }
    }

    public function aksi_edit_karyawan()
    {
        $id = $this->input->post('karyawan_id', true);

        $where = array(
            'karyawan_id' => $this->input->post('karyawan_id', true),
        );

        $status_karyawan = $this->input->post('status_karyawan', true);
        $tanggal_masuk = $this->input->post('tanggal_masuk', true);
        $akhir_kontrak = $this->input->post('akhir_kontrak', true);

        if (($status_karyawan == 'Kontrak' || $status_karyawan == 'Magang') && empty($akhir_kontrak)) {
            $akhir_kontrak = date('Y-m-d', strtotime($tanggal_masuk . ' + 2 years'));
        }

        $data = [
            'nama_lengkap' => $this->input->post('nama_lengkap', true),
            'nik' => $this->input->post('nip', true),
            'jenis_kelamin' => $this->input->post('jenis_kelamin', true),
            'tempat_lahir' => $this->input->post('tempat_lahir', true),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', true),
            'alamat' => $this->input->post('alamat', true),
            'telepon' => $this->input->post('telepon', true),
            'email' => $this->input->post('email', true),
            'tanggal_masuk' => $tanggal_masuk,
            'status_karyawan' => $status_karyawan,
            'akhir_kontrak' => ($status_karyawan == 'Kontrak' || $status_karyawan == 'Magang') ? $akhir_kontrak : NULL,
            'jenis_gaji' => $this->input->post('jenis_gaji', true) ?: 'Bulanan',
            'jabatan_id' => $this->input->post('jabatan_id', true),
            'perusahaan_id' => $this->input->post('perusahaan_id', true),
            'divisi_id' => $this->input->post('divisi_id', true),
            'pin_produksi' => $this->input->post('pin_produksi', true),
            'transport_aktif' => $this->input->post('transport_aktif', true) ?? 0,
        ];

        // PIN Uniqueness Check
        if (!empty($data['pin_produksi'])) {
            $this->db->where('pin_produksi', $data['pin_produksi']);
            $this->db->where('perusahaan_id', $data['perusahaan_id']);
            $this->db->where('karyawan_id !=', $id);
            $exists = $this->db->get('tbl_karyawan')->row();
            if ($exists) {
                $this->session->set_flashdata('error', 'PIN Produksi sudah digunakan oleh ' . $exists->nama_lengkap . ' di cabang ini!');
                redirect('karyawan/edit_karyawan/' . $id);
                return;
            }
        }

        // Validation Rules for Edit
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        // Check uniqueness only if changed
        $original_data = $this->db->get_where('tbl_karyawan', array('karyawan_id' => $id))->row();
        if ($this->input->post('nip', true) != $original_data->nik) {
            $this->form_validation->set_rules('nip', 'NIK', 'required|is_unique[tbl_karyawan.nik]');
        }
        if ($this->input->post('email', true) != $original_data->email) {
            $this->form_validation->set_rules('email', 'Email', 'valid_email|is_unique[tbl_karyawan.email]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('karyawan/edit_karyawan/' . $id);
            return;
        }

        if (!empty($_FILES['foto']['name'])) {
            $upload_path = FCPATH . 'assets/images/karyawan/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config = [];
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|webp|gif|JPG|JPEG|PNG|WEBP|GIF';
            $config['max_size'] = 10240; // 10MB
            $config['file_name'] = 'karyawan_' . time() . '_' . uniqid();

            $this->upload->initialize($config);

            if ($this->upload->do_upload('foto')) {
                $upload_data = $this->upload->data();
                $data['foto'] = $upload_data['file_name'];

                // Hapus foto lama (jika ada)
                $old_foto = $this->M_master->get_by_id($id)->foto;
                if ($old_foto && !in_array($old_foto, ['default.png', 'user.png']) && file_exists($upload_path . $old_foto)) {
                    @unlink($upload_path . $old_foto);
                }
            } else {
                // Jika upload gagal
                $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
                redirect('karyawan/edit-karyawan/' . $id);
            }
        }

        $update = $this->M_master->updateData('tbl_karyawan', $where, $data);
        print_r($update);

        if ($update == true) {
            $this->session->set_flashdata('success', 'Data Berhasil Diupdate');
            redirect('karyawan/daftar-karyawan');
        }
    }

    function viewData($id)
    {

        $userid = $this->session->userdata('id');
        $controller = $this->uri->segment(1);

        $data = array(
            'controller' => $controller,
            'function' => $this->uri->segment(2),
            'title' => 'Update Alur Kerja',
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),

            'submenu' => $this->M_master->getmenu($controller)->result(),
        );

        $this->load_admin_view('admin/' . $controller . '/form_update', $data);

    }

    public function divisi()
    {
        $extra_data = array(
            'tampilData' => $this->M_master->tampilsemuadata('tbl_divisi', 'divisi_id')->result(),
            'total_rows' => $this->M_master->tampilsemuadata('tbl_divisi', 'divisi_id')->num_rows(),
        );

        $data = $this->_prepare_view_data('Daftar Divisi', $extra_data);
        $this->load_admin_view('admin/karyawan/divisi', $data);
    }





    // public function penggajian() {
    //     $this->load->model('M_master');
    //     $this->load->model('Penggajian_model');
    //     $bulan = $this->input->get('bulan', true) ?: date('Y-m');
    //     $list_karyawan = $this->M_master->get_karyawan()->result();
    //     $gaji = [];
    //     foreach ($list_karyawan as $k) {
    //         $gaji[$k->karyawan_id] = $this->Penggajian_model->proses_gaji($k->karyawan_id, $bulan);
    //     }
    //     $data = [
    //         'list_karyawan' => $list_karyawan,
    //         'gaji'          => $gaji,
    //         'bulan'         => $bulan
    //     ];
    //     $this->load_admin_view('admin/karyawan/penggajian', $data);
    // }

    public function proses_penggajian($id_karyawan, $bulan)
    {
        $result = $this->Penggajian_model->proses_gaji($id_karyawan, $bulan);
        if ($result) {
            redirect('penggajian');
        }
    }

    function hapus_lembur($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tbl_lembur');
        $this->session->set_flashdata('success', 'Data lembur berhasil dihapus.');
        redirect('karyawan/absen_lembur');
    }




    /**
     * Test function to check database structure
     */
    public function test_db_structure()
    {
        if (!$this->input->is_ajax_request()) {
            echo "Not AJAX request";
            return;
        }

        $result = array();

        // Test jabatan table
        $jabatan_data = $this->db->get('tbl_jabatan')->result();
        $result['jabatan'] = $jabatan_data;

        // Test divisi table
        $divisi_data = $this->db->get('tbl_divisi')->result();
        $result['divisi'] = $divisi_data;

        // Test perusahaan table
        $perusahaan_data = $this->db->get('tbl_perusahaan')->result();
        $result['perusahaan'] = $perusahaan_data;

        // Test karyawan table with joins
        $this->db->select('k.karyawan_id, k.nama_lengkap, k.jabatan_id, k.divisi_id, j.nama_jabatan, d.divisi_nama');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left');
        $this->db->limit(5);
        $karyawan_sample = $this->db->get()->result();
        $result['karyawan_sample'] = $karyawan_sample;

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * AJAX search method for employee data
     */
    public function search_ajax()
    {
        // Check if it's an AJAX request
        if (!$this->input->is_ajax_request()) {
            $response = array(
                'success' => false,
                'message' => 'Invalid request method'
            );
            echo json_encode($response);
            return;
        }

        try {
            // Get search parameters
            $search = trim($this->input->post('search', true));
            $gender = trim($this->input->post('gender', true));
            $status = trim($this->input->post('status', true));
            $jabatan = trim($this->input->post('jabatan', true));
            $divisi = trim($this->input->post('divisi', true));
            $perusahaan = trim($this->input->post('perusahaan', true));

            // Build the query
            $this->db->select('k.*, j.nama_jabatan, d.divisi_nama, p.nama_perusahaan, ' .
                '(SELECT COUNT(*) FROM tbl_karyawan_dokumen WHERE karyawan_id = k.karyawan_id) as doc_count, ' .
                '(SELECT status FROM tbl_pengajuan_kontrak WHERE karyawan_id = k.karyawan_id ORDER BY created_at DESC LIMIT 1) as pengajuan_kontrak_status, ' .
                '(SELECT durasi_perpanjangan FROM tbl_pengajuan_kontrak WHERE karyawan_id = k.karyawan_id ORDER BY created_at DESC LIMIT 1) as pengajuan_kontrak_durasi');
            $this->db->from('tbl_karyawan k');
            $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
            $this->db->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left');
            $this->db->join('tbl_perusahaan p', 'k.perusahaan_id = p.id_perusahaan', 'left');

            // Exclude Owner and Directors
            $this->db->where('k.divisi_id !=', 8);
            $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);

            // Exclude Dummy Terminal Accounts
            $this->db->where("k.nik NOT LIKE 'STATION%'");
            $this->db->where("k.nama_lengkap NOT LIKE '%Terminal%'");
            $this->db->where("k.nama_lengkap NOT LIKE '%DTF%'");
            $this->db->where("k.nama_lengkap NOT LIKE 'OPERATOR %'");

            // Apply company filter
            if (!empty($this->session->userdata('perusahaan_id')) && $this->session->userdata('perusahaan_id') != 1) {
                $this->db->where('k.perusahaan_id', $this->session->userdata('perusahaan_id'));
            }

            // Apply search filter
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('k.nama_lengkap', $search);
                $this->db->or_like('k.nik', $search);
                $this->db->or_like('k.email', $search);
                $this->db->group_end();
            }

            // Apply gender filter
            if (!empty($gender)) {
                $this->db->where('k.jenis_kelamin', $gender);
            }

            // Apply status filter
            if (!empty($status)) {
                $this->db->where('k.status_karyawan', $status);
            }

            // Apply jabatan filter (Handle both ID or Name for backward compatibility)
            if (!empty($jabatan)) {
                if (is_numeric($jabatan)) {
                    $this->db->where('k.jabatan_id', $jabatan);
                } else {
                    $this->db->where('j.nama_jabatan', $jabatan);
                }
            }

            // Apply divisi filter
            if (!empty($divisi)) {
                if (is_numeric($divisi)) {
                    $this->db->where('k.divisi_id', $divisi);
                } else {
                    $this->db->where('d.divisi_nama', $divisi);
                }
            }

            // Apply perusahaan filter
            if (!empty($perusahaan)) {
                if (is_numeric($perusahaan)) {
                    $this->db->where('k.perusahaan_id', $perusahaan);
                } else {
                    $this->db->where('p.nama_perusahaan', $perusahaan);
                }
            }

            // Order by: Tanggal Masuk (Oldest First) then Nama (A-Z)
            $this->db->order_by('k.tanggal_masuk', 'asc');
            $this->db->order_by('k.nama_lengkap', 'asc');

            // Get the data
            $data = $this->db->get()->result();

            // Calculate statistics and progress
            $total = count($data);
            $male = 0;
            $female = 0;
            $active = 0;

            $progress_fields = ['nik', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'telepon', 'email', 'tanggal_masuk', 'status_karyawan', 'gaji_pokok', 'nominal_lembur', 'tunjangan_jabatan', 'jabatan_id', 'divisi_id', 'perusahaan_id', 'foto'];

            foreach ($data as &$row) {
                // Stats
                if ($row->jenis_kelamin == 'L' || $row->jenis_kelamin == 'Laki-laki')
                    $male++;
                if ($row->jenis_kelamin == 'P' || $row->jenis_kelamin == 'Perempuan')
                    $female++;
                if ($row->status_karyawan == 'Aktif' || $row->status_karyawan == '1')
                    $active++;

                // Progress Calculation
                $filled = 0;
                foreach ($progress_fields as $f) {
                    if (!empty($row->$f))
                        $filled++;
                }

                // Document progress (Weight: 3 documents = 100% of doc part)
                $doc_filled = ($row->doc_count >= 3) ? 3 : $row->doc_count;
                $row->progress_percentage = round((($filled + $doc_filled) / (count($progress_fields) + 3)) * 100);
            }

            // Prepare response
            $response = array(
                'success' => true,
                'data' => $data,
                'stats' => array(
                    'total' => $total,
                    'male' => $male,
                    'female' => $female,
                    'active' => $active
                ),
                'debug' => array(
                    'search' => $search,
                    'gender' => $gender,
                    'status' => $status,
                    'jabatan' => $jabatan,
                    'divisi' => $divisi,
                    'perusahaan' => $perusahaan,
                    'query' => $this->db->last_query(),
                    'perusahaan_id' => $this->session->userdata('perusahaan_id')
                )
            );

        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'debug' => array(
                    'error' => $e->getMessage(),
                    'search' => $search ?? '',
                    'jabatan' => $jabatan ?? '',
                    'divisi' => $divisi ?? '',
                    'perusahaan' => $perusahaan ?? ''
                )
            );
        }

        // Set content type and return JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * AJAX method to get employees by division
     */
    public function get_employees_by_divisi($divisi_id = null)
    {
        // Check if user is authorized
        $user_level = $this->session->userdata('level');
        $user_level = $this->session->userdata('level');
        $is_owner = ($user_level == 17);
        $is_admin = ($user_level == 1);
        $is_sdm = ($user_level == 21);

        if (!$is_owner && !$is_admin && !$is_sdm) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        if (!$divisi_id) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'Divisi ID is required']);
            return;
        }

        try {
            // Get employees by division
            $this->db->select('k.karyawan_id as id, k.nama_lengkap as nama, k.nik as nip, k.email, k.jenis_kelamin, j.nama_jabatan as jabatan, d.divisi_nama as divisi, p.nama_perusahaan as lokasi_cabang, u.username');
            $this->db->from('tbl_karyawan k');
            $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
            $this->db->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left');
            $this->db->join('tbl_perusahaan p', 'k.perusahaan_id = p.id_perusahaan', 'left');
            $this->db->join('users u', 'k.karyawan_id = u.karyawan_id', 'left');
            $this->db->where('k.divisi_id', $divisi_id);

            // Apply company filter
            // Apply company filter
            if (!empty($this->session->userdata('perusahaan_id')) && $this->session->userdata('perusahaan_id') != 1) {
                $this->db->where('k.perusahaan_id', $this->session->userdata('perusahaan_id'));
            }

            $this->db->order_by('k.nama_lengkap', 'asc');
            $employees = $this->db->get()->result();

            // Get statistics
            $this->db->select('COUNT(*) as total');
            $this->db->from('tbl_karyawan');
            $this->db->where('divisi_id', $divisi_id);

            // Apply company filter
            // Apply company filter
            // NO FILTER

            $stats = $this->db->get()->row();

            // Process employee data
            $processed_employees = array();
            foreach ($employees as $employee) {
                $processed_employees[] = array(
                    'id' => $employee->id,
                    'nama' => $employee->nama,
                    'nip' => $employee->nip,
                    'email' => $employee->email,
                    'jabatan' => $employee->jabatan,
                    'status' => $employee->status_aktif == 1 ? 'Aktif' : 'Nonaktif',
                    'gender' => $employee->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                    'divisi' => $employee->divisi,
                    'lokasi_cabang' => $employee->lokasi_cabang,
                    'username' => $employee->username
                );
            }

            $response = array(
                'success' => true,
                'employees' => $processed_employees,
                'stats' => array(
                    'total' => $stats->total
                )
            );

        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            );
        }

        // Set content type and return JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Export divisi employee data to Excel
     */
    public function export_divisi($divisi_id = null)
    {
        // Check if user is authorized
        $user_level = $this->session->userdata('level');
        $is_owner = ($user_level == 17);
        $is_admin = ($user_level == 1);

        if (!$is_owner && !$is_admin) {
            show_error('Access denied', 403);
            return;
        }

        if (!$divisi_id) {
            show_error('Divisi ID is required', 400);
            return;
        }

        try {
            // Get divisi name
            $divisi = $this->db->get_where('tbl_divisi', array('divisi_id' => $divisi_id))->row();
            if (!$divisi) {
                show_error('Divisi not found', 404);
                return;
            }

            // Get employees by division
            $this->db->select('k.nama_lengkap, k.nik, k.email, k.jenis_kelamin, k.status_aktif, j.nama_jabatan, d.divisi_nama, p.nama_perusahaan, d.divisi_id, u.username');
            $this->db->from('tbl_karyawan k');
            $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
            $this->db->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left');
            $this->db->join('tbl_perusahaan p', 'k.perusahaan_id = p.id_perusahaan', 'left');
            $this->db->join('users u', 'k.karyawan_id = u.karyawan_id', 'left');
            $this->db->where('k.divisi_id', $divisi_id);

            $this->db->order_by('k.nama_lengkap', 'asc');
            $employees = $this->db->get()->result();

            // Load PHPExcel library
            $this->load->library('excel');

            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("TGM ERP System")
                ->setLastModifiedBy("TGM ERP System")
                ->setTitle("Data Karyawan Divisi " . $divisi->divisi_nama)
                ->setSubject("Data Karyawan Divisi " . $divisi->divisi_nama)
                ->setDescription("Data karyawan divisi " . $divisi->divisi_nama . " yang diexport dari sistem TGM ERP");

            // Add header
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'DATA KARYAWAN DIVISI ' . strtoupper($divisi->divisi_nama))
                ->setCellValue('A2', 'Tanggal Export: ' . date('d/m/Y H:i:s'));

            // Add table headers
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A4', 'No')
                ->setCellValue('B4', 'Nama Lengkap')
                ->setCellValue('C4', 'Username')
                ->setCellValue('D4', 'NIK')
                ->setCellValue('E4', 'Email')
                ->setCellValue('F4', 'Jabatan')
                ->setCellValue('G4', 'Divisi')
                ->setCellValue('H4', 'Lokasi Cabang')
                ->setCellValue('I4', 'Jenis Kelamin')
                ->setCellValue('J4', 'Status');

            // Style header
            $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true)->setSize(14);
            $objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');

            // Add data
            $row = 5;
            foreach ($employees as $index => $employee) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, $index + 1)
                    ->setCellValue('B' . $row, $employee->nama_lengkap)
                    ->setCellValue('C' . $row, $employee->username)
                    ->setCellValue('D' . $row, $employee->nik)
                    ->setCellValue('E' . $row, $employee->email)
                    ->setCellValue('F' . $row, $employee->nama_jabatan)
                    ->setCellValue('G' . $row, $employee->divisi_nama)
                    ->setCellValue('H' . $row, $employee->nama_perusahaan)
                    ->setCellValue('I' . $row, $employee->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan')
                    ->setCellValue('J' . $row, $employee->status_aktif == 1 ? 'Aktif' : 'Nonaktif');
                $row++;
            }

            // Auto size columns
            foreach (range('A', 'J') as $col) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            }

            // Set active sheet index to the first sheet
            $objPHPExcel->setActiveSheetIndex(0);

            // Redirect output to client browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="karyawan_divisi_' . $divisi->divisi_nama . '_' . date('Y-m-d') . '.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;

        } catch (Exception $e) {
            show_error('Export error: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Update employee data via AJAX
     */
    public function update_karyawan_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $id = (int) $this->input->post('karyawan_id', true);

        // Validation
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');

        // Check NIK uniqueness
        $current_nik = $this->db->select('nik')->where('karyawan_id', $id)->get('tbl_karyawan')->row()->nik;
        if ($this->input->post('nik', true) != $current_nik) {
            $this->form_validation->set_rules('nik', 'NIK', 'required|is_unique[tbl_karyawan.nik]');
        }

        // Check Email uniqueness
        $current_email = $this->db->select('email')->where('karyawan_id', $id)->get('tbl_karyawan')->row()->email;
        if ($this->input->post('email', true) != $current_email) {
            $this->form_validation->set_rules('email', 'Email', 'valid_email|is_unique[tbl_karyawan.email]');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => false, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'nama_lengkap' => $this->input->post('nama_lengkap', true),
            'nik' => $this->input->post('nik', true),
            'jenis_kelamin' => $this->input->post('jenis_kelamin', true),
            'tempat_lahir' => $this->input->post('tempat_lahir', true),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', true),
            'alamat' => $this->input->post('alamat', true),
            'telepon' => $this->input->post('telepon', true),
            'email' => $this->input->post('email', true),
            'tanggal_masuk' => $this->input->post('tanggal_masuk', true),
            'status_karyawan' => $this->input->post('status_karyawan', true),
            'jenis_gaji' => $this->input->post('jenis_gaji', true) ?: 'Bulanan',
            'jabatan_id' => (int) $this->input->post('jabatan_id', true),
            'divisi_id' => (int) $this->input->post('divisi_id', true),
            'perusahaan_id' => (int) $this->input->post('perusahaan_id', true),
            'pin_produksi' => $this->input->post('pin_produksi', true),
            'transport_aktif' => (int) ($this->input->post('transport_aktif', true) ?? 0),
        ];

        // Check PIN Uniqueness within same branch
        if (!empty($data['pin_produksi'])) {
            $this->db->where('pin_produksi', $data['pin_produksi']);
            $this->db->where('perusahaan_id', $data['perusahaan_id']);
            $this->db->where('karyawan_id !=', $id);
            $exists = $this->db->get('tbl_karyawan')->row();
            if ($exists) {
                echo json_encode(['status' => false, 'message' => 'PIN Produksi sudah digunakan oleh ' . $exists->nama_lengkap . ' di cabang ini!']);
                return;
            }
        }

        // Handle File Upload (Foto Profil)
        if (!empty($_FILES['foto']['name'])) {
            $upload_path = FCPATH . 'assets/images/karyawan/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config = [];
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|webp|gif|JPG|JPEG|PNG|WEBP|GIF';
            $config['max_size'] = 10240; // 10MB
            $config['file_name'] = 'karyawan_' . time() . '_' . uniqid();

            $this->upload->initialize($config);

            if ($this->upload->do_upload('foto')) {
                $upload_data = $this->upload->data();
                $data['foto'] = $upload_data['file_name'];

                // Delete old photo
                $old_data = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $id])->row();
                if ($old_data && !empty($old_data->foto) && !in_array($old_data->foto, ['default.png', 'user.png']) && file_exists($upload_path . $old_data->foto)) {
                    @unlink($upload_path . $old_data->foto);
                }
            } else {
                echo json_encode(['status' => false, 'message' => 'Gagal upload foto: ' . strip_tags($this->upload->display_errors())]);
                return;
            }
        }

        // Handle Document Uploads
        $dokumen_types = [
            'dokumen_ktp' => 'ktp',
            'dokumen_kk' => 'kk',
            'dokumen_npwp' => 'npwp',
            'dokumen_ijazah' => 'ijazah',
            'dokumen_cv' => 'cv',
            'dokumen_kontrak' => 'kontrak',
            'dokumen_pernyataan' => 'pernyataan',
            'dokumen_form_data' => 'form_data'
        ];

        $doc_upload_path = FCPATH . 'assets/documents/karyawan/';
        if (!is_dir($doc_upload_path)) {
            mkdir($doc_upload_path, 0777, true);
        }

        $doc_config = [];
        $doc_config['upload_path'] = $doc_upload_path;
        $doc_config['allowed_types'] = 'pdf|jpg|jpeg|png|doc|docx|PDF|JPG|JPEG|PNG|DOC|DOCX';
        $doc_config['max_size'] = 10240; // 10MB
        $doc_config['encrypt_name'] = TRUE;

        $this->upload->initialize($doc_config);

        $upload_errors = [];

        foreach ($dokumen_types as $input_name => $db_type) {
            if (!empty($_FILES[$input_name]['name'])) {
                $this->upload->initialize($doc_config);
                if ($this->upload->do_upload($input_name)) {
                    $upload_data = $this->upload->data();

                    // Check if document already exists for this type
                    $existing_doc = $this->db->get_where('tbl_karyawan_dokumen', [
                        'karyawan_id' => $id,
                        'jenis_dokumen' => $db_type
                    ])->row();

                    if ($existing_doc) {
                        // Delete old file
                        if (!empty($existing_doc->file_path) && file_exists($doc_upload_path . $existing_doc->file_path)) {
                            @unlink($doc_upload_path . $existing_doc->file_path);
                        }
                        // Update existing record
                        $this->db->where('id', $existing_doc->id);
                        $this->db->update('tbl_karyawan_dokumen', [
                            'file_path' => $upload_data['file_name'],
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    } else {
                        // Insert new record
                        $doc_data = [
                            'karyawan_id' => $id,
                            'jenis_dokumen' => $db_type,
                            'file_path' => $upload_data['file_name']
                        ];
                        $this->db->insert('tbl_karyawan_dokumen', $doc_data);
                    }

                } else {
                    $upload_errors[] = "Gagal mengunggah $db_type: " . strip_tags($this->upload->display_errors());
                }
            }
        }

        $this->db->where('karyawan_id', $id);
        $update = $this->db->update('tbl_karyawan', $data);

        if ($update) {
            $response = [
                'status' => true,
                'message' => 'Data berhasil diperbarui',
                'stats' => $this->_get_karyawan_stats()
            ];
            if (!empty($upload_errors)) {
                $response['message'] .= '. Peringatan: ' . implode(', ', $upload_errors);
            }
            echo json_encode($response);
        } else {
            echo json_encode(['status' => false, 'message' => 'Gagal memperbarui database']);
        }
    }

    /**
     * Get Employee Report & Score
     */
    public function get_karyawan_report_json($id)
    {
        // Get Employee Data
        $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $id])->row();

        if (!$karyawan) {
            echo json_encode(['success' => false, 'message' => 'Data karyawan tidak ditemukan']);
            return;
        }

        // Define Period (Current Year)
        $year = date('Y');

        // Count Stats
        // 1. Telat: Hadir AND jam_masuk > 08:00:00
        $telat = $this->db->where('karyawan_id', $id)
            ->where('YEAR(tanggal)', $year)
            ->where('status', 'Hadir')
            ->where('TIME(jam_masuk) >', '08:00:00')
            ->count_all_results('absensi');

        // 2. Sakit
        $sakit = $this->db->where('karyawan_id', $id)
            ->where('YEAR(tanggal)', $year)
            ->where('status', 'Sakit')
            ->count_all_results('absensi');

        // 3. Alpha (Implicit + Explicit)
        // Explicit Alpha
        // $alpha_explicit = $this->db->where('karyawan_id', $id)
        //     ->where('YEAR(tanggal)', $year)
        //     ->where('status', 'Alpha')
        //     ->count_all_results('absensi');

        // Hadir (Total)
        $hadir = $this->db->where('karyawan_id', $id)
            ->where('YEAR(tanggal)', $year)
            ->where('status', 'Hadir')
            ->count_all_results('absensi');

        // Izin (Total - including Cuti if stored as Izin)
        $izin_all = $this->db->where('karyawan_id', $id)
            ->where('YEAR(tanggal)', $year)
            ->where('status', 'Izin')
            ->count_all_results('absensi');

        // Sakit (Total)
        $sakit_total = $this->db->where('karyawan_id', $id)
            ->where('YEAR(tanggal)', $year)
            ->where('status', 'Sakit')
            ->count_all_results('absensi');

        // Cuti (Total - stored as Cuti or in Izin)
        $this->db->where('karyawan_id', $id);
        $this->db->where('YEAR(tanggal)', $year);
        $this->db->group_start();
        $this->db->where_in('status', ['Cuti', 'Cuti Keluarga', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Menikah']);
        $this->db->or_group_start();
        $this->db->where('status', 'Izin');
        $this->db->like('keterangan', 'cuti');
        $this->db->group_end();
        $this->db->group_end();
        $cuti_total = $this->db->count_all_results('absensi');

        // Recorded Days
        $total_recorded = $this->db->where('karyawan_id', $id)
            ->where('YEAR(tanggal)', $year)
            ->where_in('status', ['Hadir', 'Sakit', 'Izin', 'Cuti', 'Cuti Keluarga', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Menikah'])
            ->count_all_results('absensi');

        // Calculate Working Days (Mon-Sat) for Current Year up to Today
        $start_date = new DateTime("$year-01-01");
        $end_date = new DateTime(); // Today
        // If report year is past year, end_date should be Dec 31
        if ($year < date('Y')) {
            $end_date = new DateTime("$year-12-31");
        }

        // Get Holidays for this year
        $holidays = $this->db->select('tanggal')
            ->where('YEAR(tanggal)', $year)
            ->get('hari_libur')
            ->result_array();

        $holiday_dates = array_column($holidays, 'tanggal');

        $working_days = 0;
        $period = new DatePeriod($start_date, new DateInterval('P1D'), $end_date->modify('+1 day'));
        foreach ($period as $dt) {
            $isHoliday = in_array($dt->format('Y-m-d'), $holiday_dates);

            if ($dt->format('N') <= 6 && !$isHoliday) { // Mon (1) - Sat (6) AND Not Holiday
                $working_days++;
            }
        }


        $alpha = max(0, $working_days - $total_recorded);

        // 4. Cuti (Status 'Izin' or 'Cuti' with 'cuti' in remarks, or specific Cuti types)
        $this->db->where('karyawan_id', $id);
        $this->db->where('YEAR(tanggal)', $year);
        $this->db->group_start();
        $this->db->where_in('status', ['Cuti', 'Cuti Keluarga', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Menikah']);
        $this->db->or_group_start();
        $this->db->where('status', 'Izin');
        $this->db->like('keterangan', 'cuti');
        $this->db->group_end();
        $this->db->group_end();
        $cuti = $this->db->count_all_results('absensi');

        // 5. Izin (Status 'Izin' AND NOT Cuti)
        $this->db->where('karyawan_id', $id);
        $this->db->where('YEAR(tanggal)', $year);
        $this->db->where('status', 'Izin');
        $this->db->not_like('keterangan', 'cuti');
        $izin = $this->db->count_all_results('absensi');

        // 6. Hadir (Status 'Hadir')
        $hadir = $this->db->where('karyawan_id', $id)
            ->where('YEAR(tanggal)', $year)
            ->where('status', 'Hadir')
            ->count_all_results('absensi');

        // Scoring Formula
        // Initial Score: 100%
        // Deductions:
        // - Telat: 2%
        // - Alpha: 5%
        // - Izin: 1%
        // - Sakit: 1%

        $deduction_telat = $telat * 2;
        $deduction_alpha = $alpha * 5;
        $deduction_izin = $izin * 1; // Explicit permissions only
        $deduction_sakit = $sakit * 1;

        $total_deduction = $deduction_telat + $deduction_alpha + $deduction_izin + $deduction_sakit;
        $score = max(0, 100 - $total_deduction);

        // Determine Category
        if ($score >= 95)
            $category = 'Sangat Baik';
        elseif ($score >= 85)
            $category = 'Baik';
        elseif ($score >= 75)
            $category = 'Cukup';
        elseif ($score >= 65)
            $category = 'Kurang';
        else
            $category = 'Sangat Kurang';

        $data = [
            'karyawan_id' => $karyawan->karyawan_id,
            'nama_lengkap' => $karyawan->nama_lengkap,
            'tahun' => $year,
            'stats' => [
                'telat' => $telat,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'cuti' => $cuti,
                'hadir' => $hadir
            ],
            'score' => $score,
            'category' => $category
        ];

        echo json_encode(['success' => true, 'data' => $data]);
    }

    /**
     * Export Payroll to PDF (Print View)
     */
    public function export_penggajian_pdf()
    {
        $bulan = $this->input->get('bulan', true) ? $this->input->get('bulan', true) : date('Y-m');
        $perusahaan_id = $this->input->get('perusahaan_id', true);
        $data['salary_data'] = $this->get_salary_data($bulan, '', '', $perusahaan_id);
        $data['bulan'] = $bulan;
        $active_pr = $perusahaan_id ?: $this->session->userdata('perusahaan_id');
        $data['perusahaan'] = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $active_pr])->row();

        $this->load->view('admin/karyawan/cetak_penggajian', $data);
    }

    /**
     * Print Salary Slip (All)
     */
    public function slip_gaji()
    {
        $bulan = $this->input->get('bulan', true) ? $this->input->get('bulan', true) : date('Y-m');
        $perusahaan_id = $this->input->get('perusahaan_id', true);
        $data['salary_data'] = $this->get_salary_data($bulan, '', '', $perusahaan_id);
        $data['bulan'] = $bulan;
        $active_pr = $perusahaan_id ?: $this->session->userdata('perusahaan_id');
        $data['perusahaan'] = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $active_pr])->row();

        $this->load->view('admin/karyawan/slip_gaji_all', $data);
    }

    /**
     * Print Salary Slip (Single)
     */
    public function slip_gaji_single($id)
    {
        $bulan = $this->input->get('bulan', true) ? $this->input->get('bulan', true) : date('Y-m');
        $data_salary = $this->get_salary_data($bulan, $id);

        if (empty($data_salary)) {
            show_error('Data gaji tidak ditemukan', 404);
            return;
        }

        $data['slip'] = $data_salary[0];
        $data['bulan'] = $bulan;
        $data['payroll_range'] = $this->_get_payroll_range($bulan);
        $data['perusahaan'] = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $this->session->userdata('perusahaan_id')])->row();

        $this->load->view('admin/karyawan/slip_gaji_single', $data);
    }

    /**
     * Export Single Salary Slip to PDF
     */
    public function export_slip_single_pdf($id)
    {
        $bulan = $this->input->get('bulan', true) ? trim($this->input->get('bulan', true)) : date('Y-m');
        $token = $this->input->get('token', true);

        // Security check for public access
        $expected_token = md5(trim($id) . '-' . $bulan . '-tgm_slip_secret');
        if ($token !== $expected_token) {
            show_error('Akses tidak sah. Token tidak valid.', 403);
            return;
        }

        $data_salary = $this->get_salary_data($bulan, $id);

        if (empty($data_salary)) {
            show_error('Data gaji tidak ditemukan', 404);
            return;
        }

        $data['slip'] = $data_salary[0];
        $data['bulan'] = $bulan;

        // Fetch perusahaan from data instead of session to allow public access
        $emp_perusahaan_id = $data['slip']['perusahaan_id'];
        $data['perusahaan'] = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $emp_perusahaan_id])->row();

        // Load view directly (this will trigger window.print() in the browser)
        // No mode set means it will print automatically
        $this->load->view('admin/karyawan/slip_gaji_single', $data);
    }



    public function save_gaji_manual()
    {
        $karyawan_id = $this->input->post('karyawan_id', true);
        $bulan = $this->input->post('bulan', true);

        $data = [
            'karyawan_id' => $karyawan_id,
            'bulan' => $bulan,
            'gaji_pokok' => $this->input->post('gaji_pokok', true),
            'tunjangan_jabatan' => $this->input->post('tunjangan_jabatan', true),
            'tunjangan_transportasi' => $this->input->post('tunjangan_transportasi', true),
            'bonus_kinerja' => $this->input->post('bonus_kinerja', true),
            'bonus_kehadiran' => $this->input->post('bonus_kehadiran', true),
            'uang_lembur' => $this->input->post('uang_lembur', true),
            'biaya_makan_lembur' => $this->input->post('biaya_makan_lembur', true) ? $this->input->post('biaya_makan_lembur', true) : 0,
            'reward' => $this->input->post('reward', true) ? $this->input->post('reward', true) : 0,
            'bpjs' => $this->input->post('bpjs', true) ? $this->input->post('bpjs', true) : 0,
            'tunjangan_extra' => $this->input->post('tunjangan_extra', true) ? $this->input->post('tunjangan_extra', true) : 0,
            'tunjangan_kinerja_manual' => $this->input->post('tunjangan_kinerja_manual', true) ? $this->input->post('tunjangan_kinerja_manual', true) : 0,
            'potongan_terlambat' => $this->input->post('potongan_terlambat', true) ? $this->input->post('potongan_terlambat', true) : 0,
            'potongan_alpha' => $this->input->post('potongan_alpha', true),
            'kasbon' => $this->input->post('kasbon', true) ? $this->input->post('kasbon', true) : 0,
            'pinjaman' => $this->input->post('pinjaman', true) ? $this->input->post('pinjaman', true) : 0,
            'total_gaji' => $this->input->post('total_gaji', true),
            'keterangan' => $this->input->post('keterangan', true),
        ];

        // Check if exists
        $exists = $this->db->get_where('tbl_gaji_manual', ['karyawan_id' => $karyawan_id, 'bulan' => $bulan])->row();

        if ($exists) {
            $this->db->where('id', $exists->id);
            $res = $this->db->update('tbl_gaji_manual', $data);
        } else {
            $res = $this->db->insert('tbl_gaji_manual', $data);
        }

        echo json_encode(['success' => $res]);
    }

    public function reset_gaji_manual()
    {
        $karyawan_id = $this->input->post('karyawan_id', true);
        $bulan = $this->input->post('bulan', true);

        $this->db->where('karyawan_id', $karyawan_id);
        $this->db->where('bulan', $bulan);
        $res = $this->db->delete('tbl_gaji_manual');

        echo json_encode(['success' => $res]);
    }

    // --- AJAX ---
    public function get_karyawan_by_perusahaan_ajax()
    {
        $id = $this->input->post('perusahaan_id', true) ? $this->input->post('perusahaan_id', true) : $this->input->post('id', true);

        $this->db->select('k.nama_lengkap, j.nama_jabatan, k.jenis_kelamin');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->where('k.perusahaan_id', $id);

        // Hide Owner
        $this->db->where('k.divisi_id !=', 17);

        $this->db->order_by('k.nama_lengkap', 'asc');

        $data = $this->db->get()->result();

        echo json_encode($data);
    }

    public function update_payroll_settings()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $karyawan_id = $this->input->post('karyawan_id', true);
        $gaji_pokok = $this->input->post('gaji_pokok', true);
        $nominal_lembur = $this->input->post('nominal_lembur', true);
        $tunjangan_jabatan = $this->input->post('tunjangan_jabatan', true);
        $jenis_gaji = $this->input->post('jenis_gaji', true);

        $this->form_validation->set_rules('karyawan_id', 'ID Karyawan', 'required|numeric');
        $this->form_validation->set_rules('gaji_pokok', 'Gaji Pokok', 'required|numeric');
        $this->form_validation->set_rules('nominal_lembur', 'Nominal Lembur', 'required|numeric');
        $this->form_validation->set_rules('tunjangan_jabatan', 'Tunjangan Jabatan', 'required|numeric');
        $this->form_validation->set_rules('jenis_gaji', 'Jenis Gaji', 'required|in_list[Bulanan,Harian]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors()
            ]);
            return;
        }

        $data = [
            'gaji_pokok' => $gaji_pokok,
            'nominal_lembur' => $nominal_lembur,
            'tunjangan_jabatan' => $tunjangan_jabatan,
            'jenis_gaji' => $jenis_gaji,
            'transport_aktif' => $this->input->post('transport_aktif', true) ?? 1
        ];

        $this->db->where('karyawan_id', $karyawan_id);
        $update = $this->db->update('tbl_karyawan', $data);

        if ($update) {
            echo json_encode([
                'success' => true,
                'message' => 'Pengaturan payroll berhasil diperbarui'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal memperbarui data'
            ]);
        }
    }

    public function pengaturan_lembur()
    {
        $extra_data = [
            'configs' => $this->db->get('tbl_lembur_config')->result(),
            'jam_kerja' => $this->db->order_by('urutan', 'asc')->get('tbl_jam_kerja')->result(),
            'jam_configs' => $this->db->order_by('hari', 'asc')->order_by('jam_mulai', 'asc')->get('tbl_lembur_jam_config')->result(),
            'kedisiplinan' => $this->db->get('tbl_pengaturan_kedisiplinan')->row()
        ];
        $data = $this->_prepare_view_data('Pengaturan Kebijakan Lembur & Kedisiplinan', $extra_data);
        $this->load_admin_view('admin/karyawan/pengaturan_lembur', $data);
    }

    public function update_jam_lembur_ajax()
    {
        $id = $this->input->post('id', true);
        $data = [
            'hari' => $this->input->post('hari', true),
            'keterangan' => $this->input->post('keterangan', true),
            'jam_mulai' => $this->input->post('jam_mulai', true),
            'jam_selesai' => $this->input->post('jam_selesai', true),
            'total_jam' => $this->input->post('total_jam', true),
            'is_wajib' => $this->input->post('is_wajib', true) ? 1 : 0
        ];

        if ($id) {
            $this->db->where('id', $id);
            $res = $this->db->update('tbl_lembur_jam_config', $data);
            $msg = 'Konfigurasi jam lembur berhasil diperbarui';
        } else {
            $res = $this->db->insert('tbl_lembur_jam_config', $data);
            $msg = 'Sesi lembur baru berhasil ditambahkan';
        }

        if ($res) {
            echo json_encode(['success' => true, 'message' => $msg]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memproses data']);
        }
    }

    public function delete_jam_lembur_ajax()
    {
        $id = $this->input->post('id', true);
        if ($this->db->delete('tbl_lembur_jam_config', ['id' => $id])) {
            echo json_encode(['success' => true, 'message' => 'Sesi lembur berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus sesi']);
        }
    }

    public function update_pengaturan_lembur_ajax()
    {
        $configs = $this->input->post('config', true);
        if ($configs) {
            foreach ($configs as $key => $value) {
                $this->db->where('config_key', $key);
                $this->db->update('tbl_lembur_config', ['config_value' => $value]);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No data received']);
        }
    }

    public function update_pengaturan_kedisiplinan_ajax()
    {
        $data = [
            'potongan_telat' => $this->input->post('potongan_telat', true),
            'potongan_alpha' => $this->input->post('potongan_alpha', true),
            'potongan_izin' => $this->input->post('potongan_izin', true),
            'potongan_sakit' => $this->input->post('potongan_sakit', true),
            'potongan_pulang_cepat' => $this->input->post('potongan_pulang_cepat', true)
        ];

        $this->db->where('id', 1);
        if ($this->db->update('tbl_pengaturan_kedisiplinan', $data)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui pengaturan kedisiplinan.']);
        }
    }
    /**
     * Display activity logs of employees for a specific date
     */
    public function logs()
    {
        $tanggal = $this->input->get('tanggal', true) ?: date('Y-m-d');

        // Build query for logs
        $this->db->select('ual.*, u.username, k.nama_lengkap, k.foto, d.divisi_nama');
        $this->db->from('user_activity_log ual');
        $this->db->join('users u', 'ual.user_id = u.user_id', 'left');
        $this->db->join('tbl_karyawan k', 'u.karyawan_id = k.karyawan_id', 'left');
        $this->db->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left');
        $this->db->where('ual.timestamp >=', $tanggal . ' 00:00:00');
        $this->db->where('ual.timestamp <=', $tanggal . ' 23:59:59');
        $this->db->order_by('ual.timestamp', 'DESC');
        $this->db->limit(500); // Batasi 500 data terbaru agar tidak hang

        $logs = $this->db->get()->result();

        $stats = [
            'total_activities' => count($logs),
            'unique_users' => count(array_unique(array_column($logs, 'user_id'))),
            'most_active' => $this->_get_most_active_user($tanggal)
        ];

        $extra_data = [
            'logs' => $logs,
            'tanggal' => $tanggal,
            'stats' => $stats
        ];

        $data = $this->_prepare_view_data('Log Aktivitas Karyawan', $extra_data);
        $this->load_admin_view('admin/karyawan/logs', $data);
    }

    private function _get_most_active_user($date)
    {
        $this->db->select('u.username, COUNT(*) as activity_count');
        $this->db->from('user_activity_log');
        $this->db->join('users u', 'user_activity_log.user_id = u.user_id');
        $this->db->where('user_activity_log.timestamp >=', $date . ' 00:00:00');
        $this->db->where('user_activity_log.timestamp <=', $date . ' 23:59:59');
        $this->db->group_by('u.user_id');
        $this->db->order_by('activity_count', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    /**
     * PAYROLL ENHANCEMENTS
     * Get manual payroll adjustments for a specific employee/month
     */
    private function get_manual_payroll($karyawan_id, $bulan, $start_date = '', $end_date = '')
    {
        $bulan_date = $bulan . '-01';
        $manual = $this->db->get_where('tbl_payroll_manual', [
            'karyawan_id' => $karyawan_id,
            'bulan' => $bulan_date
        ])->row();

        if ($manual) {
            return (object) [
                'reward' => $manual->reward ?? 0,
                'bpjs' => $manual->bpjs ?? 0,
                'tunjangan_extra' => $manual->tunjangan_extra ?? 0,
                'tunjangan_kinerja_manual' => $manual->tunjangan_kinerja_manual ?? 0,
                'kasbon' => $manual->kasbon ?? 0,
                'pinjaman' => $manual->pinjaman ?? 0
            ];
        }

        return (object) [
            'reward' => 0,
            'bpjs' => 0,
            'tunjangan_extra' => 0,
            'tunjangan_kinerja_manual' => 0,
            'kasbon' => 0,
            'pinjaman' => 0
        ];
    }

    /**
     * Save manual payroll adjustments (AJAX endpoint)
     */
    public function save_manual_payroll()
    {
        $karyawan_id = $this->input->post('karyawan_id', true);
        $bulan = $this->input->post('bulan', true); // Format: YYYY-MM
        $bulan_date = $bulan . '-01';

        $data = [
            'karyawan_id' => $karyawan_id,
            'bulan' => $bulan_date,
            'reward' => $this->input->post('reward', true) ?? 0,
            'bpjs' => $this->input->post('bpjs', true) ?? 0,
            'tunjangan_extra' => $this->input->post('tunjangan_extra', true) ?? 0,
            'tunjangan_kinerja_manual' => $this->input->post('tunjangan_kinerja_manual', true) ?? 0,
            'kasbon' => $this->input->post('kasbon', true) ?? 0,
            'pinjaman' => $this->input->post('pinjaman', true) ?? 0,
            'custom_in1_nama' => $this->input->post('custom_in1_nama', true) ?? '',
            'custom_in1_nom' => $this->input->post('custom_in1_nom', true) ?? 0,
            'custom_in2_nama' => $this->input->post('custom_in2_nama', true) ?? '',
            'custom_in2_nom' => $this->input->post('custom_in2_nom', true) ?? 0,
            'custom_in3_nama' => $this->input->post('custom_in3_nama', true) ?? '',
            'custom_in3_nom' => $this->input->post('custom_in3_nom', true) ?? 0,
            'custom_out1_nama' => $this->input->post('custom_out1_nama', true) ?? '',
            'custom_out1_nom' => $this->input->post('custom_out1_nom', true) ?? 0,
            'custom_out2_nama' => $this->input->post('custom_out2_nama', true) ?? '',
            'custom_out2_nom' => $this->input->post('custom_out2_nom', true) ?? 0,
            'custom_out3_nama' => $this->input->post('custom_out3_nama', true) ?? '',
            'custom_out3_nom' => $this->input->post('custom_out3_nom', true) ?? 0,
        ];

        // UPSERT logic
        $existing = $this->db->get_where('tbl_payroll_manual', [
            'karyawan_id' => $karyawan_id,
            'bulan' => $bulan_date
        ])->row();

        if ($existing) {
            $this->db->where('karyawan_id', $karyawan_id);
            $this->db->where('bulan', $bulan_date);
            $this->db->update('tbl_payroll_manual', $data);
        } else {
            $this->db->insert('tbl_payroll_manual', $data);
        }

        echo json_encode(['success' => true, 'message' => 'Data manual berhasil disimpan']);
    }

    /**
     * Record lateness penalty (AJAX endpoint)
     */
    public function record_lateness()
    {
        $karyawan_id = $this->input->post('karyawan_id', true);
        $tanggal = $this->input->post('tanggal', true);
        $jam_terlambat = $this->input->post('jam_terlambat', true); // in hours (decimal)
        $keterangan = $this->input->post('keterangan', true) ?? '';

        // Get employee's basic salary for calculation
        $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $karyawan_id])->row();
        if (!$karyawan) {
            echo json_encode(['success' => false, 'message' => 'Karyawan tidak ditemukan']);
            return;
        }

        // Calculate penalty based on salary type
        // Monthly: (Gaji pokok ÷ 26 days ÷ 8 hours) × jam terlambat
        // Daily: (Gaji pokok ÷ 8 hours) × jam terlambat
        $gaji_pokok = $karyawan->gaji_pokok ?? 0;
        if ($karyawan->jenis_gaji == 'Harian') {
            $nominal_potongan = ($gaji_pokok / 8) * $jam_terlambat;
        } else {
            $nominal_potongan = ($gaji_pokok / 26 / 8) * $jam_terlambat;
        }
        $nominal_potongan = round($nominal_potongan);

        $data = [
            'karyawan_id' => $karyawan_id,
            'tanggal' => $tanggal,
            'jam_terlambat' => $jam_terlambat,
            'nominal_potongan' => $nominal_potongan,
            'keterangan' => $keterangan
        ];

        $this->db->insert('tbl_potongan_terlambat', $data);

        echo json_encode([
            'success' => true,
            'message' => 'Keterlambatan berhasil dicatat',
            'nominal_potongan' => $nominal_potongan
        ]);
    }

    /**
     * Get total lateness penalty for a specific month
     */
    private function get_lateness_penalty($karyawan_id, $bulan, $start_date = '', $end_date = '')
    {
        $this->db->select_sum('nominal_potongan');
        $this->db->where('karyawan_id', $karyawan_id);
        if ($start_date && $end_date) {
            $this->db->where('tanggal >=', $start_date);
            $this->db->where('tanggal <=', $end_date);
        } else {
            $this->db->where('DATE_FORMAT(tanggal, "%Y-%m") =', $bulan);
        }
        $result = $this->db->get('tbl_potongan_terlambat')->row();

        return $result->nominal_potongan ?? 0;
    }

    /**
     * Get meal allowance from overtime records
     */
    private function get_meal_allowance($karyawan_id, $bulan, $start_date = '', $end_date = '')
    {
        $this->db->select_sum('biaya_makan');
        $this->db->where('karyawan_id', $karyawan_id);
        $this->db->where('status', 'disetujui');
        if ($start_date && $end_date) {
            $this->db->where('tanggal >=', $start_date);
            $this->db->where('tanggal <=', $end_date);
        } else {
            $this->db->where('DATE_FORMAT(tanggal, "%Y-%m") =', $bulan);
        }
        $result = $this->db->get('tbl_lembur')->row();

        return $result->biaya_makan ?? 0;
    }

    public function check_pin_duplicate_ajax()
    {
        $pin = $this->input->get('pin', true);
        $pid = $this->input->get('perusahaan_id', true);
        $karyawan_id = $this->input->get('karyawan_id', true);

        if (empty($pin)) {
            echo json_encode(['status' => true]);
            return;
        }

        $this->db->where('pin_produksi', $pin);
        $this->db->where('perusahaan_id', $pid);
        if ($karyawan_id) {
            $this->db->where('karyawan_id !=', $karyawan_id);
        }
        $exists = $this->db->get('tbl_karyawan')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'PIN ' . $pin . ' sudah digunakan oleh ' . $exists->nama_lengkap
            ]);
        } else {
            echo json_encode(['status' => true]);
        }
    }

    /**
     * Generate Random PIN for all employees (Batch)
     * Access via: site_url('karyawan/generate_batch_pin_random')
     */
    public function generate_batch_pin_random()
    {
        // Simple security: check if user is logged in
        if (!$this->session->userdata('user_id')) {
            die("Silakan login terlebih dahulu.");
        }

        $this->db->where("nik NOT LIKE 'STATION%'");
        $this->db->where("nik NOT LIKE 'TERM%'");
        $this->db->where('nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('nama_lengkap NOT LIKE', '%OPERATOR%');
        $this->db->where('nama_lengkap NOT LIKE', '%DEPT%');
        $this->db->where('nama_lengkap NOT LIKE', '%DTG%');
        $this->db->where('nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('nama_lengkap NOT LIKE', '%WORK CENTER%');
        $this->db->where('nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('nama_lengkap NOT LIKE', '%A3+%');
        $all_karyawan = $this->db->get('tbl_karyawan')->result();
        $updated_count = 0;

        foreach ($all_karyawan as $emp) {
            $is_unique = false;
            $new_pin = "";
            $attempts = 0;

            // Generate unique PIN for each branch
            while (!$is_unique && $attempts < 20) {
                $new_pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

                $this->db->where('pin_produksi', $new_pin);
                $this->db->where('perusahaan_id', $emp->perusahaan_id);
                $this->db->where('karyawan_id !=', $emp->karyawan_id);
                $exists = $this->db->get('tbl_karyawan')->row();

                if (!$exists) {
                    $is_unique = true;
                }
                $attempts++;
            }

            if ($is_unique) {
                $this->db->where('karyawan_id', $emp->karyawan_id);
                $this->db->update('tbl_karyawan', ['pin_produksi' => $new_pin]);
                $updated_count++;
            }
        }

        echo "<h2>Sistem TGM ERP - Generator PIN</h2>";
        echo "Berhasil mengupdate <b>" . $updated_count . "</b> PIN karyawan secara acak (4 digit).";
        echo "<br><br><a href='" . site_url('karyawan/karyawan') . "'>Kembali ke Data Karyawan</a>";
    }

    public function get_pengajuan_kontrak_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $karyawan_id = (int) $this->input->post('karyawan_id', true);
        
        $pengajuan = $this->db->where('karyawan_id', $karyawan_id)
            ->where('status', 'Pending')
            ->order_by('created_at', 'DESC')
            ->limit(1)
            ->get('tbl_pengajuan_kontrak')
            ->row();

        if ($pengajuan) {
            echo json_encode(['success' => true, 'data' => $pengajuan]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tidak ada pengajuan perpanjangan kontrak berstatus Pending untuk karyawan ini.']);
        }
    }

    public function aksi_proses_kontrak()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $karyawan_id = (int) $this->input->post('karyawan_id', true);
        $status_keputusan = $this->input->post('status', true);
        $admin_id = $this->session->userdata('id');

        if (!in_array($status_keputusan, ['Approved', 'Rejected'])) {
            echo json_encode(['success' => false, 'message' => 'Status keputusan tidak valid']);
            return;
        }

        $pengajuan = $this->db->where('karyawan_id', $karyawan_id)
            ->where('status', 'Pending')
            ->order_by('created_at', 'DESC')
            ->limit(1)
            ->get('tbl_pengajuan_kontrak')
            ->row();

        if (!$pengajuan) {
            echo json_encode(['success' => false, 'message' => 'Pengajuan perpanjangan kontrak tidak ditemukan atau sudah diproses.']);
            return;
        }

        $this->db->trans_start();

        $this->db->where('id', $pengajuan->id);
        $this->db->update('tbl_pengajuan_kontrak', [
            'status' => $status_keputusan,
            'responded_at' => date('Y-m-d H:i:s'),
            'responded_by' => $admin_id
        ]);

        if ($status_keputusan === 'Approved') {
            $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $karyawan_id])->row();
            if ($karyawan) {
                $durasi_bulan = (int) $pengajuan->durasi_perpanjangan;
                $tgl_akhir_lama = $karyawan->akhir_kontrak;
                $tgl_sekarang = date('Y-m-d');
                
                if (!empty($tgl_akhir_lama) && $tgl_akhir_lama >= $tgl_sekarang) {
                    $tgl_akhir_baru = date('Y-m-d', strtotime($tgl_akhir_lama . " + {$durasi_bulan} months"));
                } else {
                    $tgl_akhir_baru = date('Y-m-d', strtotime($tgl_sekarang . " + {$durasi_bulan} months"));
                }
                
                $this->db->where('karyawan_id', $karyawan_id);
                $this->db->update('tbl_karyawan', [
                    'akhir_kontrak' => $tgl_akhir_baru
                ]);
            }
        }

        $action_log = $status_keputusan === 'Approved' ? 'Approve_Kontrak' : 'Reject_Kontrak';
        $user_action = $this->db->get_where('users', ['user_id' => $admin_id])->row();
        $username = $user_action ? $user_action->username : 'System';

        $this->db->insert('system_audit_logs', [
            'user_id' => $admin_id,
            'username' => $username,
            'action_type' => $action_log,
            'description' => "Proses kontrak Karyawan ID: {$karyawan_id} menjadi {$status_keputusan}",
            'reference_id' => $pengajuan->id,
            'ip_address' => $this->input->ip_address()
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal memproses keputusan kontrak di database.']);
        } else {
            $msg = $status_keputusan === 'Approved' ? 'Pengajuan perpanjangan kontrak disetujui & masa kontrak diperbarui!' : 'Pengajuan perpanjangan kontrak berhasil ditolak.';
            echo json_encode(['success' => true, 'message' => $msg]);
        }
    }

    public function export_karyawan_excel()
    {
        // Check if user is authorized
        $user_level = $this->session->userdata('level');
        $nama_level = (string)$this->session->userdata('nama_level');
        $username = (string)$this->session->userdata('username');

        $is_owner = ($user_level == 17 || strpos($nama_level, 'Owner') !== false);
        $is_admin = ($user_level == 1 || strpos($nama_level, 'Admin') !== false);
        $is_sdm = ($user_level == 21 || strpos($nama_level, 'HR') !== false);
        $is_developer = ($username === 'nizarsuryadi');

        if (!$is_owner && !$is_admin && !$is_sdm && !$is_developer) {
            show_error('Access denied', 403);
            return;
        }

        try {
            // Turn off error reporting to prevent warnings/notices from breaking the Excel file
            error_reporting(0);
            
            // Get search parameters
            $search = trim($this->input->get('search', true));
            $gender = trim($this->input->get('gender', true));
            $status = trim($this->input->get('status', true));
            $jabatan = trim($this->input->get('jabatan', true));
            $divisi = trim($this->input->get('divisi', true));
            $perusahaan = trim($this->input->get('perusahaan', true));

            // Build the query
            $this->db->select('k.*, j.nama_jabatan, d.divisi_nama, p.nama_perusahaan, u.username');
            $this->db->from('tbl_karyawan k');
            $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
            $this->db->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left');
            $this->db->join('tbl_perusahaan p', 'k.perusahaan_id = p.id_perusahaan', 'left');
            $this->db->join('users u', 'k.karyawan_id = u.karyawan_id', 'left');

            // Exclude Owner and Directors
            $this->db->where('k.divisi_id !=', 8);
            $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);

            // Exclude Dummy Terminal Accounts
            $this->db->where("k.nik NOT LIKE 'STATION%'");
            $this->db->where("k.nama_lengkap NOT LIKE '%Terminal%'");
            $this->db->where("k.nama_lengkap NOT LIKE '%DTF%'");
            $this->db->where("k.nama_lengkap NOT LIKE 'OPERATOR %'");

            // Apply company filter
            if (!empty($this->session->userdata('perusahaan_id')) && $this->session->userdata('perusahaan_id') != 1) {
                $this->db->where('k.perusahaan_id', $this->session->userdata('perusahaan_id'));
            }

            // Apply search filter
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('k.nama_lengkap', $search);
                $this->db->or_like('k.nik', $search);
                $this->db->or_like('k.email', $search);
                $this->db->group_end();
            }

            // Apply gender filter
            if (!empty($gender)) {
                $this->db->where('k.jenis_kelamin', $gender);
            }

            // Apply status filter
            if (!empty($status)) {
                $this->db->where('k.status_karyawan', $status);
            }

            // Apply jabatan filter
            if (!empty($jabatan)) {
                if (is_numeric($jabatan)) {
                    $this->db->where('k.jabatan_id', $jabatan);
                } else {
                    $this->db->where('j.nama_jabatan', $jabatan);
                }
            }

            // Apply divisi filter
            if (!empty($divisi)) {
                if (is_numeric($divisi)) {
                    $this->db->where('k.divisi_id', $divisi);
                } else {
                    $this->db->where('d.divisi_nama', $divisi);
                }
            }

            // Apply perusahaan filter
            if (!empty($perusahaan)) {
                if (is_numeric($perusahaan)) {
                    $this->db->where('k.perusahaan_id', $perusahaan);
                } else {
                    $this->db->where('p.nama_perusahaan', $perusahaan);
                }
            }

            // Order by: Tanggal Masuk (Oldest First) then Nama (A-Z)
            $this->db->order_by('k.tanggal_masuk', 'asc');
            $this->db->order_by('k.nama_lengkap', 'asc');

            $employees = $this->db->get()->result();

            // Load PHPExcel library
            $this->load->library('excel');

            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("TGM ERP System")
                ->setLastModifiedBy("TGM ERP System")
                ->setTitle("Data Karyawan Export")
                ->setSubject("Data Karyawan Export")
                ->setDescription("Data karyawan yang diexport dari sistem TGM ERP");

            // Add header
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'DATA KARYAWAN')
                ->setCellValue('A2', 'Tanggal Export: ' . date('d/m/Y H:i:s'));

            // Add table headers
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A4', 'No')
                ->setCellValue('B4', 'NIK')
                ->setCellValue('C4', 'Nama Lengkap')
                ->setCellValue('D4', 'Jenis Kelamin')
                ->setCellValue('E4', 'Jabatan')
                ->setCellValue('F4', 'Divisi')
                ->setCellValue('G4', 'Lokasi Cabang')
                ->setCellValue('H4', 'Status')
                ->setCellValue('I4', 'Tanggal Masuk')
                ->setCellValue('J4', 'Telepon')
                ->setCellValue('K4', 'Email')
                ->setCellValue('L4', 'Alamat');

            // Style header
            $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true)->setSize(14);
            $objPHPExcel->getActiveSheet()->getStyle('A4:L4')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:L4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');

            // Add data
            $row = 5;
            foreach ($employees as $index => $employee) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, $index + 1)
                    ->setCellValue('B' . $row, ' ' . $employee->nik)
                    ->setCellValue('C' . $row, $employee->nama_lengkap)
                    ->setCellValue('D' . $row, $employee->jenis_kelamin == 'L' ? 'Laki-laki' : ($employee->jenis_kelamin == 'P' ? 'Perempuan' : $employee->jenis_kelamin))
                    ->setCellValue('E' . $row, $employee->nama_jabatan)
                    ->setCellValue('F' . $row, $employee->divisi_nama)
                    ->setCellValue('G' . $row, $employee->nama_perusahaan)
                    ->setCellValue('H' . $row, $employee->status_karyawan)
                    ->setCellValue('I' . $row, $employee->tanggal_masuk)
                    ->setCellValue('J' . $row, ' ' . $employee->telepon)
                    ->setCellValue('K' . $row, $employee->email)
                    ->setCellValue('L' . $row, $employee->alamat);
                $row++;
            }

            // Auto size columns
            foreach (range('A', 'L') as $col) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            }

            // Set active sheet index to the first sheet
            $objPHPExcel->setActiveSheetIndex(0);

            $temp_file = FCPATH . 'assets/temp_export_' . time() . '.xlsx';
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save($temp_file);

            // Clean output buffer before sending headers
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Redirect output to client browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="karyawan_export_' . date('Ymd_His') . '.xlsx"');
            header('Cache-Control: max-age=0');
            header('Content-Length: ' . filesize($temp_file));

            readfile($temp_file);
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }
            exit;

        } catch (Exception $e) {
            show_error('Export error: ' . $e->getMessage(), 500);
        }
    }

    public function export_karyawan_pdf()
    {
        $user_level = $this->session->userdata('level');
        $nama_level = (string)$this->session->userdata('nama_level');
        $username = (string)$this->session->userdata('username');

        $is_owner = ($user_level == 17 || strpos($nama_level, 'Owner') !== false);
        $is_admin = ($user_level == 1 || strpos($nama_level, 'Admin') !== false);
        $is_sdm = ($user_level == 21 || strpos($nama_level, 'HR') !== false);
        $is_developer = ($username === 'nizarsuryadi');

        if (!$is_owner && !$is_admin && !$is_sdm && !$is_developer) {
            show_error('Access denied', 403);
            return;
        }

        $search = trim($this->input->get('search', true));
        $gender = trim($this->input->get('gender', true));
        $status = trim($this->input->get('status', true));
        $jabatan = trim($this->input->get('jabatan', true));
        $divisi = trim($this->input->get('divisi', true));
        $perusahaan = trim($this->input->get('perusahaan', true));

        $this->db->select('k.*, j.nama_jabatan, d.divisi_nama, p.nama_perusahaan, u.username');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left');
        $this->db->join('tbl_perusahaan p', 'k.perusahaan_id = p.id_perusahaan', 'left');
        $this->db->join('users u', 'k.karyawan_id = u.karyawan_id', 'left');
        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $this->db->where("k.nik NOT LIKE 'STATION%'");
        $this->db->where("k.nama_lengkap NOT LIKE '%Terminal%'");
        $this->db->where("k.nama_lengkap NOT LIKE '%DTF%'");
        $this->db->where("k.nama_lengkap NOT LIKE 'OPERATOR %'");

        if (!empty($this->session->userdata('perusahaan_id')) && $this->session->userdata('perusahaan_id') != 1) {
            $this->db->where('k.perusahaan_id', $this->session->userdata('perusahaan_id'));
        }
        if (!empty($search)) {
            $this->db->group_start()->like('k.nama_lengkap', $search)->or_like('k.nik', $search)->or_like('k.email', $search)->group_end();
        }
        if (!empty($gender)) $this->db->where('k.jenis_kelamin', $gender);
        if (!empty($status)) $this->db->where('k.status_karyawan', $status);
        if (!empty($jabatan)) { is_numeric($jabatan) ? $this->db->where('k.jabatan_id', $jabatan) : $this->db->where('j.nama_jabatan', $jabatan); }
        if (!empty($divisi)) { is_numeric($divisi) ? $this->db->where('k.divisi_id', $divisi) : $this->db->where('d.divisi_nama', $divisi); }
        if (!empty($perusahaan)) { is_numeric($perusahaan) ? $this->db->where('k.perusahaan_id', $perusahaan) : $this->db->where('p.nama_perusahaan', $perusahaan); }

        $this->db->order_by('k.tanggal_masuk', 'asc');
        $this->db->order_by('k.nama_lengkap', 'asc');
        
        $data['karyawan'] = $this->db->get()->result();
        $this->load->view('admin/karyawan/cetak_karyawan_pdf', $data);
    }
}
?>