<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mobile extends MY_Controller
{
    private $karyawan_id;
    private $user_id;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");

        // Load Models
        $this->load->model([
            'Absensi_model',
            'Produk_model',
            'M_gudang',
            'M_master',
            'M_transaksi',
            'M_profile',
            'User_activity_model'
        ]);

        // Set Global Vars
        $this->user_id = $this->session->userdata('id');
        $this->karyawan_id = $this->session->userdata('karyawan_id');
    }

    // --- HELPER FUNCTION ---
    private function _get_common_data($title = 'Sistem Absensi Lembur')
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $level_id = $this->session->userdata('level');

        $perusahaan_data = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row_array();

        // Standarisasi output array perusahaan agar kompatibel dg view lama
        // View lama mengakses $perusahaan (array) dan $perusahaan['nama_perusahaan'] (string di beberapa controller)
        // Disini kita kirim array objek penuh.

        return [
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => $title,
            'logo' => $perusahaan_data['logo'] ?? '',
            'perusahaan' => $perusahaan_data, // Akses view: $perusahaan['nama_perusahaan']
            'hakakses' => $this->db->get_where('tbl_divisi', ['divisi_id' => $level_id])->row_array(),
            'level' => $this->db->get_where('users', ['user_id' => $this->user_id])->row_array(),
            'userid' => $this->db->get_where('users', ['user_id' => $this->user_id])->row_array(),
        ];
    }

    public function verify_password_ajax()
    {
        if (!$this->input->is_ajax_request())
            show_404();

        $password = $this->input->post('password', true);
        if (empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Password wajib diisi']);
            return;
        }

        $user = $this->db->get_where('users', ['user_id' => $this->user_id])->row();

        if ($user && password_verify($password, $user->password)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Password salah']);
        }
    }

    // --- MAIN PAGES ---

    public function index()
    {
        $data = $this->_get_common_data();
        $tanggal = date('Y-m-d');
        $jamSekarang = date('H:i');

        $data['karyawan'] = $this->M_master->karyawan_lembur()->result();
        $data['jml'] = $this->M_master->karyawan_lembur()->num_rows();
        $data['listkaryawan'] = $this->M_master->get_karyawan()->result();

        $has_dashboard_role = $this->db->where(['role_id' => $this->session->userdata('level'), 'menu_id' => 1])->get('tbl_menu_access')->num_rows() > 0;
        $has_dashboard_user = $this->db->where(['user_id' => $this->user_id, 'menu_id' => 1])->get('tbl_user_menu_access')->num_rows() > 0;
        $data['has_desktop_access'] = $this->session->userdata('level') == 1 || $has_dashboard_role || $has_dashboard_user;

        $data['karyawan_terpilih'] = $this->db->select('k.*, d.divisi_nama, j.nama_jabatan')
            ->from('tbl_karyawan k')
            ->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left')
            ->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left')
            ->where('k.karyawan_id', $this->karyawan_id)
            ->get()->row_array();

        $data['cekabsen'] = $this->db->get_where('absensi', ['karyawan_id' => $this->karyawan_id, 'tanggal' => $tanggal])->row_array();
        $data['hasilcek'] = $this->db->get_where('absensi', ['karyawan_id' => $this->karyawan_id, 'tanggal' => $tanggal])->num_rows();

        $data['success'] = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where('tanggal', $tanggal)
            ->where('jam_masuk IS NOT NULL AND jam_masuk != "00:00:00"', null, false)
            ->where('jam_pulang IS NOT NULL AND jam_pulang != "00:00:00"', null, false)
            ->get('absensi')->num_rows();

        // Logika Boleh Absen (Ambil dari tbl_jam_kerja)
        $daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hariNama = $daysIndo[date('w')];
        $ruleJam = $this->db->get_where('tbl_jam_kerja', ['hari' => $hariNama])->row_array() ?: [];

        $isLibur = $this->db->where('tanggal', $tanggal)->get('hari_libur')->num_rows() > 0;

        // Window Operasional: Boleh absen mulai jam 05:00 pagi sampai jam 23:00
        $jamMasukLimit = '05:00';
        $isJamOperasional = ($jamSekarang >= $jamMasukLimit && $jamSekarang <= '23:00');

        $data['boleh_absen'] = !$isLibur && (!empty($ruleJam['jam_masuk']) && $ruleJam['jam_masuk'] != '00:00:00') && $isJamOperasional;

        // Cek apakah kesiangan hari ini
        $data['is_late_today'] = false;
        if (!empty($data['cekabsen']) && is_array($data['cekabsen']) && ($data['cekabsen']['status'] ?? '') == 'Hadir' && !empty($ruleJam['jam_masuk'])) {
            $batas_telat = date('H:i:s', strtotime($ruleJam['jam_masuk'] . ' +1 minute'));
            if (($data['cekabsen']['jam_masuk'] ?? '') > $batas_telat) {
                $data['is_late_today'] = true;
            }
        }

        // Karyawan Sakit/Izin Hari Ini
        $data['absen_hari_ini'] = $this->db->select('k.nama_lengkap, k.foto, a.status, a.keterangan')
            ->from('absensi a')
            ->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left')
            ->where('a.tanggal', $tanggal)
            ->where_in('a.status', ['Sakit', 'Izin', 'Alpha', 'Cuti'])
            ->get()->result();

        // Security Check: Is user still using default password?
        $user_info = $this->db->get_where('users', ['user_id' => $this->user_id])->row();
        $data['is_default_password'] = password_verify('tgmprint', $user_info->password);

        // Pengumuman Aktif
        $data['pengumuman'] = $this->db->select('p.*, u.username as pembuat')
            ->from('tbl_pengumuman p')
            ->join('users u', 'u.user_id = p.user_id', 'left')
            ->where('p.status', 1)
            ->where('p.tanggal_mulai <=', $tanggal)
            ->where('p.tanggal_selesai >=', $tanggal)
            ->order_by('p.created_at', 'DESC')
            ->get()->result();

        // Data Gamifikasi (Skor Kinerja Periode Penggajian)
        $bulan_ini = date('Y-m');
        $this->load->model('Payroll_model');
        $range = $this->Payroll_model->_get_payroll_range($bulan_ini);
        $perf_data = $this->Payroll_model->get_performance_data($bulan_ini, $this->karyawan_id, '', $range['start'], $range['end']);
        $data['performance'] = !empty($perf_data) ? $perf_data[0] : null;

        // Cek Peringatan Kontrak
        $data['kontrak_info'] = null;
        if (!empty($data['karyawan_terpilih']) && ($data['karyawan_terpilih']['status_karyawan'] ?? '') !== 'Tetap' && !empty($data['karyawan_terpilih']['akhir_kontrak'])) {
            $akhir_kontrak_str = $data['karyawan_terpilih']['akhir_kontrak'];
            $tgl_akhir = new DateTime($akhir_kontrak_str);
            $tgl_sekarang = new DateTime(date('Y-m-d'));

            // Hitung selisih hari
            $interval = $tgl_sekarang->diff($tgl_akhir);
            $sisa_hari = $interval->days;
            // Jika tanggal sudah lewat, buat sisa hari menjadi negatif
            if ($tgl_sekarang > $tgl_akhir) {
                $sisa_hari = -$sisa_hari;
            }

            // Ambil pengajuan perpanjangan kontrak terakhir
            $pengajuan_terakhir = $this->db->where('karyawan_id', $this->karyawan_id)
                ->order_by('created_at', 'DESC')
                ->limit(1)
                ->get('tbl_pengajuan_kontrak')
                ->row_array();

            $data['kontrak_info'] = [
                'akhir_kontrak' => $akhir_kontrak_str,
                'sisa_hari' => $sisa_hari,
                'pengajuan_terakhir' => $pengajuan_terakhir
            ];
        }

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/index', $data);
        $this->load->view('admin/mobile/footer', $data);
        $this->load->view('layout_admin/alert', $data);
    }

    public function absen()
    {
        $this->load->model('Payroll_model');
        $data = $this->_get_common_data();
        $tanggal = date('Y-m-d');
        $jamSekarang = date('H:i');
        $bulan_ini = date('Y-m');

        $salary_data = $this->Payroll_model->get_salary_data($bulan_ini, $this->karyawan_id);
        $data['rekap_lembur'] = !empty($salary_data) ? $salary_data[0] : null;

        $data['karyawan'] = $this->M_master->karyawan_lembur()->result();
        $data['jml'] = $this->M_master->karyawan_lembur()->num_rows();
        $data['listkaryawan'] = $this->M_master->get_karyawan()->result();
        $data['karyawan_terpilih'] = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $this->karyawan_id])->row_array();

        $data['cekabsen'] = $this->db->get_where('absensi', ['karyawan_id' => $this->karyawan_id, 'tanggal' => $tanggal])->row_array();
        $data['hasilcek'] = $this->db->get_where('absensi', ['karyawan_id' => $this->karyawan_id, 'tanggal' => $tanggal])->num_rows();

        $data['success'] = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where('tanggal', $tanggal)
            ->where('jam_masuk IS NOT NULL AND jam_masuk != "00:00:00"', null, false)
            ->where('jam_pulang IS NOT NULL AND jam_pulang != "00:00:00"', null, false)
            ->get('absensi')->num_rows();

        $data['absensi_mingguan'] = $this->db->select('tanggal, jam_masuk, jam_pulang, status, foto_masuk, foto_pulang')
            ->where('karyawan_id', $this->karyawan_id)
            ->where('tanggal >=', date('Y-m-d', strtotime('-6 days')))
            ->where('tanggal <=', $tanggal)
            ->order_by('tanggal', 'DESC')
            ->get('absensi')->result();

        $bulan_ini = date('Y-m');
        $this->db->where('karyawan_id', $this->karyawan_id);
        $this->db->where("DATE_FORMAT(tanggal, '%Y-%m') =", $bulan_ini);
        $this->db->where('status', 'Hadir');
        $this->db->join('tbl_jam_kerja jk', 'jk.urutan = (DAYOFWEEK(absensi.tanggal) + 5) % 7 + 1', 'left');
        // Kesiangan dihitung jika absen >= 08:01 (lewat 1 menit dari jam masuk)
        $this->db->select('absensi.jam_masuk, jk.jam_masuk as rule_jam_masuk');
        $this->db->where('absensi.jam_masuk >= ADDTIME(jk.jam_masuk, "00:01:00")', null, false);
        $late_records = $this->db->get('absensi')->result();
        
        $terlambat_count = count($late_records);
        $terlambat_menit = 0;
        foreach ($late_records as $lr) {
            $diff = strtotime($lr->jam_masuk) - strtotime($lr->rule_jam_masuk);
            if ($diff > 0) {
                $terlambat_menit += floor($diff / 60);
            }
        }

        $start_date = date('Y-m-01', strtotime($bulan_ini . '-01'));
        $end_date = date('Y-m-t', strtotime($start_date));

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
            if ($date > $today)
                break;

            $dayOfWeek = date('N', $current);
            $isHoliday = in_array($date, $holiday_dates);
            if ($dayOfWeek <= 6 && !$isHoliday) {
                $workingDays++;
            }
            $current = strtotime('+1 day', $current);
        }

        $records = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where('tanggal >=', $start_date)
            ->where('tanggal <=', $end_date)
            ->get('absensi')->result();

        $total_recorded = 0;
        $total_izin = 0;
        $total_sakit = 0;

        foreach ($records as $r) {
            $s = $r->status;
            $ket = strtolower($r->keterangan ?? '');
            $is_alpha_like = ($s === 'Alpha' || $s === '' || is_null($s));

            if ($s === 'Hadir') {
                $total_recorded++;
            } else if ($s === 'Izin' || ($is_alpha_like && strpos($ket, 'izin') !== false)) {
                $total_recorded++;
                $total_izin++;
            } else if ($s === 'Sakit' || ($is_alpha_like && strpos($ket, 'sakit') !== false)) {
                $total_recorded++;
                $total_sakit++;
            } else if (in_array($s, ['Cuti', 'Cuti Keluarga', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Menikah']) || ($is_alpha_like && strpos($ket, 'cuti') !== false)) {
                $total_recorded++;
            }
        }

        $calculated_alpha = max(0, $workingDays - $total_recorded);

        $data['statistik_bulan_ini'] = [
            'sakit' => $total_sakit,
            'izin' => $total_izin,
            'alpha' => $calculated_alpha,
            'terlambat' => $terlambat_count,
            'terlambat_menit' => $terlambat_menit
        ];

        // Ambil Aturan Jam Kerja dari Database
        $daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hariNama = $daysIndo[date('w')];
        $ruleJam = $this->db->get_where('tbl_jam_kerja', ['hari' => $hariNama])->row_array();

        $holiday = $this->db->where('tanggal', $tanggal)->get('hari_libur')->row_array();
        $isLibur = !empty($holiday);
        $dayOfWeek = date('w', strtotime($tanggal));
        $isMinggu = $dayOfWeek == 0;

        // Aturan jam pulang: Mengambil dari tbl_jam_kerja
        $data['jam_pulang_minimal'] = !empty($ruleJam['jam_pulang']) ? substr($ruleJam['jam_pulang'], 0, 5) : '17:00';

        // Window akses aplikasi: dibuka dari jam 05:00 pagi sampai jam 23:00 agar bisa absen pulang
        $jamMasukLimit = '05:00';
        $isJamOperasional = ($jamSekarang >= $jamMasukLimit && $jamSekarang <= '23:00');

        $data['is_libur'] = $isLibur;
        $data['holiday_name'] = $isLibur ? $holiday['keterangan'] : '';
        $data['is_minggu'] = $isMinggu;

        $data['boleh_absen'] = !$isLibur && (!empty($ruleJam['jam_masuk']) && $ruleJam['jam_masuk'] != '00:00:00') && $isJamOperasional;

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/absen', $data);
        $this->load->view('admin/mobile/footer', $data);
        $this->load->view('layout_admin/alert', $data);
    }

    public function lembur()
    {
        $data = $this->_get_common_data('Absensi Lembur');
        $tanggal = date('Y-m-d');
        $jamSekarang = date('H:i');

        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];
        $data['karyawan_terpilih'] = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $this->karyawan_id])->row_array();

        // Cek lembur aktif (sudah mulai tapi belum selesai)
        $data['lembur_aktif'] = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where('tanggal', $tanggal)
            ->where('(jam_selesai = "00:00:00" OR jam_selesai IS NULL)', null, false)
            ->get('tbl_lembur')->row_array();

        // Cek lembur selesai
        $data['lembur_selesai'] = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where('tanggal', $tanggal)
            ->where('jam_selesai !=', '00:00:00')
            ->where('jam_selesai IS NOT NULL', null, false)
            ->order_by('jam_mulai', 'ASC')
            ->get('tbl_lembur')->result_array();

        // Ambil Aturan Jam Kerja & Lembur hari ini
        $hari_ini_indo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][date('w')];
        $sett_hari = $this->db->get_where('tbl_jam_kerja', ['hari' => $hari_ini_indo])->row_array();

        $jam_mulai_lembur = (!empty($sett_hari['jam_mulai_lembur'])) ? substr($sett_hari['jam_mulai_lembur'], 0, 5) : '18:00';
        $jam_masuk_kerja = (!empty($sett_hari['jam_masuk'])) ? substr($sett_hari['jam_masuk'], 0, 5) : '08:00';
        $boleh_lembur_hari_ini = (!empty($sett_hari['boleh_lembur'])) ? $sett_hari['boleh_lembur'] : '1';

        // Logika Boleh Mulai Lembur (Support Pagi & Sore/Malam)
        // Pagi: 06:00 s.d Jam Masuk
        // Sore: Jam Mulai Lembur s.d 23:59
        $is_pagi = ($jamSekarang >= '06:00' && $jamSekarang < $jam_masuk_kerja);
        $is_sore = ($jamSekarang >= $jam_mulai_lembur && $jamSekarang <= '23:59');

        $data['boleh_mulai'] = ($boleh_lembur_hari_ini == '1' && ($is_pagi || $is_sore));
        $data['jam_mulai_lembur'] = $jam_mulai_lembur;
        $data['jam_masuk_kerja'] = $jam_masuk_kerja; // Pass to view if needed
        $data['jam_sekarang'] = $jamSekarang; // Pass current time (simulated or real) to view

        $data['boleh_lembur_hari_ini'] = $boleh_lembur_hari_ini;

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/lembur', $data);
        $this->load->view('admin/mobile/footer', $data);
        $this->load->view('layout_admin/alert', $data);
    }

    public function aksi_mulai_lembur()
    {
        $tanggal = date('Y-m-d');
        $jam_mulai = date('H:i:s');
        $jam_sekarang = date('H:i');

        // Ambil Pengaturan Jam Kerja & Lembur hari ini
        $hari_ini_indo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][date('w')];
        $sett_hari = $this->db->get_where('tbl_jam_kerja', ['hari' => $hari_ini_indo])->row_array();

        $jam_mulai_lembur = (!empty($sett_hari['jam_mulai_lembur'])) ? substr($sett_hari['jam_mulai_lembur'], 0, 5) : '18:00';
        $boleh_lembur_hari_ini = (!empty($sett_hari['boleh_lembur'])) ? $sett_hari['boleh_lembur'] : '1';

        if ($boleh_lembur_hari_ini == '0') {
            $this->session->set_flashdata('error', 'Punten, untuk hari ini tidak dibuka sesi lembur.');
            redirect('mobile/lembur');
        }

        // Ambil data jam masuk kerja
        $jam_masuk_kerja = (!empty($sett_hari['jam_masuk'])) ? substr($sett_hari['jam_masuk'], 0, 5) : '08:00';

        // Logika Validasi Waktu
        $is_pagi = ($jam_sekarang >= '06:00' && $jam_sekarang < $jam_masuk_kerja);
        $is_sore = ($jam_sekarang >= $jam_mulai_lembur);

        if (!$is_pagi && !$is_sore) {
            $this->session->set_flashdata('error', 'Absensi lembur hanya tersedia Pagi (06:00 - ' . $jam_masuk_kerja . ') atau Sore (Mulai ' . $jam_mulai_lembur . ' WIB).');
            redirect('mobile/lembur');
        }

        $existing_unfinished = $this->db->get_where('tbl_lembur', [
            'karyawan_id' => $this->karyawan_id,
            'tanggal' => $tanggal,
            'jam_selesai' => '00:00:00'
        ])->row();

        if ($existing_unfinished) {
            $this->session->set_flashdata('error', 'Anda masih memiliki sesi lembur yang belum ditutup hari ini.');
            redirect('mobile/lembur');
        }

        $lokasi = $this->input->post('lokasi', true);
        
        $this->db->insert('tbl_lembur', [
            'karyawan_id' => $this->karyawan_id,
            'tanggal' => $tanggal,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => '00:00:00',
            'status' => 'menunggu',
            'create_at' => date('Y-m-d H:i:s'),
            'lokasi' => $lokasi
        ]);

        $this->session->set_flashdata('success', 'Lembur dimulai! Selamat bekerja.');
        redirect('mobile/lembur');
    }

    public function aksi_selesai_lembur()
    {
        $id_lembur = $this->input->post('id_lembur', true);
        $kegiatan = $this->input->post('kegiatan', true);
        $jam_selesai = date('H:i:s');

        if (empty($kegiatan)) {
            $this->session->set_flashdata('error', 'Laporan kegiatan wajib diisi.');
            redirect('mobile/lembur');
        }

        $foto = '';
        $foto_base64 = $this->input->post('foto_base64', false); // XSS Clean memotong data base64 ukuran besar

        if (!empty($foto_base64)) {
            $foto = $this->_upload_base64($foto_base64, 'lembur');
            if (empty($foto)) {
                $detail = $this->session->flashdata('error_detail');
                $this->session->set_flashdata('error', 'Gagal memproses foto kamera. Detail: ' . $detail);
                redirect('mobile/lembur');
                return;
            }
        } elseif (!empty($_FILES['foto']['name'])) {
            $upload_path = FCPATH . 'assets/images/lembur/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
                chmod($upload_path, 0777);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|webp|heic|heif';
            $config['max_size'] = 10240; // Increase to 10MB for modern phone cameras
            $config['file_name'] = 'lembur_' . ($this->karyawan_id ?? 'unknown') . '_' . time();

            $this->load->library('upload');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('foto')) {
                $upload_data = $this->upload->data();
                $foto = $upload_data['file_name'];
            } else {
                $error = $this->upload->display_errors('', '');
                log_message('error', 'Gagal upload foto lembur: ' . $error . ' | Karyawan ID: ' . $this->karyawan_id);
                $this->session->set_flashdata('error', 'Gagal upload foto: ' . $error);
                redirect('mobile/lembur');
                return;
            }
        } else {
            // Check if there was a PHP upload error (e.g. exceeded post_max_size despite our check)
            if (!empty($_FILES['foto']['error'])) {
                log_message('error', 'PHP Upload Error: ' . $_FILES['foto']['error'] . ' | Karyawan ID: ' . $this->karyawan_id);
            }
            $this->session->set_flashdata('error', 'Foto kegiatan wajib diupload.');
            redirect('mobile/lembur');
            return;
        }

        $this->db->where('id', $id_lembur);
        $update = $this->db->update('tbl_lembur', [
            'jam_selesai' => $jam_selesai,
            'kegiatan' => $kegiatan,
            'foto_kegiatan' => $foto
        ]);

        if ($update) {
            $this->session->set_flashdata('success', 'Lembur selesai! Laporan kerja telah disimpan.');
        } else {
            log_message('error', 'Gagal update tbl_lembur ID: ' . $id_lembur);
            $this->session->set_flashdata('error', 'Gagal memperbarui data lembur di database.');
        }

        redirect('mobile/lembur');
    }

    public function aksi_simpan_lembur()
    {
        if ($this->input->method() !== 'post') {
            return $this->output->set_status_header(405)->set_output(json_encode(['status' => 'error', 'message' => 'Method not allowed']));
        }

        $karyawan_id = $this->input->post('karyawan_id', true);
        $tanggal = $this->input->post('tanggal', true);
        $jam_mulai = $this->input->post('jam_mulai', true);
        $jam_selesai = $this->input->post('jam_selesai', true);
        $kegiatan = $this->input->post('kegiatan', true);

        if (empty($karyawan_id) || empty($tanggal) || empty($jam_mulai) || empty($jam_selesai) || empty($kegiatan)) {
            return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'Semua field harus diisi']));
        }

        if (strtotime($jam_selesai) <= strtotime($jam_mulai)) {
            return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'Jam selesai harus lebih besar dari jam mulai']));
        }

        $existing_lembur = $this->db->get_where('tbl_lembur', ['karyawan_id' => $karyawan_id, 'tanggal' => $tanggal])->row();
        if ($existing_lembur) {
            return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'Sudah ada lembur tanggal ini']));
        }

        $this->db->trans_start();
        $this->db->insert('tbl_lembur', [
            'karyawan_id' => $karyawan_id,
            'tanggal' => $tanggal,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'kegiatan' => $kegiatan,
            'status' => 'menunggu',
            'create_at' => date('Y-m-d H:i:s')
        ]);
        /** @var CI_DB_query_builder $db */
        $db = $this->db;
        $lembur_id = $db->insert_id();

        // Log
        if ($this->db->table_exists('tbl_log_aktivitas')) {
            $this->db->insert('tbl_log_aktivitas', [
                'user_id' => $this->user_id,
                'activity' => 'Pengajuan lembur berhasil disimpan',
                'detail' => 'Lembur ID: ' . $lembur_id,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->output->set_status_header(500)->set_output(json_encode(['status' => 'error', 'message' => 'Database error']));
        }

        $this->session->set_flashdata('success', 'Pengajuan lembur berhasil disimpan!');
        redirect('mobile/lembur');
    }

    public function get_riwayat_lembur()
    {
        if (!$this->karyawan_id) {
            return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']));
        }

        $bulan_lalu = date('Y-m-d', strtotime('-1 month'));
        $today = date('Y-m-d');

        $riwayat_lembur = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where('tanggal >=', $bulan_lalu)
            ->where('tanggal <=', $today)
            ->order_by('tanggal', 'DESC')
            ->order_by('jam_mulai', 'DESC')
            ->get('tbl_lembur')->result();

        $formatted_data = [];
        foreach ($riwayat_lembur as $lembur) {
            $jam_mulai = strtotime($lembur->jam_mulai);
            $jam_selesai = strtotime($lembur->jam_selesai);
            $durasi = $jam_selesai - $jam_mulai;
            $jam = floor($durasi / 3600);
            $menit = floor(($durasi % 3600) / 60);

            $formatted_data[] = [
                'id' => $lembur->id,
                'tanggal' => $lembur->tanggal,
                'tanggal_formatted' => date('d M Y', strtotime($lembur->tanggal)),
                'jam_mulai' => $lembur->jam_mulai,
                'jam_selesai' => $lembur->jam_selesai,
                'kegiatan' => $lembur->kegiatan,
                'status' => $lembur->status,
                'create_at_formatted' => date('d/m H:i', strtotime($lembur->create_at)),
                'durasi' => $jam . 'j ' . $menit . 'm'
            ];
        }

        echo json_encode(['status' => 'success', 'data' => $formatted_data]);
    }

    public function sakit()
    {
        $data = $this->_get_common_data();
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        $bulan_ini = date('Y-m');

        // Ambil data Absensi yang sudah tercatat
        $history_absensi = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where("DATE_FORMAT(tanggal, '%Y-%m') =", $bulan_ini)
            ->where_in('status', ['Izin', 'Sakit'])
            ->get('absensi')->result();

        // Ambil data Pengajuan (Mungkin masih Pending)
        $pengajuan_izin = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where("DATE_FORMAT(tanggal_mulai, '%Y-%m') =", $bulan_ini)
            ->order_by('created_at', 'DESC')
            ->get('tbl_pengajuan_izin')->result();

        // Gabungkan untuk ditampilkan di riwayat
        $combined_history = [];

        // 1. Tambah dari pengajuan (utk liat status Pending/Rejected)
        foreach ($pengajuan_izin as $p) {
            $combined_history[] = (object) [
                'tanggal' => $p->tanggal_mulai,
                'status' => ($p->status == 'Pending') ? 'Pending (' . ucfirst($p->tipe_izin) . ')' : ucfirst($p->tipe_izin),
                'status_raw' => $p->tipe_izin,
                'keterangan' => $p->keterangan,
                'dokumen' => $p->dokumen,
                'status_pengajuan' => $p->status // Pending/Approved/Rejected
            ];
        }

        // 2. Tambah dari absensi (untuk yg sudah Approved/Input Manual)
        // Kita bandingkan tgl agar tidak double jika sudah Approved dan masuk absensi
        foreach ($history_absensi as $a) {
            $is_already_in = false;
            foreach ($combined_history as $c) {
                if ($c->tanggal == $a->tanggal && strtolower($c->status_raw) == strtolower($a->status)) {
                    $is_already_in = true;
                    break;
                }
            }
            if (!$is_already_in) {
                $combined_history[] = (object) [
                    'tanggal' => $a->tanggal,
                    'status' => ucfirst($a->status),
                    'status_raw' => $a->status,
                    'keterangan' => $a->keterangan,
                    'dokumen' => '', // absensi table doesn't have it yet
                    'status_pengajuan' => 'Approved'
                ];
            }
        }

        // Urutkan berdasarkan tanggal terbaru
        usort($combined_history, function ($a, $b) {
            return strtotime($b->tanggal) - strtotime($a->tanggal);
        });

        $data['data_izin_sakit_bulan_ini'] = $combined_history;

        $data['total_izin'] = $this->db->where('karyawan_id', $this->karyawan_id)->where("DATE_FORMAT(tanggal, '%Y-%m') =", $bulan_ini)->where('status', 'Izin')->count_all_results('absensi');
        $data['total_sakit'] = $this->db->where('karyawan_id', $this->karyawan_id)->where("DATE_FORMAT(tanggal, '%Y-%m') =", $bulan_ini)->where('status', 'Sakit')->count_all_results('absensi');

        // Cek Cuti
        $data['total_cuti'] = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where("DATE_FORMAT(tanggal, '%Y-%m') =", $bulan_ini)
            ->where('status', 'Izin')
            ->like('keterangan', 'cuti', 'both')
            ->count_all_results('absensi');

        $data['total_hari'] = $data['total_izin'] + $data['total_sakit'];

        // Data tambahan utk view
        $data['karyawan'] = $this->M_master->karyawan_lembur()->result();
        $data['jml'] = $this->M_master->karyawan_lembur()->num_rows();
        $data['listkaryawan'] = $this->M_master->get_karyawan()->result();
        $data['karyawan_terpilih'] = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $this->karyawan_id])->row_array();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/sakit', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function laporan()
    {
        $data = $this->_get_common_data();
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        $bulan = $this->input->get('bulan', true) ?: date('m');
        $tahun = $this->input->get('tahun', true) ?: date('Y');
        $periode = $tahun . '-' . sprintf('%02d', $bulan);

        $data['absensi'] = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where("DATE_FORMAT(tanggal, '%Y-%m') =", $periode)
            ->order_by('tanggal', 'DESC')
            ->get('absensi')->result_array();

        $data['lembur'] = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where("DATE_FORMAT(tanggal, '%Y-%m') =", $periode)
            ->order_by('tanggal', 'DESC')
            ->get('tbl_lembur')->result();

        $data['karyawan_terpilih'] = $this->db->select('k.*, d.divisi_nama, j.nama_jabatan')
            ->from('tbl_karyawan k')
            ->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left')
            ->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left')
            ->where('k.karyawan_id', $this->karyawan_id)
            ->get()->row_array();

        $data['bulan_filter'] = $bulan;
        $data['tahun_filter'] = $tahun;

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/laporan', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function direktori()
    {
        $data = $this->_get_common_data('Direktori Karyawan');
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'] ?? '';

        // Fetch all active employees
        $this->db->select('k.karyawan_id, k.nama_lengkap, k.email, k.telepon, k.foto, j.nama_jabatan, o.operator_nama as divisi_nama');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->join('tbl_operator o', 'o.operator_id = k.divisi_id', 'left');
        $this->db->where('k.status_karyawan !=', 'Tidak Aktif');
        $this->db->order_by('k.nama_lengkap', 'ASC');
        $data['direktori'] = $this->db->get()->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/direktori', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function helpdesk()
    {
        $data = $this->_get_common_data('Pusat Bantuan ERP');
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'] ?? '';

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/helpdesk', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function profile()
    {
        $data = $this->_get_common_data();
        $data['title'] = 'Profile';
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        $data['listkaryawan'] = $this->M_master->get_karyawan()->result();
        $data['karyawan_terpilih'] = $this->M_profile->getprofilebyid($this->karyawan_id)->row_array();
        $data['profile'] = $this->M_profile->getprofile()->row_array();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/profile', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function aksi_edit_profile()
    {
        $karyawan_id = $this->input->post('karyawan_id', true);
        $data_update = [
            'nama_lengkap' => $this->input->post('nama_lengkap', true),
            'jenis_kelamin' => $this->input->post('jenis_kelamin', true),
            'tempat_lahir' => $this->input->post('tempat_lahir', true),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', true),
            'telepon' => $this->input->post('telepon', true),
            'email' => $this->input->post('email', true),
            'alamat' => $this->input->post('alamat', true),
        ];

        // Handle upload foto
        if (!empty($_FILES['foto']['name'])) {
            $upload_path = './assets/images/karyawan/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
                chmod($upload_path, 0777);
            }

            // Manual Extension Validation (bypass CI's flaky MIME check)
            $file_name = $_FILES['foto']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'];

            if (!in_array($file_ext, $allowed_extensions)) {
                $this->session->set_flashdata('error', 'Format file .' . $file_ext . ' tidak didukung. Gunakan JPG, PNG, atau HEIC.');
                redirect('mobile/profile');
                return;
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = '*'; // Bypass internal MIME check
            $config['max_size'] = 10240; // 10MB
            $config['file_name'] = 'profile_' . $karyawan_id . '_' . time();
            $config['overwrite'] = TRUE;

            $this->load->library('upload');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('foto')) {
                $upload_data = $this->upload->data();
                $data_update['foto'] = $upload_data['file_name'];

                // Cleanup old photo
                $old_data = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $karyawan_id])->row();
                $old_foto = $old_data ? $old_data->foto : '';

                if ($old_foto && !in_array($old_foto, ['user.png', 'default.png']) && file_exists($upload_path . $old_foto)) {
                    @unlink($upload_path . $old_foto);
                }
            } else {
                $error = $this->upload->display_errors('', '');
                log_message('error', 'Critical Upload Failure: ' . $error);
                $this->session->set_flashdata('error', 'Gagal upload foto: ' . $error);
                redirect('mobile/profile');
                return;
            }
        }

        $this->M_master->updateData('tbl_karyawan', ['karyawan_id' => $karyawan_id], $data_update);
        $this->session->set_flashdata('success', 'Profile berhasil diperbarui.');
        redirect('mobile/profile');
    }

    public function reset_pass()
    {
        $data = $this->_get_common_data();
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        $data['karyawan'] = $this->M_master->karyawan_lembur()->result();
        $data['jml'] = $this->M_master->karyawan_lembur()->num_rows();
        $data['listkaryawan'] = $this->M_master->get_karyawan()->result();

        $data['karyawan_terpilih'] = $this->db->select('k.*, d.divisi_nama, j.nama_jabatan')
            ->from('tbl_karyawan k')
            ->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left')
            ->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left')
            ->where('k.karyawan_id', $this->karyawan_id)
            ->get()->row_array();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/ubah_password', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function product()
    {
        $data = $this->_get_common_data('Katalog Produk');
        $data['nama_perusahaan'] = $data['perusahaan']['nama_perusahaan'] ?? '';

        // Pre-load items for the initial view (AJAX will take over for search/paging)
        $limit = 10;
        $page = 1;
        $start = 0;

        $this->db->select('p.*, k.kategori_nama');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_kategori k', 'k.kategori_id = p.kategori_id', 'left');
        $this->db->order_by('p.product_nama', 'ASC');
        $this->db->limit($limit, $start);
        $data['produk'] = $this->db->get()->result();

        $total_rows = $this->db->count_all('tbl_product');
        $data['total_pages'] = ceil($total_rows / $limit);
        $data['page'] = $page;

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/product', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function ajax_list()
    {
        $limit = 10;
        $page = max(1, (int) $this->input->get('page', true));
        $keyword = $this->input->get('keyword', true);
        $start = ($page - 1) * $limit;

        $this->db->select('p.*, k.kategori_nama');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_kategori k', 'k.kategori_id = p.kategori_id', 'left');

        if ($keyword) {
            $this->db->group_start();
            $this->db->like('p.product_nama', $keyword);
            $this->db->or_like('k.kategori_nama', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('p.product_nama', 'ASC');

        // Clone query to count total rows for pagination
        $count_query = clone $this->db;
        $total_rows = $count_query->count_all_results();

        $this->db->limit($limit, $start);
        $data['produk'] = $this->db->get()->result();
        $data['total_pages'] = ceil($total_rows / $limit);
        $data['page'] = $page;

        $this->load->view('admin/mobile/product_list', $data);
    }

    public function produk_detail($id = null)
    {
        if (!$id)
            $id = $this->uri->segment(3);

        $data = $this->_get_common_data('Detail Produk');
        $data['nama_perusahaan'] = $data['perusahaan']['nama_perusahaan'] ?? '';

        $data['getProduct'] = $this->M_master->getProduct($id)->row_array();

        if (empty($data['getProduct'])) {
            $this->session->set_flashdata('error', 'Produk tidak ditemukan.');
            redirect('mobile/product');
        }

        $data['getHarga'] = $this->M_master->getHarga($id)->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/detail_product', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function owner()
    {
        $data = $this->_get_common_data();
        // $data['perusahaan'] is already array object from _get_common_data, 
        // unlike other methods which override it to string. 
        // Kept consistent with index logic.

        $tanggal = date('Y-m-d');
        $jamSekarang = date('H:i');

        $data['karyawan'] = $this->M_master->karyawan_lembur()->result();
        $data['jml'] = $this->M_master->karyawan_lembur()->num_rows();
        $data['listkaryawan'] = $this->M_master->get_karyawan()->result();
        $data['karyawan_terpilih'] = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $this->karyawan_id])->row_array();

        $data['cekabsen'] = $this->db->get_where('absensi', ['karyawan_id' => $this->karyawan_id, 'tanggal' => $tanggal])->row_array();
        $data['hasilcek'] = $this->db->get_where('absensi', ['karyawan_id' => $this->karyawan_id, 'tanggal' => $tanggal])->num_rows();

        $data['success'] = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where('tanggal', $tanggal)
            ->where('jam_masuk IS NOT NULL AND jam_masuk != "00:00:00"', null, false)
            ->where('jam_pulang IS NOT NULL AND jam_pulang != "00:00:00"', null, false)
            ->get('absensi')->num_rows();

        $isLibur = $this->db->where('tanggal', $tanggal)->get('hari_libur')->num_rows() > 0;
        $isMinggu = date('w', strtotime($tanggal)) == 0;
        $isJamKerja = ($jamSekarang >= '07:00' && $jamSekarang <= '18:00');
        $data['boleh_absen'] = !$isLibur && !$isMinggu && $isJamKerja;

        // --- TAMBAHAN: DATA ABSENSI HARIAN UNTUK OWNER ---
        // 1. Ambil Log Absensi Hari Ini
        $logs = $this->Absensi_model->get_today_attendance(); // Return array of objects

        // 2. Siapkan Variabel Counter
        $hadir_tepat_waktu = 0;
        $terlambat = 0;
        $izin_sakit = 0;
        $processed_ids = []; // Utk melacak siapa yg sudah absen

        // 3. Loop Log untuk Hitung Statistik & Tambah properti 'terlambat' (menit)
        foreach ($logs as &$log) { // Pass by reference agar bisa modif objek
            $processed_ids[] = $log->karyawan_id;
            $log->status_kehadiran = $log->status; // View Compatibility

            // Hitung Keterlambatan (Misal masuk jam 8 + 15 menit toleransi)
            // Logika sederhana: ambil jam masuk dr tabel jam kerja (via join di model) atau hardcode sementara
            // Di Absensi_model::get_today_attendance() join belum ada jam_kerja, kita ambil simple dulu

            // Cek status
            if ($log->status == 'Hadir') {
                // Asumsi telat > 08:15:00 (Sesuaikan dengan aturan perusahaan, atau ambil dr jam_kerja)
                // Idealnya: $log->jam_masuk > $jam_masuk_jadwal + toleransi. 
                // Kita cek field 'terlambat' dr query jika ada, atau hitung manual. 
                // Disini kita hitung manual simple (contoh 08:00 masuk)

                // Asumsi telat > jadwal + 1 menit (misal 08:01:00)
                $daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $hari_ini_indo = $daysIndo[date('w')];
                $jadwal = $this->db->get_where('tbl_jam_kerja', ['hari' => $hari_ini_indo])->row();
                $jam_masuk_jadwal = $jadwal ? $jadwal->jam_masuk : '08:00:00';

                $batas_telat = date('H:i:s', strtotime($jam_masuk_jadwal . ' +1 minute'));

                if ($log->jam_masuk > $batas_telat) {
                    $terlambat++;
                    $log->terlambat = (strtotime($log->jam_masuk) - strtotime($jam_masuk_jadwal)); // detil detik
                } else {
                    $hadir_tepat_waktu++;
                    $log->terlambat = 0;
                }
            } elseif (in_array($log->status, ['Izin', 'Sakit', 'Cuti'])) {
                $izin_sakit++;
                $log->terlambat = 0;
            } else {
                // Alpha / Status lain dianggap tidak hadir tepat waktu
                $log->terlambat = 0;
            }
        }
        $data['log_absensi'] = $logs;
        $data['hadir_tepat_waktu'] = $hadir_tepat_waktu;
        $data['terlambat'] = $terlambat;
        $data['izin_sakit'] = $izin_sakit;

        // 4. Cari Karyawan Belum Hadir
        // Ambil semua karyawan aktif
        $all_employees = $this->db->select('k.karyawan_id, k.nama_lengkap, k.telepon, k.foto, j.nama_jabatan')
            ->from('tbl_karyawan k')
            ->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left')
            ->where('k.status_karyawan', 'Tetap') // Hanya yg aktif
            ->where('k.divisi_id !=', 8) // Kecuali jabatan tertentu yg tidak wajib absen (misal Owner)
            ->get()->result();

        $belum_hadir = [];
        foreach ($all_employees as $emp) {
            if (!in_array($emp->karyawan_id, $processed_ids)) {
                $belum_hadir[] = $emp;
            }
        }
        $data['belum_hadir'] = $belum_hadir;
        // -----------------------------------------------------

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/owner', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function keuangan()
    {
        $data = $this->_get_common_data();
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        $data['karyawan_terpilih'] = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $this->karyawan_id])->row_array();
        $data['lembur'] = $this->M_master->karyawan_lembur()->result();
        $data['keuangan'] = $this->M_transaksi->getkeuanganmobile()->result();
        $data['pengeluaran'] = $this->M_transaksi->getpengeluaran()->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/keuangan', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function piutang()
    {
        // Security check: Only Admin (1) or Owner (17) can access
        $level = $this->session->userdata('level');
        if ($level != 1 && $level != 17) {
            redirect('mobile');
        }

        $data = $this->_get_common_data('Monitoring Piutang');
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        // 1. Get Summary Stats
        $data['total_piutang'] = $this->db->select_sum('total_tagihan')->get_where('tbl_piutang', ['status !=' => 'LUNAS'])->row()->total_tagihan;
        $data['sisa_piutang'] = $this->db->select_sum('sisa_piutang')->get_where('tbl_piutang', ['status !=' => 'LUNAS'])->row()->sisa_piutang;
        $data['count_piutang'] = $this->db->where('status !=', 'LUNAS')->count_all_results('tbl_piutang');

        // 2. Get Detail List (Outstanding only)
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_nohp');
        $this->db->from('tbl_piutang p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->where('p.status !=', 'LUNAS');
        $this->db->order_by('p.jatuh_tempo', 'ASC'); // Prioritaskan yang sudah jatuh tempo
        $this->db->limit(50);
        $data['list_piutang'] = $this->db->get()->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/piutang', $data);
        $this->load->view('admin/mobile/footer', $data);
    }





    public function hutang()
    {
        // Security check: Only Admin (1) or Owner (17) can access
        $level = $this->session->userdata('level');
        if ($level != 1 && $level != 17) {
            redirect('mobile');
        }

        $data = $this->_get_common_data('Monitoring Hutang Usaha');
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        // 1. Get Summary Stats
        $data['total_hutang'] = $this->db->select_sum('total_tagihan')->get_where('tbl_hutang', ['status !=' => 'LUNAS'])->row()->total_tagihan;
        $data['sisa_hutang'] = $this->db->select_sum('sisa_hutang')->get_where('tbl_hutang', ['status !=' => 'LUNAS'])->row()->sisa_hutang;
        $data['count_hutang'] = $this->db->where('status !=', 'LUNAS')->count_all_results('tbl_hutang');

        // 2. Get Detail List (Outstanding only)
        $this->db->select('h.*, s.supplier_nama, s.supplier_nohp');
        $this->db->from('tbl_hutang h');
        $this->db->join('tbl_supplier s', 'h.supplier_id = s.supplier_id', 'left');
        $this->db->where('h.status !=', 'LUNAS');
        $this->db->order_by('h.jatuh_tempo', 'ASC'); // Prioritaskan yang sudah jatuh tempo
        $this->db->limit(50);
        $data['list_hutang'] = $this->db->get()->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/hutang', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function konsumen()
    {
        // Security check: Only Admin (1) or Owner (17) can access
        $level = $this->session->userdata('level');
        if ($level != 1 && $level != 17) {
            redirect('mobile');
        }

        $data = $this->_get_common_data('Top Konsumen Bulan Ini');
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        // Get Top Consumers Logic
        $this->db->select('k.konsumen_nama, k.konsumen_nohp, COUNT(d.pembayaran_id) as total_trx, SUM(d.total_bayar) as total_belanja');
        $this->db->from('tbl_pembayaran d');
        $this->db->join('tbl_konsumen k', 'd.konsumen_id = k.konsumen_id');
        $this->db->where('MONTH(d.tgl_bayar)', date('m'));
        $this->db->where('YEAR(d.tgl_bayar)', date('Y'));
        $this->db->where('d.status_lunas', '1'); // Only counted if paid (status_lunas = 1)
        $this->db->group_by('d.konsumen_id');
        $this->db->order_by('total_belanja', 'DESC'); // Sort by spending amount
        $this->db->limit(10); // Top 10 only
        $data['top_konsumen'] = $this->db->get()->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/konsumen', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function penjualan()
    {
        $data = $this->_get_common_data();
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        $data['karyawan_terpilih'] = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $this->karyawan_id])->row_array();
        $data['absensi'] = $this->Absensi_model->get_absensi_hari_ini();
        $data['lembur'] = $this->M_master->karyawan_lembur()->result();
        $data['keuangan'] = $this->M_transaksi->getkeuanganmobile()->result();
        $data['pengeluaran'] = $this->M_transaksi->getpengeluaran()->result();
        $data['transaksi_sudahbayar'] = $this->M_transaksi->getpenjualanall()->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/penjualan', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function sdm()
    {
        $data = $this->_get_common_data();
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        $data['karyawan_terpilih'] = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $this->karyawan_id])->row_array();
        $data['lembur'] = $this->M_master->karyawan_lembur()->result();
        $this->db->select('tbl_karyawan.*, tbl_jabatan.nama_jabatan as jabatan');
        $this->db->from('tbl_karyawan');
        $this->db->join('tbl_jabatan', 'tbl_jabatan.jabatan_id = tbl_karyawan.jabatan_id', 'left');
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%DUMMY%'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%Terminal%'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%DTF%'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE 'Operator %'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%DEPT%'");
        $this->db->where("tbl_karyawan.nik NOT LIKE 'STATION%'");
        $data['karyawan'] = $this->db->get()->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/sdm', $data);
        $this->load->view('admin/mobile/footer', $data);

    }

    public function laporan_gaji()
    {
        if (!in_array($this->session->userdata('level'), ['1', '16', '17', '21'])) {
            redirect('mobile');
        }

        $bulan = $this->input->get('bulan', true) ?: date('Y-m');
        $data = $this->_get_common_data('Laporan Gaji / Payroll');
        $data['bulan_aktif'] = $bulan;

        $this->load->model('Payroll_model');
        $raw_salary_data = $this->Payroll_model->get_salary_data($bulan);

        // Filter dummy accounts
        $filtered_salary = [];
        $dummy_keywords = ['DUMMY', 'Terminal', 'DTF', 'Operator ', 'DEPT'];

        foreach ($raw_salary_data as $row) {
            $is_dummy = false;
            foreach ($dummy_keywords as $keyword) {
                if (isset($row['nama_lengkap']) && stripos($row['nama_lengkap'], $keyword) !== false) {
                    $is_dummy = true;
                    break;
                }
            }
            if (isset($row['nik']) && strpos($row['nik'], 'STATION') === 0) {
                $is_dummy = true;
            }
            if (!$is_dummy) {
                $filtered_salary[] = $row;
            }
        }

        $data['salary_data'] = $filtered_salary;

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/laporan_gaji', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function sdm_detail($karyawan_id = null)
    {
        $data = $this->_get_common_data('Detail Karyawan');
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        $data['karyawan'] = $this->db->select('k.*, j.nama_jabatan, d.divisi_nama')
            ->from('tbl_karyawan k')
            ->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left')
            ->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left')
            ->where('k.karyawan_id', $karyawan_id)
            ->get()->row_array();

        if (!$data['karyawan']) {
            redirect('mobile/sdm');
            return;
        }

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/sdm_detail', $data);
        $this->load->view('admin/mobile/footer', $data);
    }


    public function laporan_absensi()
    {
        // Security check: Only Admin (1) or Owner (17) or HR (16, 21)
        $level = $this->session->userdata('level');
        if (!in_array($level, [1, 16, 17, 21])) {
            redirect('mobile');
        }

        $data = $this->_get_common_data('Laporan Kehadiran Hari Ini');
        $today = date('Y-m-d');

        // Statistik
        $data['total_karyawan'] = $this->db->count_all('tbl_karyawan');
        $data['hadir_tepat_waktu'] = $this->db->where(['tanggal' => $today, 'status' => 'Hadir', 'jam_masuk <=' => '08:01:00'])->count_all_results('absensi');
        $data['terlambat_count'] = $this->db->where(['tanggal' => $today, 'status' => 'Hadir', 'jam_masuk >' => '08:01:00'])->count_all_results('absensi');
        $data['terlambat'] = $data['terlambat_count']; // For view
        $data['izin_sakit'] = $this->db->where('tanggal', $today)->where_in('status', ['Izin', 'Sakit', 'Cuti'])->count_all_results('absensi');

        // List Absensi Hari Ini
        $this->db->select('a.*, k.foto, k.nama_lengkap, j.nama_jabatan');
        $this->db->from('absensi a');
        $this->db->join('tbl_karyawan k', 'a.karyawan_id = k.karyawan_id', 'left');
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->where('a.tanggal', $today);
        $this->db->order_by('a.jam_masuk', 'DESC');
        $logs = $this->db->get()->result();

        foreach ($logs as $log) {
            $log->terlambat = 0;
            if ($log->status == 'Hadir' && $log->jam_masuk > '08:01:00') {
                $log->terlambat = (strtotime($log->jam_masuk) - strtotime('08:00:00'));
            }
            $log->status_kehadiran = strtolower($log->status);
        }
        $data['log_absensi'] = $logs;

        // List Belum Hadir (Total Karyawan - Yang Sudah Absen)
        $sudah_absen_ids = $this->db->select('karyawan_id')->where('tanggal', $today)->get('absensi')->result_array();
        $ids = array_column($sudah_absen_ids, 'karyawan_id');

        if (!empty($ids)) {
            $this->db->where_not_in('tbl_karyawan.karyawan_id', $ids);
        }
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%DUMMY%'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%Terminal%'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%DTF%'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%DTG%'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%OPERATOR%'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%DEPT%'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%WORK CENTER%'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%LARGE FORMAT%'");
        $this->db->where("tbl_karyawan.nama_lengkap NOT LIKE '%A3+%'");
        $this->db->where("tbl_karyawan.nik NOT LIKE 'STATION%'");
        $this->db->where("tbl_karyawan.nik NOT LIKE 'TERM%'");
        $this->db->join('tbl_jabatan', 'tbl_karyawan.jabatan_id = tbl_jabatan.jabatan_id', 'left');
        $data['belum_hadir'] = $this->db->get('tbl_karyawan')->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/laporan_absensi', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function laporan_izin()
    {
        $level = $this->session->userdata('level');
        if (!in_array($level, [1, 16, 17, 21])) {
            redirect('mobile');
        }

        $bulan = $this->input->get('bulan', true) ?: date('Y-m');
        $data['bulan_aktif'] = $bulan;
        $data = $this->_get_common_data('Laporan Pengajuan Izin');
        $data['bulan_aktif'] = $bulan; // set again after common data
        
        $this->db->select('p.*, k.nama_lengkap, k.foto, j.nama_jabatan');
        $this->db->from('tbl_pengajuan_izin p');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = p.karyawan_id', 'left');
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->where('DATE_FORMAT(p.tanggal_mulai, "%Y-%m") =', $bulan);
        $this->db->order_by('p.created_at', 'DESC');
        $data['list_izin'] = $this->db->get()->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/laporan_izin', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function laporan_lembur()
    {
        $level = $this->session->userdata('level');
        if (!in_array($level, [1, 16, 17, 21])) {
            redirect('mobile');
        }

        $bulan = $this->input->get('bulan', true) ?: date('Y-m');
        $data = $this->_get_common_data('Laporan Pengajuan Lembur');
        $data['bulan_aktif'] = $bulan;

        $this->db->select('l.*, k.nama_lengkap, k.foto, j.nama_jabatan');
        $this->db->from('tbl_lembur l');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = l.karyawan_id', 'left');
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->where('DATE_FORMAT(l.tanggal, "%Y-%m") =', $bulan);
        $this->db->order_by('l.tanggal', 'DESC');
        $this->db->order_by('l.create_at', 'DESC');
        $data['list_lembur'] = $this->db->get()->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/laporan_lembur', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function update_status_lembur_ajax()
    {
        if (!in_array($this->session->userdata('level'), ['1', '16', '17', '21'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $id = (int) $this->input->post('id', true);
        $status = $this->input->post('status', true);

        if ($id && in_array($status, ['disetujui', 'ditolak'])) {
            $this->db->where('id', $id);
            $this->db->update('tbl_lembur', ['status' => $status]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
        }
    }

    public function kirim_teguran_wa($karyawan_id)
    {
        // Get Karyawan Data
        $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $karyawan_id])->row();

        if (!$karyawan || empty($karyawan->telepon)) {
            $this->session->set_flashdata('error', 'Nomor HP Karyawan tidak valid.');
            redirect('mobile/laporan_absensi');
            return;
        }

        // Get WA Token (Optional, if using API directly in future)
        // For now, we use direct WA link as requested, but we structure it nicely.

        $nama = $karyawan->nama_lengkap;
        $pesan = "Halo *$nama*,\n\n";
        $pesan .= "Kami memperhatikan bahwa Anda *belum melakukan presensi* hari ini (" . date('d M Y') . ").\n";
        $pesan .= "Mohon segera melakukan konfirmasi kehadiran atau mengajukan izin jika berhalangan hadir.\n\n";
        $pesan .= "_Sistem HRD TGM Print_";

        $no_hp = $karyawan->telepon;
        // Format number: remove 0 or 62 prefix, add 62
        if (substr($no_hp, 0, 1) == '0') {
            $no_hp = '62' . substr($no_hp, 1);
        }

        // Menggunakan Fonnte Link Logic (Sesuai request user: 'WA Fonte')
        // Tapi karena 'WA Fonte' biasanya API, user mungkin maksud 'target="_blank"' link WA biasa tapi dengan format Fonte? 
        // Atau redirect ke API? Karena user bilang 'link WA', kemungkinan besar maksudnya 'https://wa.me' yang sudah ada,
        // CUMA minta diotomatiskan pesannya.

        // Namun, jika user mau pakai API Fonte server-side (biar otomatis terkirim tanpa buka WA app HRD),
        // Kita butuh fungsi curl.
        // Mari kita coba redirect ke wa.me dulu seperti yang sudah ada, tapi pesannya lebih formal.
        // JIKA user nanti minta "langsung kirim tanpa buka app", baru kita pakai CURL.

        // UPDATE: User says "WA Fonte agar lebih otomatis". 
        // This usually means using the API to send message automatically.

        $wa_setting = $this->db->get('tbl_wa_setting')->row();
        $token = $wa_setting->wa_token;
        $target = $no_hp;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30, // Naikkan timeout
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',

            // PENTING: Bypass SSL untuk hosting
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,

            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $pesan,
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $token"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            // Jika CURL Error (Masalah Server/Koneksi)
            $this->session->set_flashdata('error', 'Gagal Kirim WA (CURL Error): ' . $err);
            // Fallback ke manual
            redirect("https://wa.me/$target?text=" . urlencode($pesan));
        } else {
            // Cek respon dari Fonnte
            $res = json_decode($response, true);

            if (isset($res['status']) && $res['status'] == true) {
                $this->session->set_flashdata('success', 'Pesan teguran otomatis dikirim ke ' . $nama);
            } else {
                // Jika Fonnte Error (Misal: Token salah / Perangkat mati)
                $reason = $res['reason'] ?? 'Unknown Error';
                $this->session->set_flashdata('error', 'Gagal Kirim WA (Fonnte): ' . $reason);

                // Fallback ke manual jika otomatis gagal
                echo "<script>window.open('https://wa.me/$target?text=" . urlencode($pesan) . "', '_blank'); window.location.href='" . base_url('mobile/laporan_absensi') . "';</script>";
                return;
            }

            redirect('mobile/laporan_absensi');
        }
    }

    public function koreksi_absen()
    {
        $data = $this->_get_common_data('Koreksi Absen');
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        // Get history
        $data['riwayat'] = $this->db->where('karyawan_id', $this->karyawan_id)
            ->order_by('created_at', 'DESC')
            ->get('tbl_pengajuan_koreksi_absen')->result();

        // Get daftar tanggal kesiangan (jam_masuk > 08:00:00) hanya bulan berjalan
        $data['tgl_kesiangan'] = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where('jam_masuk >', '08:00:00')
            ->where('status !=', 'Alpha') // hanya yang hadir/telat
            ->where('MONTH(tanggal)', date('m'))
            ->where('YEAR(tanggal)', date('Y'))
            ->order_by('tanggal', 'DESC')
            ->get('absensi')->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/koreksi_absen', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function get_absensi_info_by_date()
    {
        $tanggal = $this->input->get('tanggal');
        if (!$tanggal) {
            echo json_encode(['status' => false, 'message' => 'Tanggal tidak valid.']);
            return;
        }

        $absen = $this->db->get_where('absensi', [
            'karyawan_id' => $this->karyawan_id,
            'tanggal' => $tanggal
        ])->row();

        if ($absen) {
            $jam_masuk = $absen->jam_masuk ? substr($absen->jam_masuk, 0, 5) : '-';
            $jam_pulang = $absen->jam_pulang ? substr($absen->jam_pulang, 0, 5) : '-';
            
            $terlambat_menit = 0;
            if ($absen->jam_masuk && $absen->jam_masuk > '08:00:00') {
                $masuk_time = strtotime($tanggal . ' ' . $absen->jam_masuk);
                $batas_time = strtotime($tanggal . ' 08:00:00');
                $terlambat_menit = round(($masuk_time - $batas_time) / 60);
            }
            
            $terlambat = $terlambat_menit > 0 ? $terlambat_menit . ' menit' : '0 menit';
            
            echo json_encode([
                'status' => true,
                'jam_masuk' => $jam_masuk,
                'jam_pulang' => $jam_pulang,
                'terlambat' => $terlambat,
                'message' => "Tercatat Masuk: $jam_masuk, Pulang: $jam_pulang (Kesiangan: $terlambat)"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => 'Tidak ada data kehadiran pada tanggal tersebut.'
            ]);
        }
    }

    public function submit_koreksi_absen()
    {
        $tanggal = $this->input->post('tanggal_absen');
        $jam_masuk_baru = $this->input->post('jam_masuk_baru');
        $jam_pulang_baru = $this->input->post('jam_pulang_baru');
        $alasan = $this->input->post('alasan');

        if (empty($tanggal) || empty($alasan)) {
            $this->session->set_flashdata('error', 'Tanggal dan Alasan wajib diisi.');
            redirect('mobile/koreksi_absen');
        }

        // Cek apakah sudah ada pengajuan pending di tanggal yang sama
        $exist = $this->db->get_where('tbl_pengajuan_koreksi_absen', [
            'karyawan_id' => $this->karyawan_id,
            'tanggal_absen' => $tanggal,
            'status' => 'Pending'
        ])->num_rows();

        if ($exist > 0) {
            $this->session->set_flashdata('error', 'Anda sudah memiliki pengajuan koreksi yang sedang diproses untuk tanggal tersebut.');
            redirect('mobile/koreksi_absen');
        }

        $data_insert = [
            'karyawan_id' => $this->karyawan_id,
            'tanggal_absen' => $tanggal,
            'jam_masuk_baru' => !empty($jam_masuk_baru) ? $jam_masuk_baru : null,
            'jam_pulang_baru' => !empty($jam_pulang_baru) ? $jam_pulang_baru : null,
            'alasan' => $alasan,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('tbl_pengajuan_koreksi_absen', $data_insert)) {
            $this->session->set_flashdata('success', 'Pengajuan Koreksi Absen berhasil dikirim dan menunggu persetujuan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengirim pengajuan.');
        }

        redirect('mobile/koreksi_absen');
    }

    public function cuti()
    {
        $data = $this->_get_common_data('Pengajuan Cuti');
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        $karyawan_data = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $this->karyawan_id])->row();

        // Hitung masa kerja
        $join_date = new DateTime($karyawan_data->tanggal_masuk);
        $now = new DateTime();
        $interval = $join_date->diff($now);
        $months_worked = ($interval->y * 12) + $interval->m;

        $year = date('Y');

        // --- Integrated Quota Table ---
        $quota_row = $this->db->get_where('tbl_quota_cuti', [
            'karyawan_id' => $this->karyawan_id,
            'tahun' => $year
        ])->row();

        if (!$quota_row) {
            $quota_init = [
                'karyawan_id' => $this->karyawan_id,
                'tahun' => $year,
                'quota_awal' => 12,
                'terpakai' => 0,
                'sisa' => 12
            ];
            $this->db->insert('tbl_quota_cuti', $quota_init);
            $quota_total = 12;
        } else {
            // Fix: Jika di table terlanjur 0, beri default 12
            $quota_total = ($quota_row->quota_awal > 0) ? $quota_row->quota_awal : 12;
        }

        // Logic Hitung Sisa Cuti & Limit (Robust Case-Insensitive)
        $requests = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where('YEAR(tanggal_mulai)', $year)
            ->where_in('status', ['Pending', 'Approved', 'Disetujui', 'approved', 'disetujui'])
            ->get('tbl_pengajuan_izin')->result();

        $used_annual_approved = 0;
        $used_annual_pending = 0;

        $limits = [
            'cuti menikah' => ($quota_row && isset($quota_row->menikah_awal)) ? $quota_row->menikah_awal : 7,
            'cuti duka' => ($quota_row && isset($quota_row->duka_awal)) ? $quota_row->duka_awal : 3,
            'cuti keluarga sakit' => ($quota_row && isset($quota_row->sakitkel_awal)) ? $quota_row->sakitkel_awal : 2,
            'cuti melahirkan suami' => ($quota_row && isset($quota_row->melahirkansuami_awal)) ? $quota_row->melahirkansuami_awal : 3,
            'cuti melahirkan' => ($quota_row && isset($quota_row->melahirkan_awal)) ? $quota_row->melahirkan_awal : 90
        ];

        $usage_special_approved = ['cuti menikah' => 0, 'cuti duka' => 0, 'cuti keluarga sakit' => 0, 'cuti melahirkan suami' => 0, 'cuti melahirkan' => 0];
        $usage_special_pending = ['cuti menikah' => 0, 'cuti duka' => 0, 'cuti keluarga sakit' => 0, 'cuti melahirkan suami' => 0, 'cuti melahirkan' => 0];

        foreach ($requests as $req) {
            $status_lower = strtolower($req->status);
            $type_lower = strtolower($req->tipe_izin);
            $is_approved = in_array($status_lower, ['approved', 'disetujui']);

            if ($type_lower == 'cuti' || $type_lower == 'cuti tahunan') {
                if ($is_approved) {
                    $used_annual_approved += $req->durasi_jumlah;
                } else {
                    $used_annual_pending += $req->durasi_jumlah;
                }
            } elseif (array_key_exists($type_lower, $limits)) {
                if ($is_approved) {
                    $usage_special_approved[$type_lower] += $req->durasi_jumlah;
                } else {
                    $usage_special_pending[$type_lower] += $req->durasi_jumlah;
                }
            }
        }

        // Hitung Spillover (Kelebihan cuti khusus yang memotong cuti tahunan)
        $spillover_approved = 0;
        $spillover_pending = 0;

        foreach ($limits as $type => $limit) {
            $total_type_usage = $usage_special_approved[$type] + $usage_special_pending[$type];
            if ($total_type_usage > $limit) {
                // Prioritaskan hitung spillover dari yang sudah approved, sisanya dari pending
                $approved_usage = $usage_special_approved[$type];
                if ($approved_usage > $limit) {
                    $spillover_approved += ($approved_usage - $limit);
                    $spillover_pending += $usage_special_pending[$type];
                } else {
                    $spillover_pending += ($total_type_usage - $limit);
                }
            }
        }

        $final_used_annual = $used_annual_approved + $used_annual_pending + $spillover_approved + $spillover_pending;
        $sisa_quota = max(0, $quota_total - $final_used_annual);

        // Calculate specific usages, bounded by limits (spillover went to annual)
        $menikah_used = min($limits['cuti menikah'], $usage_special_approved['cuti menikah'] + $usage_special_pending['cuti menikah']);
        $duka_used = min($limits['cuti duka'], $usage_special_approved['cuti duka'] + $usage_special_pending['cuti duka']);
        $sakitkel_used = min($limits['cuti keluarga sakit'], $usage_special_approved['cuti keluarga sakit'] + $usage_special_pending['cuti keluarga sakit']);
        $melahirkansuami_used = min($limits['cuti melahirkan suami'], $usage_special_approved['cuti melahirkan suami'] + $usage_special_pending['cuti melahirkan suami']);
        $melahirkan_used = min($limits['cuti melahirkan'], $usage_special_approved['cuti melahirkan'] + $usage_special_pending['cuti melahirkan']);

        // Update table to stay in sync
        $this->db->where(['karyawan_id' => $this->karyawan_id, 'tahun' => $year])
            ->update('tbl_quota_cuti', [
                'quota_awal' => $quota_total,
                'terpakai' => $final_used_annual,
                'sisa' => $sisa_quota,
                'menikah_terpakai' => $menikah_used,
                'menikah_sisa' => max(0, $limits['cuti menikah'] - $menikah_used),
                'duka_terpakai' => $duka_used,
                'duka_sisa' => max(0, $limits['cuti duka'] - $duka_used),
                'sakitkel_terpakai' => $sakitkel_used,
                'sakitkel_sisa' => max(0, $limits['cuti keluarga sakit'] - $sakitkel_used),
                'melahirkansuami_terpakai' => $melahirkansuami_used,
                'melahirkansuami_sisa' => max(0, $limits['cuti melahirkan suami'] - $melahirkansuami_used),
                'melahirkan_terpakai' => $melahirkan_used,
                'melahirkan_sisa' => max(0, $limits['cuti melahirkan'] - $melahirkan_used)
            ]);

        // Fetch History (All types for current year)
        $history = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where('YEAR(tanggal_mulai)', $year)
            ->order_by('created_at', 'DESC')
            ->get('tbl_pengajuan_izin')->result();

        $eligible_tahunan = ($months_worked >= 12); // Minimal 12 bulan masa kerja

        $data['karyawan'] = $karyawan_data;
        $data['eligible_tahunan'] = $eligible_tahunan;
        $data['months_worked'] = $months_worked;
        $data['quota_total'] = $quota_total;
        $data['used_annual'] = $final_used_annual;
        $data['used_approved'] = $used_annual_approved + $spillover_approved;
        $data['used_pending'] = $used_annual_pending + $spillover_pending;
        $data['sisa_quota'] = $sisa_quota;
        $data['menikah_sisa'] = max(0, $limits['cuti menikah'] - $menikah_used);
        $data['duka_sisa'] = max(0, $limits['cuti duka'] - $duka_used);
        $data['sakitkel_sisa'] = max(0, $limits['cuti keluarga sakit'] - $sakitkel_used);
        $data['melahirkansuami_sisa'] = max(0, $limits['cuti melahirkan suami'] - $melahirkansuami_used);
        $data['melahirkan_sisa'] = max(0, $limits['cuti melahirkan'] - $melahirkan_used);
        $data['history'] = $history;
        $data['limits'] = $limits;
        $data['usage_special_approved'] = $usage_special_approved;
        $data['usage_special_pending'] = $usage_special_pending;

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/cuti', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function cuti_save()
    {
        $jumlah_hari = $this->input->post('jumlah_hari', true);
        $tanggal_mulai = $this->input->post('tanggal_mulai', true);
        $tipe_cuti = $this->input->post('tipe_cuti', true);
        $keperluan = $this->input->post('keperluan', true);

        if (!$jumlah_hari || !$tanggal_mulai || !$keperluan || !$tipe_cuti) {
            $this->session->set_flashdata('error', 'Data tidak lengkap!');
            redirect('mobile/cuti');
        }

        // --- Prevent Double Submission ---
        $this->db->where('karyawan_id', $this->karyawan_id);
        $this->db->where('tanggal_mulai', $tanggal_mulai);
        $this->db->where('tipe_izin', $tipe_cuti);
        $this->db->where_in('status', ['Pending', 'Approved', 'Disetujui']);
        $check_duplicate = $this->db->get('tbl_pengajuan_izin')->num_rows();

        if ($check_duplicate > 0) {
            $this->session->set_flashdata('error', 'Anda sudah pernah mengajukan permohonan yang sama untuk tanggal ini (Status: Pending/Disetujui).');
            redirect('mobile/cuti');
        }

        $karyawan_data = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $this->karyawan_id])->row();
        $months_worked = (new DateTime($karyawan_data->tanggal_masuk))->diff(new DateTime())->y * 12 + (new DateTime($karyawan_data->tanggal_masuk))->diff(new DateTime())->m;

        // --- Recalculate Quota Limits (Shared Logic) ---
        // Validate Division Leave Collision
        $karyawan_divisi = $karyawan_data->divisi_id;
        $tanggal_selesai = date('Y-m-d', strtotime($tanggal_mulai . ' + ' . ($jumlah_hari - 1) . ' days'));

        $collision_check = $this->db->select('p.*, k.nama_lengkap')
            ->from('tbl_pengajuan_izin p')
            ->join('tbl_karyawan k', 'k.karyawan_id = p.karyawan_id')
            ->where('k.divisi_id', $karyawan_divisi)
            ->where('p.karyawan_id !=', $this->karyawan_id) // Exclude self
            ->where_in('p.status', ['Pending', 'Disetujui'])
            ->group_start()
            ->where("'$tanggal_mulai' BETWEEN p.tanggal_mulai AND DATE_ADD(p.tanggal_mulai, INTERVAL (p.durasi_jumlah - 1) DAY)")
            ->or_where("'$tanggal_selesai' BETWEEN p.tanggal_mulai AND DATE_ADD(p.tanggal_mulai, INTERVAL (p.durasi_jumlah - 1) DAY)")
            ->or_where("p.tanggal_mulai BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'")
            ->group_end()
            ->count_all_results();

        if ($collision_check > 0) {
            $this->session->set_flashdata('error', 'Maaf, sudah ada karyawan lain dalam satu divisi yang mengajukan cuti pada tanggal tersebut. Tidak boleh cuti bersamaan dalam satu divisi.');
            redirect('mobile/cuti');
        }

        $year = date('Y', strtotime($tanggal_mulai));

        // Fetch current quota from table
        $quota_row = $this->db->get_where('tbl_quota_cuti', ['karyawan_id' => $this->karyawan_id, 'tahun' => $year])->row();
        $quota_total = $quota_row ? $quota_row->quota_awal : 12;

        $requests = $this->db->where('karyawan_id', $this->karyawan_id)
            ->where('YEAR(tanggal_mulai)', $year)
            ->where_in('status', ['Pending', 'Approved', 'Disetujui'])->get('tbl_pengajuan_izin')->result();

        $used_annual = 0;
        $limits = [
            'cuti menikah' => ($quota_row && isset($quota_row->menikah_awal)) ? $quota_row->menikah_awal : 7,
            'cuti duka' => ($quota_row && isset($quota_row->duka_awal)) ? $quota_row->duka_awal : 3,
            'cuti keluarga sakit' => ($quota_row && isset($quota_row->sakitkel_awal)) ? $quota_row->sakitkel_awal : 2,
            'cuti melahirkan suami' => ($quota_row && isset($quota_row->melahirkansuami_awal)) ? $quota_row->melahirkansuami_awal : 3,
            'cuti melahirkan' => ($quota_row && isset($quota_row->melahirkan_awal)) ? $quota_row->melahirkan_awal : 90
        ];
        $usage_special = ['cuti menikah' => 0, 'cuti duka' => 0, 'cuti keluarga sakit' => 0, 'cuti melahirkan suami' => 0, 'cuti melahirkan' => 0];

        foreach ($requests as $req) {
            if ($req->tipe_izin == 'cuti')
                $used_annual += $req->durasi_jumlah;
            elseif (array_key_exists($req->tipe_izin, $limits))
                $usage_special[$req->tipe_izin] += $req->durasi_jumlah;
        }

        $spillover = 0;
        foreach ($limits as $type => $limit) {
            if ($usage_special[$type] > $limit)
                $spillover += ($usage_special[$type] - $limit);
        }
        $current_sisa_annual = $quota_total - ($used_annual + $spillover);

        // --- Validation ---
        if ($tipe_cuti == 'cuti') {
            if ($months_worked < 12) {
                $this->session->set_flashdata('error', 'Masa kerja belum cukup untuk Cuti Tahunan (Min 12 Bulan)!');
                redirect('mobile/cuti');
            }
            if ($jumlah_hari > $current_sisa_annual) {
                $this->session->set_flashdata('error', 'Sisa kuota Cuti Tahunan tidak mencukupi!');
                redirect('mobile/cuti');
            }
        } else {
            // Check Spillover
            $current_usage = $usage_special[$tipe_cuti] ?? 0;
            $limit = $limits[$tipe_cuti] ?? 0;

            if (($current_usage + $jumlah_hari) > $limit) {
                $sisa_jatah = max(0, $limit - $current_usage);
                $this->session->set_flashdata('error', "Maaf, pengajuan ditolak! Jatah $tipe_cuti Anda hanya tersisa $sisa_jatah hari.");
                redirect('mobile/cuti');
                return;
            }
        }

        // Handle File Upload
        $dokumen = '';
        if (!empty($_FILES['dokumen']['name'])) {
            $upload_path = FCPATH . 'assets/uploads/cuti/';

            // Ensure directory exists before loading library
            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    // Just proceed without document
                    $dokumen = '';
                }
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'pdf|jpg|jpeg|png|webp|heic|heif';
            $config['max_size'] = 10240; // 10MB for modern phones
            $config['file_name'] = 'cuti_' . $this->karyawan_id . '_' . date('YmdHis');
            $config['overwrite'] = TRUE;

            $this->load->library('upload', $config);
            // Re-initialize in case library was already loaded
            $this->upload->initialize($config);

            if ($this->upload->do_upload('dokumen')) {
                $upload_data = $this->upload->data();
                $dokumen = $upload_data['file_name'];
            } else {
                // If upload fails, just leave dokumen empty and proceed
                $dokumen = '';
                // Optional: log or set flashdata warning, but don't abort
            }
        }

        // Insert
        $data_pengajuan = [
            'karyawan_id' => $this->karyawan_id,
            'tanggal_mulai' => $tanggal_mulai,
            'durasi_jumlah' => $jumlah_hari,
            'durasi_tipe' => 'hari',
            'tipe_izin' => $tipe_cuti,
            'keterangan' => $keperluan,
            'dokumen' => $dokumen, // Save filename
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('tbl_pengajuan_izin', $data_pengajuan)) {
            broadcast_notifikasi("Pengajuan Izin Baru", "Karyawan " . $karyawan_data->nama_lengkap . " mengajukan " . $tipe_cuti, "absensi/approval_cuti");
            /** @var CI_DB_query_builder $db */
            $db = $this->db;
            $this->log_activity($this->user_id, 'Pengajuan_Cuti', 'Karyawan', $db->insert_id());

            // --- FIREBASE NOTIFICATION TRIGGER (SIMULASI) ---
            // Disini tempat Anda menaruh kode CURL Firebase nanti
            // ...

            $this->session->set_flashdata('success', 'Pengajuan Berhasil Disimpan & Menunggu Approval.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data.');
        }

        redirect('mobile/cuti');
    }

    public function kinerja()
    {
        $data = $this->_get_common_data('Kinerja Saya');
        // Avoid overwriting array with string if head/footer expect array
        $data['nama_perusahaan'] = $data['perusahaan']['nama_perusahaan'] ?? '';

        $bulan = $this->input->get('bulan', true) ?: date('Y-m');
        $data['bulan'] = $bulan;

        // Get Attendance Based Performance Data (Matching HR Menu - Payroll Range)
        $this->load->model('Payroll_model');
        $range = $this->Payroll_model->_get_payroll_range($bulan);
        $perf = $this->Payroll_model->get_performance_data($bulan, $this->karyawan_id, '', $range['start'], $range['end']);
        $data['performance'] = !empty($perf) ? $perf[0] : null;

        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime('-29 days'));

        $data['skor_kinerja_personal'] = $data['performance'] ? $data['performance']->skor_kinerja : 0;

        $data['chart_data'] = $this->db->select('DATE(timestamp) as tanggal, COUNT(*) as total_aktivitas')
            ->where('user_id', $this->user_id)
            ->where('DATE(timestamp) >=', $start_date)
            ->where('DATE(timestamp) <=', $end_date)
            ->group_by('DATE(timestamp)')
            ->order_by('tanggal', 'ASC')
            ->get('user_activity_log')->result();

        // Stats for cards
        $data['total_hadir'] = $data['performance'] ? $data['performance']->total_hadir : 0;
        $data['total_telat'] = $data['performance'] ? $data['performance']->total_telat : 0;
        $data['total_alpha'] = $data['performance'] ? $data['performance']->total_alpha : 0;
        $data['total_izin_sakit'] = $data['performance'] ? ($data['performance']->total_izin + $data['performance']->total_sakit) : 0;

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/kinerja', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function slip_gaji()
    {
        $data = $this->_get_common_data('Slip Gaji Karyawan');
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];

        $bulan = $this->input->get('bulan', true) ?: date('Y-m');
        $data['bulan_filter'] = $bulan;

        // Fetch Comprehensive Salary Data (Real-time Calculation)
        $this->load->model('Payroll_model');
        $salary_data = $this->Payroll_model->get_salary_data($bulan, $this->karyawan_id);
        $data['slip_gaji'] = $salary_data[0] ?? null;

        // History (Last 6 Months)
        $data['riwayat_slip'] = array();
        for ($i = 0; $i < 6; $i++) {
            $prev_bulan = date('Y-m', strtotime("-$i months"));
            $prev_data = $this->Payroll_model->get_salary_data($prev_bulan, $this->karyawan_id);
            if (!empty($prev_data)) {
                $data['riwayat_slip'][] = $prev_data[0];
            }
        }

        // Summary Stats (YTD)
        $current_year = date('Y');
        $total_year = 0;
        $count_year = 0;
        for ($m = 1; $m <= 12; $m++) {
            $m_str = $current_year . '-' . sprintf('%02d', $m);
            if ($m_str > date('Y-m'))
                break;

            $m_data = $this->Payroll_model->get_salary_data($m_str, $this->karyawan_id);
            if (!empty($m_data)) {
                $total_year += $m_data[0]['total_gaji'];
                $count_year++;
            }
        }
        $data['total_gaji_tahun'] = $total_year;
        $data['avg_gaji_bulan'] = $count_year > 0 ? $total_year / $count_year : 0;

        $data['karyawan_terpilih'] = $this->db->select('k.*, j.nama_jabatan, o.operator_nama as divisi_nama')
            ->from('tbl_karyawan k')
            ->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left')
            ->join('tbl_operator o', 'o.operator_id = k.divisi_id', 'left')
            ->where('k.karyawan_id', $this->karyawan_id)
            ->get()->row_array();
        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/slip_gaji', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    public function sdm_slip_pdf($id)
    {
        $bulan = $this->input->get('bulan', true) ?: date('Y-m');
        $this->load->model('Payroll_model');
        $salary_data = $this->Payroll_model->get_salary_data($bulan, $id);

        if (empty($salary_data)) {
            show_error('Data gaji tidak ditemukan', 404);
            return;
        }

        $data['slip'] = $salary_data[0];
        $data['bulan'] = $bulan;
        $data['payroll_range'] = $this->Payroll_model->_get_payroll_range($bulan);
        $data['perusahaan'] = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $this->session->userdata('perusahaan_id') ?: 1])->row();

        // Use the premium single slip view from Admin
        $this->load->view('admin/karyawan/slip_gaji_single', $data);
    }

    public function inventory()
    {
        $data = $this->_get_common_data('Inventory');
        $data['perusahaan'] = $data['perusahaan']['nama_perusahaan'];
        $data['tampildata'] = $this->M_gudang->getGudangBahan()->result();

        $this->load->view('admin/mobile/head', $data);
        $this->load->view('admin/mobile/inventory', $data);
        $this->load->view('admin/mobile/footer', $data);
    }

    // --- PRIVATE UTILS FOR KINERJA ---

    private function calculate_total_activities($id, $start, $end)
    {
        return $this->db->where('user_id', $id)->where('DATE(timestamp) >=', $start)->where('DATE(timestamp) <=', $end)->count_all_results('user_activity_log');
    }

    private function calculate_working_days($id, $start, $end)
    {
        return $this->db->where('karyawan_id', $id)
            ->where('tanggal >=', $start)->where('tanggal <=', $end)
            ->where('jam_masuk IS NOT NULL AND jam_masuk != "00:00:00"', null, false)
            ->count_all_results('absensi');
    }

    private function calculate_average_activities($id, $start, $end)
    {
        $total = $this->calculate_total_activities($id, $start, $end);
        $days = $this->calculate_working_days($id, $start, $end);
        return $days > 0 ? $total / $days : 0;
    }

    private function calculate_performance_days($id, $start, $end, $level)
    {
        $daily = $this->db->select('DATE(timestamp) as tanggal, COUNT(*) as total')
            ->where('user_id', $id)->where('DATE(timestamp) >=', $start)->where('DATE(timestamp) <=', $end)
            ->group_by('DATE(timestamp)')->get('user_activity_log')->result();

        $count = 0;
        foreach ($daily as $d) {
            $n = (int) $d->total;
            if ($level == 'excellent' && $n >= 10)
                $count++;
            if ($level == 'good' && $n >= 7 && $n <= 9)
                $count++;
            if ($level == 'fair' && $n >= 4 && $n <= 6)
                $count++;
            if ($level == 'poor' && $n < 4)
                $count++;
        }
        return $count;
    }

    private function generate_pagination_links($page, $total_pages, $q = '')
    {
        $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
        $range = 2;

        if ($page > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . base_url('mobile/product?page=' . ($page - 1) . '&q=' . urlencode($q)) . '">&laquo;</a></li>';
        }

        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == 1 || $i == $total_pages || ($i >= $page - $range && $i <= $page + $range)) {
                $active = ($i == $page) ? 'active' : '';
                $link = $active ? '<span class="page-link">' . $i . '</span>' : '<a class="page-link" href="' . base_url('mobile/product?page=' . $i . '&q=' . urlencode($q)) . '">' . $i . '</a>';
                $html .= '<li class="page-item ' . $active . '">' . $link . '</li>';
            } elseif ($i == $page - $range - 1 || $i == $page + $range + 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        if ($page < $total_pages) {
            $html .= '<li class="page-item"><a class="page-link" href="' . base_url('mobile/product?page=' . ($page + 1) . '&q=' . urlencode($q)) . '">&raquo;</a></li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }

    public function log_activity($user_id, $action, $module, $reference_id = null)
    {
        // Insert into new Audit Log Table
        $user = $this->db->get_where('users', ['user_id' => $user_id])->row();
        $username = $user ? $user->username : 'System';

        $data = [
            'user_id' => $user_id,
            'username' => $username,
            'action_type' => $action, // e.g., DELETE, UPDATE, LOGIN
            'description' => $module, // e.g., "Deleted Transaction #123"
            'reference_id' => $reference_id,
            'ip_address' => $this->input->ip_address()
        ];
        $this->db->insert('system_audit_logs', $data);
    }

    public function action_reset_password($karyawan_id)
    {
        // Security check
        $admin_level = (int) $this->session->userdata('level');
        if (!in_array($admin_level, [1, 17])) {
            $this->session->set_flashdata('error', 'Akses ditolak. Anda bukan Administrator.');
            redirect('mobile/sdm');
            return;
        }

        // Find user associated with this employee
        $user = $this->db->get_where('users', ['karyawan_id' => $karyawan_id])->row();
        if (!$user) {
            $this->session->set_flashdata('error', 'Data akun user tidak ditemukan untuk karyawan ini.');
            redirect('mobile/sdm_detail/' . $karyawan_id);
            return;
        }

        // Default password from USER_REQUEST requirements
        $new_password = password_hash('erptgm2026', PASSWORD_DEFAULT);

        $this->db->where('user_id', $user->user_id);
        if ($this->db->update('users', ['password' => $new_password])) {
            $this->session->set_flashdata('success', 'Password berhasil direset ke default (erptgm2026).');
        } else {
            $this->session->set_flashdata('error', 'Gagal mereset password.');
        }

        redirect('mobile/sdm_detail/' . $karyawan_id);
    }

    private function _upload_base64($base64_image, $prefix)
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type_match)) {
                $data = substr($base64_image, strpos($base64_image, ',') + 1);
                $ext = strtolower($type_match[1]); // jpeg, png, etc
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                    $ext = 'jpg';
                $data = base64_decode($data);
                if ($data === false) {
                    $this->session->set_flashdata('error_detail', 'Base64 decode failed.');
                    return '';
                }
            } else {
                $this->session->set_flashdata('error_detail', 'Format preg_match gagal. Data awal: ' . substr($base64_image, 0, 50));
                return '';
            }

            $filename = $prefix . '_' . ($this->karyawan_id ?? 'unknown') . '_' . time() . '.' . $ext;
            $relative_path = 'assets/images/lembur/' . $filename;
            $absolute_path = rtrim(FCPATH, '/') . '/' . ltrim($relative_path, '/');

            if (!is_dir(dirname($absolute_path))) {
                if (!mkdir(dirname($absolute_path), 0777, true)) {
                    $this->session->set_flashdata('error_detail', 'Gagal mkdir di: ' . dirname($absolute_path));
                    return '';
                }
            }

            $bytes = file_put_contents($absolute_path, $data);
            if ($bytes !== false && $bytes > 0) {
                return $filename; // Return just the filename for database
            } else {
                $error = error_get_last();
                $this->session->set_flashdata('error_detail', 'file_put_contents gagal: ' . ($error['message'] ?? 'Unknown') . ' | Path: ' . $absolute_path);
                return '';
            }
        } catch (Exception $e) {
            log_message('error', 'Base64 Upload Error: ' . $e->getMessage());
            $this->session->set_flashdata('error_detail', 'Exception: ' . $e->getMessage());
        }
        return '';
    }

    public function aksi_pengajuan_kontrak()
    {

        $durasi = (int) $this->input->post('durasi', true);
        $alasan = $this->input->post('alasan', true);

        if (empty($durasi) || empty($alasan)) {
            echo json_encode(['success' => false, 'message' => 'Durasi dan alasan pengajuan wajib diisi']);
            return;
        }

        // Cek data karyawan
        $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $this->karyawan_id])->row();
        if (!$karyawan || $karyawan->status_karyawan === 'Tetap') {
            echo json_encode(['success' => false, 'message' => 'Anda tidak memenuhi syarat untuk mengajukan perpanjangan kontrak']);
            return;
        }

        // Pastikan ada sisa kontrak <= 30 hari atau sudah lewat
        if (empty($karyawan->akhir_kontrak)) {
            echo json_encode(['success' => false, 'message' => 'Data akhir kontrak Anda tidak valid']);
            return;
        }

        $akhir_kontrak = new DateTime($karyawan->akhir_kontrak);
        $sekarang = new DateTime(date('Y-m-d'));
        $interval = $sekarang->diff($akhir_kontrak);
        $sisa_hari = $interval->days;
        if ($sekarang > $akhir_kontrak) {
            $sisa_hari = -$sisa_hari;
        }

        if ($sisa_hari > 30) {
            echo json_encode(['success' => false, 'message' => 'Pengajuan perpanjangan kontrak hanya dapat dilakukan maksimal 30 hari sebelum kontrak berakhir']);
            return;
        }

        // Cek apakah sudah ada pengajuan pending
        $check_pending = $this->db->get_where('tbl_pengajuan_kontrak', [
            'karyawan_id' => $this->karyawan_id,
            'status' => 'Pending'
        ])->num_rows();

        if ($check_pending > 0) {
            echo json_encode(['success' => false, 'message' => 'Anda sudah memiliki pengajuan perpanjangan kontrak yang berstatus Pending']);
            return;
        }

        // Insert
        $data_insert = [
            'karyawan_id' => $this->karyawan_id,
            'tanggal_pengajuan' => date('Y-m-d'),
            'durasi_perpanjangan' => $durasi,
            'alasan' => $alasan,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('tbl_pengajuan_kontrak', $data_insert)) {
            /** @var CI_DB_query_builder $db */
            $db = $this->db;
            $insert_id = $db->insert_id();

            // Log activity
            $this->log_activity($this->user_id, 'Pengajuan_Kontrak', 'Karyawan', $insert_id);

            echo json_encode(['success' => true, 'message' => 'Pengajuan perpanjangan kontrak berhasil dikirim dan sedang diproses']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data pengajuan']);
        }
    }
}
