<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payroll_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function _get_payroll_range($bulan)
    {
        $start_day = 26;
        $end_day = 25;
        $perusahaan_id = 1;

        if ($this->session && $this->session->userdata('perusahaan_id')) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        $company = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
        if ($company && isset($company->payroll_start_day) && isset($company->payroll_end_day)) {
            $start_day = $company->payroll_start_day ?: 26;
            $end_day = $company->payroll_end_day ?: 25;
        }

        $current_month_first_day = $bulan . '-01';
        if ($start_day > $end_day) {
            $start_date = date('Y-m-', strtotime('-1 month', strtotime($current_month_first_day))) . str_pad($start_day, 2, '0', STR_PAD_LEFT);
            $end_date = $bulan . '-' . str_pad($end_day, 2, '0', STR_PAD_LEFT);
        } else {
            $start_date = $bulan . '-' . str_pad($start_day, 2, '0', STR_PAD_LEFT);
            $end_date = $bulan . '-' . str_pad($end_day, 2, '0', STR_PAD_LEFT);
        }

        return ['start' => $start_date, 'end' => $end_date];
    }

    public function get_salary_data($bulan, $karyawan_id = '')
    {
        $range = $this->_get_payroll_range($bulan);
        $start_date = $range['start'];
        $end_date = $range['end'];

        $this->db->select('k.*, j.nama_jabatan, j.tunjangan_jabatan as default_tunjangan_jabatan, o.operator_nama as divisi_nama, p.nama_perusahaan');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->join('tbl_operator o', 'o.operator_id = k.divisi_id', 'left');
        $this->db->join('tbl_perusahaan p', 'p.id_perusahaan = k.perusahaan_id', 'left');
        if ($karyawan_id) {
            $this->db->where('k.karyawan_id', $karyawan_id);
        }
        $employees = $this->db->get()->result();

        if (empty($employees))
            return [];

        $perf_data = $this->get_performance_data($bulan, $karyawan_id, '', $start_date, $end_date);
        $perf_map = [];
        foreach ($perf_data as $p) {
            $perf_map[$p->karyawan_id] = $p;
        }

        // PRE-FETCH: SPLs
        $this->db->where('status', 'disetujui');
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        if ($karyawan_id)
            $this->db->where('karyawan_id', $karyawan_id);
        $spls = $this->db->get('tbl_lembur')->result();
        $spl_map = [];
        foreach ($spls as $s) {
            $spl_map[$s->karyawan_id][$s->tanggal][] = $s;
        }

        // PRE-FETCH: Attendance
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        if ($karyawan_id)
            $this->db->where('karyawan_id', $karyawan_id);
        $atts = $this->db->get('absensi')->result();
        $att_map = [];
        foreach ($atts as $a) {
            $att_map[$a->karyawan_id][$a->tanggal] = $a;
        }

        $period_dates = [];
        $curr = strtotime($start_date);
        $last_p = strtotime($end_date);
        while ($curr <= $last_p) {
            $period_dates[] = date('Y-m-d', $curr);
            $curr = strtotime('+1 day', $curr);
        }

        $salary_data = [];
        foreach ($employees as $emp) {
            $k_id = $emp->karyawan_id;
            $perf = $perf_map[$k_id] ?? (object) ['total_hadir' => 0, 'total_telat' => 0, 'total_sakit' => 0, 'total_izin' => 0, 'total_alpha' => 0, 'total_detik_telat' => 0, 'skor_kinerja' => 0];

            $rate_lembur = $emp->nominal_lembur ?: 10000;
            $sum_pagi = 0;
            $sum_wajib = 0;
            $sum_kerjaan = 0;

            foreach ($period_dates as $d) {
                $day_spls = $spl_map[$k_id][$d] ?? [];
                $att_day = $att_map[$k_id][$d] ?? null;
                if (empty($day_spls) && !$att_day)
                    continue;

                $earliest_start = '00:00';
                if (!empty($day_spls)) {
                    $earliest_start = $day_spls[0]->jam_mulai;
                    foreach ($day_spls as $s)
                        if (strtotime($s->jam_mulai) < strtotime($earliest_start))
                            $earliest_start = $s->jam_mulai;
                }

                $res_wajib = $this->_hitung_jam_lembur($k_id, $earliest_start, $earliest_start, $d, $att_day);
                $sum_wajib += $res_wajib['raw_wajib'];

                foreach ($day_spls as $spl) {
                    $res = $this->_hitung_jam_lembur($k_id, $spl->jam_mulai, $spl->jam_selesai, $d, $att_day);
                    $sum_pagi += $res['raw_pagi'];
                    $sum_kerjaan += $res['raw_kerjaan'];
                }
            }

            $gaji_pokok = ($emp->jenis_gaji == 'Harian') ? ($emp->gaji_pokok * $perf->total_hadir) : $emp->gaji_pokok;
            $uang_pagi = $sum_pagi * $rate_lembur;
            $uang_wajib = $sum_wajib * $rate_lembur;
            $uang_kerjaan = $sum_kerjaan * $rate_lembur;
            $uang_lembur = $uang_pagi + $uang_wajib + $uang_kerjaan;

            $tunjangan_jabatan = $emp->tunjangan_jabatan ?: ($emp->default_tunjangan_jabatan ?: 0);
            $tunjangan_transportasi = ($emp->transport_aktif == 1) ? round(($gaji_pokok * 0.05 / 26) * $perf->total_hadir) : 0;

            $manual = $this->get_manual_payroll($k_id, $bulan);
            $biaya_makan = $this->get_meal_allowance($k_id, $start_date, $end_date);

            $rate_per_jam = ($gaji_pokok / 26) / 8;
            $pot_terlambat = round($rate_per_jam * (($perf->total_detik_telat ?? 0) / 3600)) + $this->get_lateness_penalty($k_id, $start_date, $end_date);
            $pot_alpha = round(($gaji_pokok / 26) * $perf->total_alpha);

            $total_gaji = $gaji_pokok + $tunjangan_jabatan + $tunjangan_transportasi + $uang_lembur + $biaya_makan + $manual->reward + $manual->bpjs + $manual->tunjangan_extra + $manual->tunjangan_kinerja_manual - $pot_alpha - $manual->kasbon - $manual->pinjaman - $pot_terlambat;

            $legacy = $this->db->get_where('tbl_gaji_manual', ['karyawan_id' => $k_id, 'bulan' => $bulan])->row();
            if ($legacy) {
                $gaji_pokok = $legacy->gaji_pokok;
                $tunjangan_jabatan = $legacy->tunjangan_jabatan;
                $tunjangan_transportasi = $legacy->tunjangan_transportasi;
                $uang_lembur = $legacy->uang_lembur;
                $pot_alpha = $legacy->potongan_alpha;
                $manual->kasbon = $legacy->kasbon ?? 0;
                $total_gaji = $legacy->total_gaji;
                $uang_pagi = 0;
                $uang_wajib = 0;
                $uang_kerjaan = 0;
            }

            $salary_data[] = [
                'karyawan_id' => $k_id,
                'nama_lengkap' => $emp->nama_lengkap,
                'gaji_pokok' => $gaji_pokok,
                'tunjangan_jabatan' => $tunjangan_jabatan,
                'tunjangan_transportasi' => $tunjangan_transportasi,
                'uang_lembur' => $uang_lembur,
                'detail_lembur' => [
                    'pagi' => $uang_pagi,
                    'wajib' => $uang_wajib,
                    'kerjaan' => $uang_kerjaan
                ],
                'biaya_makan_lembur' => $biaya_makan,
                'reward' => $manual->reward,
                'bpjs' => $manual->bpjs,
                'tunjangan_extra' => $manual->tunjangan_extra,
                'tunjangan_kinerja_manual' => $manual->tunjangan_kinerja_manual,
                'potongan_alpha' => $pot_alpha,
                'potongan_terlambat' => $pot_terlambat,
                'kasbon' => $manual->kasbon,
                'pinjaman' => $manual->pinjaman,
                'total_gaji' => $total_gaji,
                'total_hadir' => $perf->total_hadir,
                'total_alpha' => $perf->total_alpha,
                'total_telat' => $perf->total_telat,
                'total_sakit' => $perf->total_sakit,
                'total_izin' => $perf->total_izin,
                'bulan' => $bulan,
                'skor_kinerja' => $perf->skor_kinerja
            ];
        }

        return $salary_data;
    }

    private function _hitung_jam_lembur($karyawan_id, $jam_mulai, $jam_selesai, $tanggal, $absensi_data = null)
    {
        $start_spl = strtotime("$tanggal $jam_mulai");
        $end_spl = strtotime("$tanggal $jam_selesai");
        if ($end_spl < $start_spl)
            $end_spl += 86400;

        $absensi = $absensi_data ?: $this->db->get_where('absensi', ['karyawan_id' => $karyawan_id, 'tanggal' => $tanggal])->row();
        $jam_pulang_real = (isset($absensi->jam_pulang) && $absensi->jam_pulang != '00:00:00') ? strtotime("$tanggal $absensi->jam_pulang") : $start_spl;

        $day_num = date('N', strtotime($tanggal));
        $breakdown = ['pagi' => 0, 'wajib' => 0, 'kerjaan' => 0];

        $calc_jumping = function ($minutes, $multiplier) {
            $hours = floor($minutes / 60);
            $rem = $minutes % 60;
            if ($multiplier == 1.0)
                return $hours + ($rem > 30 ? 0.5 : 0);
            if ($multiplier == 1.5) {
                if ($hours == 0)
                    return ($rem > 30 ? 1.5 : 0);
                return 1.5 + ($hours - 1) * 1.5 + ($rem > 30 ? 0.5 : 0);
            }
            if ($multiplier == 2.0)
                return ($hours * 2.0) + ($rem > 30 ? 1.0 : 0);
            return 0;
        };

        if ($day_num <= 5 && $jam_mulai != '00:00' && strtotime($jam_mulai) < strtotime("12:00:00")) {
            $p_start = max($start_spl, strtotime("$tanggal 06:00:00"));
            $p_end = min($end_spl, strtotime("$tanggal 08:00:00"));
            if ($p_end > $p_start)
                $breakdown['pagi'] = (($p_end - $p_start) / 3600) * 1.5;
        }

        static $jk_cache = [];
        if (!isset($jk_cache[$day_num]))
            $jk_cache[$day_num] = $this->db->get_where('tbl_jam_kerja', ['urutan' => $day_num])->row();
        $jk = $jk_cache[$day_num];

        $w_start_time = ($day_num == 6) ? "15:00:00" : "16:00:00";
        $w_end_time = ($jk && $jk->jam_pulang) ? $jk->jam_pulang : (($day_num == 6) ? "17:00:00" : "18:00:00");

        if ($day_num <= 6) {
            $w_start = strtotime("$tanggal $w_start_time");
            $w_end_limit = strtotime("$tanggal $w_end_time");
            $w_actual_end = min($jam_pulang_real, $w_end_limit);
            if ($w_actual_end > $w_start)
                $breakdown['wajib'] = $calc_jumping(floor(($w_actual_end - $w_start) / 60), 1.0);
        }

        $k_start = max($start_spl, strtotime("$tanggal $w_end_time") + 60);
        if ($day_num == 7)
            $k_start = $start_spl;
        $k_end = $end_spl;
        if ($k_end > $k_start) {
            $task_min = floor(($k_end - $k_start) / 60);
            if ($day_num == 7) {
                $breakdown['kerjaan'] += $calc_jumping($task_min, 2.0);
            } else {
                $t1_min = min($task_min, 120);
                $breakdown['kerjaan'] += $calc_jumping($t1_min, 1.5);
                if ($task_min > 120)
                    $breakdown['kerjaan'] += $calc_jumping($task_min - 120, 2.0);
            }
        }

        return ['raw_pagi' => $breakdown['pagi'], 'raw_wajib' => $breakdown['wajib'], 'raw_kerjaan' => $breakdown['kerjaan']];
    }

    public function get_performance_data($bulan, $karyawan_id = '', $jabatan_id = '', $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $start_date = $bulan . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
        }
        $this->db->select('
                k.karyawan_id,
                k.nama_lengkap,
                k.nik,
                j.nama_jabatan,
                o.operator_nama as divisi_nama,
                COUNT(CASE WHEN a.status = "Hadir" THEN 1 END) as total_hadir,
                COUNT(CASE WHEN a.status = "Sakit" THEN 1 END) as total_sakit,
                COUNT(CASE WHEN a.status = "Izin" THEN 1 END) as total_izin,
                COUNT(CASE WHEN a.status LIKE "Cuti%" THEN 1 END) as total_cuti,
                COUNT(CASE WHEN a.status = "Hadir" AND a.jam_masuk > "08:15:00" THEN 1 END) as total_telat,
                SUM(CASE WHEN a.status = "Hadir" AND a.jam_masuk > "08:00:00" THEN TIME_TO_SEC(TIMEDIFF(a.jam_masuk, "08:00:00")) ELSE 0 END) as total_detik_telat,
                COUNT(CASE WHEN a.status = "Hadir" AND a.jam_pulang < "17:00:00" AND a.jam_pulang != "00:00:00" THEN 1 END) as total_pulang_cepat
        ');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->join('tbl_operator o', 'o.operator_id = k.divisi_id', 'left');
        $this->db->join('absensi a', 'a.karyawan_id = k.karyawan_id AND a.tanggal >= "' . $start_date . '" AND a.tanggal <= "' . $end_date . '"', 'left');
        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        if ($karyawan_id)
            $this->db->where('k.karyawan_id', $karyawan_id);
        $this->db->group_by('k.karyawan_id, k.nama_lengkap, k.nik, j.nama_jabatan, o.operator_nama');
        $result = $this->db->get()->result();
        $workingDays = $this->get_working_days_logic($bulan, $start_date, $end_date);
        foreach ($result as $row) {
            $total_recorded = $row->total_hadir + $row->total_izin + $row->total_sakit + $row->total_cuti;
            $calculated_alpha = max(0, $workingDays - $total_recorded);
            $row->total_alpha = $calculated_alpha;
            $score = 100 - ($row->total_sakit * 1) - ($row->total_izin * 1) - ($calculated_alpha * 5) - ($row->total_telat * 1) - ($row->total_pulang_cepat * 3);
            $row->skor_kinerja = $score;
        }
        return $result;
    }

    private function get_working_days_logic($bulan, $start_date, $end_date)
    {
        $this->db->select('tanggal');
        $this->db->where(['tanggal >=' => $start_date, 'tanggal <=' => $end_date]);
        $holidays = array_column($this->db->get('hari_libur')->result_array(), 'tanggal');
        $workingDays = 0;
        $today = date('Y-m-d');
        $current = strtotime($start_date);
        $last = strtotime($end_date);
        while ($current <= $last) {
            $date = date('Y-m-d', $current);
            if ($date > $today)
                break;
            $dayOfWeek = date('N', $current);
            if ($dayOfWeek <= 6 && !in_array($date, $holidays))
                $workingDays++;
            $current = strtotime('+1 day', $current);
        }
        return $workingDays;
    }

    private function get_manual_payroll($karyawan_id, $bulan)
    {
        $bulan_date = $bulan . '-01';
        $manual = $this->db->get_where('tbl_payroll_manual', ['karyawan_id' => $karyawan_id, 'bulan' => $bulan_date])->row();
        return (object) [
            'reward' => $manual->reward ?? 0,
            'bpjs' => $manual->bpjs ?? 0,
            'tunjangan_extra' => $manual->tunjangan_extra ?? 0,
            'tunjangan_kinerja_manual' => $manual->tunjangan_kinerja_manual ?? 0,
            'kasbon' => $manual->kasbon ?? 0,
            'pinjaman' => $manual->pinjaman ?? 0
        ];
    }

    private function get_meal_allowance($karyawan_id, $start_date, $end_date)
    {
        $this->db->select_sum('biaya_makan');
        $this->db->where(['karyawan_id' => $karyawan_id, 'status' => 'disetujui', 'tanggal >=' => $start_date, 'tanggal <=' => $end_date]);
        return $this->db->get('tbl_lembur')->row()->biaya_makan ?? 0;
    }

    private function get_lateness_penalty($karyawan_id, $start_date, $end_date)
    {
        $this->db->select_sum('nominal_potongan');
        $this->db->where(['karyawan_id' => $karyawan_id, 'tanggal >=' => $start_date, 'tanggal <=' => $end_date]);
        return $this->db->get('tbl_potongan_terlambat')->row()->nominal_potongan ?? 0;
    }
}
