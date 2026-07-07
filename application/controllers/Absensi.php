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
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($controller)->result(),
            'list_bahan' => $this->M_master->getDataBahan()->result(),
            'tanggal' => $tanggal,
            'status' => $status,
            'karyawan_id' => $karyawan_id,
        );

        // Get filtered attendance data
        $data['absensi'] = $this->Absensi_model->get_filtered_absensi($tanggal, $status, $karyawan_id);

        // Calculate Distance
        $kantor_lat = $conf['perusahaan']['latitude'];
        $kantor_long = $conf['perusahaan']['longitude'];
        $data['radius_kantor'] = $conf['perusahaan']['radius_meter'];

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
                        $target_lat = !empty($absen->lat_perusahaan) ? $absen->lat_perusahaan : $kantor_lat;
                        $target_long = !empty($absen->long_perusahaan) ? $absen->long_perusahaan : $kantor_long;

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

        // Logic for payroll/attendance cycle based on company settings
        if (empty($start_date) && empty($end_date)) {
            $cutoff = isset($conf['perusahaan']['payroll_start_day']) ? (int) $conf['perusahaan']['payroll_start_day'] : 24;
            $current_day = (int) date('d');
            if ($current_day >= $cutoff) {
                $start_date = date('Y-m-' . str_pad($cutoff, 2, '0', STR_PAD_LEFT));
                $end_date = date('Y-m-' . str_pad($cutoff, 2, '0', STR_PAD_LEFT), strtotime('+1 month', strtotime($start_date)));
            } else {
                $start_date = date('Y-m-' . str_pad($cutoff, 2, '0', STR_PAD_LEFT), strtotime('-1 month'));
                $end_date = date('Y-m-' . str_pad($cutoff, 2, '0', STR_PAD_LEFT));
            }
            $bulan = date('Y-m', strtotime($end_date));
        }

        if (empty($bulan) && !empty($end_date)) {
            $bulan = date('Y-m', strtotime($end_date));
        }

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Raport Absensi Karyawan',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
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
            $data['karyawan'] = $this->db->get()->row();

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
                $lembur_calc[$date] = $this->_build_daily_lembur_calc($karyawan_id, $date, $att_day, $day_spls);
                
                // Calculate dynamic meal sum
                if ($att_day && $att_day->jam_pulang != '00:00:00' && !empty($att_day->jam_pulang)) {
                    $out_ts = strtotime($att_day->jam_pulang);
                    $day_num = date('N', strtotime($date));
                    $biaya_makan = 0;
                    if ($day_num <= 6) {
                        $batas_1 = strtotime('19:00:00');
                        $batas_2 = strtotime('22:00:00');
                        if ($out_ts >= $batas_2) $biaya_makan = 20000;
                        elseif ($out_ts >= $batas_1) $biaya_makan = 15000;
                    } else {
                        $batas_minggu_1 = strtotime('14:00:00');
                        $batas_minggu_2 = strtotime('18:00:00');
                        if ($out_ts >= $batas_minggu_2) $biaya_makan = 35000;
                        elseif ($out_ts >= $batas_minggu_1) $biaya_makan = 15000;
                    }
                    $biaya_makan_lembur_total += $biaya_makan;
                }
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
                        if (strtotime($att_map[$d]->jam_masuk) > strtotime($limit . ' +15 minutes'))
                            $stats['telat']++;
                    } else if ($s == 'Sakit')
                        $stats['sakit']++;
                    else if ($s == 'Izin')
                        $stats['izin']++;
                    else if (in_array($s, ['Cuti', 'Cuti Keluarga', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Menikah']))
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
        $id = $this->input->post('id', true);
        $karyawan_id = $this->input->post('karyawan_id', true);
        $tanggal = $this->input->post('tanggal', true);
        $status = $this->input->post('status', true);
        $jam_masuk = $this->input->post('jam_masuk', true);
        $jam_pulang = $this->input->post('jam_pulang', true);
        $keterangan = $this->input->post('keterangan', true);

        $data = [
            'karyawan_id' => $karyawan_id,
            'tanggal' => $tanggal,
            'status' => $status,
            'jam_masuk' => !empty($jam_masuk) ? $jam_masuk . ':00' : '00:00:00',
            'jam_pulang' => !empty($jam_pulang) ? $jam_pulang . ':00' : '00:00:00',
            'keterangan' => $keterangan
        ];

        if (!empty($id)) {
            $this->db->where('id', $id);
            $this->db->update('absensi', $data);
        } else {
            $this->db->insert('absensi', $data);
        }

        $this->session->set_flashdata('success', 'Data absensi berhasil diperbarui!');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function cetak_rekap($karyawan_id = NULL)
    {
        if ($karyawan_id === NULL) {
            show_error('Karyawan ID diperlukan.', 400);
        }
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);

        // Logic for payroll cycle if dates are empty
        if (empty($start_date) || empty($end_date)) {
            $perusahaan_data = $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array();
            $cutoff = isset($perusahaan_data['payroll_start_day']) ? (int) $perusahaan_data['payroll_start_day'] : 24;

            $current_day = (int) date('d');
            if ($current_day >= $cutoff) {
                $start_date = date('Y-m-' . str_pad($cutoff, 2, '0', STR_PAD_LEFT));
                $end_date = date('Y-m-' . str_pad($cutoff, 2, '0', STR_PAD_LEFT), strtotime('+1 month', strtotime($start_date)));
            } else {
                $start_date = date('Y-m-' . str_pad($cutoff, 2, '0', STR_PAD_LEFT), strtotime('-1 month'));
                $end_date = date('Y-m-' . str_pad($cutoff, 2, '0', STR_PAD_LEFT));
            }
        }

        // Get employee info with joins
        $this->db->select('k.*, j.nama_jabatan, d.divisi_nama');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left');
        $this->db->where('k.karyawan_id', $karyawan_id);
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
            $lembur_calc[$date] = $this->_build_daily_lembur_calc($karyawan_id, $date, $att_day, $day_spls);
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
        $userid = $this->session->userdata('id');
        $module = 'Karyawan';
        $action = 'Absen_Masuk';
        $ref = '1';

        // Validate session
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Anda harus login terlebih dahulu!');
            redirect('auth');
        }

        // Validate input
        $this->form_validation->set_rules('karyawan_id', 'Karyawan ID', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('jam_masuk', 'Jam Masuk', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('mobile');
        }

        // Validate Security (IDOR Protection)
        if ($this->input->post('karyawan_id', true) != $this->session->userdata('karyawan_id')) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak bisa mengabsen karyawan lain.');
            redirect('mobile');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('mobile');
        }

        // Cek apakah ada lembur yang belum diselesaikan (Prevent Overlap)
        $cek_lembur_aktif = $this->db->get_where('tbl_lembur', [
            'karyawan_id' => $this->input->post('karyawan_id', true),
            'tanggal' => date('Y-m-d'),
            'jam_selesai' => '00:00:00'
        ])->row();

        if ($cek_lembur_aktif) {
            $this->session->set_flashdata('error', 'Anda masih memiliki sesi LEMBUR AKTIF. Mohon selesaikan/tutup lembur terlebih dahulu sebelum Absen Masuk.');
            redirect('mobile/lembur'); // Redirect langsung ke halaman lembur agar user bisa menutupnya
        }

        // Check if already attended today
        $existing = $this->Absensi_model->check_existing_attendance(
            $this->input->post('karyawan_id', true),
            date('Y-m-d')
        );

        if ($existing) {
            $this->session->set_flashdata('error', 'Anda sudah melakukan absen masuk hari ini!');
            redirect('mobile');
        }

        // Handle Selfie Photo
        $foto_path = '';
        $foto_data = $this->input->post('foto_selfie', false);
        if (!empty($foto_data)) {
            $foto_path = $this->_upload_selfie($foto_data, $this->input->post('karyawan_id', true), 'masuk');
        }

        $os = $this->agent->platform();
        if ($this->agent->is_mobile()) {
            $device = $os . ' (' . $this->agent->mobile() . ')';
        } else {
            $device = 'Web (' . $this->agent->browser() . ' on ' . $os . ')';
        }

        $data = [
            'karyawan_id' => $this->input->post('karyawan_id', true),
            'tanggal' => date('Y-m-d'),
            'jam_masuk' => date('H:i:s'),
            'foto_masuk' => $foto_path,
            'lokasi_absen' => $this->input->post('lokasi_absen', true),
            'device_type' => $device,
            'user_agent' => $this->input->user_agent(),
            'status' => 'Hadir'
        ];

        $result = $this->Absensi_model->insert($data);

        if ($result) {
            log_activity($userid, $action, $module, $ref);

            // --- KIRIM WA NOTIFIKASI ---
            /* 
            $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $data['karyawan_id']])->row();
            if ($karyawan && !empty($karyawan->telepon)) {
                $wa_msg = "*ABSENSI BERHASIL (MASUK)*\n\n";
                $wa_msg .= "Halo *" . $karyawan->nama_lengkap . "*,\n";
                $wa_msg .= "Absen masuk Anda telah berhasil dicatat oleh sistem.\n\n";
                $wa_msg .= "Tanggal: *" . date('d M Y', strtotime($data['tanggal'])) . "*\n";
                $wa_msg .= "Jam Masuk: *" . $data['jam_masuk'] . " WIB*\n\n";
                $wa_msg .= "Selamat bekerja dan semangat!\n";
                $wa_msg .= "_Pesan otomatis via ERP TGM Print_";

                send_wa_notif($karyawan->telepon, $wa_msg);

                // --- NOTIFIKASI TERLAMBAT KE MANAGER ---
                $setting = $this->db->get_where('tbl_wa_setting', ['id' => 1])->row();
                if ($setting && !empty($setting->manager_number)) {
                    // Get Jadwal Masuk (urutan 1=Senin, 7=Minggu)
                    $day_index = (date('N', strtotime($data['tanggal']))); // 1-7
                    $jadwal = $this->db->get_where('tbl_jam_kerja', ['urutan' => $day_index])->row();

                    if ($jadwal) {
                        $jam_masuk = $data['jam_masuk'];
                        $jam_jadwal = $jadwal->jam_masuk;

                        // Cek jika telat > 15 menit
                        $limit_terlambat = date('H:i:s', strtotime($jam_jadwal . ' +15 minutes'));

                        if ($jam_masuk > $limit_terlambat) {
                            $selisih = (strtotime($jam_masuk) - strtotime($jam_jadwal)) / 60;

                            $msg_manager = "*LAPORAN KETERLAMBATAN KARYAWAN*\n\n";
                            $msg_manager .= "Nama: *" . $karyawan->nama_lengkap . "*\n";
                            $msg_manager .= "Tanggal: *" . date('d M Y', strtotime($data['tanggal'])) . "*\n";
                            $msg_manager .= "Jam Absen: *" . $jam_masuk . "*\n";
                            $msg_manager .= "Jadwal Masuk: *" . $jam_jadwal . "*\n";
                            $msg_manager .= "Terlambat: *" . round($selisih) . " Menit*\n\n";
                            $msg_manager .= "_Sistem ERP TGM Print_";

                            send_wa($setting->manager_number, $msg_manager, $setting->wa_token);
                        }
                    }
                }
            }
            */
            // ---------------------------

            $this->session->set_flashdata('success', 'Absen masuk berhasil dicatat!');
        } else {
            $this->session->set_flashdata('error', 'Gagal mencatat absen masuk!');
        }

        redirect('mobile');
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
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[izin,sakit,cuti]');
        $this->form_validation->set_rules('durasi_tipe', 'Tipe Durasi', 'required|in_list[jam,hari]');
        $this->form_validation->set_rules('durasi_jumlah', 'Jumlah Durasi', 'required|numeric|greater_than[0]');
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

        if ($existing_absensi && $existing_absensi->status != 'Alpha') {
            $this->session->set_flashdata('error', 'Sudah ada absensi untuk tanggal ini!');
            redirect('mobile/sakit');
        }

        // Ambil aturan jam kerja dari database
        $daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hariNama = $daysIndo[date('w', strtotime($tanggal))];
        $ruleJam = $this->db->get_where('tbl_jam_kerja', ['hari' => $hariNama])->row_array();

        // Hitung jam masuk dan jam pulang berdasarkan durasi
        $jam_masuk = !empty($ruleJam['jam_masuk']) ? $ruleJam['jam_masuk'] : '08:00:00';
        $jam_pulang = !empty($ruleJam['jam_pulang']) ? $ruleJam['jam_pulang'] : '17:00:00';

        if ($durasi_tipe == 'jam') {
            // Jika izin per jam, hitung jam pulang
            $jam_pulang = date('H:i:s', strtotime($jam_masuk) + ($durasi_jumlah * 3600));
        } else {
            // Jika izin per hari, set jam pulang sesuai aturan DB
        }

        // Konversi status ke format yang sesuai dengan enum tabel
        $status_absensi = 'Hadir'; // Default
        if ($status == 'izin') {
            $status_absensi = 'Izin';
        } elseif ($status == 'sakit') {
            $status_absensi = 'Sakit';
        } elseif ($status == 'cuti') {
            $status_absensi = 'Izin'; // Cuti masuk ke kategori Izin
        }

        // Format keterangan dengan informasi durasi
        $keterangan_lengkap = $kegiatan;
        if ($durasi_tipe == 'jam') {
            $keterangan_lengkap .= " (Durasi: {$durasi_jumlah} jam)";
        } else {
            $keterangan_lengkap .= " (Durasi: {$durasi_jumlah} hari)";
        }

        // Khusus Pulang Cepat
        $jam_pulang_cepat = null;
        if ($status == 'pulang_cepat') {
            $jam_pulang_cepat = $this->input->post('jam_pulang_cepat', true);
            if (empty($jam_pulang_cepat)) {
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
                $durasi_jumlah = round(($end_seconds - $start_seconds) / 3600, 1);
            } else {
                $durasi_jumlah = 0; // Sudah melebihi jam kerja normal (untuk hitungan ijin jam)
            }
            $durasi_tipe = 'jam';
        }

        // Handle Upload Dokumen/Surat Sakit
        $dokumen_path = '';
        if ($status == 'sakit' && !empty($_FILES['dokumen']['name'])) {
            $upload_res = $this->_upload_dokumen($karyawan_id);
            if (!$upload_res['status']) {
                $this->session->set_flashdata('error', 'Gagal mengunggah surat sakit: ' . $upload_res['error'] . ' (Pastikan format PDF/JPG/PNG max 5MB)');
                redirect('mobile/sakit');
            }
            $dokumen_path = $upload_res['path'];
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

        // Insert ke database
        $this->db->trans_start();

        $result = $this->db->insert('tbl_pengajuan_izin', $data);

        if ($result) {
            // Log aktivitas
            log_activity($userid, $action, $module, $ref);

            // Set flash message sukses
            $status_text = ucfirst($status);
            $this->session->set_flashdata('success', "Pengajuan {$status_text} berhasil dikirim dan menunggu persetujuan!");

            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Gagal mengirim pengajuan!');
        }

        $this->db->trans_complete();

        redirect('mobile/sakit');
    }

    public function absen_pulang()
    {
        $userid = $this->session->userdata('id');
        $module = 'Karyawan';
        $action = 'Absen_Pulang';
        $ref = '1';

        // Validate session
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Anda harus login terlebih dahulu!');
            redirect('auth');
        }

        $this->form_validation->set_rules('karyawan_id', 'Karyawan ID', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('jam_pulang', 'Jam Pulang', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('mobile');
        }

        // Validate Security (IDOR Protection)
        if ($this->input->post('karyawan_id', true) != $this->session->userdata('karyawan_id')) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak bisa mengabsen karyawan lain.');
            redirect('mobile');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('mobile');
        }

        // Handle Selfie Photo
        $foto_path = '';
        $foto_data = $this->input->post('foto_selfie', false);
        if (!empty($foto_data)) {
            $foto_path = $this->_upload_selfie($foto_data, $this->input->post('karyawan_id', true), 'pulang');
        }

        $where = [
            'karyawan_id' => $this->input->post('karyawan_id', true),
            'tanggal' => date('Y-m-d'),
        ];

        $os = $this->agent->platform();
        if ($this->agent->is_mobile()) {
            $device = $os . ' (' . $this->agent->mobile() . ')';
        } else {
            $device = 'Web (' . $this->agent->browser() . ' on ' . $os . ')';
        }

        $update = [
            'jam_pulang' => date('H:i:s'),
            'foto_pulang' => $foto_path,
            'device_type' => $device, // Update device pada saat pulang (jika beda device)
            'user_agent' => $this->input->user_agent(),
        ];

        $result = $this->Absensi_model->update($where, $update);

        if ($result) {
            log_activity($userid, $action, $module, $ref);

            // --- KIRIM WA NOTIFIKASI ---
            /*
            $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $where['karyawan_id']])->row();
            if ($karyawan && !empty($karyawan->telepon)) {
                $wa_msg = "*ABSENSI BERHASIL (PULANG)*\n\n";
                $wa_msg .= "Halo *" . $karyawan->nama_lengkap . "*,\n";
                $wa_msg .= "Absen pulang Anda telah berhasil dicatat oleh sistem.\n\n";
                $wa_msg .= "Tanggal: *" . date('d M Y', strtotime($where['tanggal'])) . "*\n";
                $wa_msg .= "Jam Pulang: *" . $update['jam_pulang'] . " WIB*\n\n";
                $wa_msg .= "Hati-hati di jalan dan selamat beristirahat!\n";
                $wa_msg .= "_Pesan otomatis via ERP TGM Print_";

                send_wa_notif($karyawan->telepon, $wa_msg);
            }
            */
            // ---------------------------

            $this->session->set_flashdata('success', 'Absen pulang berhasil dicatat!');
        } else {
            $this->session->set_flashdata('error', 'Gagal mencatat absen pulang!');
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

    private function _build_daily_lembur_calc($karyawan_id, $date, $att_day, $day_spls = [])
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

        return [
            'pagi'    => $total_pagi,
            'wajib'   => $total_wajib,
            'kerjaan' => $total_kerjaan,
            'total'   => $total_pagi + $total_wajib + $total_kerjaan,
        ];
    }

    private function _upload_selfie($base64_image, $karyawan_id, $type)
    {
        try {
            // Robust Base64 Decoding
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type_match)) {
                $data = substr($base64_image, strpos($base64_image, ',') + 1);
                $ext = strtolower($type_match[1]); // jpeg, png, etc
                if (!in_array($ext, ['jpg', 'jpeg', 'png']))
                    $ext = 'jpg';
                $data = base64_decode($data);
            } else {
                return '';
            }

            $filename = 'absen_' . $type . '_' . $karyawan_id . '_' . date('YmdHis') . '.' . $ext;
            $relative_path = 'uploads/absensi/' . $filename;
            $absolute_path = FCPATH . $relative_path;

            // Ensure directory is ready
            if (!is_dir(FCPATH . 'uploads/absensi')) {
                mkdir(FCPATH . 'uploads/absensi', 0777, true);
            }

            if (file_put_contents($absolute_path, $data)) {
                return $relative_path;
            }
        } catch (Throwable $e) {
            log_message('error', 'Selfie Upload Fatal Error: ' . $e->getMessage());
        }
        return '';
    }

    public function scan()
    {
        $nip = $this->input->post('nip_qr', true);
        $karyawan = $this->M_master->get_karyawan_by_nik($nip);

        if ($karyawan) {
            $data = [
                'karyawan_id' => $karyawan->karyawan_id,
                'tanggal' => date('Y-m-d'),
                'jam_masuk' => date('H:i:s'),
                'status' => 'Hadir'
            ];
            $this->Absensi_model->insert($data);
            $this->session->set_flashdata('success', 'Absen berhasil dicatat!');
        } else {
            $this->session->set_flashdata('error', 'NIK tidak ditemukan!');
        }

        if (!empty($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect('dashboard');
        }
    }

    public function export_excel()
    {
        $tanggal = $this->input->get('tanggal', true) ? $this->input->get('tanggal', true) : date('Y-m-d');
        $status = $this->input->get('status', true) ? $this->input->get('status', true) : '';
        $karyawan_id = $this->input->get('karyawan_id', true) ? $this->input->get('karyawan_id', true) : '';

        $data['absensi'] = $this->Absensi_model->get_filtered_absensi($tanggal, $status, $karyawan_id);
        $data['tanggal'] = $tanggal;

        $this->load->view('admin/absensi/export_excel', $data);
    }

    public function export_pdf()
    {
        $tanggal = $this->input->get('tanggal', true) ? $this->input->get('tanggal', true) : date('Y-m-d');
        $status = $this->input->get('status', true) ? $this->input->get('status', true) : '';
        $karyawan_id = $this->input->get('karyawan_id', true) ? $this->input->get('karyawan_id', true) : '';

        $data['absensi'] = $this->Absensi_model->get_filtered_absensi($tanggal, $status, $karyawan_id);
        $data['tanggal'] = $tanggal;

        $this->load->view('admin/absensi/export_pdf', $data);
    }

    public function export_excel_rekap()
    {
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);

        $data['rekap_data'] = $this->Absensi_model->get_rekap_all_karyawan($start_date, $end_date);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $this->load->view('admin/absensi/export_excel_rekap', $data);
    }

    public function export_pdf_rekap()
    {
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);

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
                        if ($att->jam_pulang) {
                            $work_hours = (strtotime($att->jam_pulang) - $arrival_time) / 3600;
                            $stats['total_work_hours'] += $work_hours;
                        }
                    }
                    break;
                case 'Izin':
                    // Check if it's Cuti
                    if (stripos($att->keterangan, 'cuti') !== false) {
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
                    $stats['cuti']++;
                    break;
                case 'Alpha':
                default:
                    // If status is Alpha or unknown/empty, try to detect from keterangan (notes)
                    if (stripos($att->keterangan, 'cuti') !== false) {
                        $stats['cuti']++;
                    } elseif (stripos($att->keterangan, 'sakit') !== false) {
                        $stats['sakit']++;
                    } elseif (stripos($att->keterangan, 'izin') !== false) {
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
        $userid = $this->session->userdata('id');
        $module = 'Karyawan';
        $action = 'Delete_Absensi';
        $ref = $id;

        $result = $this->Absensi_model->delete($id);

        if ($result) {
            log_activity($userid, $action, $module, $ref);
            $this->session->set_flashdata('success', 'Data absensi berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data absensi!');
        }

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
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        $data['absensi'] = $this->Absensi_model->get_by_id($id);
        $data['karyawan_list'] = $this->M_master->get_karyawan()->result();

        if (empty($data['absensi'])) {
            show_404();
        }

        $this->load->view('layout_admin/head', $data);
        $this->load->view('layout_admin/top_header', $data);
        $this->load->view('layout_admin/sidebar', $data);
        $this->load->view('admin/absensi/edit', $data);
        $this->load->view('layout_admin/footer');
    }

    public function update()
    {
        $userid = $this->session->userdata('id');
        $module = 'Karyawan';
        $action = 'Update_Absensi';
        $ref = $this->input->post('id', true);

        $this->form_validation->set_rules('karyawan_id', 'Karyawan ID', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('absensi/edit/' . $this->input->post('id', true));
        }

        $data = [
            'karyawan_id' => $this->input->post('karyawan_id', true),
            'tanggal' => $this->input->post('tanggal', true),
            'jam_masuk' => $this->input->post('jam_masuk', true),
            'jam_pulang' => $this->input->post('jam_pulang', true),
            'status' => $this->input->post('status', true),
            'keterangan' => $this->input->post('keterangan', true),
        ];

        $result = $this->Absensi_model->update_by_id($this->input->post('id', true), $data);

        if ($result) {
            $status = $data['status'];
            $karyawan_id = $data['karyawan_id'];
            $tanggal = $data['tanggal'];

            if (strpos(strtolower($status), 'cuti') !== false) {
                // Auto insert an approved leave record if it doesn't exist
                $exist = $this->db->get_where('tbl_pengajuan_izin', [
                    'karyawan_id' => $karyawan_id,
                    'tanggal_mulai' => $tanggal,
                    'status' => 'Approved'
                ])->row();

                if (!$exist) {
                    $this->db->insert('tbl_pengajuan_izin', [
                        'karyawan_id' => $karyawan_id,
                        'tipe_izin' => $status,
                        'tanggal_mulai' => $tanggal,
                        'durasi_jumlah' => 1,
                        'keterangan' => 'Koreksi Data Absensi (HR)',
                        'status' => 'Approved',
                        'created_at' => date('Y-m-d H:i:s'),
                        'responded_at' => date('Y-m-d H:i:s'),
                        'responded_by' => $userid
                    ]);
                }
            } else {
                // If reverted back to Hadir/Alpha etc, remove auto-generated leave record
                $this->db->where('karyawan_id', $karyawan_id);
                $this->db->where('tanggal_mulai', $tanggal);
                $this->db->where('status', 'Approved');
                $this->db->where('keterangan', 'Koreksi Data Absensi (HR)');
                $this->db->delete('tbl_pengajuan_izin');
            }

            // Sync the quota so the changes reflect immediately on Sisa Cuti
            $this->_sync_quota_cuti($karyawan_id);

            log_activity($userid, $action, $module, $ref);
            $this->session->set_flashdata('success', 'Data absensi berhasil diperbarui dan Kuota Cuti disinkronisasi!');
            redirect('absensi');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data absensi!');
            redirect('absensi/edit/' . $this->input->post('id', true));
        }
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
            $start_date = $bulan . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
        } else {
            // If range is provided, make sure bulan is consistent or empty
            if (empty($bulan)) {
                $bulan = date('Y-m', strtotime($start_date));
            }
        }

        $nama = $this->input->get('nama', true) ? $this->input->get('nama', true) : '';
        $jabatan = $this->input->get('jabatan', true) ? $this->input->get('jabatan', true) : '';
        $divisi = $this->input->get('divisi', true) ? $this->input->get('divisi', true) : '';
        $perusahaan = $this->input->get('perusahaan', true) ? $this->input->get('perusahaan', true) : '';

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
            'logo' => $conf['perusahaan']['logo'],
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
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
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
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        // Get Pending Requests
        $this->db->select('p.*, k.nama_lengkap, k.nik, j.nama_jabatan as jabatan');
        $this->db->from('tbl_pengajuan_izin p');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = p.karyawan_id');
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->where('p.status', 'Pending');
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->order_by('p.created_at', 'DESC');
        $data['pengajuan'] = $this->db->get()->result();

        $this->load_admin_view('admin/absensi/approval', $data);
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
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        // Get Pending Requests
        $this->db->select('p.*, k.nama_lengkap, k.nik, j.nama_jabatan as jabatan');
        $this->db->from('tbl_pengajuan_izin p');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = p.karyawan_id');
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
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        $year = date('Y');

        // --- SINKRONISASI KUOTA MASSAL SEBELUM DITAMPILKAN ---
        // Agar HRD selalu melihat data sisa cuti yang paling akurat 100%
        $karyawan_aktif = $this->db->select('karyawan_id')
            ->from('tbl_karyawan')
            ->where("nama_lengkap NOT LIKE '%Terminal%'")
            ->where("nama_lengkap NOT LIKE '%DUMMY%'")
            ->where("nama_lengkap NOT LIKE 'DEPT.%'")
            ->where("nama_lengkap NOT LIKE 'OPERATOR %'")
            ->get()->result();
            
        foreach ($karyawan_aktif as $k) {
            $this->_sync_quota_cuti($k->karyawan_id);
        }
        // -----------------------------------------------------

        $this->db->select('k.karyawan_id, k.nama_lengkap, j.nama_jabatan, q.quota_awal, q.terpakai, q.sisa, q.menikah_sisa, q.duka_sisa, q.sakitkel_sisa, q.melahirkansuami_sisa');
        $this->db->from('tbl_karyawan k');
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
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->where('p.status', 'Pending');
        $this->db->where_in('p.tipe_izin', ['Cuti', 'Cuti Keluarga', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Menikah']);
        $this->db->order_by('p.created_at', 'DESC');
        $data['pending_cuti'] = $this->db->get()->result();

        $this->load_admin_view('admin/absensi/sisa_cuti', $data);
    }

    public function get_riwayat_cuti($karyawan_id)
    {
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

                $delete_btn = '';
                if ($r->status == 'Rejected' || $r->status == 'Ditolak') {
                    $delete_btn = '<button class="btn btn-sm btn-outline-danger mt-2 py-0 px-2" style="font-size: 0.7rem;" onclick="hapusRiwayat(' . $r->id . ')"><i class="bi bi-trash"></i> Hapus</button>';
                }

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
        // Pastikan hanya bisa menghapus jika statusnya Rejected (Ditolak)
        $this->db->where('id', $id);
        $this->db->where_in('status', ['Ditolak', 'Rejected']);
        
        $this->db->delete('tbl_pengajuan_izin');

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan atau status bukan Ditolak']);
        }
    }

    public function riwayat_approval()
    {
        $userid = $this->session->userdata('id');
        $start_date = $this->input->get('start_date', true) ?? date('Y-m-01');
        $end_date = $this->input->get('end_date', true) ?? date('Y-m-t');

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => 'riwayat_approval',
            'title' => 'Riwayat Pengajuan Izin & Cuti',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
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

    private function _sync_quota_cuti($karyawan_id)
    {
        $year = date('Y');

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
                'sisa' => 12
            ];
            $this->db->insert('tbl_quota_cuti', $quota_init);
            $quota_total = 12;
        } else {
            $quota_total = ($quota_row->quota_awal > 0) ? $quota_row->quota_awal : 12;
        }

        // Ambil semua pengajuan tahun ini
        $requests = $this->db->where('karyawan_id', $karyawan_id)
            ->where('YEAR(tanggal_mulai)', $year)
            ->where_in('status', ['Pending', 'Approved', 'Disetujui', 'approved', 'disetujui'])
            ->get('tbl_pengajuan_izin')->result();

        $used_annual_approved = 0;
        $used_annual_pending = 0;

        $limits = [
            'cuti menikah' => 7,
            'cuti duka' => 3,
            'cuti keluarga sakit' => 2,
            'cuti melahirkan suami' => 3
        ];

        $usage_special_approved = ['cuti menikah' => 0, 'cuti duka' => 0, 'cuti keluarga sakit' => 0, 'cuti melahirkan suami' => 0];
        $usage_special_pending = ['cuti menikah' => 0, 'cuti duka' => 0, 'cuti keluarga sakit' => 0, 'cuti melahirkan suami' => 0];

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
        $melahirkan_used = min($limits['cuti melahirkan suami'], $usage_special_approved['cuti melahirkan suami'] + $usage_special_pending['cuti melahirkan suami']);

        $this->db->where(['karyawan_id' => $karyawan_id, 'tahun' => $year])
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
                'melahirkansuami_terpakai' => $melahirkan_used,
                'melahirkansuami_sisa' => max(0, $limits['cuti melahirkan suami'] - $melahirkan_used)
            ]);
    }

    public function approve_action($id)
    {
        $userid = $this->session->userdata('id');
        $request = $this->db->get_where('tbl_pengajuan_izin', ['id' => $id])->row();

        if (!$request) {
            $this->session->set_flashdata('error', 'Data pengajuan tidak ditemukan!');
            redirect('absensi/approval');
        }

        if ($request->status != 'Pending') {
            $this->session->set_flashdata('error', 'Pengajuan sudah diproses!');
            redirect('absensi/approval');
        }

        $this->db->trans_start();

        // 1. Update status di tbl_pengajuan_izin
        $update_data = [
            'status' => 'Approved',
            'responded_at' => date('Y-m-d H:i:s'),
            'responded_by' => $userid
        ];
        $this->db->where('id', $id);
        $this->db->update('tbl_pengajuan_izin', $update_data);

        // 2. Insert ke tabel absensi

        // Prepare data variables
        $karyawan_id = $request->karyawan_id;
        $tanggal_mulai = $request->tanggal_mulai;
        $durasi_jumlah = $request->durasi_jumlah;
        $durasi_tipe = $request->durasi_tipe;
        $tipe_izin = $request->tipe_izin; // izin, sakit, cuti
        $keterangan = $request->keterangan;
        $dokumen = $request->dokumen;

        // Determine Absensi Status
        $status_absensi = 'Hadir';
        $tipe_izin_lower = strtolower($tipe_izin);

        if ($tipe_izin_lower == 'izin') {
            $status_absensi = 'Izin';
        } elseif ($tipe_izin_lower == 'sakit') {
            $status_absensi = 'Sakit';
        } elseif (strpos($tipe_izin_lower, 'cuti') !== false) {
            $status_map = [
                'cuti' => 'Cuti',
                'cuti keluarga' => 'Cuti Keluarga',
                'cuti keluarga sakit' => 'Cuti Keluarga Sakit',
                'cuti sakit' => 'Cuti Sakit',
                'cuti duka' => 'Cuti Duka',
                'cuti melahirkan' => 'Cuti Melahirkan',
                'cuti melahirkan suami' => 'Cuti Melahirkan Suami',
                'cuti menikah' => 'Cuti Menikah'
            ];
            $status_absensi = $status_map[$tipe_izin_lower] ?? 'Cuti';
        }

        // Calculation logic consistent with previous izin_absen
        $jam_masuk = '08:00:00';
        $jam_pulang = '08:00:00';

        if ($tipe_izin_lower == 'pulang_cepat') {
            $jam_pulang = $request->jam_pulang_cepat;
            $keterangan_lengkap = $keterangan . " (Pulang Cepat jam {$jam_pulang})";

            // Cek apakah sudah ada baris di tabel absensi untuk hari tersebut
            $existing_absensi = $this->db->get_where('absensi', [
                'karyawan_id' => $karyawan_id,
                'tanggal' => $tanggal_mulai
            ])->row();

            $data_absensi = [
                'karyawan_id' => $karyawan_id,
                'tanggal' => $tanggal_mulai,
                'jam_pulang' => $jam_pulang,
                'status' => 'Hadir',
                'keterangan' => $keterangan_lengkap,
                'dokumen' => $dokumen,
            ];

            if ($existing_absensi) {
                $this->db->where('id', $existing_absensi->id);
                $this->db->update('absensi', $data_absensi);
            } else {
                $data_absensi['jam_masuk'] = '08:00:00'; // Default jika belum absen sama sekali
                $data_absensi['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('absensi', $data_absensi);
            }
        } elseif ($durasi_tipe == 'jam') {
            $jam_pulang = date('H:i:s', strtotime($jam_masuk) + ($durasi_jumlah * 3600));
            $keterangan_lengkap = $keterangan . " (Durasi: {$durasi_jumlah} jam)";

            // Insert single day
            $data_absensi = [
                'karyawan_id' => $karyawan_id,
                'tanggal' => $tanggal_mulai,
                'jam_masuk' => $jam_masuk,
                'jam_pulang' => $jam_pulang,
                'status' => $status_absensi,
                'keterangan' => $keterangan_lengkap,
                'dokumen' => $dokumen,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('absensi', $data_absensi);
        } else {
            // Hari
            $keterangan_lengkap = $keterangan . " (Durasi: {$durasi_jumlah} hari)";
            $jam_pulang = '17:00:00';

            // Loop to insert for each day
            for ($i = 0; $i < $durasi_jumlah; $i++) {
                $current_date = date('Y-m-d', strtotime($tanggal_mulai . ' + ' . $i . ' days'));

                // Check if exists to avoid duplicate, but UPDATE status to the approved one
                $exist = $this->db->get_where('absensi', ['karyawan_id' => $karyawan_id, 'tanggal' => $current_date])->row();

                $data_absensi = [
                    'karyawan_id' => $karyawan_id,
                    'tanggal' => $current_date,
                    'jam_masuk' => $jam_masuk,
                    'jam_pulang' => $jam_pulang,
                    'status' => $status_absensi,
                    'keterangan' => $keterangan_lengkap,
                    'dokumen' => $dokumen
                ];

                if ($exist) {
                    // UPDATE existing record (e.g. from Hadir to Sakit)
                    $this->db->where('id', $exist->id);
                    $this->db->update('absensi', $data_absensi);
                } else {
                    // INSERT new record
                    $data_absensi['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('absensi', $data_absensi);
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Gagal memproses persetujuan!');
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Pengajuan berhasil disetujui dan data absensi telah diperbarui.');

            // --- KIRIM NOTIFIKASI FCM KE MOBILE APP ---
            $this->db->select('fcm_token');
            $this->db->from('tbl_karyawan');
            $this->db->where('karyawan_id', $karyawan_id);
            $karyawan_fcm = $this->db->get()->row();

            if ($karyawan_fcm && !empty($karyawan_fcm->fcm_token)) {
                try {
                    $this->load->library('firebase_cloud_messaging');
                    $title = 'Pengajuan Disetujui';
                    $body = 'Pengajuan ' . ucfirst($tipe_izin) . ' Anda tanggal ' . date('d M Y', strtotime($tanggal_mulai)) . ' telah disetujui oleh HRD.';
                    $this->firebase_cloud_messaging->send_notification($karyawan_fcm->fcm_token, $title, $body);
                } catch (\Throwable $e) {
                    log_message('error', 'Gagal kirim FCM di Approve Izin: ' . $e->getMessage());
                }
            }
            // ------------------------------------------
        }

        $this->_sync_quota_cuti($karyawan_id);

        $this->db->trans_complete();
        
        $ref = $this->input->get('ref');
        if ($ref == 'sisa_cuti') {
            redirect('absensi/sisa_cuti');
        }
        redirect('absensi/approval');
    }
    public function reject_action($id)
    {
        $userid = $this->session->userdata('id');

        $request = $this->db->get_where('tbl_pengajuan_izin', ['id' => $id])->row();
        if (!$request) {
            $this->session->set_flashdata('error', 'Data pengajuan tidak ditemukan!');
            redirect('absensi/approval');
        }

        $update_data = [
            'status' => 'Rejected',
            'responded_at' => date('Y-m-d H:i:s'),
            'responded_by' => $userid
        ];

        $this->db->where('id', $id);
        $result = $this->db->update('tbl_pengajuan_izin', $update_data);

        if ($result) {
            $this->session->set_flashdata('success', 'Pengajuan berhasil ditolak.');

            // --- KIRIM NOTIFIKASI FCM KE MOBILE APP ---
            $this->db->select('fcm_token');
            $this->db->from('tbl_karyawan');
            $this->db->where('karyawan_id', $request->karyawan_id);
            $karyawan_fcm = $this->db->get()->row();

            if ($karyawan_fcm && !empty($karyawan_fcm->fcm_token)) {
                try {
                    $this->load->library('firebase_cloud_messaging');
                    $title = 'Pengajuan Ditolak';
                    $body = 'Maaf, pengajuan ' . ucfirst($request->tipe_izin) . ' Anda tanggal ' . date('d M Y', strtotime($request->tanggal_mulai)) . ' tidak disetujui.';
                    $this->firebase_cloud_messaging->send_notification($karyawan_fcm->fcm_token, $title, $body);
                } catch (\Throwable $e) {
                    log_message('error', 'Gagal kirim FCM di Reject Izin: ' . $e->getMessage());
                }
            }
            // ------------------------------------------

            $this->_sync_quota_cuti($request->karyawan_id);

        } else {
            $this->session->set_flashdata('error', 'Gagal menolak pengajuan.');
        }

        $ref = $this->input->get('ref');
        if ($ref == 'sisa_cuti') {
            redirect('absensi/sisa_cuti');
        }
        redirect('absensi/approval');
    }

    public function save_manual_batch()
    {
        if (!$this->input->is_ajax_request())
            exit('No direct script access allowed');

        $tanggal = $this->input->post('tanggal', true);
        $attendance = $this->input->post('attendance', true); // Array of data

        if (empty($tanggal) || empty($attendance)) {
            echo json_encode(['status' => false, 'message' => 'Data tidak lengkap']);
            return;
        }

        $this->db->trans_start();

        foreach ($attendance as $row) {
            $karyawan_id = $row['karyawan_id'];
            $status = $row['status'];
            $jam_masuk = !empty($row['jam_masuk']) ? $row['jam_masuk'] : '00:00:00';
            $jam_pulang = !empty($row['jam_pulang']) ? $row['jam_pulang'] : '00:00:00';
            $keterangan = $row['keterangan'];
            $id = isset($row['id']) ? $row['id'] : null;

            $data = [
                'karyawan_id' => $karyawan_id,
                'tanggal' => $tanggal,
                'status' => $status,
                'jam_masuk' => $jam_masuk,
                'jam_pulang' => $jam_pulang,
                'keterangan' => $keterangan,
            ];

            if (!empty($id)) {
                $this->db->where('id', $id);
                $this->db->update('absensi', $data);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                // Prevent duplicates if ID was missing but record exists
                $exist = $this->db->get_where('absensi', ['karyawan_id' => $karyawan_id, 'tanggal' => $tanggal])->row();
                if ($exist) {
                    $this->db->where('id', $exist->id);
                    $this->db->update('absensi', $data);
                } else {
                    $this->db->insert('absensi', $data);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => false, 'message' => 'Database Error']);
        } else {
            echo json_encode(['status' => true, 'message' => 'Berhasil']);
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
        if (empty($end_date)) {
            $end_date = date('Y-m-t', strtotime($start_date));
        }

        $jabatan = $this->input->get('jabatan', true) ? $this->input->get('jabatan', true) : '';
        $divisi = $this->input->get('divisi', true) ? $this->input->get('divisi', true) : '';

        // Get all active employees
        $this->db->select('k.karyawan_id, k.nama_lengkap, k.nik, j.nama_jabatan as jabatan, d.divisi_nama as divisi');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->join('tbl_operator o', 'o.operator_id = k.divisi_id', 'left');
        $this->db->join('users u', 'u.karyawan_id = k.karyawan_id', 'left');
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%'); // Exclude specific division if needed
        $this->db->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left');
        $this->db->where('k.status_karyawan !=', 'Tidak Aktif');
        $this->db->where('k.divisi_id !=', 8); // Exclude specific division if needed
        if ($jabatan)
            $this->db->where('j.nama_jabatan', $jabatan);
        if ($divisi)
            $this->db->where('d.divisi_nama', $divisi);
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
            'logo' => $conf['perusahaan']['logo'],
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
        if (empty($lat1) || empty($lon1) || empty($lat2) || empty($lon2)) {
            return 0;
        }

        $earthRadius = 6371000; // Radius of Earth in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($angle * $earthRadius);
    }
    public function sync_to_payroll()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $karyawan_id = $this->input->post('karyawan_id', true);
        $bulan = $this->input->post('bulan', true);
        $start_date = $this->input->post('start_date', true);
        $end_date = $this->input->post('end_date', true);

        if (empty($bulan) && !empty($end_date)) {
            $bulan = date('Y-m', strtotime($end_date));
        }

        $data = [
            'karyawan_id' => $karyawan_id,
            'bulan' => $bulan,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_hadir' => $this->input->post('total_hadir', true),
            'total_izin' => $this->input->post('total_izin', true),
            'total_sakit' => $this->input->post('total_sakit', true),
            'total_alpha' => $this->input->post('total_alpha', true),
            'total_terlambat' => $this->input->post('total_terlambat', true),
            'lembur_pagi' => $this->input->post('total_pagi', true),
            'lembur_wajib_tambahan' => $this->input->post('total_wajib', true),
            'lembur_kerjaan' => $this->input->post('total_kerjaan', true),
            'total_lembur' => $this->input->post('total_lembur', true),
            'biaya_makan_lembur' => $this->input->post('biaya_makan', true) ?: 0,
            'sync_at' => date('Y-m-d H:i:s')
        ];

        // Disable DB debug temporarily to prevent 500 error on migration failure
        $db_debug_original = $this->db->db_debug;
        $this->db->db_debug = false;

        // Ensure table exists
        $this->db->query("CREATE TABLE IF NOT EXISTS tbl_absensi_rekap_karyawan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            karyawan_id INT,
            bulan VARCHAR(10),
            start_date DATE,
            end_date DATE,
            total_hadir INT,
            total_izin INT,
            total_sakit INT,
            total_alpha INT,
            total_terlambat INT,
            lembur_pagi FLOAT,
            lembur_wajib_tambahan FLOAT,
            lembur_kerjaan FLOAT,
            total_lembur FLOAT,
            biaya_makan_lembur INT DEFAULT 0,
            sync_at DATETIME,
            UNIQUE KEY unique_sync (karyawan_id, bulan)
        )");

        // Dynamic Migration: Add lembur_kerjaan if it doesn't exist (for live server update)
        if (!$this->db->field_exists('lembur_kerjaan', 'tbl_absensi_rekap_karyawan')) {
            $this->db->query("ALTER TABLE tbl_absensi_rekap_karyawan ADD COLUMN lembur_kerjaan FLOAT NULL DEFAULT 0 AFTER lembur_wajib_tambahan");
        }
        
        // Dynamic Migration: Add biaya_makan_lembur if it doesn't exist
        if (!$this->db->field_exists('biaya_makan_lembur', 'tbl_absensi_rekap_karyawan')) {
            $this->db->query("ALTER TABLE tbl_absensi_rekap_karyawan ADD COLUMN biaya_makan_lembur INT NULL DEFAULT 0 AFTER total_lembur");
        }

        // Restore DB debug
        $this->db->db_debug = $db_debug_original;

        // UPSERT logic
        $this->db->where('karyawan_id', $karyawan_id);
        $this->db->where('bulan', $bulan);
        $existing = $this->db->get('tbl_absensi_rekap_karyawan')->row();

        if ($existing) {
            $this->db->where('id', $existing->id);
            $res = $this->db->update('tbl_absensi_rekap_karyawan', $data);
        } else {
            $res = $this->db->insert('tbl_absensi_rekap_karyawan', $data);
        }

        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data ke database rekap payroll.']);
        }
    }
}
