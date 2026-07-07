<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Api_mobile Controller
 * 
 * @property CI_DB_query_builder $db
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Loader $load
 * @property CI_Session $session
 * @property CI_User_agent $agent
 * @property M_transaksi $M_transaksi
 * @property M_master $M_master
 * @property Absensi_model $Absensi_model
 * @property Payroll_model $Payroll_model
 */
class Api_mobile extends MY_Controller
{
    private $secret_key;
    private $foto_col = 'images'; // Default

    public function __construct()
    {
        parent::__construct();
        error_reporting(0);
        ini_set('display_errors', 0);

        $this->secret_key = 'RahasiaNegara123!@#';
        date_default_timezone_set("Asia/Jakarta");

        $this->load->model('Absensi_model');
        $this->load->model('M_master');
        $this->load->model('M_transaksi');
        $this->load->library('user_agent');

        // AUTO-DETECT Column Name (image vs images)
        $cols = $this->db->list_fields('users');
        if (in_array('image', $cols)) {
            $this->foto_col = 'image';
        } elseif (in_array('images', $cols)) {
            $this->foto_col = 'images';
        } else {
            $this->foto_col = null;
        }

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }
    }

    /**
     * @return void
     */
    private function json_response($status, $message, $data = null, $code = 200)
    {
        $this->output->set_content_type('application/json');
        $this->output->set_status_header($code);
        $response = ['status' => $status, 'message' => $message, 'data' => $data];
        echo json_encode($response);
        exit;
    }

    public function check_version()
    {
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $q = $this->db->get('tbl_app_version');
        $version = $q ? $q->row_array() : null;

        if ($version) {
            $this->json_response(true, 'Latest version fetched', [
                'latest_version' => $version['version_name'],
                'build_number' => (int) $version['version_code'],
                'force_update' => (bool) $version['is_force_update'],
                'download_url' => $version['download_url'],
                'message' => $version['update_message']
            ]);
        } else {
            $this->json_response(false, 'Version data not found', null, 404);
        }
    }

    public function login()
    {
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);

        if (!$username || !$password)
            $this->json_response(false, 'Isi username & password', null, 400);

        $q_user = $this->db->get_where('users', ['username' => $username]);
        $user = $q_user ? $q_user->row_array() : null;

        $is_valid_password = false;
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $is_valid_password = true;
            } elseif (md5($password) === $user['password']) {
                $is_valid_password = true;
                // Auto-migrate to BCRYPT
                $this->db->update('users', ['password' => password_hash($password, PASSWORD_DEFAULT)], ['user_id' => $user['user_id']]);
                $user['password'] = password_hash($password, PASSWORD_DEFAULT); // Update in-memory for consistency
            }
        }

        if ($is_valid_password) {
            if ($user['active'] != 1)
                $this->json_response(false, 'Akun non-aktif', null, 401);

            // Cek dan update FCM Token jika dikirim
            $fcm_token = $this->input->post('fcm_token', true);
            if ($fcm_token) {
                // Update tbl_karyawan dgn token baru
                if ($this->db->field_exists('fcm_token', 'tbl_karyawan')) {
                    $this->db->where('karyawan_id', $user['karyawan_id']);
                    $this->db->update('tbl_karyawan', ['fcm_token' => $fcm_token]);
                } else if ($this->db->field_exists('fcm_token', 'users')) {
                    $this->db->where('user_id', $user['user_id']);
                    $this->db->update('users', ['fcm_token' => $fcm_token]);
                }
            }

            $payload = [
                "iss" => base_url(),
                "iat" => time(),
                "exp" => time() + (86400),
                "data" => [
                    "user_id" => $user['user_id'],
                    "karyawan_id" => $user['karyawan_id'],
                    "role_id" => $user['role_id'],
                    "username" => $user['username'],
                    "perusahaan_id1" => $user['perusahaan_id1']
                ]
            ];

            $jwt = JWT::encode($payload, $this->secret_key, 'HS256');

            $this->json_response(true, 'Login Sukses', [
                'token' => $jwt,
                'user' => [
                    'nama' => $user['username'],
                    'email' => $user['email'],
                    'foto' => $this->foto_col && isset($user[$this->foto_col]) ? $user[$this->foto_col] : 'default.png',
                    'karyawan_id' => $user['karyawan_id']
                ]
            ]);
        }
        $this->json_response(false, 'Username/Password Salah', null, 401);
    }

    /**
     * Verify JWT Token from Header or Request
     * 
     * @return object|null Decoded token data
     */
    private function verify_token()
    {
        $token = null;
        $headers = array_change_key_case(apache_request_headers(), CASE_LOWER);
        if (isset($headers['authorization'])) {
            preg_match('/Bearer\s(\S+)/', $headers['authorization'], $matches);
            if (isset($matches[1])) {
                $token = $matches[1];
            }
        }
        if (!$token) {
            $token = $this->input->get_post('token');
        }

        if (!$token) {
            $this->json_response(false, 'Token Invalid', null, 401);
        }

        try {
            return JWT::decode($token, new Key($this->secret_key, 'HS256'))->data;
        } catch (Exception $e) {
            $this->json_response(false, 'Sesi Habis', null, 401);
        }
        return null; // Should not reach here due to json_response exit
    }

    /**
     * Resolve user role to mobile dashboard scope
     * 
     * @param int|string $role_id
     * @param string $role_name
     * @param string $username
     * @return string 'developer'|'hr'|'owner'|'employee'
     */
    private function resolve_mobile_scope($role_id, $role_name = '', $username = '')
    {
        $id = (int) $role_id;
        $name = strtolower((string) $role_name);
        $uname = strtolower(trim((string) $username));

        // Developer mode: expose all role panels for full QA checking.
        if ($uname === 'nizarsuryadi' || $uname === 'developer' || strpos($name, 'developer') !== false) {
            return 'developer';
        }

        if ($id === 21 || strpos($name, 'hr') !== false || strpos($name, 'human') !== false) {
            return 'hr';
        }

        if (in_array($id, [17, 8], true) || strpos($name, 'owner') !== false) {
            return 'owner';
        }

        return 'employee';
    }

    private function get_mobile_hr_summary()
    {
        $today = date('Y-m-d');

        // Pending approval from request table
        $pending_pengajuan = $this->db->where('status', 'Pending')->count_all_results('tbl_pengajuan_izin');

        // Daily attendance categories
        $izin = $this->db->where('tanggal', $today)->where('status', 'Izin')->count_all_results('absensi');
        $sakit = $this->db->where('tanggal', $today)->where('status', 'Sakit')->count_all_results('absensi');

        $cuti_types = ['Cuti', 'Cuti Keluarga', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Menikah'];
        $cuti = $this->db->where('tanggal', $today)->where_in('status', $cuti_types)->count_all_results('absensi');

        $lembur_hari_ini = 0;
        $lembur_pending = 0;
        if ($this->db->table_exists('tbl_lembur')) {
            $lembur_hari_ini = $this->db->where('tanggal', $today)->count_all_results('tbl_lembur');
            $lembur_pending = $this->db->where('status', 'menunggu')->count_all_results('tbl_lembur');
        }

        return [
            'pending_pengajuan' => (int) $pending_pengajuan,
            'izin_hari_ini' => (int) $izin,
            'sakit_hari_ini' => (int) $sakit,
            'cuti_hari_ini' => (int) $cuti,
            'lembur_hari_ini' => (int) $lembur_hari_ini,
            'lembur_pending' => (int) $lembur_pending,
        ];
    }

    private function get_mobile_owner_summary()
    {
        $today = date('Y-m-d');
        $month = date('Y-m');

        $pendapatan_hari_ini = 0;
        $pendapatan_bulan_ini = 0;
        if ($this->db->table_exists('tbl_pembayaran')) {
            $q_today = $this->db->query(
                "SELECT COALESCE(SUM(total_tagihan),0) AS total FROM tbl_pembayaran WHERE DATE(tgl_bayar) = ?",
                [$today]
            )->row_array();
            $pendapatan_hari_ini = (float) ($q_today['total'] ?? 0);

            $q_month = $this->db->query(
                "SELECT COALESCE(SUM(total_tagihan),0) AS total FROM tbl_pembayaran WHERE DATE_FORMAT(tgl_bayar, '%Y-%m') = ?",
                [$month]
            )->row_array();
            $pendapatan_bulan_ini = (float) ($q_month['total'] ?? 0);
        }

        $karyawan_kesiangan = $this->db
            ->where('tanggal', $today)
            ->where('status', 'Hadir')
            ->where('jam_masuk >', '08:15:00')
            ->count_all_results('absensi');

        $status_hari_ini = ['Sakit', 'Izin', 'Cuti', 'Cuti Keluarga', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Menikah'];
        $karyawan_sic = $this->db
            ->where('tanggal', $today)
            ->where_in('status', $status_hari_ini)
            ->count_all_results('absensi');

        $stok_minim = 0;
        $stok_minim_list = [];
        if ($this->db->table_exists('tbl_material') && $this->db->table_exists('tbl_stok_gudang')) {
            $q_count = $this->db->query("
                SELECT COUNT(*) AS total
                FROM (
                    SELECT m.material_id
                    FROM tbl_material m
                    LEFT JOIN tbl_stok_gudang sg ON m.material_id = sg.material_id
                    GROUP BY m.material_id, m.stok_min
                    HAVING COALESCE(SUM(sg.qty), 0) <= COALESCE(m.stok_min, 0)
                ) x
            ")->row_array();
            $stok_minim = (int) ($q_count['total'] ?? 0);

            $q_list = $this->db->query("
                SELECT m.varian, COALESCE(SUM(sg.qty), 0) AS total_qty, COALESCE(m.stok_min, 0) AS stok_min
                FROM tbl_material m
                LEFT JOIN tbl_stok_gudang sg ON m.material_id = sg.material_id
                GROUP BY m.material_id, m.varian, m.stok_min
                HAVING COALESCE(SUM(sg.qty), 0) <= COALESCE(m.stok_min, 0)
                ORDER BY total_qty ASC
                LIMIT 5
            ")->result_array();

            foreach ($q_list as $r) {
                $stok_minim_list[] = [
                    'nama' => $r['varian'] ?? 'Material',
                    'stok' => (float) ($r['total_qty'] ?? 0),
                    'stok_min' => (float) ($r['stok_min'] ?? 0),
                ];
            }
        }

        $late_list = [];
        $late_rows = $this->db
            ->select('k.nama_lengkap, a.jam_masuk')
            ->from('absensi a')
            ->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left')
            ->where('a.tanggal', $today)
            ->where('a.status', 'Hadir')
            ->where('a.jam_masuk >', '08:15:00')
            ->order_by('a.jam_masuk', 'DESC')
            ->limit(5)
            ->get()
            ->result_array();
        foreach ($late_rows as $r) {
            $late_list[] = [
                'nama_lengkap' => $r['nama_lengkap'] ?? '-',
                'jam_masuk' => $r['jam_masuk'] ?? '-',
            ];
        }

        $total_piutang = 0;
        try {
            if ($this->db->table_exists('tbl_piutang')) {
                $q_piutang = $this->db->query("SELECT SUM(sisa_piutang) as total FROM tbl_piutang")->row_array();
                $total_piutang = (float) ($q_piutang['total'] ?? 0);
            }
        } catch (Throwable $e) {
            // Silently fail to avoid hanging the whole dashboard
        }

        return [
            'pendapatan_hari_ini' => $pendapatan_hari_ini,
            'pendapatan_bulan_ini' => $pendapatan_bulan_ini,
            'total_piutang' => $total_piutang,
            'karyawan_kesiangan_hari_ini' => (int) $karyawan_kesiangan,
            'karyawan_sakit_izin_cuti_hari_ini' => (int) $karyawan_sic,
            'stok_minim_count' => (int) $stok_minim,
            'karyawan_kesiangan_list' => $late_list,
            'stok_minim_list' => $stok_minim_list,
        ];
    }

    public function index()
    {
        $this->db->db_debug = FALSE;
        try {
            $userData = $this->verify_token();
            $karyawan_id = $userData->karyawan_id;
            $role_id = isset($userData->role_id) ? (int) $userData->role_id : 0;

            // 1. CEK HARI LIBUR / WEEKEND
            $is_holiday = false;
            $holiday_name = '';
            $today_date = date('Y-m-d');

            // Cek Tabel Hari Libur
            if ($this->db->table_exists('hari_libur')) {
                $h = $this->db->get_where('hari_libur', ['tanggal' => $today_date])->row();
                if ($h) {
                    $is_holiday = true;
                    $holiday_name = $h->keterangan ?? 'Hari Libur';
                }
            }

            // Cek Minggu (Default Libur jika tidak ada shift khusus)
            if (!$is_holiday && date('N') == 7) {
                $is_holiday = true;
                $holiday_name = 'Hari Minggu (Libur)';
            }

            $boleh_absen = !$is_holiday;

            // 1. ABSENSI HARI INI
            $cekabsen = [];
            if ($karyawan_id) {
                $q = $this->db->get_where('absensi', [
                    'karyawan_id' => $karyawan_id,
                    'tanggal' => $today_date
                ]);
                if ($q)
                    $cekabsen = $q->row_array();
            }

            // 2. STATISTIK & KINERJA
            $statistik = ['sakit' => 0, 'izin' => 0, 'alpha' => 0, 'terlambat' => 0];
            $hadir_count = 0;

            if ($karyawan_id) {
                $bln = date('Y-m');
                // Helper Query
                $count = function ($st) use ($karyawan_id, $bln) {
                    return $this->db->where('karyawan_id', $karyawan_id)
                        ->where("DATE_FORMAT(tanggal, '%Y-%m') =", $bln)
                        ->where('status', $st)->count_all_results('absensi');
                };

                $statistik['sakit'] = $count('Sakit');
                $statistik['izin'] = $count('Izin');
                $statistik['alpha'] = $count('Alpha');
                $hadir_count = $count('Hadir');

                // Terlambat
                $statistik['terlambat'] = $this->db->where('karyawan_id', $karyawan_id)
                    ->where("DATE_FORMAT(tanggal, '%Y-%m') =", $bln)
                    ->where('status', 'Hadir')->where('jam_masuk >', '08:15:00')
                    ->count_all_results('absensi');
            }

            $skor = 100 - ($statistik['alpha'] * 10) - ($statistik['terlambat'] * 2) - ($statistik['sakit'] * 1);
            $performance = [
                'skor_kinerja' => max(0, min(100, $skor)),
                'total_hadir' => $hadir_count,
                'total_telat' => $statistik['terlambat'],
                'total_alpha' => $statistik['alpha']
            ];

            // 3. PROFIL
            $profil = [];
            if ($karyawan_id) {
                // Fallback foto: users.image/images -> tbl_karyawan.foto -> default.png
                if ($this->foto_col) {
                    $foto_select = "COALESCE(NULLIF(u.{$this->foto_col}, ''), NULLIF(k.foto, ''), 'default.png') as foto";
                } else {
                    $foto_select = "COALESCE(NULLIF(k.foto, ''), 'default.png') as foto";
                }
                $select_cols = "k.*, u.username, u.role_id, {$foto_select}, j.nama_jabatan, d.divisi_nama";
                if ($this->db->table_exists('roles')) {
                    $select_cols .= ", r.role_name";
                } else {
                    $select_cols .= ", '' as role_name";
                }
                $this->db->select($select_cols);
                $this->db->from('tbl_karyawan k');
                $this->db->join('users u', 'u.karyawan_id = k.karyawan_id', 'left');
                $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
                $this->db->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left');
                if ($this->db->table_exists('roles')) {
                    $this->db->join('roles r', 'r.role_id = u.role_id', 'left');
                }
                $this->db->where('k.karyawan_id', $karyawan_id);
                $q = $this->db->get();
                if ($q)
                    $profil = $q->row_array();
            }

            if (!$profil) {
                $profil = [
                    'nama_lengkap' => $userData->username,
                    'foto' => 'default.png',
                    'role_id' => $role_id,
                    'role_name' => '',
                ];
            }

            // 4. RIWAYAT ABSENSI BULAN INI (New Feature)
            $riwayat = [];
            $q_riwayat = $this->db->where('karyawan_id', $karyawan_id)
                ->where("DATE_FORMAT(tanggal, '%Y-%m') =", date('Y-m'))
                ->order_by('tanggal', 'DESC')
                ->get('absensi');
            $riwayat = $q_riwayat ? $q_riwayat->result_array() : [];

            // 5. JAM KERJA
            $jamKerja = null;
            try {
                if ($karyawan_id) {
                    // Cek kolom ada gak
                    if ($this->db->field_exists('shift_id', 'tbl_karyawan')) {
                        $q_karyawan = $this->db->select('shift_id')->get_where('tbl_karyawan', ['karyawan_id' => $karyawan_id]);
                        if ($q_karyawan) {
                            $karyawan = $q_karyawan->row();
                            if ($karyawan && $karyawan->shift_id) {
                                // Cek table jam kerja
                                if ($this->db->table_exists('tbl_jam_kerja')) {
                                    $q_jam = $this->db->get_where('tbl_jam_kerja', ['id' => $karyawan->shift_id]);
                                    if ($q_jam) {
                                        $jamKerja = $q_jam->row_array();
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (Throwable $ex) { /* Catch Errors and Exceptions */
            }

            $resolved_role_id = isset($profil['role_id']) ? (int) $profil['role_id'] : $role_id;
            $resolved_role_name = isset($profil['role_name']) ? $profil['role_name'] : '';
            $resolved_username = isset($profil['username']) ? $profil['username'] : (isset($userData->username) ? $userData->username : '');
            $role_scope = $this->resolve_mobile_scope($resolved_role_id, $resolved_role_name, $resolved_username);

            $hr_summary = null;
            $owner_summary = null;
            if ($role_scope === 'hr') {
                $hr_summary = $this->get_mobile_hr_summary();
            } elseif ($role_scope === 'owner') {
                $owner_summary = $this->get_mobile_owner_summary();
            } elseif ($role_scope === 'developer') {
                $hr_summary = $this->get_mobile_hr_summary();
                $owner_summary = $this->get_mobile_owner_summary();
            }

            $this->json_response(true, 'OK', [
                'user' => $profil,
                'performance' => $performance,
                'absensi_hari_ini' => $cekabsen,
                'statistik' => $statistik,
                'riwayat_bulan_ini' => $riwayat,
                'jam_kerja' => $jamKerja, // New Data
                'role_scope' => $role_scope,
                'hr_summary' => $hr_summary,
                'owner_summary' => $owner_summary,
                'status_app' => [
                    'jam_server' => date('H:i'),
                    'boleh_absen' => $boleh_absen,
                    'is_holiday' => $is_holiday,
                    'holiday_name' => $holiday_name
                ]
            ]);

        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage(), null, 500);
        }
    }

    public function profil()
    {
        try {
            $u = $this->verify_token();
            $karyawan_fields = $this->db->list_fields('tbl_karyawan');

            $select = [
                'u.user_id',
                'u.karyawan_id',
                'u.username',
                'u.email',
                'k.foto as foto',
                'k.nama_lengkap',
                'k.nik',
                'k.telepon as no_hp',
                'k.alamat',
                'k.tempat_lahir',
                'k.tanggal_lahir',
                'k.divisi_id',
                'j.nama_jabatan',
                'd.divisi_nama'
            ];

            $optional_fields = [
                'status_karyawan',
                'tanggal_masuk',
                'jenis_kelamin',
                'agama',
                'pendidikan_terakhir',
                'no_rekening',
                'nama_bank',
                'nama_kontak_darurat',
                'no_kontak_darurat'
            ];

            foreach ($optional_fields as $field) {
                if (in_array($field, $karyawan_fields, true)) {
                    $select[] = 'k.' . $field;
                }
            }

            $row = $this->db
                ->select(implode(', ', $select), false)
                ->from('users u')
                ->join('tbl_karyawan k', 'k.karyawan_id = u.karyawan_id', 'left')
                ->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left')
                ->join('tbl_divisi d', 'd.divisi_id = k.divisi_id', 'left')
                ->where('u.user_id', $u->user_id)
                ->get()
                ->row_array();

            if (!$row) {
                $this->json_response(false, 'Profil tidak ditemukan', null, 404);
            }

            // Fetch Machines Handled by this Operator/Division
            $machines = [];
            if (!empty($row['divisi_id'])) {
                $q_machines = $this->db->get_where('tbl_mesin', ['operator_id' => $row['divisi_id'], 'status' => '1']);
                if ($q_machines) {
                    foreach ($q_machines->result_array() as $m) {
                        $machines[] = [
                            'id' => $m['mesin_id'],
                            'nama' => $m['mesin_nama'],
                            'kategori' => $m['kategori']
                        ];
                    }
                }
            }
            $row['list_mesin'] = $machines;

            $doc_defaults = [
                'ktp' => null,
                'kk' => null,
                'npwp' => null,
                'bpjs' => null,
                'ijazah' => null,
                'kontrak' => null,
            ];
            $dokumen_map = $this->_get_karyawan_dokumen_map((int) $u->karyawan_id, $doc_defaults);

            foreach ($dokumen_map as $jenis => $path) {
                $row['dokumen_' . $jenis] = $path;
                $row[$jenis] = $path;
            }

            $this->json_response(true, 'OK', $row);
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    // --- MODUL IZIN / CUTI / SAKIT (NEW) ---
    public function ajukan_izin()
    {
        try {
            $u = $this->verify_token();
            $karyawan_id = $u->karyawan_id;

            $tanggal = $this->input->post('tanggal_mulai', true);
            $tipe = $this->input->post('tipe_izin', true); // izin, sakit, cuti
            $ket = $this->input->post('keterangan', true);
            $jml = (int) ($this->input->post('durasi_jumlah', true) ?: 1);

            if (!$tanggal || !$tipe || !$ket)
                $this->json_response(false, 'Data wajib lengkap (Tanggal, Tipe, Alasan)');
            
            if ($jml <= 0)
                $this->json_response(false, 'Durasi/Jumlah hari harus lebih dari 0');

            // Cek Duplikat di Absensi
            $q_cek = $this->db->get_where('absensi', ['karyawan_id' => $karyawan_id, 'tanggal' => $tanggal]);
            $cek = $q_cek ? $q_cek->row() : null;
            if ($cek)
                $this->json_response(false, 'Anda sudah absen di tanggal ini!');

            // Upload Dokumen (Wajib jika sakit)
            $dokumen = null;
            if (!empty($_FILES['dokumen']['name'])) {
                $dokumen = $this->_upload_file('dokumen', 'uploads/dokumen_sakit/');
            }

            if ($tipe == 'sakit' && !$dokumen) {
                // $this->json_response(false, 'Wajib upload surat dokter untuk izin Sakit!');
                // Sementara kita buat warning saja, atau opsional biar ga ribet testing
            }

            $data = [
                'karyawan_id' => $karyawan_id,
                'tanggal_mulai' => $tanggal,
                'tipe_izin' => $tipe,
                'durasi_tipe' => 'hari',
                'durasi_jumlah' => $jml,
                'keterangan' => $ket,
                'dokumen' => $dokumen,
                'status' => 'Pending',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_pengajuan_izin', $data);
            $this->json_response(true, 'Pengajuan Berhasil Dikirim');

        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    public function riwayat_izin()
    {
        try {
            $u = $this->verify_token();
            $q_data = $this->db->where('karyawan_id', $u->karyawan_id)
                ->order_by('created_at', 'DESC')
                ->get('tbl_pengajuan_izin');
            $data = $q_data ? $q_data->result_array() : [];
            $this->json_response(true, 'OK', $data);
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    public function info_cuti()
    {
        try {
            $u = $this->verify_token();
            $karyawan_id = $u->karyawan_id;
            $year = date('Y');

            $karyawan_data = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $karyawan_id])->row();
            if (!$karyawan_data)
                $this->json_response(false, 'Karyawan tidak ditemukan');

            // Hitung masa kerja
            $join_date = new DateTime($karyawan_data->tanggal_masuk);
            $now = new DateTime();
            $interval = $join_date->diff($now);
            $months_worked = ($interval->y * 12) + $interval->m;

            // --- Integrated Quota Table ---
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

            // Logic Hitung Sisa Cuti
            $requests = $this->db->where('karyawan_id', $karyawan_id)
                ->where('YEAR(tanggal_mulai)', $year)
                ->where_in('status', ['Pending', 'Approved', 'Disetujui', 'approved', 'disetujui'])
                ->get('tbl_pengajuan_izin')->result();

            $used_annual_approved = 0;
            $used_annual_pending = 0;
            $limits = ['cuti menikah' => 7, 'cuti duka' => 3, 'cuti keluarga sakit' => 2, 'cuti melahirkan suami' => 3];
            $usage_special_approved = ['cuti menikah' => 0, 'cuti duka' => 0, 'cuti keluarga sakit' => 0, 'cuti melahirkan suami' => 0];
            $usage_special_pending = ['cuti menikah' => 0, 'cuti duka' => 0, 'cuti keluarga sakit' => 0, 'cuti melahirkan suami' => 0];

            foreach ($requests as $req) {
                $status_lower = strtolower($req->status);
                $type_lower = strtolower($req->tipe_izin);
                $is_approved = in_array($status_lower, ['approved', 'disetujui']);

                if ($type_lower == 'cuti' || $type_lower == 'cuti tahunan') {
                    if ($is_approved)
                        $used_annual_approved += $req->durasi_jumlah;
                    else
                        $used_annual_pending += $req->durasi_jumlah;
                } elseif (array_key_exists($type_lower, $limits)) {
                    if ($is_approved)
                        $usage_special_approved[$type_lower] += $req->durasi_jumlah;
                    else
                        $usage_special_pending[$type_lower] += $req->durasi_jumlah;
                }
            }

            // Hitung Spillover
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

            $eligible_tahunan = ($months_worked >= 12);

            $this->json_response(true, 'OK', [
                'eligible_tahunan' => $eligible_tahunan,
                'months_worked' => $months_worked,
                'quota_total' => $quota_total,
                'used_annual' => $final_used_annual,
                'sisa_quota' => $sisa_quota,
                'menikah_sisa' => max(0, $limits['cuti menikah'] - $menikah_used),
                'duka_sisa' => max(0, $limits['cuti duka'] - $duka_used),
                'sakitkel_sisa' => max(0, $limits['cuti keluarga sakit'] - $sakitkel_used),
                'melahirkansuami_sisa' => max(0, $limits['cuti melahirkan suami'] - $melahirkan_used),
                'limits' => $limits,
                'usage_special_approved' => $usage_special_approved,
                'usage_special_pending' => $usage_special_pending
            ]);
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    // --- MODUL LEMBUR (REVISI: START - FINISH) ---

    // 1. Cek Apakah Sedang Lembur?
    public function cek_status_lembur()
    {
        try {
            $u = $this->verify_token();
            if (!$this->db->table_exists('tbl_lembur'))
                $this->json_response(true, 'OK', null);

            $today = date('Y-m-d');

            // Cari lembur HARI INI yg terbaru
            $q_status = $this->db->where('karyawan_id', $u->karyawan_id)
                ->where('tanggal', $today)
                ->order_by('id', 'DESC')
                ->get('tbl_lembur');
            $status = $q_status ? $q_status->row_array() : null;

            if ($status) {
                // Jika jam_selesai 00:00:00 atau null, berarti masih running (walau di DB 'menunggu')
                if ($status['jam_selesai'] == '00:00:00' || is_null($status['jam_selesai'])) {
                    $status['status'] = 'running'; // Override temporary agar flutter tahu ini aktif
                }
            }

            $this->json_response(true, 'OK', $status);
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    // 2. Mulai Lembur
    public function start_lembur()
    {
        $this->db->db_debug = FALSE; // Disable HTML errors
        try {
            $u = $this->verify_token();
            $karyawan_id = $u->karyawan_id;
            $tanggal = date('Y-m-d');
            $jam_mulai = date('H:i:s');
            $jam_sekarang = date('H:i');

            if (!$this->db->table_exists('tbl_lembur')) {
                $this->json_response(false, 'Tabel tbl_lembur belum ada. Silakan hubungi IT.');
            }

            // Ambil Pengaturan Jam Kerja & Lembur hari ini
            $hari_ini_indo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][date('w')];
            $sett_hari = $this->db->get_where('tbl_jam_kerja', ['hari' => $hari_ini_indo])->row_array();

            $jam_mulai_lembur = (!empty($sett_hari['jam_mulai_lembur'])) ? substr($sett_hari['jam_mulai_lembur'], 0, 5) : '18:00';
            $boleh_lembur_hari_ini = (!empty($sett_hari['boleh_lembur'])) ? $sett_hari['boleh_lembur'] : '1';

            if ($boleh_lembur_hari_ini == '0') {
                $this->json_response(false, 'Punten, untuk hari ini tidak dibuka sesi lembur.');
            }

            // Ambil data jam masuk kerja
            $jam_masuk_kerja = (!empty($sett_hari['jam_masuk'])) ? substr($sett_hari['jam_masuk'], 0, 5) : '08:00';

            // Logika Validasi Waktu
            $is_pagi = ($jam_sekarang >= '06:00' && $jam_sekarang < $jam_masuk_kerja);
            $is_sore = ($jam_sekarang >= $jam_mulai_lembur);

            if (!$is_pagi && !$is_sore) {
                $this->json_response(false, 'Absensi lembur hanya tersedia Pagi (06:00 - ' . $jam_masuk_kerja . ') atau Sore (Mulai ' . $jam_mulai_lembur . ' WIB).');
            }

            // Cek exist uncompleted lembur hari ini
            $existing_unfinished = $this->db->get_where('tbl_lembur', [
                'karyawan_id' => $karyawan_id,
                'tanggal' => $tanggal,
                'jam_selesai' => '00:00:00'
            ])->row();

            if ($existing_unfinished) {
                $this->json_response(false, 'Anda masih memiliki sesi lembur yang belum ditutup hari ini.');
            }

            // Insert Data sesuai dengan DB schema dan alur web
            $data = [
                'karyawan_id' => $karyawan_id,
                'tanggal' => date('Y-m-d'),
                'jam_mulai' => $jam_mulai, // H:i:s
                'jam_selesai' => '00:00:00',
                'status' => 'menunggu',
                'create_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->db->insert('tbl_lembur', $data)) {
                $err = $this->db->error();
                throw new Exception("DB Error: " . $err['message']);
            }

            $this->json_response(true, 'Lembur Dimulai!', ['jam_mulai' => substr($jam_mulai, 0, 5)]);

        } catch (Exception $e) { // Use Exception for compatibility
            $this->json_response(false, "Server Error: " . $e->getMessage());
        }
    }

    // 3. Selesai Lembur (Kirim Data Akhir)
    public function finish_lembur()
    {
        try {
            $u = $this->verify_token();

            $keterangan = $this->input->post('keterangan', true);

            // Cari sesi lembur yg blm selesai HARI INI
            $tanggal = date('Y-m-d');
            $q_running = $this->db->where('karyawan_id', $u->karyawan_id)
                ->where('tanggal', $tanggal)
                ->where('jam_selesai', '00:00:00')
                ->get('tbl_lembur');
            $running = $q_running ? $q_running->row() : null;

            if (!$running)
                $this->json_response(false, 'Tidak ada sesi lembur aktif!');

            if (empty($keterangan)) {
                $this->json_response(false, 'Laporan kegiatan wajib diisi.');
            }

            // Upload format mengikuti folder yg dibaca di Web
            $bukti = null;
            if (!empty($_FILES['bukti_foto']['name'])) {
                $upload_result = $this->_upload_file('bukti_foto', 'assets/images/lembur/');
                $bukti = basename($upload_result);
            } else {
                $this->json_response(false, 'Foto kegiatan wajib diupload.');
            }

            $updateData = [
                'jam_selesai' => date('H:i:s'), // Jam skr
                'kegiatan' => $keterangan,
                'foto_kegiatan' => $bukti
                // status tetap 'menunggu' (dibaca untuk Web HR)
            ];

            $this->db->where('id', $running->id);
            $this->db->update('tbl_lembur', $updateData);

            $this->json_response(true, 'Lembur Berhasil Disimpan!');

        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    public function riwayat_lembur()
    {
        try {
            $u = $this->verify_token();
            if (!$this->db->table_exists('tbl_lembur'))
                $this->json_response(true, 'OK', []);
            $q_data = $this->db->where('karyawan_id', $u->karyawan_id)->order_by('tanggal', 'DESC')->get('tbl_lembur');

            $data = $q_data ? $q_data->result_array() : [];
            // Mapping column for flutter compatibility
            foreach ($data as &$row) {
                $row['keterangan'] = $row['kegiatan'];
            }

            $this->json_response(true, 'OK', $data);
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    // ----------------------------------------
    // ABSENSI UTAMA (MASUK/PULANG)
    // ----------------------------------------

    private function _save_base64_image($base64_string, $type)
    {
        if (empty($base64_string))
            return null;

        // Clean up string
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $type_match)) {
            $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
            $type_match = strtolower($type_match[1]); // jpg, png, etc
            if (!in_array($type_match, ['jpg', 'jpeg', 'png'])) {
                $type_match = 'jpg';
            }
            $ext = $type_match;
        } else {
            $ext = 'jpg';
        }

        $base64_string = str_replace(' ', '+', $base64_string);
        $data = base64_decode($base64_string);

        $path = 'uploads/absensi/';
        if (!is_dir(FCPATH . $path)) {
            mkdir(FCPATH . $path, 0777, true);
        }

        $filename = 'absen_' . $type . '_' . time() . '.' . $ext;
        if (file_put_contents(FCPATH . $path . $filename, $data)) {
            return $path . $filename;
        }
        return null;
    }

    public function absen_masuk()
    {
        try {
            $u = $this->verify_token();
            $karyawan_id = $u->karyawan_id;
            $tanggal = date('Y-m-d');

            // Cek Double
            $q_cek = $this->db->get_where('absensi', ['karyawan_id' => $karyawan_id, 'tanggal' => $tanggal]);
            $cek = $q_cek ? $q_cek->row() : null;

            if ($cek) {
                $this->json_response(false, 'Anda sudah absen masuk hari ini!', null, 400);
            }

            $foto = $this->input->post('foto', true);
            $lat = $this->input->post('latitude', true);
            $long = $this->input->post('longitude', true);

            $foto_path = $this->_save_base64_image($foto, 'masuk');

            $device = $this->input->post('device_type', true);
            if (empty($device)) {
                $os = $this->agent->platform();
                if ($this->agent->is_mobile()) {
                    $device = $os . ' (' . $this->agent->mobile() . ')';
                } else {
                    $device = 'App/Web (' . $os . ')';
                }
            }

            $data = [
                'karyawan_id' => $karyawan_id,
                'tanggal' => $tanggal,
                'jam_masuk' => date('H:i:s'),
                'status' => 'Hadir',
                'lokasi_absen' => "Lat: $lat, Long: $long",
                'foto_masuk' => $foto_path,
                'device_type' => $device,
                'user_agent' => $this->input->user_agent()
            ];

            if ($this->db->insert('absensi', $data)) {
                $this->json_response(true, 'Absen Masuk Berhasil!');
            } else {
                $this->json_response(false, 'Gagal Insert DB');
            }
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    public function absen_pulang()
    {
        try {
            $u = $this->verify_token();
            $karyawan_id = $u->karyawan_id;
            $tanggal = date('Y-m-d');

            $q_cek = $this->db->get_where('absensi', ['karyawan_id' => $karyawan_id, 'tanggal' => $tanggal]);
            $absensi = $q_cek ? $q_cek->row() : null;

            if (!$absensi) {
                $this->json_response(false, 'Anda belum melakukan absen masuk!', null, 400);
            }

            if (!empty($absensi->jam_pulang) && $absensi->jam_pulang != '00:00:00') {
                $this->json_response(false, 'Anda sudah absen pulang hari ini!', null, 400);
            }

            $foto = $this->input->post('foto', true);
            $foto_path = $this->_save_base64_image($foto, 'pulang');

            $device = $this->input->post('device_type', true);
            if (empty($device)) {
                $os = $this->agent->platform();
                if ($this->agent->is_mobile()) {
                    $device = $os . ' (' . $this->agent->mobile() . ')';
                } else {
                    $device = 'App/Web (' . $os . ')';
                }
            }

            $data = [
                'jam_pulang' => date('H:i:s'),
                'foto_pulang' => $foto_path,
                'device_type' => $device,
                'user_agent' => $this->input->user_agent()
            ];

            $this->db->where('id', $absensi->id);
            if ($this->db->update('absensi', $data)) {
                $this->json_response(true, 'Absen Pulang Berhasil!');
            } else {
                $this->json_response(false, 'Gagal Update DB');
            }
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    public function riwayat_absen()
    {
        try {
            $u = $this->verify_token();
            $karyawan_id = $u->karyawan_id;

            $this->db->select('tanggal, jam_masuk as jam, status');
            $this->db->from('absensi');
            $this->db->where('karyawan_id', $karyawan_id);
            $this->db->order_by('tanggal', 'DESC');
            $this->db->limit(30);

            $query = $this->db->get();
            $this->json_response(true, 'OK', $query->result_array());
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    // --- KATALOG PRODUK ---
    public function get_catalog()
    {
        try {
            $this->verify_token(); // Auth check
            $keyword = $this->input->get('keyword', TRUE);

            $this->db->select('p.product_id, p.product_nama, p.product_deskripsi, k.kategori_nama, hp.harga_1 as harga, hp.qty_min, hp.qty_max');
            $this->db->from('tbl_product p');
            $this->db->join('tbl_kategori k', 'k.kategori_id = p.kategori_id', 'left');
            $this->db->join('tbl_harga_product hp', 'hp.product_id = p.product_id AND hp.is_active = 1', 'left');

            if ($keyword) {
                $this->db->group_start();
                $this->db->like('p.product_nama', $keyword);
                $this->db->or_like('k.kategori_nama', $keyword);
                $this->db->group_end();
            }

            $this->db->order_by('p.product_nama', 'ASC');
            $this->db->order_by('hp.qty_min', 'ASC');
            $query = $this->db->get();
            $result = $query->result_array();

            $this->json_response(true, 'OK', $result);
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }


    private function _upload_file($field, $target_dir)
    {
        if (!is_dir(FCPATH . $target_dir))
            mkdir(FCPATH . $target_dir, 0777, true);

        $config['upload_path'] = FCPATH . $target_dir;
        $config['allowed_types'] = 'pdf|jpg|png|jpeg|webp|heic|heif|hevc';
        $config['max_size'] = 10240; // 10MB
        $config['encrypt_name'] = TRUE; // Nama acak aman

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload($field)) {
            return $target_dir . $this->upload->data('file_name');
        } else {
            throw new Exception($this->upload->display_errors('', ''));
        }
    }
    private function _get_karyawan_dokumen_map($karyawan_id, $defaults = [])
    {
        $result = $defaults;

        if (!$karyawan_id || !$this->db->table_exists('tbl_karyawan_dokumen')) {
            return $result;
        }

        $rows = $this->db
            ->where('karyawan_id', (int) $karyawan_id)
            ->get('tbl_karyawan_dokumen')
            ->result_array();

        foreach ($rows as $row) {
            $jenis = strtolower(trim((string) ($row['jenis_dokumen'] ?? '')));
            if ($jenis === '' || !array_key_exists($jenis, $result)) {
                continue;
            }

            $file_name = trim((string) ($row['file_path'] ?? ''));
            if ($file_name === '') {
                continue;
            }

            $result[$jenis] = 'assets/documents/karyawan/' . ltrim($file_name, '/');
        }

        return $result;
    }

    private function _upload_document_file_name($field, $target_dir)
    {
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $config['upload_path'] = $target_dir;
        $config['allowed_types'] = 'pdf|jpg|jpeg|png|webp|heic|heif|hevc';
        $config['max_size'] = 10240;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload($field)) {
            return $this->upload->data('file_name');
        }

        throw new Exception(strip_tags($this->upload->display_errors('', '')));
    }

    public function upload_dokumen_karyawan()
    {
        try {
            $u = $this->verify_token();
            $karyawan_id = (int) $u->karyawan_id;
            if ($karyawan_id <= 0) {
                $this->json_response(false, 'Karyawan tidak valid', null, 400);
            }

            if (!$this->db->table_exists('tbl_karyawan_dokumen')) {
                $this->json_response(false, 'Tabel dokumen karyawan belum tersedia', null, 500);
            }

            $mapping = [
                'ktp' => ['ktp', 'dokumen_ktp'],
                'kk' => ['kk', 'dokumen_kk'],
                'npwp' => ['npwp', 'dokumen_npwp'],
                'bpjs' => ['bpjs', 'dokumen_bpjs'],
                'ijazah' => ['ijazah', 'dokumen_ijazah'],
                'kontrak' => ['kontrak', 'dokumen_kontrak'],
            ];

            $target_dir = FCPATH . 'assets/documents/karyawan/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $updated = [];

            foreach ($mapping as $jenis => $possible_fields) {
                $uploaded_field = null;
                foreach ($possible_fields as $field) {
                    if (isset($_FILES[$field]) && !empty($_FILES[$field]['name'])) {
                        $uploaded_field = $field;
                        break;
                    }
                }

                if ($uploaded_field === null) {
                    continue;
                }

                $file_name = $this->_upload_document_file_name($uploaded_field, $target_dir);

                $existing = $this->db->get_where('tbl_karyawan_dokumen', [
                    'karyawan_id' => $karyawan_id,
                    'jenis_dokumen' => $jenis,
                ])->row_array();

                if ($existing && !empty($existing['file_path'])) {
                    $old_file = $target_dir . ltrim($existing['file_path'], '/');
                    if (is_file($old_file)) {
                        @unlink($old_file);
                    }

                    $this->db->where('id', $existing['id']);
                    $this->db->update('tbl_karyawan_dokumen', [
                        'file_path' => $file_name,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $this->db->insert('tbl_karyawan_dokumen', [
                        'karyawan_id' => $karyawan_id,
                        'jenis_dokumen' => $jenis,
                        'file_path' => $file_name,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                $updated[$jenis] = 'assets/documents/karyawan/' . $file_name;
            }

            if (empty($updated)) {
                $this->json_response(false, 'Tidak ada file yang dikirim', null, 400);
            }

            $this->json_response(true, 'Dokumen berhasil diupload', $updated);
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    public function update_profil_karyawan()
    {
        try {
            $u = $this->verify_token();
            $karyawan_id = (int) $u->karyawan_id;
            if ($karyawan_id <= 0) {
                $this->json_response(false, 'Karyawan tidak valid', null, 400);
            }

            $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $karyawan_id])->row_array();
            if (!$karyawan) {
                $this->json_response(false, 'Data karyawan tidak ditemukan', null, 404);
            }

            $blocked_fields = [
                'karyawan_id',
                'jabatan_id',
                'divisi_id',
                'shift_id',
                'nik',
                'is_active',
                'is_delete',
                'tanggal_masuk',
                'status_karyawan',
                'transport_aktif',
                'jenis_gaji',
                'gaji_pokok',
                'nominal_lembur',
                'tunjangan_jabatan',
                'perusahaan_id',
                'created_at',
                'updated_at',
            ];

            $karyawan_fields = $this->db->list_fields('tbl_karyawan');
            $allowed_fields = array_diff($karyawan_fields, $blocked_fields);

            $payload = $this->input->post(NULL, true);
            if (!is_array($payload)) {
                $payload = [];
            }

            $update_data = [];
            foreach ($payload as $key => $value) {
                if ($key === 'token' || !in_array($key, $allowed_fields, true)) {
                    continue;
                }

                $update_data[$key] = is_string($value) ? trim($value) : $value;
            }

            if (!empty($_FILES['foto']['name']) && in_array('foto', $allowed_fields, true)) {
                $upload_path = FCPATH . 'assets/images/karyawan/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }

                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'jpg|jpeg|png|webp';
                $config['max_size'] = 5120;
                $config['file_name'] = 'profile_' . $karyawan_id . '_' . time();

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('foto')) {
                    throw new Exception(strip_tags($this->upload->display_errors('', '')));
                }

                $upload_data = $this->upload->data();
                $new_foto = $upload_data['file_name'];
                $update_data['foto'] = $new_foto;

                $old_foto = trim((string) ($karyawan['foto'] ?? ''));
                $is_default = in_array(strtolower($old_foto), ['default.jpg', 'default.png', 'user.png', ''], true);
                $old_path = $upload_path . $old_foto;
                if (!$is_default && is_file($old_path)) {
                    @unlink($old_path);
                }
            }

            if (empty($update_data)) {
                $this->json_response(false, 'Tidak ada data yang diubah', null, 400);
            }

            $this->db->where('karyawan_id', $karyawan_id);
            $this->db->update('tbl_karyawan', $update_data);

            if (array_key_exists('email', $update_data)) {
                $this->db->where('user_id', $u->user_id);
                $this->db->update('users', ['email' => $update_data['email']]);
            }

            $this->json_response(true, 'Profil karyawan berhasil diperbarui', [
                'updated_fields' => array_keys($update_data),
            ]);
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    // ----------------------------------------
    public function update_password()
    {
        try {
            $u = $this->verify_token();
            $old = $this->input->post('password_lama', true);
            $new = $this->input->post('password_baru', true);
            if (!$old || !$new)
                $this->json_response(false, 'Data kurang');

            $q_user = $this->db->get_where('users', ['user_id' => $u->user_id]);
            $user = $q_user ? $q_user->row_array() : null;
            $is_valid_password = false;
            if (password_verify($old, $user['password'])) {
                $is_valid_password = true;
            } elseif (md5($old) === $user['password']) {
                $is_valid_password = true;
            }

            if (!$is_valid_password)
                $this->json_response(false, 'Pass Lama Salah');

            $this->db->update('users', ['password' => password_hash($new, PASSWORD_DEFAULT)], ['user_id' => $u->user_id]);
            $this->json_response(true, 'Sukses Ganti Password');
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    // ----------------------------------------
    // SLIP GAJI
    // ----------------------------------------
    public function slip_gaji()
    {
        try {
            $u = $this->verify_token();
            $bulan = $this->input->post('bulan', true) ?: date('Y-m');

            // Kita gunakan Payroll_model agar aman di execution API
            $this->load->model('Payroll_model');
            $salary_data = $this->Payroll_model->get_salary_data($bulan, $u->karyawan_id);

            if (!empty($salary_data)) {
                $this->json_response(true, 'OK', $salary_data[0]);
            } else {
                $this->json_response(false, 'Slip gaji belum tersedia untuk bulan ini.');
            }
        } catch (Exception $e) {
            $this->json_response(false, 'Exception: ' . $e->getMessage());
        } catch (Throwable $t) {
            $this->json_response(false, 'Throwable: ' . $t->getMessage(), null, 500);
        }
    }

    public function riwayat_slip_gaji()
    {
        try {
            $u = $this->verify_token();

            $this->load->model('Payroll_model');

            $riwayat = [];
            for ($i = 0; $i < 6; $i++) {
                $prev_bulan = date('Y-m', strtotime("-$i months"));
                $prev_data = $this->Payroll_model->get_salary_data($prev_bulan, $u->karyawan_id);
                if (!empty($prev_data)) {
                    $riwayat[] = $prev_data[0];
                }
            }

            $this->json_response(true, 'OK', $riwayat);
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        } catch (Throwable $t) {
            $this->json_response(false, 'Throwable: ' . $t->getMessage(), null, 500);
        }
    }

    // --- PENGUMUMAN ---
    public function pengumuman()
    {
        try {
            $u = $this->verify_token();

            // Ambil data user lengkap untuk mendapatkan perusahaan_id1 (karena di token payload tidak ada)
            $user_data = $this->db->get_where('users', ['user_id' => $u->user_id])->row();
            $perusahaan_id = $user_data ? $user_data->perusahaan_id1 : 0;

            $this->db->select('id, judul, isi, kategori, tanggal_mulai, tanggal_selesai, status, created_at');
            $this->db->from('tbl_pengumuman');
            $this->db->where('status', 1);
            $this->db->where('perusahaan_id', $perusahaan_id);
            $this->db->where('tanggal_mulai <=', date('Y-m-d'));
            $this->db->where('tanggal_selesai >=', date('Y-m-d'));
            $this->db->order_by('created_at', 'DESC');

            $query = $this->db->get();
            $this->json_response(true, 'OK', $query->result_array());
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

    // --- TUGAS PRODUKSI (OPERATOR) ---
    public function tugas_produksi()
    {
        try {
            $u = $this->verify_token();

            // Query mengambil daftar SPK yang ditugaskan ke operator ini
            // Berdasarkan operator_id di detail_produksi
            $this->db->select('dp.detail_produksi_id, dp.produksi_id, dp.status_produksi, dp.catatan as catatan_tugas, 
                               tp.nospk, tp.dateline_tgl, tp.dateline_jam, tp.catatan as catatan_spk,
                               p.product_nama, tp.qty, tp.panjang, tp.lebar');
            $this->db->from('detail_produksi dp');
            $this->db->join('tbl_produksi tp', 'tp.produksi_id = dp.produksi_id');
            $this->db->join('tbl_product p', 'p.product_id = tp.product_id');
            $this->db->where('dp.operator_id', $u->karyawan_id);
            $this->db->order_by('tp.dateline_tgl', 'ASC');
            $this->db->order_by('tp.dateline_jam', 'ASC');

            $query = $this->db->get();
            $result = $query->result_array();

            $this->json_response(true, 'OK', $result);
        } catch (Exception $e) {
            $this->json_response(false, $e->getMessage());
        }
    }

}