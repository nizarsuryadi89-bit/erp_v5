<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_User_agent $agent
 * @property Absensi_model $Absensi_model
 * @property M_master $M_master
 * @property CI_Form_validation $form_validation
 */
class Absensi extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Absensi_model');
        $this->load->model('M_master');
        $this->load->model('Payroll_model');
        $this->load->helper('whatsapp');
        date_default_timezone_set('Asia/Jakarta');
        $this->load->library('form_validation');
        $this->load->library('user_agent');
    }

    public function index()
    {
        $table = 'tbl_product';
        $controller = $this->uri->segment(1);
        $userid = $this->session->userdata('id');

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        // Get filter parameters
        $tanggal = $this->input->get('tanggal', true) ? $this->input->get('tanggal', true) : date('Y-m-d');
        $status = $this->input->get('status', true) ? $this->input->get('status', true) : '';
        $karyawan_id = $this->input->get('karyawan_id', true) ? $this->input->get('karyawan_id', true) : '';

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Daftar Absensi Karyawan',
            'table' => 'tbl_kategori',
            'perusahaan' => ($conf['perusahaan']['nama_perusahaan'] ?? ''),
            'hakakses' => ($conf['hakakses']['divisi_nama'] ?? ''),
            'logo' => ($conf['perusahaan']['logo'] ?? ''),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($controller)->result(),
            'list_bahan' => $this->M_master->getDataBahan()->result(),
            'tanggal' => $tanggal,
            'status' => $status,
            'karyawan_id' => $karyawan_id,
        );

        // Get filtered attendance data
        if (!$this->_valid_date($tanggal)) { show_error('Tanggal tidak valid.', 400); return; }
        $data['absensi'] = $this->Absensi_model->get_filtered_absensi($tanggal, $status, $karyawan_id);

        // Calculate Distance
        $kantor_lat = ($conf['perusahaan']['latitude'] ?? 0);
        $kantor_long = ($conf['perusahaan']['longitude'] ?? 0);
        $data['radius_kantor'] = ($conf['perusahaan']['radius_meter'] ?? 0);

        if (!empty($data['absensi'])) {
            foreach ($data['absensi'] as $key => $absen) {
                $data['absensi'][$key]->jarak_meter = 0;
                $data['absensi'][$key]->lokasi_map_url = '';

                if (!empty($absen->lokasi_absen)) {
                    // Expected format: "Lat: -6.123, Long: 106.123"
                    if (preg_match('/Lat:\s*([-\d\.]+),\s*Long:\s*([-\d\.]+)/', $absen->lokasi_absen, $matches)) {
                        $user_lat = $matches[1];
                        $user_long = $matches[2];

                        // Use Branch Coordinates if available, else fallback to session/main office
                        $target_lat = isset($absen->lat_perusahaan) ? $absen->lat_perusahaan : $kantor_lat;
                        $target_long = isset($absen->long_perusahaan) ? $absen->long_perusahaan : $kantor_long;

                        $data['absensi'][$key]->jarak_meter = $this->calculate_distance($target_lat, $target_long, $user_lat, $user_long);
                        $data['absensi'][$key]->lokasi_map_url = "https://www.google.com/maps/search/?api=1&query={$user_lat},{$user_long}";
                    }
                }
            }
        }

        $data['karyawan_list'] = $this->M_master->get_karyawan()->result();

        // Get attendance statistics
        $data['stats'] = $this->get_attendance_stats($tanggal);
        $data['late_employees_today'] = $this->Absensi_model->get_late_employees($tanggal);


        $this->load_admin_view('admin/absensi/index', $data);

    }

    public function rekap($karyawan_id = null)
    {
        $userid = $this->session->userdata('id');

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $bulan = $this->input->get('bulan', true);
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);

        if (empty($start_date) && empty($end_date)) {
            $company = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $this->session->userdata('perusahaan_id')])->row_array();
            list($start_date, $end_date) = $this->_payroll_range($company['payroll_start_day'] ?? 24);
        }
        if (!$this->_valid_range($start_date, $end_date)) { show_error('Rentang tanggal tidak valid (maksimal 366 hari).', 400); return; }

        if (empty($bulan) && !empty($end_date)) {
            $bulan = date('Y-m', strtotime($end_date));
        }

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Raport Absensi Karyawan',
            'perusahaan' => ($conf['perusahaan']['nama_perusahaan'] ?? ''),
            'hakakses' => ($conf['hakakses']['divisi_nama'] ?? ''),
            'logo' => ($conf['perusahaan']['logo'] ?? ''),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
            'bulan' => $bulan,
            'start_date' => $start_date,
            'end_date' => $end_date,
        );

        if ($karyawan_id) {
            $this->db->select('k.*, j.nama_jabatan, d.divisi_nama');
            $this->db->from('tbl_karyawan k');
            $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
            $this->db->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left');
            $this->db->where('k.karyawan_id', $karyawan_id);
            if ((int)$this->session->userdata('perusahaan_id') !== 1) $this->db->where('k.perusahaan_id', $this->session->userdata('perusahaan_id'));
            $data['karyawan'] = $this->db->get()->row();
            if (!$data['karyawan']) { show_404(); return; }

            // Get attendance data
            $this->db->where('karyawan_id', $karyawan_id);
            $this->db->where('tanggal >=', $start_date);
            $this->db->where('tanggal <=', $end_date);
            $this->db->order_by('tanggal', 'ASC');
            $rekap = $this->db->get('absensi')->result();

            $att_map = [];
            foreach ($rekap as $r) {
                $att_map[$r->tanggal] = $r;
            }
            $data['att_map'] = $att_map;

            // Jam Kerja Map
            $jam_kerja = $this->db->get('tbl_jam_kerja')->result();
            $jk_map = [];
            foreach ($jam_kerja as $jk) {
                $jk_map[$jk->hari] = $jk;
            }
            $data['jk_map'] = $jk_map;

            // Holidays
            $holidays = $this->db->where('tanggal >=', $start_date)
                ->where('tanggal <=', $end_date)
                ->get('hari_libur')->result();
            $holiday_map = [];
            foreach ($holidays as $h) {
                $holiday_map[$h->tanggal] = $h->keterangan;
            }
            $data['holiday_map'] = $holiday_map;

            // Lembur data
            $lembur = $this->db->where('karyawan_id', $karyawan_id)
                ->where('tanggal >=', $start_date)
                ->where('tanggal <=', $end_date)
                ->where('status', 'disetujui')
                ->get('tbl_lembur')->result();
            $lembur_map = [];
            foreach ($lembur as $l) {
                $lembur_map[$l->tanggal][] = $l;
            }
            $data['lembur_map'] = $lembur_map;

            // Generate dates range
            $all_dates = [];
            $curr = strtotime($start_date);
            $last = strtotime($end_date);
            while ($curr <= $last) {
                $all_dates[] = date('Y-m-d', $curr);
                $curr = strtotime('+1 day', $curr);
            }
            $data['dates_range'] = $all_dates;

            // Pre-calculate overtime per hari memakai logika yang sama dengan modul lembur.
            $lembur_calc = [];
            $biaya_makan_lembur_total = 0;
            
            // Check manual meal allowance in tbl_lembur if exists
            $this->db->select_sum('biaya_makan');
            $this->db->where('karyawan_id', $karyawan_id);
            $this->db->where('tanggal >=', $start_date);
            $this->db->where('tanggal <=', $end_date);
            $this->db->where('status', 'disetujui');
            $row_makan = $this->db->get('tbl_lembur')->row();
            $biaya_makan_stored = $row_makan ? (float)$row_makan->biaya_makan : 0;
            
            foreach ($all_dates as $date) {
                $att_day = isset($att_map[$date]) ? $att_map[$date] : null;
                $day_spls = isset($lembur_map[$date]) ? $lembur_map[$date] : [];
                $lembur_calc[$date] = $this->_build_daily_lembur_calc($karyawan_id, $date, $att_day, $day_spls, $jk_map);
                
                // Calculate dynamic meal sum
                $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $schedule = $jk_map[$days[date('w', strtotime($date))]] ?? null;
                $biaya_makan_lembur_total += $this->_meal_allowance($date, $att_day, $schedule);
            }

            $data['biaya_makan_lembur_total'] = $biaya_makan_stored > 0 ? $biaya_makan_stored : $biaya_makan_lembur_total;
            $data['lembur_calc'] = $lembur_calc;

            // Simple stats for header summary
            $stats = ['hadir' => 0, 'telat' => 0, 'alpha' => 0, 'izin' => 0, 'sakit' => 0, 'cuti' => 0];
            $today = date('Y-m-d');
            foreach ($all_dates as $d) {
                $dw = date('w', strtotime($d));
                if ($dw == 0 || isset($holiday_map[$d]))
                    continue;

                if (isset($att_map[$d])) {
                    $s = $att_map[$d]->status;
                    if ($s == 'Hadir') {
                        $stats['hadir']++;
                        $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$dw];
                        $limit = isset($jk_map[$hariIndo]) ? $jk_map[$hariIndo]->jam_masuk : '08:00:00';
                        if (strtotime($att_map[$d]->jam_masuk) > strtotime($limit) + 59)
                            $stats['telat']++;
                    } else if ($s == 'Sakit')
                        $stats['sakit']++;
                    else if ($s == 'Izin')
                        $stats['izin']++;
                    else if (stripos((string)$s, 'cuti') === 0)
                        $stats['cuti']++;
                    else if ($s == 'Alpha')
                        $stats['alpha']++;
                } else if ($d < $today) {
                    $stats['alpha']++;
                }
            }
            $score = 100 - ($stats['telat'] * 2) - ($stats['alpha'] * 5) - ($stats['izin'] * 1) - ($stats['sakit'] * 1) - ($stats['cuti'] * 1);
            $data['score'] = max(0, $score);
            $data['stats'] = $stats;
            $data['predicate'] = ($score >= 90) ? 'ISTIMEWA' : (($score >= 80) ? 'BAIK' : 'CUKUP');

        } else {
            // Index mode - shows summary of all or selection
            $rekap_all = $this->Absensi_model->get_rekap_all_karyawan($start_date, $end_date);
            $data['rekap_all'] = $rekap_all;
            $data['rekap'] = $this->Absensi_model->get_filtered_absensi($start_date, '', '');

            // Calculate global stats for index view
            $global_stats = ['hadir' => 0, 'telat' => 0, 'alpha' => 0, 'izin' => 0, 'sakit' => 0, 'cuti' => 0, 'total' => count($rekap_all)];
            foreach ($rekap_all as $r) {
                $global_stats['hadir'] += $r->total_hadir;
                $global_stats['telat'] += $r->total_terlambat;
                $global_stats['alpha'] += $r->total_alpha;
                $global_stats['izin'] += $r->total_izin;
                $global_stats['sakit'] += $r->total_sakit;
                $global_stats['cuti'] += $r->total_cuti;
            }
            $data['stats'] = $global_stats;
        }

        $this->load_admin_view('admin/absensi/rekap', $data);
    }

    public function update_attendance_manual()
    {
        $this->_update_manual_action('absensi/rekap/' . (int)$this->input->post('karyawan_id', true));
    }

    public function cetak_rekap($karyawan_id = NULL)
    {
        if ($karyawan_id === NULL) {
            show_error('Karyawan ID diperlukan.', 400);
        }
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);

        if (empty($start_date) && empty($end_date)) {
            $company = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $this->session->userdata('perusahaan_id')])->row_array();
            list($start_date, $end_date) = $this->_payroll_range($company['payroll_start_day'] ?? 24);
        }
        if (!$this->_valid_range($start_date, $end_date)) { show_error('Rentang tanggal tidak valid (maksimal 366 hari).', 400); return; }

        $this->db->select('k.*, j.nama_jabatan, d.divisi_nama');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left');
        $this->db->where('k.karyawan_id', $karyawan_id);
        if ((int)$this->session->userdata('perusahaan_id') !== 1) $this->db->where('k.perusahaan_id', $this->session->userdata('perusahaan_id'));
        $karyawan = $this->db->get()->row();

        if (!$karyawan) {
            show_error('Karyawan tidak ditemukan', 404);
        }

        // Get perusahaan info
        $perusahaan_data = $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array();
        $perusahaan_nama = isset($perusahaan_data['nama_perusahaan']) ? $perusahaan_data['nama_perusahaan'] : 'PT. TATA GUNA MANDIRI';

        // Get attendance data
        $this->db->where('karyawan_id', $karyawan_id);
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        $this->db->order_by('tanggal', 'ASC');
        $rekap = $this->db->get('absensi')->result();

        $att_map = [];
        foreach ($rekap as $r) {
            $att_map[$r->tanggal] = $r;
        }

        // Holidays
        $holidays = $this->db->where('tanggal >=', $start_date)
            ->where('tanggal <=', $end_date)
            ->get('hari_libur')->result();
        $holiday_map = [];
        foreach ($holidays as $h) {
            $holiday_map[$h->tanggal] = $h->keterangan;
        }

        // Jam Kerja Map (needed for calculations)
        $jam_kerja = $this->db->get('tbl_jam_kerja')->result();
        $jk_map = [];
        foreach ($jam_kerja as $jk) {
            $jk_map[$jk->hari] = $jk;
        }

        // Generate dates range
        $all_dates = [];
        $curr = strtotime($start_date);
        $last = strtotime($end_date);
        while ($curr <= $last) {
            $all_dates[] = date('Y-m-d', $curr);
            $curr = strtotime('+1 day', $curr);
        }

        // Lembur data (SPL)
        $lembur = $this->db->where('karyawan_id', $karyawan_id)
            ->where('tanggal >=', $start_date)
            ->where('tanggal <=', $end_date)
            ->where('status', 'disetujui')
            ->get('tbl_lembur')->result();
        $lembur_map = [];
        foreach ($lembur as $l) {
            $lembur_map[$l->tanggal][] = $l; // Changed to array to match rekap logic
        }

        // Pre-calculate overtime per hari memakai helper yang sama dengan halaman rekap.
        $lembur_calc = [];
        foreach ($all_dates as $date) {
            $att_day = isset($att_map[$date]) ? $att_map[$date] : null;
            $day_spls = isset($lembur_map[$date]) ? $lembur_map[$date] : [];
            $lembur_calc[$date] = $this->_build_daily_lembur_calc($karyawan_id, $date, $att_day, $day_spls, $jk_map);
        }

        $data = [
            'karyawan' => $karyawan,
            'att_map' => $att_map,
            'jk_map' => $jk_map,
            'holiday_map' => $holiday_map,
            'lembur_map' => $lembur_map,
            'lembur_calc' => $lembur_calc,
            'dates_range' => $all_dates,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'perusahaan' => $perusahaan_nama
        ];

        $this->load->view('admin/absensi/cetak_rekap', $data);
    }

    public function absen_masuk()
    {
        $this->_self_attendance(false);
    }

    public function izin_absen()
    {
        $userid = $this->session->userdata('id');
        $module = 'Karyawan';
        $action = 'Izin_Absen';
        $ref = '1';

        // Validasi session
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Anda harus login terlebih dahulu!');
            redirect('auth');
        }

        // Validasi input
        $this->form_validation->set_rules('karyawan_id', 'Karyawan ID', 'required|numeric');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[izin,sakit,cuti,pulang_cepat,izin_telat]');
        $this->form_validation->set_rules('durasi_tipe', 'Tipe Durasi', 'required|in_list[jam,hari]');
        if (!in_array($this->input->post('status', true), ['pulang_cepat', 'izin_telat'], true)) {
            $this->form_validation->set_rules('durasi_jumlah', 'Jumlah Durasi', 'required|integer|greater_than[0]');
        }
        $this->form_validation->set_rules('kegiatan', 'Alasan', 'max_length[500]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('mobile/sakit');
        }

        // Ambil data dari form
        $karyawan_id = $this->input->post('karyawan_id', true);
        $tanggal = $this->input->post('tanggal', true);
        $status = $this->input->post('status', true);
        $durasi_tipe = $this->input->post('durasi_tipe', true);
        $durasi_jumlah = $this->input->post('durasi_jumlah', true);
        $kegiatan = $this->input->post('kegiatan', true);

        if (!is_scalar($karyawan_id) || !ctype_digit((string)$karyawan_id) || (int)$karyawan_id <= 0
            || (int)$karyawan_id !== (int)$this->session->userdata('karyawan_id')) {
            $this->session->set_flashdata('error', 'Anda hanya dapat mengajukan izin untuk diri sendiri.');
            redirect('mobile/sakit');
        }
        if (!$this->_valid_date($tanggal)) {
            $this->session->set_flashdata('error', 'Tanggal pengajuan tidak valid.');
            redirect('mobile/sakit');
        }

        // Validasi tambahan
        if (strtotime($tanggal) < strtotime(date('Y-m-d'))) {
            $this->session->set_flashdata('error', 'Tanggal izin tidak boleh tanggal lampau!');
            redirect('mobile/sakit');
        }

        // Validasi durasi
        if ($durasi_tipe == 'jam' && $durasi_jumlah > 24) {
            $this->session->set_flashdata('error', 'Durasi jam maksimal 24 jam!');
            redirect('mobile/sakit');
        }

        if ($durasi_tipe == 'hari' && $durasi_jumlah > 30) {
            $this->session->set_flashdata('error', 'Durasi hari maksimal 30 hari!');
            redirect('mobile/sakit');
        }

        // Cek apakah sudah ada absensi di tanggal yang sama
        $existing_absensi = $this->db->get_where('absensi', [
            'karyawan_id' => $karyawan_id,
            'tanggal' => $tanggal
        ])->row();

        if ($existing_absensi && $existing_absensi->status != 'Alpha'
            && !in_array($status, ['pulang_cepat', 'izin_telat'], true)) {
            $this->session->set_flashdata('error', 'Sudah ada absensi untuk tanggal ini!');
            redirect('mobile/sakit');
        }

        // Khusus Pulang Cepat
        $jam_pulang_cepat = null;
        if ($status == 'pulang_cepat') {
            $jam_pulang_cepat = $this->input->post('jam_pulang_cepat', true);
            if (!is_string($jam_pulang_cepat) || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/D', $jam_pulang_cepat)) {
                $this->session->set_flashdata('error', 'Jam rencana pulang wajib diisi untuk Pulang Cepat.');
                redirect('mobile/sakit');
            }

            $current_hour = (int) date('H', strtotime($jam_pulang_cepat));
            if ($current_hour < 14) {
                $this->session->set_flashdata('error', 'Pulang cepat hanya bisa dilakukan mulai dari jam 14.00.');
                redirect('mobile/sakit');
            }

            // Hitung Durasi Otomatis berdasarkan Hari
            $day_of_week = date('w', strtotime($tanggal)); // 0 (Sun) - 6 (Sat)
            $target_time = ($day_of_week == 6) ? '15:00:00' : '16:00:00';

            $start_seconds = strtotime($jam_pulang_cepat);
            $end_seconds = strtotime($target_time);

            if ($start_seconds < $end_seconds) {
                $durasi_jumlah = (int)ceil(($end_seconds - $start_seconds) / 3600);
            } else {
                $durasi_jumlah = 0; // Sudah melebihi jam kerja normal (untuk hitungan ijin jam)
            }
            $durasi_tipe = 'jam';
            $kegiatan .= ' (Rencana Pulang: ' . $jam_pulang_cepat . ')';
        }

        // Khusus Izin Telat
        $jam_rencana_hadir = null;
        if ($status == 'izin_telat') {
            $jam_rencana_hadir = $this->input->post('jam_rencana_hadir', true);
            if (!is_string($jam_rencana_hadir) || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/D', $jam_rencana_hadir)) {
                $this->session->set_flashdata('error', 'Jam rencana hadir wajib diisi untuk Izin Keterlambatan.');
                redirect('mobile/sakit');
            }
            $durasi_tipe = 'jam';
            $durasi_jumlah = 0; // Keterlambatan dihitung otomatis saat absensi aktual
            $kegiatan = $kegiatan . " (Rencana Hadir: {$jam_rencana_hadir})";
        }

        // Handle Upload Dokumen/Surat Sakit/Bukti Telat
        $dokumen_path = '';
        if (in_array($status, ['sakit', 'izin_telat']) && !empty($_FILES['dokumen']['name'])) {
            $upload_res = $this->_upload_dokumen($karyawan_id);
            if (!$upload_res['status']) {
                $this->session->set_flashdata('error', 'Gagal mengunggah dokumen: ' . $upload_res['error'] . ' (Pastikan format dokumen didukung, max 10MB)');
                redirect('mobile/sakit');
            }
            $dokumen_path = $upload_res['path'];
        } elseif ($status == 'izin_telat' && empty($_FILES['dokumen']['name'])) {
            $this->session->set_flashdata('error', 'Bukti foto wajib dilampirkan untuk Izin Keterlambatan.');
            redirect('mobile/sakit');
        }

        // Data untuk insert ke tabel pengajuan izin (pending)
        $data = [
            'karyawan_id' => $karyawan_id,
            'tanggal_mulai' => $tanggal,
            'durasi_tipe' => $durasi_tipe,
            'durasi_jumlah' => $durasi_jumlah,
            // 'jam_pulang_cepat' => $jam_pulang_cepat, // Kolom belum ada di DB
            'tipe_izin' => $status, // izin, sakit, cuti, pulang_cepat
            'keterangan' => $kegiatan,
            'dokumen' => $dokumen_path,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->_require_write($this->db->trans_begin());
            $this->_lock_employee($karyawan_id);
            $this->_require_write($this->db->insert('tbl_pengajuan_izin', $data));
            $this->_sync_quota_cuti($karyawan_id, (int)substr($tanggal, 0, 4));
            $this->_commit_attendance();
            log_activity($userid, $action, $module, $ref);
            $this->session->set_flashdata('success', 'Pengajuan berhasil dikirim dan menunggu persetujuan.');
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $e->getMessage());
        }

        redirect('mobile/sakit');
    }

    public function absen_pulang()
    {
        $this->_self_attendance(true);
    }

    private function _self_attendance($checkout, $from_scan = false)
    {
        if (!$this->session->userdata('logged_in')) { redirect('auth'); return; }
        $photo = ''; $saved = false;
        try {
            $employee = $from_scan ? $this->session->userdata('karyawan_id') : $this->input->post('karyawan_id', true);
            if (!is_scalar($employee) || !ctype_digit((string)$employee) || (int)$employee <= 0
                || (int)$employee !== (int)$this->session->userdata('karyawan_id')) {
                throw new RuntimeException('Anda hanya dapat mencatat absensi untuk diri sendiri.');
            }
            $this->_require_write($this->db->trans_begin());
            $this->_lock_employee($employee);
            $date = date('Y-m-d');
            $existing = $this->db->get_where('absensi', ['karyawan_id' => $employee, 'tanggal' => $date])->row();
            if ($checkout) {
                if (!$existing || $existing->status !== 'Hadir' || empty($existing->jam_masuk) || $existing->jam_masuk === '00:00:00') {
                    throw new RuntimeException('Absen masuk belum tercatat hari ini.');
                }
                if (!empty($existing->jam_pulang) && $existing->jam_pulang !== '00:00:00') throw new RuntimeException('Absen pulang sudah tercatat.');
            } else {
                if ($existing && !in_array($existing->status, ['Alpha', 'Alfa', ''], true)) throw new RuntimeException('Sudah ada absensi hari ini.');
                $overtime = $this->db->get_where('tbl_lembur', ['karyawan_id' => $employee, 'tanggal' => $date, 'jam_selesai' => '00:00:00'])->row();
                if ($overtime) throw new RuntimeException('Selesaikan sesi lembur aktif terlebih dahulu.');
            }
            $image = $this->input->post('foto_selfie', false);
            if ($image !== null && $image !== '') {
                $photo = $this->_upload_selfie($image, $employee, $checkout ? 'pulang' : 'masuk');
                if ($photo === '') throw new RuntimeException('Foto selfie tidak valid atau gagal disimpan (JPG/PNG, max 5MB).');
            }
            $os = $this->agent->platform();
            $device = $this->agent->is_mobile() ? $os . ' (' . $this->agent->mobile() . ')'
                : 'Web (' . $this->agent->browser() . ' on ' . $os . ')';
            $data = ['device_type' => substr($device, 0, 50), 'user_agent' => $this->input->user_agent()];
            if ($checkout) {
                $data['jam_pulang'] = date('H:i:s'); $data['foto_pulang'] = $photo;
            } else {
                $data = array_merge($data, ['karyawan_id' => $employee, 'tanggal' => $date, 'status' => 'Hadir',
                    'jam_masuk' => date('H:i:s'), 'foto_masuk' => $photo, 'lokasi_absen' => $this->input->post('lokasi_absen', true)]);
                $late = $this->db->get_where('tbl_pengajuan_izin', ['karyawan_id' => $employee, 'tanggal_mulai' => $date,
                    'tipe_izin' => 'izin_telat', 'status' => 'Approved'])->row();
                $data['is_izin_telat'] = $late ? 1 : 0;
            }
            if ($existing) $this->_require_write($this->db->update('absensi', $data, ['id' => $existing->id]));
            else $this->_require_write($this->db->insert('absensi', $this->_attendance_defaults($data)));
            $this->_commit_attendance();
            $photo = ''; $saved = true; // Committed photo belongs to attendance.
            $this->session->set_flashdata('success', $checkout ? 'Absen pulang berhasil dicatat!' : 'Absen masuk berhasil dicatat!');
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            if ($photo !== '' && is_file(FCPATH . $photo)) unlink(FCPATH . $photo);
            $this->session->set_flashdata('error', $e->getMessage());
        }
        if ($saved) {
            try { log_activity($this->session->userdata('id'), $checkout ? 'Absen_Pulang' : 'Absen_Masuk', 'Karyawan', $employee); }
            catch (Throwable $e) { log_message('error', 'Audit absensi gagal: ' . $e->getMessage()); }
        }
        redirect('mobile');
    }

    private function _upload_dokumen($karyawan_id)
    {
        $path = FCPATH . 'uploads/dokumen_sakit/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $config['upload_path'] = $path;
        $config['allowed_types'] = 'pdf|jpg|jpeg|png|webp|heic|heif';
        $config['max_size'] = 10240; // 10MB for modern phones
        $config['file_name'] = 'surat_sakit_' . $karyawan_id . '_' . date('YmdHis');

        $this->load->library('upload');
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('dokumen')) {
            $error = $this->upload->display_errors('', '');
            log_message('error', 'Document Upload Error: ' . $error);
            return ['status' => false, 'error' => $error];
        } else {
            $upload_data = $this->upload->data();
            return ['status' => true, 'path' => 'uploads/dokumen_sakit/' . $upload_data['file_name']];
        }
    }

    private function _build_daily_lembur_calc($karyawan_id, $date, $att_day, $day_spls = [], $jk_map = [])
    {
        $res_wajib = $this->Payroll_model->_hitung_jam_lembur($karyawan_id, '00:00', '00:00', $date, $att_day);

        $total_pagi    = 0;
        $total_wajib   = (float)($res_wajib['raw_wajib'] ?? 0);
        $total_kerjaan = 0;

        // FALLBACK: Jika karyawan status Hadir tapi jam_pulang tidak tercatat (00:00:00),
        // auto-wajib tidak bisa dihitung dari jam_pulang. Sesuai aturan:
        // "Karyawan yang hadir dianggap melakukan lembur wajib sesuai kebijakan."
        // → Ambil langsung dari konfigurasi wajib tertinggi sesuai hari.
        if ($total_wajib == 0
            && $att_day
            && isset($att_day->status) && $att_day->status === 'Hadir'
            && (empty($att_day->jam_pulang) || $att_day->jam_pulang === '00:00:00')
        ) {
            $day_num = date('N', strtotime($date));
            $hari_db = ($day_num == 6) ? 'Sabtu' : 'Senin-Jumat';
            $wajib_cfg = $this->db
                ->order_by('total_jam', 'DESC')
                ->get_where('tbl_lembur_jam_config', ['hari' => $hari_db, 'is_wajib' => 1])
                ->row();
            if ($wajib_cfg) {
                $total_wajib = (float)$wajib_cfg->total_jam;
            }
        }

        foreach ($day_spls as $spl) {
            if (($spl->status ?? '') !== 'disetujui') {
                continue;
            }

            $res = $this->Payroll_model->_hitung_jam_lembur(
                $karyawan_id,
                $spl->jam_mulai,
                $spl->jam_selesai,
                $date,
                $att_day
            );

            $total_pagi    += (float)($res['raw_pagi'] ?? 0);
            $total_kerjaan += (float)($res['raw_kerjaan'] ?? 0);

            if ((float)($res['raw_wajib'] ?? 0) > 0) {
                if ($total_wajib > 0) {
                    // Wajib sudah ada → SPL wajib masuk ke kerjaan (tidak double count)
                    $total_kerjaan += (float)$res['raw_wajib'];
                } else {
                    $total_wajib = (float)$res['raw_wajib'];
                }
            }
        }

        // LATE DEDUCTION LOGIC
        $late_deduction = 0;
        if ($att_day && isset($att_day->status) && $att_day->status === 'Hadir' && !empty($att_day->jam_masuk) && $att_day->jam_masuk !== '00:00:00') {
            $day_num = date('w', strtotime($date));
            $daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $hariIndo = $daysIndo[$day_num];
            
            $limit = isset($jk_map[$hariIndo]) ? $jk_map[$hariIndo]->jam_masuk : '08:00:00';
            $masuk_time = strtotime($att_day->jam_masuk);
            $limit_time = strtotime($limit);
            
            if ($masuk_time > $limit_time) {
                $late_minutes = floor(($masuk_time - $limit_time) / 60);
                if ($late_minutes >= 1 && $late_minutes <= 59) {
                    $late_deduction = 1;
                } else if ($late_minutes >= 60) {
                    $late_deduction = 2;
                }
            }
        }

        if ($late_deduction > 0) {
            $to_deduct = $late_deduction;
            
            // Deduct dari SPL dulu
            if ($total_kerjaan >= $to_deduct) {
                $total_kerjaan -= $to_deduct;
                $to_deduct = 0;
            } else {
                $to_deduct -= $total_kerjaan;
                $total_kerjaan = 0;
            }
            
            // Deduct dari Wajib jika masih ada
            if ($to_deduct > 0) {
                if ($total_wajib >= $to_deduct) {
                    $total_wajib -= $to_deduct;
                    $to_deduct = 0;
                } else {
                    $to_deduct -= $total_wajib;
                    $total_wajib = 0;
                }
            }
            
            // Deduct dari Pagi jika masih ada
            if ($to_deduct > 0) {
                if ($total_pagi >= $to_deduct) {
                    $total_pagi -= $to_deduct;
                    $to_deduct = 0;
                } else {
                    $to_deduct -= $total_pagi;
                    $total_pagi = 0;
                }
            }
        }

        return [
            'pagi'    => $total_pagi,
            'wajib'   => $total_wajib,
            'kerjaan' => $total_kerjaan,
            'total'   => $total_pagi + $total_wajib + $total_kerjaan,
        ];
    }

    private function _upload_selfie($base64_image, $karyawan_id, $type)
    {
        if (!is_string($base64_image) || strlen($base64_image) > 7 * 1024 * 1024
            || !is_scalar($karyawan_id) || !ctype_digit((string)$karyawan_id) || (int)$karyawan_id <= 0
            || !in_array($type, ['masuk', 'pulang'], true)
            || !preg_match('/^data:image\/(?:jpeg|jpg|png);base64,/', $base64_image)) return '';
        try {
            $bytes = base64_decode(substr($base64_image, strpos($base64_image, ',') + 1), true);
            if ($bytes === false || strlen($bytes) > 5 * 1024 * 1024) return '';
            $info = @getimagesizefromstring($bytes);
            if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)
                || $info[0] * $info[1] > 25000000) return '';
            $ext = $info[2] === IMAGETYPE_PNG ? 'png' : 'jpg';
            $path = 'uploads/absensi/absen_' . $type . '_' . (int)$karyawan_id . '_' . bin2hex(random_bytes(12)) . '.' . $ext;
            $dir = FCPATH . 'uploads/absensi';
            if (!is_dir($dir) && !mkdir($dir, 0755, true)) return '';
            return file_put_contents(FCPATH . $path, $bytes) === strlen($bytes) ? $path : '';
        } catch (Throwable $e) { log_message('error', 'Selfie upload gagal: ' . $e->getMessage()); }
        return '';
    }

    public function scan()
    {
        if (!$this->session->userdata('logged_in')) { redirect('auth'); return; }
        $nik = $this->input->post('nip_qr', true);
        if (!is_string($nik) || trim($nik) === '') {
            $this->session->set_flashdata('error', 'NIK QR tidak valid.'); redirect('mobile'); return;
        }
        $employee = $this->M_master->get_karyawan_by_nik(trim($nik));
        if (!$employee || (int)$employee->karyawan_id !== (int)$this->session->userdata('karyawan_id')) {
            $this->session->set_flashdata('error', 'QR tidak sesuai dengan karyawan yang login.'); redirect('mobile'); return;
        }
        $this->_self_attendance(false, true);
    }

    public function export_excel()
    {
        $tanggal = $this->input->get('tanggal', true) ? $this->input->get('tanggal', true) : date('Y-m-d');
        $status = $this->input->get('status', true) ? $this->input->get('status', true) : '';
        $karyawan_id = $this->input->get('karyawan_id', true) ? $this->input->get('karyawan_id', true) : '';

        if (!$this->_valid_date($tanggal)) { show_error('Tanggal tidak valid.', 400); return; }
        $data['absensi'] = $this->Absensi_model->get_filtered_absensi($tanggal, $status, $karyawan_id);
        $data['tanggal'] = $tanggal;

        $this->load->view('admin/absensi/export_excel', $data);
    }

    public function export_pdf()
    {
        $tanggal = $this->input->get('tanggal', true) ? $this->input->get('tanggal', true) : date('Y-m-d');
        $status = $this->input->get('status', true) ? $this->input->get('status', true) : '';
        $karyawan_id = $this->input->get('karyawan_id', true) ? $this->input->get('karyawan_id', true) : '';

        if (!$this->_valid_date($tanggal)) { show_error('Tanggal tidak valid.', 400); return; }
        $data['absensi'] = $this->Absensi_model->get_filtered_absensi($tanggal, $status, $karyawan_id);
        $data['tanggal'] = $tanggal;

        $this->load->view('admin/absensi/export_pdf', $data);
    }

    public function export_excel_rekap()
    {
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);

        if (!$this->_valid_range($start_date, $end_date)) { show_error('Rentang tanggal tidak valid.', 400); return; }
        $data['rekap_data'] = $this->Absensi_model->get_rekap_all_karyawan($start_date, $end_date);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $this->load->view('admin/absensi/export_excel_rekap', $data);
    }

    public function export_pdf_rekap()
    {
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);

        if (!$this->_valid_range($start_date, $end_date)) { show_error('Rentang tanggal tidak valid.', 400); return; }
        $data['rekap_data'] = $this->Absensi_model->get_rekap_all_karyawan($start_date, $end_date);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $this->load->view('admin/absensi/export_pdf_rekap', $data);
    }

    private function get_attendance_stats($tanggal)
    {
        $stats = [
            'total' => 0,
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'cuti' => 0,
            'alpha' => 0,
            'terlambat' => 0,
            'on_time' => 0,
            'early' => 0,
            'total_work_hours' => 0,
            'avg_work_hours' => 0
        ];

        // Get total employees (Exclude 'Tidak Aktif' and 'Owner')
        $this->db->from('tbl_karyawan k');
        $this->db->join('users u', 'u.karyawan_id = k.karyawan_id', 'left');
        $this->db->where('k.status_karyawan !=', 'Tidak Aktif');
        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $this->db->group_start();
        $this->db->where('u.role_id !=', 17);
        $this->db->or_where('u.role_id IS NULL');
        $this->db->group_end();
        $this->db->where("k.nama_lengkap NOT LIKE '%Terminal%'");
        $this->db->where("k.nama_lengkap NOT LIKE '%DUMMY%'");
        $this->db->where("k.nama_lengkap NOT LIKE 'DEPT.%'");
        $this->db->where("k.nama_lengkap NOT LIKE 'OPERATOR %'");
        $this->db->where("k.nik NOT LIKE 'STATION-%'");
        $this->db->where("k.nik NOT LIKE 'TERM%'");

        if (!empty($this->session->userdata('perusahaan_id')) && (int)$this->session->userdata('perusahaan_id') !== 1) {
            $this->db->where('k.perusahaan_id', $this->session->userdata('perusahaan_id'));
        }

        $total_employees = $this->db->count_all_results();
        $stats['total'] = $total_employees;

        $attendance_data = $this->Absensi_model->get_attendance_by_date($tanggal);

        // Ambil aturan jam masuk untuk tanggal tersebut
        $daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hariNama = $daysIndo[date('w', strtotime($tanggal))];
        $ruleJam = $this->db->get_where('tbl_jam_kerja', ['hari' => $hariNama])->row_array();
        $jamMasukStandar = !empty($ruleJam['jam_masuk']) ? $ruleJam['jam_masuk'] : '08:00:00';

        // Count explicit records
        foreach ($attendance_data as $att) {
            switch ($att->status) {
                case 'Hadir':
                    $stats['hadir']++;

                    if ($att->jam_masuk) {
                        $arrival_time = strtotime($att->jam_masuk);
                        $standard_time = strtotime($jamMasukStandar);
                        $tolerance_time = $standard_time + 59; // Maksimal 08:00:59, 08:01:00 sudah telat

                        if ($arrival_time > $tolerance_time) {
                            $stats['terlambat']++;
                        } elseif ($arrival_time <= $standard_time) {
                            $stats['early']++;
                        } else {
                            $stats['on_time']++;
                        }

                        // Calculate work hours if both times are available
                        if ($att->jam_pulang && $att->jam_pulang !== '00:00:00') {
                            $departure = strtotime($att->jam_pulang);
                            if ($departure < $arrival_time) $departure += 86400;
                            $work_hours = ($departure - $arrival_time) / 3600;
                            $stats['total_work_hours'] += $work_hours;
                        }
                    }
                    break;
                case 'Izin':
                    // Check if it's Cuti
                    if (stripos((string)$att->keterangan, 'cuti') !== false) {
                        $stats['cuti']++;
                    } else {
                        $stats['izin']++;
                    }
                    break;
                case 'Sakit':
                    $stats['sakit']++;
                    break;
                case 'Cuti':
                case 'Cuti Keluarga':
                case 'Cuti Sakit':
                case 'Cuti Melahirkan':
                case 'Cuti Menikah':
                case 'Cuti Duka':
                case 'Cuti Keluarga Sakit':
                case 'Cuti Melahirkan Suami':
                    $stats['cuti']++;
                    break;
                case 'Alpha':
                default:
                    // If status is Alpha or unknown/empty, try to detect from keterangan (notes)
                    if (stripos((string)$att->keterangan, 'cuti') !== false) {
                        $stats['cuti']++;
                    } elseif (stripos((string)$att->keterangan, 'sakit') !== false) {
                        $stats['sakit']++;
                    } elseif (stripos((string)$att->keterangan, 'izin') !== false) {
                        $stats['izin']++;
                    }
                    break;
            }
        }

        // Calculate Alpha (Implicit + Explicit)
        // Formula: Total - (Hadir + Izin + Sakit + Cuti)
        // Note: Explicit Alpha rows are NOT counted in Hadir/Izin/Sakit/Cuti above, so they are automatically included in the remainder.

        $total_recorded_presence = $stats['hadir'] + $stats['izin'] + $stats['sakit'] + $stats['cuti'];
        $stats['alpha'] = max(0, $stats['total'] - $total_recorded_presence);

        // Calculate average work hours
        if ($stats['hadir'] > 0) {
            $stats['avg_work_hours'] = round($stats['total_work_hours'] / $stats['hadir'], 1);
        }

        return $stats;
    }

    public function delete($id)
    {
        try {
            $this->_require_write($this->db->trans_begin());
            $attendance = $this->db->get_where('absensi', ['id' => $id])->row();
            if (!$attendance) throw new RuntimeException('Absensi tidak ditemukan.');
            $this->_lock_employee($attendance->karyawan_id);
            $this->_require_write($this->db->delete('absensi', ['id' => $id]));
            $this->_delete_hr_leave($attendance->karyawan_id, $attendance->tanggal);
            $this->_sync_quota_cuti($attendance->karyawan_id, (int)substr($attendance->tanggal, 0, 4));
            $this->_commit_attendance(); $this->session->set_flashdata('success', 'Absensi berhasil dihapus.');
        } catch (Throwable $e) { $this->db->trans_rollback(); $this->session->set_flashdata('error', $e->getMessage()); }
        redirect('absensi');
    }

    public function edit($id)
    {
        $userid = $this->session->userdata('id');

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Edit Absensi',
            'perusahaan' => ($conf['perusahaan']['nama_perusahaan'] ?? ''),
            'hakakses' => ($conf['hakakses']['divisi_nama'] ?? ''),
            'logo' => ($conf['perusahaan']['logo'] ?? ''),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        $data['absensi'] = $this->Absensi_model->get_by_id($id);
        $data['karyawan_list'] = $this->M_master->get_karyawan()->result();

        if (empty($data['absensi'])) { show_404(); return; }
        $employee = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $data['absensi']->karyawan_id])->row();
        if (!$employee || ((int)$this->session->userdata('perusahaan_id') !== 1
            && (int)$employee->perusahaan_id !== (int)$this->session->userdata('perusahaan_id'))) { show_404(); return; }

        $this->load->view('layout_admin/head', $data);
        $this->load->view('layout_admin/top_header', $data);
        $this->load->view('layout_admin/sidebar', $data);
        $this->load->view('admin/absensi/edit', $data);
        $this->load->view('layout_admin/footer');
    }

    public function update()
    {
        $this->_update_manual_action('absensi');
    }

    private function _update_manual_action($redirect)
    {
        try {
            $data = $this->_manual_data($this->input->post(null, true), $this->input->post('tanggal', true));
            $this->_require_write($this->db->trans_begin());
            $this->_save_manual_record($data, $this->input->post('id', true));
            $this->_commit_attendance();
            $this->session->set_flashdata('success', 'Absensi dan kuota cuti berhasil diperbarui.');
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect($redirect);
    }

    function rekap_absen_bulanan()
    {
        $userid = $this->session->userdata('id');
        $controller = $this->uri->segment(1);

        // Get filter parameters
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);
        $bulan = $this->input->get('bulan', true);

        if (empty($start_date) && empty($end_date)) {
            if (empty($bulan)) {
                $bulan = date('Y-m');
            }
            if (!is_string($bulan) || !preg_match('/^\d{4}-(?:0[1-9]|1[0-2])$/D', $bulan)) { show_error('Bulan tidak valid.', 400); return; }
            $start_date = $bulan . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
        } else {
            // If range is provided, make sure bulan is consistent or empty
            if (!$this->_valid_range($start_date, $end_date)) { show_error('Rentang tanggal tidak valid.', 400); return; }
            if (empty($bulan)) {
                $bulan = date('Y-m', strtotime($start_date));
            }
        }

        $nama = $this->input->get('nama', true) ? $this->input->get('nama', true) : '';
        $jabatan = $this->input->get('jabatan', true) ? $this->input->get('jabatan', true) : '';
        $divisi = $this->input->get('divisi', true) ? $this->input->get('divisi', true) : '';
        $perusahaan = $this->input->get('perusahaan', true) ? $this->input->get('perusahaan', true) : '';

        if (!$this->_valid_range($start_date, $end_date)) { show_error('Rentang tanggal tidak valid.', 400); return; }

        // Get monthly attendance data
        $rekap_data = $this->Absensi_model->get_rekap_all_karyawan($start_date, $end_date, $nama, $jabatan, $divisi);

        // Add scoring and performance metrics to each entry
        foreach ($rekap_data as &$r) {
            // Kalkulasi jam lembur bulanan (dikurangi total menit keterlambatan)
            $total_lembur_min = isset($r->total_lembur_minutes) ? (int)$r->total_lembur_minutes : 0;
            $total_late_min = isset($r->total_late_minutes) ? (int)$r->total_late_minutes : 0;
            $lembur_netto_minutes = max(0, $total_lembur_min - $total_late_min);
            $r->lembur_netto_jam = round($lembur_netto_minutes / 60, 1);
            
            // Formula: 100 - (Telat*2) - (Alpha*5) - (Izin*1) - (Sakit*1)
            $score = 100 - ($r->total_terlambat * 2) - ($r->total_alpha * 5) - ($r->total_izin * 1) - ($r->total_sakit * 1);
            $r->point_kinerja = max(0, $score);

            if ($score >= 95)
                $r->predikat = 'ISTIMEWA';
            elseif ($score >= 85)
                $r->predikat = 'SANGAT BAIK';
            elseif ($score >= 75)
                $r->predikat = 'BAIK';
            elseif ($score >= 65)
                $r->predikat = 'CUKUP';
            else
                $r->predikat = 'KURANG';
        }

        // Get statistics for the month
        $total_karyawan = count($rekap_data);
        $total_hadir = array_sum(array_column($rekap_data, 'total_hadir'));
        $total_izin = array_sum(array_column($rekap_data, 'total_izin'));
        $total_sakit = array_sum(array_column($rekap_data, 'total_sakit'));
        $total_cuti = array_sum(array_column($rekap_data, 'total_cuti'));
        $total_alpha = array_sum(array_column($rekap_data, 'total_alpha'));
        $total_terlambat = array_sum(array_column($rekap_data, 'total_terlambat'));

        // Get filter options
        $jabatan_list = $this->db->get('tbl_jabatan')->result();
        $divisi_list = $this->db->get('tbl_divisi')->result();
        $perusahaan_list = $this->db->get('tbl_perusahaan')->result();

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $controller,
            'function' => $this->uri->segment(2),
            'title' => 'Rekap Absensi Bulanan',
            'rekap_data' => $rekap_data,
            'bulan' => $bulan,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'nama' => $nama,
            'jabatan' => $jabatan,
            'divisi' => $divisi,
            'perusahaan' => $perusahaan,
            'jabatan_list' => $jabatan_list,
            'divisi_list' => $divisi_list,
            'perusahaan_list' => $perusahaan_list,
            'stats' => array(
                'total_karyawan' => $total_karyawan,
                'total_hadir' => $total_hadir,
                'total_izin' => $total_izin,
                'total_sakit' => $total_sakit,
                'total_cuti' => $total_cuti,
                'total_alpha' => $total_alpha,
                'total_terlambat' => $total_terlambat
            ),
            'logo' => ($conf['perusahaan']['logo'] ?? ''),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($controller)->result(),
        );

        $this->load_admin_view('admin/absensi/rekap_bulanan', $data);
    }

    public function input_manual()
    {
        $userid = $this->session->userdata('id');
        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => 'input_manual',
            'title' => 'Input Absensi Manual',
            'perusahaan' => ($conf['perusahaan']['nama_perusahaan'] ?? ''),
            'hakakses' => ($conf['hakakses']['divisi_nama'] ?? ''),
            'logo' => ($conf['perusahaan']['logo'] ?? ''),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        $this->load_admin_view('admin/absensi/input_manual', $data);
    }

    public function get_daily_attendance_json()
    {
        // Ensure no output buffer messing up JSON
        if (ob_get_length())
            ob_clean();

        header('Content-Type: application/json');

        $tanggal = $this->input->get('tanggal', true);
        if (!$tanggal)
            $tanggal = date('Y-m-d');

        if (!$this->_valid_date($tanggal)) { echo json_encode(['status' => false, 'message' => 'Tanggal tidak valid']); return; }
        $data = $this->Absensi_model->get_bulk_attendance_for_date($tanggal);

        echo json_encode($data);
    }


    public function approval()
    {
        $userid = $this->session->userdata('id');

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => 'approval',
            'title' => 'Approval Izin & Cuti Karyawan',
            'perusahaan' => ($conf['perusahaan']['nama_perusahaan'] ?? ''),
            'hakakses' => ($conf['hakakses']['divisi_nama'] ?? ''),
            'logo' => ($conf['perusahaan']['logo'] ?? ''),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        // Get Pending Requests
        $this->db->select('p.*, k.nama_lengkap, k.nik, j.nama_jabatan as jabatan');
        $this->db->from('tbl_pengajuan_izin p');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = p.karyawan_id');
        $this->_scope_employee();
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->where('p.status', 'Pending');
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->order_by('p.created_at', 'DESC');
        $data['pengajuan'] = $this->db->get()->result();

        $this->load_admin_view('admin/absensi/approval', $data);
    }

    // ==========================================
    // PENGATURAN KOREKSI ABSENSI
    // ==========================================
    public function koreksi_absen()
    {
        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => 'koreksi_absen',
            'title' => 'Koreksi Absen Karyawan',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'] ?? '',
            'hakakses' => $conf['hakakses']['divisi_nama'] ?? '',
        );
        
        $this->db->select('tbl_pengajuan_koreksi_absen.*, tbl_karyawan.nama_lengkap, tbl_karyawan.nik, tbl_karyawan.foto');
        $this->db->from('tbl_pengajuan_koreksi_absen');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = tbl_pengajuan_koreksi_absen.karyawan_id');
        $this->_scope_employee('tbl_karyawan');
        $this->db->order_by('created_at', 'DESC');
        $data['pengajuan'] = $this->db->get()->result();

        $this->load_admin_view('admin/absensi/koreksi_absen', $data);
    }

    public function approve_koreksi($id)
    {
        $this->_process_correction($id, true);
    }

    public function reject_koreksi($id)
    {
        $this->_process_correction($id, false);
    }

    private function _process_correction($id, $approved)
    {
        try {
            $this->_assert_hr_write();
            $this->_require_write($this->db->trans_begin());
            $request = $this->db->query('SELECT * FROM tbl_pengajuan_koreksi_absen WHERE id = ? FOR UPDATE', [$id])->row();
            if (!$request || $request->status !== 'Pending') throw new RuntimeException('Koreksi tidak ditemukan atau sudah diproses.');
            $this->_lock_employee($request->karyawan_id);
            if ($approved) {
                if (!$this->_valid_date($request->tanggal_absen)) throw new RuntimeException('Tanggal koreksi tidak valid.');
                $data = [];
                foreach (['jam_masuk' => 'jam_masuk_baru', 'jam_pulang' => 'jam_pulang_baru'] as $field => $input) {
                    if (isset($request->$input) && $request->$input !== '') $data[$field] = $this->_normal_time($request->$input);
                }
                if (!$data) throw new RuntimeException('Jam koreksi belum diisi.');
                $existing = $this->db->get_where('absensi', ['karyawan_id' => $request->karyawan_id, 'tanggal' => $request->tanggal_absen])->row();
                if ($existing) $this->_require_write($this->db->update('absensi', $data, ['id' => $existing->id]));
                else {
                    $data['karyawan_id'] = $request->karyawan_id; $data['tanggal'] = $request->tanggal_absen; $data['status'] = 'Hadir';
                    $this->_require_write($this->db->insert('absensi', $this->_attendance_defaults($data)));
                }
            }
            $this->_require_write($this->db->update('tbl_pengajuan_koreksi_absen', ['status' => $approved ? 'Disetujui' : 'Ditolak'], ['id' => $id, 'status' => 'Pending']));
            $this->_commit_attendance();
            $this->session->set_flashdata('success', $approved ? 'Koreksi absen berhasil disetujui.' : 'Koreksi absen berhasil ditolak.');
        } catch (Throwable $e) {
            $this->db->trans_rollback(); $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect('absensi/koreksi_absen');
    }

    public function approval_cuti()
    {
        $userid = $this->session->userdata('id');

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => 'approval',
            'title' => 'Approval Izin Karyawan',
            'perusahaan' => ($conf['perusahaan']['nama_perusahaan'] ?? ''),
            'hakakses' => ($conf['hakakses']['divisi_nama'] ?? ''),
            'logo' => ($conf['perusahaan']['logo'] ?? ''),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        // Get Pending Requests
        $this->db->select('p.*, k.nama_lengkap, k.nik, j.nama_jabatan as jabatan');
        $this->db->from('tbl_pengajuan_izin p');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = p.karyawan_id');
        $this->_scope_employee();
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->where('p.status', 'Pending');
        $this->db->order_by('p.created_at', 'DESC');
        $data['pengajuan'] = $this->db->get()->result();

        $this->load_admin_view('admin/absensi/approval', $data);
    }

    public function sisa_cuti()
    {
        $userid = $this->session->userdata('id');

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => 'sisa_cuti',
            'title' => 'Rekap Sisa Cuti Karyawan',
            'perusahaan' => ($conf['perusahaan']['nama_perusahaan'] ?? ''),
            'hakakses' => ($conf['hakakses']['divisi_nama'] ?? ''),
            'logo' => ($conf['perusahaan']['logo'] ?? ''),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        $year = date('Y');

        // --- SINKRONISASI KUOTA MASSAL SEBELUM DITAMPILKAN ---
        // Agar HRD selalu melihat data sisa cuti yang paling akurat 100%
        $this->db->select('karyawan_id')->from('tbl_karyawan')
            ->where("nama_lengkap NOT LIKE '%Terminal%'")->where("nama_lengkap NOT LIKE '%DUMMY%'")
            ->where("nama_lengkap NOT LIKE 'DEPT.%'")->where("nama_lengkap NOT LIKE 'OPERATOR %'");
        $this->_scope_employee('tbl_karyawan');
        $karyawan_aktif = $this->db->order_by('karyawan_id', 'ASC')->get()->result();
        foreach ($karyawan_aktif as $k) {
            try {
                $this->_require_write($this->db->trans_begin());
                $this->_lock_employee($k->karyawan_id);
                $this->_sync_quota_cuti($k->karyawan_id); $this->_commit_attendance();
            } catch (Throwable $e) {
                $this->db->trans_rollback(); show_error('Gagal menyinkronkan kuota cuti.', 500); return;
            }
        }
        // -----------------------------------------------------

        $this->db->select('k.karyawan_id, k.nama_lengkap, j.nama_jabatan, q.quota_awal, q.terpakai, q.sisa, q.menikah_sisa, q.duka_sisa, q.sakitkel_sisa, q.melahirkansuami_sisa, q.melahirkan_sisa');
        $this->db->from('tbl_karyawan k');
        $this->_scope_employee();
        $this->db->join('tbl_quota_cuti q', "q.karyawan_id = k.karyawan_id AND q.tahun = '$year'", 'left');
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->where("k.nama_lengkap NOT LIKE '%Terminal%'");
        $this->db->where("k.nama_lengkap NOT LIKE '%DUMMY%'");
        $this->db->where("k.nama_lengkap NOT LIKE 'DEPT.%'");
        $this->db->where("k.nama_lengkap NOT LIKE 'OPERATOR %'");
        $this->db->order_by('k.nama_lengkap', 'ASC');
        
        $data['kuota_cuti'] = $this->db->get()->result();

        // Get Pending Cuti Requests
        $this->db->select('p.*, k.nama_lengkap, j.nama_jabatan');
        $this->db->from('tbl_pengajuan_izin p');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = p.karyawan_id');
        $this->_scope_employee();
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->where('p.status', 'Pending');
        $this->db->like('p.tipe_izin', 'cuti', 'after');
        $this->db->order_by('p.created_at', 'DESC');
        $data['pending_cuti'] = $this->db->get()->result();

        $this->load_admin_view('admin/absensi/sisa_cuti', $data);
    }

    public function update_quota_cuti()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') { redirect('absensi/sisa_cuti'); return; }
        try {
            $employee = $this->input->post('karyawan_id', true); $year = (int)date('Y');
            $fields = ['sisa' => '', 'menikah_sisa' => 'menikah_', 'duka_sisa' => 'duka_', 'sakitkel_sisa' => 'sakitkel_',
                'melahirkansuami_sisa' => 'melahirkansuami_', 'melahirkan_sisa' => 'melahirkan_'];
            $values = [];
            foreach ($fields as $input => $prefix) {
                $value = $this->input->post($input, true);
                if (!is_scalar($value) || !ctype_digit((string)$value) || (float)$value > 10000) throw new InvalidArgumentException('Sisa cuti harus bilangan bulat 0–10000.');
                $values[$prefix] = (int)$value;
            }
            $this->_require_write($this->db->trans_begin()); $this->_lock_employee($employee); $this->_sync_quota_cuti($employee, $year);
            $quota = $this->db->get_where('tbl_quota_cuti', ['karyawan_id' => $employee, 'tahun' => $year])->row();
            $update = ['updated_at' => date('Y-m-d H:i:s')];
            foreach ($values as $prefix => $remaining) {
                $used = $prefix . 'terpakai'; $initial = $prefix === '' ? 'quota_awal' : $prefix . 'awal';
                $update[$initial] = $remaining + (int)$quota->$used;
            }
            $this->_require_write($this->db->update('tbl_quota_cuti', $update, ['karyawan_id' => $employee, 'tahun' => $year]));
            $this->_sync_quota_cuti($employee, $year); $this->_commit_attendance();
            $this->session->set_flashdata('success', 'Kuota cuti berhasil diupdate.');
        } catch (Throwable $e) { $this->db->trans_rollback(); $this->session->set_flashdata('error', $e->getMessage()); }
        redirect('absensi/sisa_cuti');
    }

    public function add_riwayat_manual()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') { redirect('absensi/sisa_cuti'); return; }
        try {
            $employee = $this->input->post('karyawan_id', true); $type = $this->input->post('tipe_izin', true);
            $date = $this->input->post('tanggal_mulai', true); $duration = $this->input->post('durasi_jumlah', true);
            $types = ['Cuti', 'Cuti Tahunan', 'Cuti Menikah', 'Cuti Duka', 'Cuti Keluarga Sakit', 'Cuti Melahirkan Suami', 'Cuti Melahirkan', 'Cuti Keluarga', 'Cuti Sakit', 'Izin', 'Sakit'];
            if (!in_array($type, $types, true) || !$this->_valid_date($date) || !is_scalar($duration)
                || !ctype_digit((string)$duration) || (int)$duration < 1 || (int)$duration > 366) throw new InvalidArgumentException('Data riwayat tidak valid.');
            $this->_require_write($this->db->trans_begin()); $this->_lock_employee($employee);
            $this->_require_write($this->db->insert('tbl_pengajuan_izin', ['karyawan_id' => $employee, 'tipe_izin' => $type,
                'tanggal_mulai' => $date, 'durasi_tipe' => 'hari', 'durasi_jumlah' => (int)$duration,
                'keterangan' => $this->input->post('keterangan', true), 'dokumen' => '', 'status' => 'Approved',
                'created_at' => date('Y-m-d H:i:s'), 'responded_at' => date('Y-m-d H:i:s'), 'responded_by' => $this->session->userdata('id')]));
            $this->_sync_quota_cuti($employee, (int)substr($date, 0, 4)); $this->_commit_attendance();
            $this->session->set_flashdata('success', 'Riwayat cuti manual dan kuota berhasil diperbarui.');
        } catch (Throwable $e) { $this->db->trans_rollback(); $this->session->set_flashdata('error', $e->getMessage()); }
        redirect('absensi/sisa_cuti');
    }

    public function get_riwayat_cuti($karyawan_id)
    {
        $this->_scope_employee('tbl_karyawan');
        if (!$this->db->get_where('tbl_karyawan', ['karyawan_id' => $karyawan_id])->row()) { show_404(); return; }
        $year = date('Y');
        $this->db->where('karyawan_id', $karyawan_id);
        $this->db->where('YEAR(tanggal_mulai)', $year);
        $this->db->order_by('created_at', 'DESC');
        $riwayat = $this->db->get('tbl_pengajuan_izin')->result();
        
        $html = '';
        if (empty($riwayat)) {
            $html .= '<div class="text-center text-muted p-3">Belum ada riwayat pengajuan cuti tahun ini.</div>';
        } else {
            $html .= '<div class="table-responsive">';
            $html .= '<table class="table table-bordered table-striped table-sm align-middle">';
            $html .= '<thead class="table-light">';
            $html .= '<tr>';
            $html .= '<th class="text-center" width="5%">#</th>';
            $html .= '<th width="20%">Tipe Cuti</th>';
            $html .= '<th width="25%">Tanggal & Durasi</th>';
            $html .= '<th width="35%">Keterangan</th>';
            $html .= '<th class="text-center" width="15%">Status</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            
            $no = 1;
            foreach ($riwayat as $r) {
                $status_badge = '';
                if ($r->status === 'Approved') {
                    $status_badge = '<span class="badge bg-success">Disetujui</span>';
                } elseif ($r->status === 'Pending') {
                    $status_badge = '<span class="badge bg-warning text-dark">Pending</span>';
                } elseif ($r->status === 'Rejected') {
                    $status_badge = '<span class="badge bg-danger">Ditolak</span>';
                }
                
                // Fallbacks if data is empty for some reason
                // Map Tipe Izin to Quota Label
                $tipe_lower = strtolower($r->tipe_izin);
                $tipe_label = ucwords($tipe_lower);
                $potong_jatah = '';

                if ($tipe_lower == 'cuti') {
                    $tipe_label = 'Cuti Tahunan';
                    $potong_jatah = '<div class="mt-1"><span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:0.65rem;">Mengurangi Cuti Tahunan</span></div>';
                } elseif ($tipe_lower == 'cuti menikah') {
                    $potong_jatah = '<div class="mt-1"><span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size:0.65rem;">Mengurangi Cuti Menikah</span></div>';
                } elseif ($tipe_lower == 'cuti duka') {
                    $potong_jatah = '<div class="mt-1"><span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size:0.65rem;">Mengurangi Cuti Duka</span></div>';
                } elseif ($tipe_lower == 'cuti keluarga sakit') {
                    $potong_jatah = '<div class="mt-1"><span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size:0.65rem;">Mengurangi Cuti Kel. Sakit</span></div>';
                } elseif ($tipe_lower == 'cuti melahirkan suami') {
                    $potong_jatah = '<div class="mt-1"><span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.65rem;">Mengurangi Cuti Istri Melahirkan</span></div>';
                } else {
                    // Sakit, Izin, etc don't use cuti quotas
                    $potong_jatah = '<div class="mt-1"><span class="badge bg-light text-dark border border-secondary-subtle" style="font-size:0.65rem;">Tidak Memotong Kuota Cuti</span></div>';
                }

                $ket = !empty($r->keterangan) ? $r->keterangan : '-';

                $delete_btn = '<button class="btn btn-sm btn-outline-danger mt-2 py-0 px-2" style="font-size: 0.7rem;" onclick="hapusRiwayat(' . $r->id . ')"><i class="bi bi-trash"></i> Hapus</button>';

                $html .= '<tr>';
                $html .= '<td class="text-center">'.$no++.'</td>';
                $html .= '<td><div class="fw-bold text-dark">' . htmlspecialchars($tipe_label) . '</div>' . $potong_jatah . '</td>';
                $html .= '<td><small><i class="bi bi-calendar-event me-1 text-primary"></i>' . date('d M Y', strtotime($r->tanggal_mulai)) . '<br><span class="text-muted">' . $r->durasi_jumlah . ' Hari</span></small></td>';
                $html .= '<td><small class="text-muted">' . nl2br(htmlspecialchars($ket)) . '</small></td>';
                $html .= '<td class="text-center">' . $status_badge . '<br>' . $delete_btn . '</td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }
        
        echo $html;
    }

    public function delete_riwayat_cuti($id)
    {
        try {
            $this->_require_write($this->db->trans_begin());
            $request = $this->db->query('SELECT * FROM tbl_pengajuan_izin WHERE id = ? FOR UPDATE', [$id])->row();
            if (!$request) throw new RuntimeException('Data tidak ditemukan.');
            $this->_lock_employee($request->karyawan_id);
            // Records that generated attendance must be corrected explicitly, otherwise quota and attendance diverge.
            $end = date('Y-m-d', strtotime($request->tanggal_mulai . ' +' . max(0, (int)$request->durasi_jumlah - 1) . ' days'));
            $attendance = $this->db->where('karyawan_id', $request->karyawan_id)->where('tanggal >=', $request->tanggal_mulai)
                ->where('tanggal <=', $end)->get('absensi')->result();
            if ($request->status === 'Approved') {
                foreach ($attendance as $att) {
                    if (stripos((string)$att->status, 'cuti') === 0 || in_array($att->status, ['Izin', 'Sakit'], true)) {
                        throw new RuntimeException('Riwayat ini terkait absensi. Koreksi absensi terlebih dahulu sebelum menghapus riwayat.');
                    }
                }
            }
            $this->_require_write($this->db->delete('tbl_pengajuan_izin', ['id' => $id]));
            $this->_sync_quota_cuti($request->karyawan_id, (int)substr($request->tanggal_mulai, 0, 4));
            $this->_commit_attendance(); echo json_encode(['status' => 'success']);
        } catch (Throwable $e) { $this->db->trans_rollback(); echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
    }

    public function riwayat_approval()
    {
        $userid = $this->session->userdata('id');
        $start_date = $this->input->get('start_date', true) ?? date('Y-m-01');
        $end_date = $this->input->get('end_date', true) ?? date('Y-m-t');
        if (!$this->_valid_range($start_date, $end_date)) { show_error('Rentang tanggal tidak valid.', 400); return; }

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => 'riwayat_approval',
            'title' => 'Riwayat Pengajuan Izin & Cuti',
            'perusahaan' => ($conf['perusahaan']['nama_perusahaan'] ?? ''),
            'hakakses' => ($conf['hakakses']['divisi_nama'] ?? ''),
            'logo' => ($conf['perusahaan']['logo'] ?? ''),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
            'start_date' => $start_date,
            'end_date' => $end_date
        );

        // Get History Requests
        $this->db->select('p.*, k.nama_lengkap, k.nik, j.nama_jabatan as jabatan, ka.nama_lengkap as admin_nama');
        $this->db->from('tbl_pengajuan_izin p');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = p.karyawan_id');
        $this->_scope_employee();
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->join('users u', 'u.user_id = p.responded_by', 'left');
        $this->db->join('tbl_karyawan ka', 'ka.karyawan_id = u.karyawan_id', 'left');
        $this->db->where('p.status !=', 'Pending');
        $this->db->where('p.tanggal_mulai >=', $start_date);
        $this->db->where('p.tanggal_mulai <=', $end_date);
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->order_by('p.responded_at', 'DESC');
        $data['riwayat'] = $this->db->get()->result();

        $this->load_admin_view('admin/absensi/history_pengajuan', $data);
    }

    private function _sync_quota_cuti($karyawan_id, $year = null)
    {
        $year = $year ?: (int)date('Y');

        // Pastikan ada baris quota di tahun ini
        $quota_row = $this->db->get_where('tbl_quota_cuti', [
            'karyawan_id' => $karyawan_id,
            'tahun' => $year
        ])->row();

        if (!$quota_row) {
            $quota_init = [
                'karyawan_id' => $karyawan_id,
                'tahun' => $year,
                'quota_awal' => 12,
                'terpakai' => 0,
                'sisa' => 12,
                'menikah_awal' => 7,
                'duka_awal' => 3,
                'sakitkel_awal' => 2,
                'melahirkansuami_awal' => 3,
                'melahirkan_awal' => 90
            ];
            $this->_require_write($this->db->insert('tbl_quota_cuti', $quota_init));
            $quota_total = 12;
            $menikah_awal = 7;
            $duka_awal = 3;
            $sakitkel_awal = 2;
            $melahirkansuami_awal = 3;
            $melahirkan_awal = 90;
        } else {
            $quota_total = isset($quota_row->quota_awal) ? max(0, (int)$quota_row->quota_awal) : 12;
            $menikah_awal = isset($quota_row->menikah_awal) ? $quota_row->menikah_awal : 7;
            $duka_awal = isset($quota_row->duka_awal) ? $quota_row->duka_awal : 3;
            $sakitkel_awal = isset($quota_row->sakitkel_awal) ? $quota_row->sakitkel_awal : 2;
            $melahirkansuami_awal = isset($quota_row->melahirkansuami_awal) ? $quota_row->melahirkansuami_awal : 3;
            $melahirkan_awal = isset($quota_row->melahirkan_awal) ? $quota_row->melahirkan_awal : 90;
        }

        // Ambil semua pengajuan tahun ini
        $requests = $this->db->where('karyawan_id', $karyawan_id)
            ->where('YEAR(tanggal_mulai)', $year)
            ->where_in('status', ['Pending', 'Approved', 'Disetujui', 'approved', 'disetujui'])
            ->get('tbl_pengajuan_izin')->result();

        $used_annual_approved = 0;
        $used_annual_pending = 0;

        $limits = [
            'cuti menikah' => $menikah_awal,
            'cuti duka' => $duka_awal,
            'cuti keluarga sakit' => $sakitkel_awal,
            'cuti melahirkan suami' => $melahirkansuami_awal,
            'cuti melahirkan' => $melahirkan_awal
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

        $spillover_approved = 0;
        $spillover_pending = 0;

        foreach ($limits as $type => $limit) {
            $total_type_usage = $usage_special_approved[$type] + $usage_special_pending[$type];
            if ($total_type_usage > $limit) {
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

        $menikah_used = min($limits['cuti menikah'], $usage_special_approved['cuti menikah'] + $usage_special_pending['cuti menikah']);
        $duka_used = min($limits['cuti duka'], $usage_special_approved['cuti duka'] + $usage_special_pending['cuti duka']);
        $sakitkel_used = min($limits['cuti keluarga sakit'], $usage_special_approved['cuti keluarga sakit'] + $usage_special_pending['cuti keluarga sakit']);
        $melahirkansuami_used = min($limits['cuti melahirkan suami'], $usage_special_approved['cuti melahirkan suami'] + $usage_special_pending['cuti melahirkan suami']);
        $melahirkan_used = min($limits['cuti melahirkan'], $usage_special_approved['cuti melahirkan'] + $usage_special_pending['cuti melahirkan']);

        $this->_require_write($this->db->where(['karyawan_id' => $karyawan_id, 'tahun' => $year])
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
            ]));
    }

    public function approve_action($id)
    {
        try {
            $this->_assert_hr_write();
            $this->_require_write($this->db->trans_begin());
            $request = $this->db->query('SELECT * FROM tbl_pengajuan_izin WHERE id = ? FOR UPDATE', [$id])->row();
            if (!$request || $request->status !== 'Pending') throw new RuntimeException('Pengajuan tidak ditemukan atau sudah diproses.');
            $this->_lock_employee($request->karyawan_id);
            $date = $request->tanggal_mulai;
            $type = strtolower((string)$request->tipe_izin);
            $duration = $request->durasi_jumlah;
            $statuses = ['izin' => 'Izin', 'sakit' => 'Sakit', 'cuti' => 'Cuti', 'cuti tahunan' => 'Cuti',
                'cuti keluarga' => 'Cuti Keluarga', 'cuti keluarga sakit' => 'Cuti Keluarga Sakit',
                'cuti sakit' => 'Cuti Sakit', 'cuti duka' => 'Cuti Duka', 'cuti melahirkan' => 'Cuti Melahirkan',
                'cuti melahirkan suami' => 'Cuti Melahirkan Suami', 'cuti menikah' => 'Cuti Menikah'];
            $partial = in_array($type, ['izin_telat', 'pulang_cepat'], true);
            if (!$this->_valid_date($date) || !in_array($request->durasi_tipe, ['hari', 'jam'], true)
                || !is_numeric($duration) || !is_finite((float)$duration) || (float)$duration < ($partial ? 0 : 1)
                || (float)$duration != floor((float)$duration) || (float)$duration > ($request->durasi_tipe === 'jam' ? 24 : 366)
                || (!$partial && !isset($statuses[$type]))) throw new RuntimeException('Data tanggal, tipe, atau durasi pengajuan tidak valid.');
            $notes = (string)$request->keterangan;
            if ($partial) {
                // Approval grants permission; actual arrival/departure is recorded by the employee.
                $existing = $this->db->get_where('absensi', ['karyawan_id' => $request->karyawan_id, 'tanggal' => $date])->row();
                if ($existing && $existing->status === 'Hadir') {
                    $update = ['keterangan' => trim((string)$existing->keterangan . "\n" . $notes)];
                    if ($type === 'izin_telat') $update['is_izin_telat'] = 1;
                    $this->_require_write($this->db->update('absensi', $update, ['id' => $existing->id]));
                }
            } else {
                $count = $request->durasi_tipe === 'hari' ? (int)$duration : 1;
                for ($i = 0; $i < $count; $i++) {
                    $day = date('Y-m-d', strtotime($date . ' +' . $i . ' days'));
                    $existing = $this->db->get_where('absensi', ['karyawan_id' => $request->karyawan_id, 'tanggal' => $day])->row();
                    if ($existing && !in_array($existing->status, ['Alpha', 'Alfa', ''], true)) {
                        throw new RuntimeException('Sudah ada absensi pada ' . $day . '. Koreksi dahulu sebelum menyetujui izin.');
                    }
                    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $schedule = $this->db->get_where('tbl_jam_kerja', ['hari' => $days[date('w', strtotime($day))]])->row();
                    $start = $this->_normal_time($schedule->jam_masuk ?? '08:00:00');
                    $end = $this->_normal_time($schedule->jam_pulang ?? '17:00:00');
                    if ($request->durasi_tipe === 'jam') $end = date('H:i:s', strtotime($day . ' ' . $start) + (int)$duration * 3600);
                    $data = ['karyawan_id' => $request->karyawan_id, 'tanggal' => $day,
                        'jam_masuk' => $start, 'jam_pulang' => $end, 'status' => $statuses[$type],
                        'keterangan' => $notes . ' (Durasi: ' . $duration . ' ' . $request->durasi_tipe . ')',
                        'dokumen' => $request->dokumen ?? ''];
                    if ($existing) $this->_require_write($this->db->update('absensi', $data, ['id' => $existing->id]));
                    else $this->_require_write($this->db->insert('absensi', $this->_attendance_defaults($data)));
                }
            }
            $this->_require_write($this->db->update('tbl_pengajuan_izin', ['status' => 'Approved',
                'responded_at' => date('Y-m-d H:i:s'), 'responded_by' => $this->session->userdata('id')], ['id' => $id, 'status' => 'Pending']));
            $this->_sync_quota_cuti($request->karyawan_id, (int)substr($date, 0, 4));
            $this->_commit_attendance();
            $this->session->set_flashdata('success', 'Pengajuan berhasil disetujui.');
            $this->_notify_leave($request, true);
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect($this->input->get('ref') === 'sisa_cuti' ? 'absensi/sisa_cuti' : 'absensi/approval');
    }

    public function reject_action($id)
    {
        try {
            $this->_assert_hr_write();
            $this->_require_write($this->db->trans_begin());
            $request = $this->db->query('SELECT * FROM tbl_pengajuan_izin WHERE id = ? FOR UPDATE', [$id])->row();
            if (!$request || $request->status !== 'Pending') throw new RuntimeException('Pengajuan tidak ditemukan atau sudah diproses.');
            $this->_lock_employee($request->karyawan_id);
            $this->_require_write($this->db->update('tbl_pengajuan_izin', ['status' => 'Rejected',
                'responded_at' => date('Y-m-d H:i:s'), 'responded_by' => $this->session->userdata('id')], ['id' => $id, 'status' => 'Pending']));
            $this->_sync_quota_cuti($request->karyawan_id, (int)substr($request->tanggal_mulai, 0, 4));
            $this->_commit_attendance();
            $this->session->set_flashdata('success', 'Pengajuan berhasil ditolak.');
            $this->_notify_leave($request, false);
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect($this->input->get('ref') === 'sisa_cuti' ? 'absensi/sisa_cuti' : 'absensi/approval');
    }

    public function save_manual_batch()
    {
        if (!$this->input->is_ajax_request()) { show_404(); return; }
        try {
            $date = $this->input->post('tanggal', true);
            $rows = $this->input->post('attendance', true);
            if (!is_array($rows) || !$rows || count($rows) > 1000) throw new InvalidArgumentException('Data absensi tidak lengkap.');
            $validated = []; $seen = [];
            foreach ($rows as $row) {
                $data = $this->_manual_data($row, $date);
                if (isset($seen[$data['karyawan_id']])) throw new InvalidArgumentException('Karyawan tercantum lebih dari sekali.');
                $seen[$data['karyawan_id']] = true;
                $validated[] = ['data' => $data, 'id' => $row['id'] ?? null];
            }
            usort($validated, function ($a, $b) { return $a['data']['karyawan_id'] <=> $b['data']['karyawan_id']; });
            $this->_require_write($this->db->trans_begin());
            foreach ($validated as $row) $this->_save_manual_record($row['data'], $row['id']);
            $this->_commit_attendance();
            echo json_encode(['status' => true, 'message' => 'Berhasil']);
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    function rekapitulasi()
    {
        $userid = $this->session->userdata('id');
        $controller = $this->uri->segment(1);

        // Get filter parameters
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);

        if (empty($start_date)) {
            $start_date = date('Y-m-01');
        }
        if (!$this->_valid_date($start_date)) { show_error('Tanggal awal tidak valid.', 400); return; }
        if (empty($end_date)) {
            $end_date = date('Y-m-t', strtotime($start_date));
        }

        $jabatan = $this->input->get('jabatan', true) ? $this->input->get('jabatan', true) : '';
        $divisi = $this->input->get('divisi', true) ? $this->input->get('divisi', true) : '';

        if (!$this->_valid_range($start_date, $end_date)) { show_error('Rentang tanggal tidak valid.', 400); return; }

        // Get all active employees
        $this->db->select('k.karyawan_id, k.nama_lengkap, k.nik, j.nama_jabatan as jabatan, d.divisi_nama as divisi');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%'); // Exclude specific division if needed
        $this->db->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left');
        $this->db->where('k.status_karyawan !=', 'Tidak Aktif');
        $this->db->where('k.divisi_id !=', 8); // Exclude specific division if needed
        if ($jabatan)
            $this->db->where('j.nama_jabatan', $jabatan);
        if ($divisi)
            $this->db->where('d.divisi_nama', $divisi);
        if ((int)$this->session->userdata('perusahaan_id') !== 1) $this->db->where('k.perusahaan_id', $this->session->userdata('perusahaan_id'));
        $this->db->order_by('k.nama_lengkap', 'ASC');
        $employees = $this->db->get()->result();

        // Get attendance data for the period
        $this->db->select('karyawan_id, tanggal, status, jam_masuk, jam_pulang');
        $this->db->from('absensi');
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        $attendance_raw = $this->db->get()->result();

        // Map attendance to grid [karyawan_id][tanggal]
        $attendance_map = [];
        foreach ($attendance_raw as $att) {
            $attendance_map[$att->karyawan_id][$att->tanggal] = $att;
        }

        // Generate dates array for table columns
        $dates = [];
        $current = strtotime($start_date);
        $last = strtotime($end_date);
        while ($current <= $last) {
            $dates[] = date('Y-m-d', $current);
            $current = strtotime("+1 day", $current);
        }

        // Get filter options
        $jabatan_list = $this->db->get('tbl_jabatan')->result();
        $divisi_list = $this->db->get('tbl_divisi')->result();

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $controller,
            'function' => 'rekapitulasi',
            'title' => 'Rekapitulasi Kehadiran Karyawan',
            'employees' => $employees,
            'dates' => $dates,
            'attendance_map' => $attendance_map,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'jabatan' => $jabatan,
            'divisi' => $divisi,
            'jabatan_list' => $jabatan_list,
            'divisi_list' => $divisi_list,
            'logo' => ($conf['perusahaan']['logo'] ?? ''),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($controller)->result(),
        );

        $this->load_admin_view('admin/absensi/rekapitulasi', $data);
    }

    function log_activity($user_id, $action, $module, $reference_id = null)
    {
        $this->db->insert('user_activity_log', [
            'user_id' => $user_id,
            'action' => $action,
            'module' => $module,
            'reference_id' => $reference_id,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    private function calculate_distance($lat1, $lon1, $lat2, $lon2)
    {
        foreach ([$lat1, $lon1, $lat2, $lon2] as $value) {
            if (!is_numeric($value) || !is_finite((float)$value)) return 0;
        }
        if (abs($lat1) > 90 || abs($lat2) > 90 || abs($lon1) > 180 || abs($lon2) > 180) return 0;

        $earthRadius = 6371000; // Radius of Earth in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $h = pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2);
        $angle = 2 * asin(sqrt(max(0, min(1, $h))));

        return round($angle * $earthRadius);
    }
    public function sync_to_payroll()
    {
        if (!$this->input->is_ajax_request()) { show_404(); return; }
        $debug = $this->db->db_debug;
        try {
            $employee = $this->input->post('karyawan_id', true);
            $start = $this->input->post('start_date', true); $end = $this->input->post('end_date', true);
            $month = $this->input->post('bulan', true);
            if (!$this->_valid_range($start, $end)) throw new InvalidArgumentException('Rentang tanggal payroll tidak valid.');
            if ($month === null || $month === '') $month = substr($end, 0, 7);
            if (!is_string($month) || $month !== substr($end, 0, 7)) throw new InvalidArgumentException('Bulan payroll harus sesuai tanggal akhir rekap.');
            $this->db->db_debug = false;
            if (!$this->db->table_exists('tbl_absensi_rekap_karyawan')
                || !$this->db->field_exists('lembur_kerjaan', 'tbl_absensi_rekap_karyawan')
                || !$this->db->field_exists('biaya_makan_lembur', 'tbl_absensi_rekap_karyawan')) {
                throw new RuntimeException('Struktur tabel rekap payroll belum lengkap. Jalankan migrasi database terlebih dahulu.');
            }
            $this->_require_write($this->db->trans_begin());
            $this->_lock_employee($employee);
            $data = $this->_payroll_summary($employee, $start, $end);
            $data = array_merge($data, ['karyawan_id' => (int)$employee, 'bulan' => $month,
                'start_date' => $start, 'end_date' => $end, 'sync_at' => date('Y-m-d H:i:s')]);
            $existing = $this->db->get_where('tbl_absensi_rekap_karyawan', ['karyawan_id' => $employee, 'bulan' => $month])->row();
            if ($existing) $this->_require_write($this->db->update('tbl_absensi_rekap_karyawan', $data, ['id' => $existing->id]));
            else $this->_require_write($this->db->insert('tbl_absensi_rekap_karyawan', $data));
            $this->_commit_attendance();
            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            $this->db->trans_rollback(); echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } finally { $this->db->db_debug = $debug; }
    }

    private function _meal_allowance($date, $att, $schedule)
    {
        if (!$att || $att->status !== 'Hadir' || empty($att->jam_pulang) || $att->jam_pulang === '00:00:00') return 0;
        $arrival = strtotime($date . ' ' . $att->jam_masuk);
        $departure = strtotime($date . ' ' . $att->jam_pulang);
        if ($departure < $arrival) $departure += 86400;
        $scheduled = strtotime($date . ' ' . ($schedule->jam_masuk ?? '08:00:00'));
        $departure -= max(0, $arrival - $scheduled);
        if (date('N', strtotime($date)) == 7) {
            return $departure >= strtotime($date . ' 18:00:00') ? 35000 : ($departure >= strtotime($date . ' 14:00:00') ? 15000 : 0);
        }
        return $departure >= strtotime($date . ' 22:00:00') ? 20000 : ($departure >= strtotime($date . ' 19:00:00') ? 15000 : 0);
    }

    private function _payroll_summary($employee, $start, $end)
    {
        $attendance = $this->db->where('karyawan_id', $employee)->where('tanggal >=', $start)->where('tanggal <=', $end)->get('absensi')->result();
        $overtime = $this->db->where('karyawan_id', $employee)->where('tanggal >=', $start)->where('tanggal <=', $end)->where('status', 'disetujui')->get('tbl_lembur')->result();
        $schedules = $this->db->get('tbl_jam_kerja')->result();
        $holidays = $this->db->where('tanggal >=', $start)->where('tanggal <=', $end)->get('hari_libur')->result();
        $att_map = []; $spl_map = []; $jk_map = []; $holiday_map = []; $stored_meals = 0;
        foreach ($attendance as $att) {
            if (isset($att_map[$att->tanggal])) throw new RuntimeException('Ada absensi ganda pada ' . $att->tanggal . '. Perbaiki sebelum sinkronisasi payroll.');
            $att_map[$att->tanggal] = $att;
        }
        foreach ($overtime as $spl) { $spl_map[$spl->tanggal][] = $spl; $stored_meals += (float)($spl->biaya_makan ?? 0); }
        foreach ($schedules as $jk) $jk_map[$jk->hari] = $jk;
        foreach ($holidays as $h) $holiday_map[$h->tanggal] = true;
        $result = ['total_hadir' => 0, 'total_izin' => 0, 'total_sakit' => 0, 'total_alpha' => 0, 'total_terlambat' => 0,
            'lembur_pagi' => 0, 'lembur_wajib_tambahan' => 0, 'lembur_kerjaan' => 0, 'total_lembur' => 0, 'biaya_makan_lembur' => 0];
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        for ($d = $start; $d <= $end; $d = date('Y-m-d', strtotime($d . ' +1 day'))) {
            $att = $att_map[$d] ?? null; $weekday = (int)date('w', strtotime($d)); $schedule = $jk_map[$days[$weekday]] ?? null;
            $calc = $this->_build_daily_lembur_calc($employee, $d, $att, $spl_map[$d] ?? [], $jk_map);
            $result['lembur_pagi'] += $calc['pagi']; $result['lembur_wajib_tambahan'] += $calc['wajib'];
            $result['lembur_kerjaan'] += $calc['kerjaan']; $result['total_lembur'] += $calc['total'];
            $result['biaya_makan_lembur'] += $this->_meal_allowance($d, $att, $schedule);
            if ($weekday === 0 || isset($holiday_map[$d])) continue;
            $status = $att->status ?? '';
            if ($status === 'Hadir') {
                $result['total_hadir']++;
                if (strtotime($att->jam_masuk) > strtotime($schedule->jam_masuk ?? '08:00:00') + 59) $result['total_terlambat']++;
            } elseif ($status === 'Izin') $result['total_izin']++;
            elseif ($status === 'Sakit') $result['total_sakit']++;
            elseif (stripos($status, 'cuti') === 0) continue;
            elseif ($status === 'Alpha' || $status === 'Alfa' || $d < date('Y-m-d')) $result['total_alpha']++;
        }
        if ($stored_meals > 0) $result['biaya_makan_lembur'] = $stored_meals;
        return $result;
    }

    private function _valid_date($date)
    {
        if (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date)) return false;
        $d = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date && $date >= '1900-01-01';
    }

    private function _valid_range($start, $end)
    {
        return $this->_valid_date($start) && $this->_valid_date($end) && $start <= $end
            && (strtotime($end) - strtotime($start)) <= 366 * 86400;
    }

    private function _normal_time($time)
    {
        if ($time === null || $time === '') return '00:00:00';
        if (!is_string($time) || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/D', $time)) {
            throw new InvalidArgumentException('Jam harus dalam format HH:MM atau HH:MM:SS yang valid.');
        }
        return strlen($time) === 5 ? $time . ':00' : $time;
    }

    private function _attendance_defaults($data)
    {
        return array_merge(['jam_masuk' => '00:00:00', 'jam_pulang' => '00:00:00',
            'foto_masuk' => '', 'foto_pulang' => '', 'dokumen' => ''], $data);
    }

    private function _require_write($result)
    {
        if (!$result) throw new RuntimeException('Gagal menyimpan data absensi.');
    }

    private function _commit_attendance()
    {
        if ($this->db->trans_status() === false || !$this->db->trans_commit()) {
            throw new RuntimeException('Transaksi absensi gagal.');
        }
    }

    // Serialize employee/day writes without adding a unique key to existing ERP data.
    private function _lock_employee($id)
    {
        if (!$this->session->userdata('logged_in')) throw new RuntimeException('Sesi login berakhir.');
        if (!is_scalar($id) || !ctype_digit((string)$id) || (int)$id <= 0) {
            throw new InvalidArgumentException('Karyawan tidak valid.');
        }
        $employee = $this->db->query('SELECT * FROM tbl_karyawan WHERE karyawan_id = ? FOR UPDATE', [(int)$id])->row();
        $company = $this->session->userdata('perusahaan_id');
        if (!$employee || !$company || ((int)$company !== 1 && (int)$employee->perusahaan_id !== (int)$company)) {
            throw new RuntimeException('Karyawan tidak ditemukan atau berada di cabang lain.');
        }
        return $employee;
    }

    private function _manual_data($row, $date)
    {
        $statuses = ['Hadir', 'Izin', 'Sakit', 'Alpha', 'Cuti', 'Cuti Keluarga', 'Cuti Sakit',
            'Cuti Keluarga Sakit', 'Cuti Duka', 'Cuti Melahirkan', 'Cuti Melahirkan Suami', 'Cuti Menikah'];
        if (!is_array($row) || !$this->_valid_date($date) || !isset($row['karyawan_id'], $row['status'])
            || !is_scalar($row['karyawan_id']) || !ctype_digit((string)$row['karyawan_id']) || (int)$row['karyawan_id'] <= 0
            || !in_array($row['status'], $statuses, true) || (isset($row['keterangan']) && !is_string($row['keterangan']))) {
            throw new InvalidArgumentException('Tanggal, karyawan, atau status absensi tidak valid.');
        }
        return ['karyawan_id' => (int)$row['karyawan_id'], 'tanggal' => $date, 'status' => $row['status'],
            'jam_masuk' => $this->_normal_time($row['jam_masuk'] ?? ''),
            'jam_pulang' => $this->_normal_time($row['jam_pulang'] ?? ''), 'keterangan' => $row['keterangan'] ?? ''];
    }

    private function _save_manual_record($data, $id = null)
    {
        $this->_lock_employee($data['karyawan_id']);
        $existing = $this->db->get_where('absensi', ['karyawan_id' => $data['karyawan_id'], 'tanggal' => $data['tanggal']])->row();
        $old = null;
        if ($id !== null && $id !== '') {
            if (!is_scalar($id) || !ctype_digit((string)$id) || (int)$id <= 0) throw new InvalidArgumentException('ID absensi tidak valid.');
            $old = $this->db->get_where('absensi', ['id' => $id])->row();
            if (!$old || (int)$old->karyawan_id !== $data['karyawan_id']) throw new RuntimeException('ID absensi tidak sesuai dengan karyawan.');
            if ($existing && (int)$existing->id !== (int)$id) throw new RuntimeException('Sudah ada absensi pada tanggal tujuan.');
        } else {
            $old = $existing;
        }
        if ($old) {
            $this->_require_write($this->db->update('absensi', $data, ['id' => $old->id]));
            if ($old->tanggal !== $data['tanggal']) $this->_delete_hr_leave($old->karyawan_id, $old->tanggal);
        } else {
            $this->_require_write($this->db->insert('absensi', $this->_attendance_defaults($data)));
        }
        $this->_delete_hr_leave($data['karyawan_id'], $data['tanggal']);
        if (stripos($data['status'], 'cuti') === 0) {
            $approved = $this->db->where('karyawan_id', $data['karyawan_id'])->where('status', 'Approved')
                ->where('durasi_tipe', 'hari')->where('tanggal_mulai <=', $data['tanggal'])
                ->where('DATE_ADD(tanggal_mulai, INTERVAL (durasi_jumlah - 1) DAY) >=', $data['tanggal'])->get('tbl_pengajuan_izin')->result();
            $covered = false;
            foreach ($approved as $r) if (stripos($r->tipe_izin, 'cuti') === 0) $covered = true;
            if (!$covered) {
                $this->_require_write($this->db->insert('tbl_pengajuan_izin', [
                    'karyawan_id' => $data['karyawan_id'], 'tanggal_mulai' => $data['tanggal'], 'durasi_tipe' => 'hari',
                    'durasi_jumlah' => 1, 'tipe_izin' => $data['status'], 'keterangan' => 'Koreksi Data Absensi (HR)',
                    'dokumen' => '', 'status' => 'Approved', 'created_at' => date('Y-m-d H:i:s'),
                    'responded_at' => date('Y-m-d H:i:s'), 'responded_by' => $this->session->userdata('id')]));
            }
        }
        $this->_sync_quota_cuti($data['karyawan_id'], (int)substr($data['tanggal'], 0, 4));
        if ($old && substr($old->tanggal, 0, 4) !== substr($data['tanggal'], 0, 4)) {
            $this->_sync_quota_cuti($data['karyawan_id'], (int)substr($old->tanggal, 0, 4));
        }
    }

    private function _delete_hr_leave($employee, $date)
    {
        $this->_require_write($this->db->delete('tbl_pengajuan_izin', ['karyawan_id' => $employee,
            'tanggal_mulai' => $date, 'status' => 'Approved', 'keterangan' => 'Koreksi Data Absensi (HR)']));
    }

    private function _payroll_range($cutoff, $today = null)
    {
        $today = new DateTimeImmutable($today ?: date('Y-m-d'));
        $cutoff = max(1, min(31, (int)$cutoff));
        $month = $today->modify('first day of this month');
        $boundary = function ($m) use ($cutoff) { return $m->setDate((int)$m->format('Y'), (int)$m->format('m'), min($cutoff, (int)$m->format('t'))); };
        if ($today < $boundary($month)) $month = $month->modify('-1 month');
        return [$boundary($month)->format('Y-m-d'), $boundary($month->modify('+1 month'))->format('Y-m-d')];
    }

    private function _notify_leave($request, $approved)
    {
        try {
            $employee = $this->db->select('fcm_token')->get_where('tbl_karyawan', ['karyawan_id' => $request->karyawan_id])->row();
            if ($employee && !empty($employee->fcm_token)) {
                $this->load->library('firebase_cloud_messaging');
                $this->firebase_cloud_messaging->send_notification($employee->fcm_token,
                    $approved ? 'Pengajuan Disetujui' : 'Pengajuan Ditolak',
                    'Pengajuan ' . ucfirst($request->tipe_izin) . ' Anda tanggal ' . $request->tanggal_mulai
                        . ($approved ? ' telah disetujui oleh HRD.' : ' tidak disetujui.'));
            }
        } catch (Throwable $e) { log_message('error', 'Notifikasi izin gagal: ' . $e->getMessage()); }
    }

    private function _assert_hr_write()
    {
        if (!empty($this->data['is_readonly']) && empty($this->data['is_developer'])) {
            throw new RuntimeException('Mode Read-Only tidak mengizinkan persetujuan atau penolakan.');
        }
    }

    private function _scope_employee($alias = 'k')
    {
        $company = $this->session->userdata('perusahaan_id');
        if ((int)$company !== 1) $this->db->where($alias . '.perusahaan_id', $company);
    }

}
