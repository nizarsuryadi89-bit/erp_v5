<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payroll_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get start and end date for a payroll month
     */
    public function _get_payroll_range($bulan, $perusahaan_id = '')
    {
        // Default values
        $start_day = 26;
        $end_day = 25;

        // Get company settings
        if (empty($perusahaan_id)) {
            $perusahaan_id = $this->session->userdata('perusahaan_id') ?: 1;
        }

        $company = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
        if ($company && isset($company->payroll_start_day) && isset($company->payroll_end_day)) {
            $start_day = $company->payroll_start_day ?: 26;
            $end_day = $company->payroll_end_day ?: 25;
        }

        $current_month_first_day = $bulan . '-01';

        if ($start_day > $end_day) {
            // Split across months (e.g. 26th prev to 25th curr)
            $start_date = date('Y-m-', strtotime('-1 month', strtotime($current_month_first_day))) . str_pad($start_day, 2, '0', STR_PAD_LEFT);
            $end_date = $bulan . '-' . str_pad($end_day, 2, '0', STR_PAD_LEFT);
        } else {
            // Same month (e.g. 1st to 30th)
            $start_date = $bulan . '-' . str_pad($start_day, 2, '0', STR_PAD_LEFT);
            $end_date = $bulan . '-' . str_pad($end_day, 2, '0', STR_PAD_LEFT);
        }

        return [
            'start' => $start_date,
            'end' => $end_date
        ];
    }

    /**
     * Get Comprehensive Salary Data (Real-time Calculation)
     */
    public function get_salary_data($bulan, $karyawan_id = '', $jabatan_id = '', $perusahaan_id = '')
    {
        $range = $this->_get_payroll_range($bulan, $perusahaan_id);
        $start_date = $range['start'];
        $end_date = $range['end'];

        $this->db->select('
            k.karyawan_id, k.nama_lengkap, k.nik, k.foto, k.gaji_pokok as individual_gaji,
            k.nominal_lembur as individual_lembur, k.tunjangan_jabatan as individual_tunjangan_jabatan,
            k.jenis_gaji, k.status_karyawan, k.transport_aktif, k.telepon, k.perusahaan_id,
            j.nama_jabatan, j.tunjangan_jabatan as default_tunjangan_jabatan,
            o.operator_nama as divisi_nama, p.nama_perusahaan
        ');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->join('tbl_operator o', 'o.operator_id = k.divisi_id', 'left');
        $this->db->join('tbl_perusahaan p', 'p.id_perusahaan = k.perusahaan_id', 'left');

        if ($karyawan_id) $this->db->where('k.karyawan_id', $karyawan_id);
        if ($jabatan_id) $this->db->where('k.jabatan_id', $jabatan_id);
        if ($perusahaan_id) $this->db->where('k.perusahaan_id', $perusahaan_id);

        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $this->db->where("k.nik NOT LIKE 'STATION%'");
        $this->db->where("k.nik NOT LIKE 'TERM%'");
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');
        $this->db->order_by('k.nama_lengkap', 'ASC');

        $employees = $this->db->get()->result();
        if (empty($employees)) return [];

        $perf_data = $this->get_performance_data($bulan, $karyawan_id, $jabatan_id, $start_date, $end_date);
        $perf_map = [];
        foreach ($perf_data as $p) $perf_map[$p->karyawan_id] = $p;

        // PRE-FETCH: Approved SPLs
        $this->db->where('status', 'disetujui');
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        if ($karyawan_id) $this->db->where('karyawan_id', $karyawan_id);
        $all_spls = $this->db->get('tbl_lembur')->result();
        $spl_map = [];
        foreach ($all_spls as $s) $spl_map[$s->karyawan_id][$s->tanggal][] = $s;

        // PRE-FETCH: Attendance
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        if ($karyawan_id) $this->db->where('karyawan_id', $karyawan_id);
        $all_atts = $this->db->get('absensi')->result();
        foreach ($all_atts as $a) $att_map[$a->karyawan_id][$a->tanggal] = $a;

        // PRE-FETCH: Synchronized Snapshots
        $sync_map = [];
        $this->db->where('bulan', $bulan);
        if ($karyawan_id) $this->db->where('karyawan_id', $karyawan_id);
        
        // Simple error suppression to handle missing table gracefully
        $all_sync = @$this->db->get('tbl_absensi_rekap_karyawan')->result();
        if ($all_sync) {
            foreach ($all_sync as $s) $sync_map[$s->karyawan_id] = $s;
        }

        $period_dates = [];
        $curr = strtotime($start_date);
        while ($curr <= strtotime($end_date)) {
            $period_dates[] = date('Y-m-d', $curr);
            $curr = strtotime('+1 day', $curr);
        }

        $salary_data = [];
        foreach ($employees as $emp) {
            $k_id = $emp->karyawan_id;
            $perf = $perf_map[$k_id] ?? (object)['total_hadir' => 0, 'total_telat' => 0, 'total_sakit' => 0, 'total_izin' => 0, 'total_alpha' => 0, 'total_detik_telat' => 0, 'skor_kinerja' => 0];

            $rate_lembur = ($emp->individual_lembur > 0) ? $emp->individual_lembur : 10000;
            $sum_pagi = 0; $sum_wajib = 0; $sum_kerjaan = 0;
            $sum_pagi_act = 0; $sum_wajib_act = 0; $sum_kerjaan_act = 0;
            $records_pagi = []; $records_wajib = []; $records_kerjaan = [];
            $total_spl_count = 0; $dynamic_meal_sum = 0;

            foreach ($period_dates as $d) {
                $day_spls = $spl_map[$k_id][$d] ?? [];
                $att_day = $att_map[$k_id][$d] ?? null;
                if (empty($day_spls) && !$att_day) continue;

                // 1. Auto-Wajib calculation
                $res_wajib = $this->_hitung_jam_lembur($k_id, '00:00', '00:00', $d, $att_day);
                $daily_w_sum = (float)$res_wajib['raw_wajib'];
                $daily_w_act = (float)$res_wajib['breakdown']['act_wajib'];
                $daily_w_start = $res_wajib['wajib_start'];
                $daily_w_end = $res_wajib['wajib_end'];

                $daily_p_sum = 0; $daily_p_act = 0;
                $daily_p_start = '00:00'; $daily_p_end = '00:00';

                // 2. SPL calculation
                foreach ($day_spls as $spl) {
                    $total_spl_count++;

                    // Deteksi SPL tipe auto-wajib (jam_mulai & jam_selesai = 00:00)
                    // Jam-nya SUDAH dihitung oleh auto-wajib call sebelum loop ini
                    // Jangan panggil _hitung_jam_lembur → akan menyebabkan double count!
                    $spl_jm = substr($spl->jam_mulai ?? '00:00', 0, 5);
                    $spl_js = substr($spl->jam_selesai ?? '00:00', 0, 5);
                    if ($spl_jm === '00:00' && $spl_js === '00:00') {
                        // Fallback hanya jika auto-wajib gagal (jam_pulang tidak tercatat)
                        if ($daily_w_sum == 0) {
                            $hari_db_s = (date('N', strtotime($d)) == 6) ? 'Sabtu' : 'Senin-Jumat';
                            $wajib_cfg = $this->db->order_by('total_jam', 'DESC')
                                ->get_where('tbl_lembur_jam_config', ['hari' => $hari_db_s, 'is_wajib' => 1])->row();
                            if ($wajib_cfg) {
                                $daily_w_sum   = (float)$wajib_cfg->total_jam;
                                $daily_w_act   = (strtotime($wajib_cfg->jam_selesai) - strtotime($wajib_cfg->jam_mulai)) / 3600;
                                $daily_w_start = substr($wajib_cfg->jam_mulai, 0, 5);
                                $daily_w_end   = substr($wajib_cfg->jam_selesai, 0, 5);
                            }
                        }
                        continue; // ← skip; auto-wajib sudah handle jam ini
                    }

                    // SPL dengan jam spesifik → hitung normal
                    $res = $this->_hitung_jam_lembur($k_id, $spl->jam_mulai, $spl->jam_selesai, $d, $att_day);
                    if ($res['raw_pagi'] > 0) {
                        $daily_p_sum += (float)$res['raw_pagi'];
                        $daily_p_act += (float)$res['breakdown']['act_pagi'];
                        if ($daily_p_start == '00:00' || strtotime($res['pagi_start']) < strtotime($daily_p_start)) $daily_p_start = $res['pagi_start'];
                        if ($daily_p_end == '00:00' || strtotime($res['pagi_end']) > strtotime($daily_p_end)) $daily_p_end = $res['pagi_end'];
                    }
                    if ($res['raw_kerjaan'] > 0) {
                        $sum_kerjaan += (float)$res['raw_kerjaan'];
                        $sum_kerjaan_act += (float)$res['breakdown']['act_kerjaan'];
                        $records_kerjaan[] = ['tgl' => date('d/m', strtotime($d)), 'jam' => $spl->jam_mulai . '-' . $spl->jam_selesai, 'durasi' => round($res['raw_kerjaan'], 1), 'actual' => round($res['breakdown']['act_kerjaan'], 1), 'info' => $spl->kegiatan];
                    }
                    if ($res['raw_wajib'] > 0) {
                        if ($daily_w_sum == 0) {
                            $daily_w_sum = (float)$res['raw_wajib'];
                            $daily_w_act = (float)$res['breakdown']['act_wajib'];
                            $daily_w_start = $res['wajib_start']; $daily_w_end = $res['wajib_end'];
                        } else {
                            $sum_kerjaan += (float)$res['raw_wajib'];
                            $sum_kerjaan_act += (float)$res['breakdown']['act_wajib'];
                            $records_kerjaan[] = ['tgl' => date('d/m', strtotime($d)), 'jam' => substr($res['wajib_start'], 0, 5) . '-' . substr($res['wajib_end'], 0, 5), 'durasi' => round($res['raw_wajib'], 1), 'actual' => round($res['breakdown']['act_wajib'], 1), 'info' => $spl->kegiatan . ' (SPL)'];
                        }
                    }
                }

                if ($daily_p_sum > 0) {
                    $sum_pagi += $daily_p_sum; $sum_pagi_act += $daily_p_act;
                    $records_pagi[] = ['tgl' => date('d/m', strtotime($d)), 'jam' => $daily_p_start . '-' . $daily_p_end, 'durasi' => round($daily_p_sum, 1), 'actual' => round($daily_p_act, 1), 'info' => 'Pagi'];
                }
                if ($daily_w_sum > 0) {
                    $sum_wajib += $daily_w_sum; $sum_wajib_act += $daily_w_act;
                    $records_wajib[] = ['tgl' => date('d/m', strtotime($d)), 'jam' => $daily_w_start . '-' . $daily_w_end, 'durasi' => round($daily_w_sum, 1), 'actual' => round($daily_w_act, 1), 'info' => 'Wajib'];
                }

                // 3. Dynamic Meal
                $day_n = date('N', strtotime($d));
                if ($day_n <= 6) {
                    $out_ts = 0;
                    foreach ($day_spls as $spl) if ($spl->jam_selesai != '00:00:00') $out_ts = max($out_ts, strtotime("$d $spl->jam_selesai"));
                    if ($att_day && $att_day->jam_pulang != '00:00:00') $out_ts = max($out_ts, strtotime("$d $att_day->jam_pulang"));
                    if ($out_ts > 0) {
                        $out_m = date('H:i', $out_ts);
                        if ($out_m >= "23:00") $dynamic_meal_sum += $this->_get_lembur_config('makan_23_00', 25000);
                        elseif ($out_m >= "21:00") $dynamic_meal_sum += $this->_get_lembur_config('makan_21_00', 20000);
                        elseif ($out_m >= "20:00") $dynamic_meal_sum += $this->_get_lembur_config('makan_20_00', 15000);
                        elseif ($out_m >= "19:00") $dynamic_meal_sum += $this->_get_lembur_config('makan_19_00', 10000);
                    }
                }
            }

            // OVERRIDE: Use synchronized data if available
            $is_synchronized = false;
            if (isset($sync_map[$k_id])) {
                $is_synchronized = true;
                $sync = $sync_map[$k_id];
                
                // Override attendance stats
                $perf->total_hadir = $sync->total_hadir;
                $perf->total_izin = $sync->total_izin;
                $perf->total_sakit = $sync->total_sakit;
                $perf->total_alpha = $sync->total_alpha;
                $perf->total_terlambat = $sync->total_terlambat;
                
                // Override overtime sums
                $sync_total_lembur = (float) $sync->total_lembur;
                $calc_total_lembur = $sum_pagi + $sum_wajib + $sum_kerjaan;
                
                if (abs($sync_total_lembur - $calc_total_lembur) > 0.01) {
                    $sum_wajib = (float) $sync->lembur_wajib_tambahan;
                    $sum_pagi = (float) $sync->lembur_pagi;
                    $sum_kerjaan = isset($sync->lembur_kerjaan) ? (float) $sync->lembur_kerjaan : 0;
                }
            }

            $gaji_pokok = ($emp->jenis_gaji == 'Harian') ? ($emp->individual_gaji * $perf->total_hadir) : $emp->individual_gaji;
            $uang_lembur = ($sum_pagi + $sum_wajib + $sum_kerjaan) * $rate_lembur;
            $tunjangan_jabatan = ($emp->individual_tunjangan_jabatan > 0) ? $emp->individual_tunjangan_jabatan : ($emp->default_tunjangan_jabatan ?: 0);
            $base_for_transport = ($emp->jenis_gaji == 'Harian') ? ($emp->individual_gaji * 26) : $emp->individual_gaji;
            $tunjangan_transportasi = (isset($emp->transport_aktif) && $emp->transport_aktif == 1) ? round(($base_for_transport * 0.05 / 26) * $perf->total_hadir) : 0;

            $biaya_makan_stored = $this->get_meal_allowance($k_id, $bulan, $start_date, $end_date);
            $biaya_makan_lembur = $biaya_makan_stored > 0 ? $biaya_makan_stored : $dynamic_meal_sum;
            
            // Override meal allowance if synchronized data is present and column exists
            if ($is_synchronized && isset($sync->biaya_makan_lembur) && $sync->biaya_makan_lembur > 0) {
                $biaya_makan_lembur = (float) $sync->biaya_makan_lembur;
            }
            
            $manual_pay = $this->get_manual_payroll($k_id, $bulan, $start_date, $end_date);

            $rate_per_jam = ($gaji_pokok / 26) / 8;
            $pot_telat = round($rate_per_jam * (($perf->total_detik_telat ?? 0) / 3600)) + $this->get_lateness_penalty($k_id, $bulan, $start_date, $end_date);
            $pot_alpha = round(($gaji_pokok / 26) * $perf->total_alpha);

            $total_gaji = $gaji_pokok + $tunjangan_jabatan + $tunjangan_transportasi + $uang_lembur + $biaya_makan_lembur + $manual_pay->reward + $manual_pay->bpjs + $manual_pay->tunjangan_extra + $manual_pay->tunjangan_kinerja_manual - $pot_alpha - $manual_pay->kasbon - $manual_pay->pinjaman - $pot_telat;

            $manual = $this->db->get_where('tbl_gaji_manual', ['karyawan_id' => $k_id, 'bulan' => $bulan])->row();
            $is_manual = false;
            if ($manual) {
                $is_manual = true;
                $total_gaji = $manual->total_gaji;
                $gaji_pokok = $manual->gaji_pokok; $tunjangan_jabatan = $manual->tunjangan_jabatan;
                $tunjangan_transportasi = $manual->tunjangan_transportasi; $uang_lembur = $manual->uang_lembur;
                $pot_alpha = $manual->potongan_alpha; $pot_telat = $manual->potongan_terlambat;
                $biaya_makan_lembur = $manual->biaya_makan_lembur ?? 0;
            }

            $salary_data[] = [
                'is_manual' => $is_manual, 'karyawan_id' => $k_id, 'nama_lengkap' => $emp->nama_lengkap, 'nik' => $emp->nik, 'foto' => $emp->foto,
                'nama_jabatan' => $emp->nama_jabatan, 'divisi_nama' => $emp->divisi_nama, 'nama_perusahaan' => $emp->nama_perusahaan,
                'gaji_pokok' => $gaji_pokok, 'tunjangan_jabatan' => $tunjangan_jabatan, 'tunjangan_transportasi' => $tunjangan_transportasi,
                'uang_lembur' => $uang_lembur, 'biaya_makan_lembur' => $biaya_makan_lembur,
                'total_jam_lembur' => $sum_pagi + $sum_wajib + $sum_kerjaan,
                'detail_lembur' => [
                    'pagi' => ['jam' => $sum_pagi, 'jam_act' => $sum_pagi_act, 'rate' => $rate_lembur, 'total' => $sum_pagi * $rate_lembur, 'records' => $records_pagi],
                    'wajib' => ['jam' => $sum_wajib, 'jam_act' => $sum_wajib_act, 'rate' => $rate_lembur, 'total' => $sum_wajib * $rate_lembur, 'records' => $records_wajib],
                    'kerjaan' => ['jam' => $sum_kerjaan, 'jam_act' => $sum_kerjaan_act, 'rate' => $rate_lembur, 'total' => $sum_kerjaan * $rate_lembur, 'records' => $records_kerjaan]
                ],
                'reward' => $manual_pay->reward, 'bpjs' => $manual_pay->bpjs, 'tunjangan_extra' => $manual_pay->tunjangan_extra,
                'tunjangan_kinerja_manual' => $manual_pay->tunjangan_kinerja_manual,
                'potongan_alpha' => $pot_alpha, 'potongan_terlambat' => $pot_telat, 'kasbon' => $manual_pay->kasbon, 'pinjaman' => $manual_pay->pinjaman,
                'total_gaji' => $total_gaji, 'total_hadir' => $perf->total_hadir, 'total_alpha' => $perf->total_alpha,
                'total_telat' => $perf->total_telat ?? $perf->total_terlambat, 'total_sakit' => $perf->total_sakit, 'total_izin' => $perf->total_izin,
                'skor_kinerja' => $perf->skor_kinerja, 'status_karyawan' => $emp->status_karyawan, 'jenis_gaji' => $emp->jenis_gaji, 'telepon' => $emp->telepon, 'perusahaan_id' => $emp->perusahaan_id,
                'transport_aktif' => $emp->transport_aktif,
                'is_synchronized' => $is_synchronized,
                'bonus_kinerja' => 0, 'bonus_kehadiran' => 0
            ];
        }
        return $salary_data;
    }

    public function _hitung_jam_lembur($karyawan_id, $jam_mulai, $jam_selesai, $tanggal, $absensi_data = null)
    {
        // FIX ROOT CAUSE: Normalisasi format waktu dari DB ('00:00:00') ke ('00:00')
        // MySQL TIME kolom mengembalikan 'HH:MM:SS', sedangkan input manual pakai 'HH:MM'
        // Jika tidak dinormalisasi, kondisi '00:00' == '00:00:00' SELALU FALSE
        // sehingga auto-wajib branch tidak pernah terpicu untuk SPL dari database
        $jam_mulai   = substr($jam_mulai ?? '00:00', 0, 5);
        $jam_selesai = substr($jam_selesai ?? '00:00', 0, 5);

        $jam_mulai_orig = $jam_mulai;
        if ($jam_mulai != '00:00' && strtotime($jam_mulai) >= strtotime("12:00:00")) {
            $std_v_start = (date('N', strtotime($tanggal)) == 6) ? "15:00" : "16:00";
            $diff_mins = (strtotime($jam_mulai) - strtotime($std_v_start)) / 60;
            if ($diff_mins > 0 && $diff_mins <= 15) $jam_mulai = $std_v_start;
        }

        // ─── ATURAN PEMBULATAN MENIT JAM KELUAR LEMBUR ───────────────────────
        // Berlaku global untuk semua SPL dengan jam spesifik (bukan auto-wajib '00:00')
        //   Menit  0 – 25 : dibulatkan ke bawah → jam:00 (tidak dihitung)
        //   Menit 26 – 45 : ditambah 0.5 jam   → jam:30
        //   Menit 46 – 59 : dibulatkan ke atas  → (jam+1):00  "mengikuti range"
        if ($jam_selesai !== '00:00') {
            list($es_h, $es_m) = array_map('intval', explode(':', $jam_selesai));
            if ($es_m >= 46) {
                $es_h = ($es_h + 1) % 24;   // round up → next hour
                $es_m = 0;
            } elseif ($es_m >= 26) {
                $es_m = 30;                  // round to :30
            } else {
                $es_m = 0;                   // round down → :00
            }
            $jam_selesai = str_pad($es_h, 2, '0', STR_PAD_LEFT) . ':' . str_pad($es_m, 2, '0', STR_PAD_LEFT);
        }
        // ─────────────────────────────────────────────────────────────────────

        $start_spl = strtotime("$tanggal $jam_mulai");
        $end_spl   = strtotime("$tanggal $jam_selesai");
        if ($end_spl < $start_spl && $end_spl != strtotime("$tanggal 00:00")) $end_spl += 86400;

        $absensi = $absensi_data ?: $this->db->get_where('absensi', ['karyawan_id' => $karyawan_id, 'tanggal' => $tanggal])->row();
        $jam_masuk_real = (isset($absensi->jam_masuk) && $absensi->jam_masuk != '00:00:00') ? strtotime("$tanggal $absensi->jam_masuk") : null;
        $day_num = date('N', strtotime($tanggal));
        $hari_db = ($day_num == 6) ? 'Sabtu' : 'Senin-Jumat';

        $breakdown = ['pagi' => 0, 'wajib' => 0, 'kerjaan' => 0, 'potongan_telat' => 0];
        $actual_bd = ['pagi' => 0, 'wajib' => 0, 'kerjaan' => 0];

        if ($jam_masuk_real && $jam_masuk_real >= strtotime("$tanggal 08:11:00")) $breakdown['potongan_telat'] = 1.0;

        $is_pagi = (strtotime($jam_mulai) < strtotime("12:00:00"));
        $matched_config = null; $is_auto_wajib = false;

        if ($jam_mulai == '00:00' && $jam_selesai == '00:00') {
            $is_auto_wajib = true;
            // FIX: Auto-wajib hanya berlaku jika karyawan berstatus 'Hadir'
            if (isset($absensi->status) && $absensi->status === 'Hadir') {
                $jam_pulang_real = (isset($absensi->jam_pulang) && $absensi->jam_pulang != '00:00:00') ? strtotime("$tanggal " . $absensi->jam_pulang) : null;
                if ($jam_pulang_real) {
                    // Auto-wajib hanya boleh mengikuti sesi WAJIB pada kebijakan lembur.
                    // Jika karyawan pulang melewati batas wajib tetapi tidak ada SPL,
                    // sistem tidak boleh naik ke bracket TAMBAHAN.
                    static $wajib_configs_cache = [];
                    if (!isset($wajib_configs_cache[$hari_db])) {
                        $wajib_configs_cache[$hari_db] = $this->db
                            ->order_by('jam_selesai', 'DESC')
                            ->get_where('tbl_lembur_jam_config', ['hari' => $hari_db, 'is_wajib' => 1])
                            ->result();
                    }
                    $wajib_configs = $wajib_configs_cache[$hari_db];
                    foreach ($wajib_configs as $cfg) {
                        if ($jam_pulang_real >= (strtotime("$tanggal $cfg->jam_selesai") - 900)) {
                            $breakdown['wajib'] = (float)$cfg->total_jam;
                            $actual_bd['wajib'] = (strtotime($cfg->jam_selesai) - strtotime($cfg->jam_mulai)) / 3600;
                            $start_spl = strtotime("$tanggal $cfg->jam_mulai"); $end_spl = strtotime("$tanggal $cfg->jam_selesai");
                            $matched_config = $cfg; break;
                        }
                    }
                }
            }
        } else {
            static $configs_cache = [];
            if (!isset($configs_cache[$hari_db])) {
                $configs_cache[$hari_db] = $this->db->get_where('tbl_lembur_jam_config', ['hari' => $hari_db])->result();
            }
            $configs = $configs_cache[$hari_db];
            $actual_start  = strtotime($tanggal . ' ' . substr($jam_mulai, 0, 5));
            $actual_end    = strtotime($tanggal . ' ' . substr($jam_selesai, 0, 5));
            $covering_configs = [];

            foreach ($configs as $cfg) {
                $cfg_start = strtotime($tanggal . ' ' . substr($cfg->jam_mulai, 0, 5));
                $cfg_end   = strtotime($tanggal . ' ' . substr($cfg->jam_selesai, 0, 5));
                
                if ($is_pagi) {
                    // Untuk lembur pagi, kita ambil semua range yang jam pulangnya sesuai.
                    $missed_end = max(0, $cfg_end - $actual_end) / 60;
                    if ($missed_end <= 15) {
                        $covering_configs[] = $cfg;
                    }
                } else {
                    // --- LOGIKA LAMA (Khusus Lembur Sore dsb, tidak diubah) ---
                    $d_start   = abs($actual_start - $cfg_start) / 60;
                    $d_end     = abs($actual_end   - $cfg_end)   / 60;

                    if ($cfg_start <= $actual_start && $cfg_end >= $actual_end) {
                        $start_gap_mins = ($actual_start - $cfg_start) / 60;
                        if ($start_gap_mins <= 30) {
                            $covering_configs[] = $cfg;
                        }
                        continue;
                    }

                    if ($d_start <= 15 && $d_end <= 15 && $matched_config === null) {
                        $matched_config = $cfg;
                    }
                }
            }

            if (!empty($covering_configs)) {
                if ($is_pagi) {
                    $valid_buckets = [];
                    foreach ($covering_configs as $c) {
                        $c_start = strtotime($tanggal . ' ' . substr($c->jam_mulai, 0, 5));
                        // Ambil range yang "masih masuk" (jam mulai config <= jam absen masuk)
                        if ($actual_start >= $c_start) {
                            $valid_buckets[] = $c;
                        }
                    }

                    if (!empty($valid_buckets)) {
                        // Pilih range dengan jam_mulai yang paling TERAKHIR (mendekati absen masuk)
                        usort($valid_buckets, function ($a, $b) {
                            $cmp = strcmp(substr($b->jam_mulai, 0, 5), substr($a->jam_mulai, 0, 5)); // DESC
                            if ($cmp === 0) {
                                return strcmp(substr($b->jam_selesai, 0, 5), substr($a->jam_selesai, 0, 5)); // DESC
                            }
                            return $cmp;
                        });
                        $matched_config = $valid_buckets[0];
                    } else {
                        // Jika absen lebih awal dari SEMUA range yang ada, ambil range paling AWAL
                        usort($covering_configs, function ($a, $b) {
                            $cmp = strcmp(substr($a->jam_mulai, 0, 5), substr($b->jam_mulai, 0, 5)); // ASC
                            if ($cmp === 0) {
                                return strcmp(substr($b->jam_selesai, 0, 5), substr($a->jam_selesai, 0, 5)); // DESC
                            }
                            return $cmp;
                        });
                        $matched_config = $covering_configs[0];
                    }
                } else {
                    // Logika urut lama untuk lembur sore (jam selesai paling awal)
                    usort($covering_configs, function ($a, $b) {
                        return strcmp(substr($a->jam_selesai, 0, 5), substr($b->jam_selesai, 0, 5));
                    });
                    $matched_config = $covering_configs[0];
                }
            }
        }

        if ($matched_config) {
            $val = (float)$matched_config->total_jam;
            if ($is_pagi) {
                $breakdown['pagi'] = max(0, $val);
                $actual_bd['pagi'] = (strtotime($matched_config->jam_selesai) - strtotime($matched_config->jam_mulai)) / 3600;
            } else {
                if ($matched_config->is_wajib == 1) {
                    $breakdown['wajib']   = $val;
                    $actual_bd['wajib']   = (strtotime($matched_config->jam_selesai) - strtotime($matched_config->jam_mulai)) / 3600;
                } else {
                    static $max_w_cache = [];
                    $cache_key = $hari_db . '_' . $matched_config->jam_mulai;
                    if (!isset($max_w_cache[$cache_key])) {
                        $max_w_cache[$cache_key] = $this->db->select_max('total_jam', 'max_w')
                            ->get_where('tbl_lembur_jam_config', ['hari' => $hari_db, 'is_wajib' => 1, 'jam_mulai' => $matched_config->jam_mulai])
                            ->row()->max_w ?? 0;
                    }
                    $max_w = $max_w_cache[$cache_key];
                    if ($max_w > 0 && $val > $max_w) {
                        $breakdown['wajib']    = $max_w;
                        $breakdown['kerjaan']  = $val - $max_w;
                        $actual_bd['wajib']    = $max_w;
                        $actual_bd['kerjaan']  = (strtotime($matched_config->jam_selesai) - strtotime($matched_config->jam_mulai)) / 3600 - $max_w;
                    } else {
                        $breakdown['kerjaan']  = $val;
                        $actual_bd['kerjaan']  = (strtotime($matched_config->jam_selesai) - strtotime($matched_config->jam_mulai)) / 3600;
                    }
                }
            }
        } elseif (!$is_auto_wajib && $end_spl > $start_spl) {
            // Tidak ada config yang cocok → gunakan durasi aktual SPL
            // (kasus: jam lembur tidak ada di master config, misal 18:00-18:26)
            $actual_hours = round(($end_spl - $start_spl) / 3600, 2);
            if ($is_pagi) {
                $breakdown['pagi']   = $actual_hours;
                $actual_bd['pagi']   = $actual_hours;
            } else {
                $breakdown['kerjaan'] = $actual_hours;
                $actual_bd['kerjaan'] = $actual_hours;
            }
        }

        return [
            'raw_pagi'      => $breakdown['pagi'],
            'raw_wajib'     => $breakdown['wajib'],
            'raw_kerjaan'   => $breakdown['kerjaan'],
            'raw_potongan'  => $breakdown['potongan_telat'],
            'pagi_start'    => $is_pagi ? date('H:i', $start_spl)  : '00:00',
            'pagi_end'      => $is_pagi ? date('H:i', $end_spl)    : '00:00',
            'wajib_start'   => !$is_pagi ? date('H:i', $start_spl) : '00:00',
            'wajib_end'     => !$is_pagi ? date('H:i', $end_spl)   : '00:00',
            // FIX: Tambahkan kerjaan_start/kerjaan_end agar konsisten dengan absen_lembur()
            'kerjaan_start' => (!$is_pagi && $breakdown['kerjaan'] > 0) ? date('H:i', $start_spl) : '00:00',
            'kerjaan_end'   => (!$is_pagi && $breakdown['kerjaan'] > 0) ? date('H:i', $end_spl)   : '00:00',
            'breakdown'     => ['act_pagi' => $actual_bd['pagi'], 'act_wajib' => $actual_bd['wajib'], 'act_kerjaan' => $actual_bd['kerjaan']]
        ];
    }

    public function get_performance_data($bulan, $k_id = '', $j_id = '', $sd = '', $ed = '')
    {
        if (empty($sd)) { $sd = $bulan.'-01'; $ed = date('Y-m-t', strtotime($sd)); }
        $this->db->select('k.karyawan_id, k.nama_lengkap, k.nik, j.nama_jabatan, o.operator_nama as divisi_nama, p.nama_perusahaan, u.user_id,
            COUNT(CASE WHEN a.status = "Hadir" THEN 1 END) as total_hadir, COUNT(CASE WHEN a.status = "Sakit" THEN 1 END) as total_sakit,
            COUNT(CASE WHEN a.status = "Izin" THEN 1 END) as total_izin, COUNT(CASE WHEN a.status LIKE "Cuti%" THEN 1 END) as total_cuti,
            COUNT(CASE WHEN a.status = "Hadir" AND a.jam_masuk > "08:15:00" THEN 1 END) as total_telat,
            SUM(CASE WHEN a.status = "Hadir" AND a.jam_masuk > "08:00:00" THEN TIME_TO_SEC(TIMEDIFF(a.jam_masuk, "08:00:00")) ELSE 0 END) as total_detik_telat,
            COUNT(CASE WHEN a.status = "Hadir" AND a.jam_pulang != "00:00:00" AND ((DAYOFWEEK(a.tanggal) BETWEEN 2 AND 6 AND a.jam_pulang < "17:00:00") OR (DAYOFWEEK(a.tanggal) = 7 AND a.jam_pulang < "15:00:00")) THEN 1 END) as total_pulang_cepat');
        $this->db->from('tbl_karyawan k')->join('users u', 'u.karyawan_id = k.karyawan_id', 'left')->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left')->join('tbl_operator o', 'o.operator_id = k.divisi_id', 'left')->join('tbl_perusahaan p', 'p.id_perusahaan = k.perusahaan_id', 'left')->join('absensi a', 'a.karyawan_id = k.karyawan_id AND a.tanggal >= "'.$sd.'" AND a.tanggal <= "'.$ed.'"', 'left');
        $this->db->where('k.divisi_id !=', 8);
        $this->db->where_not_in('k.jabatan_id', [1, 2, 3]);
        $this->db->where("k.nik NOT LIKE 'STATION%'");
        $this->db->where("k.nik NOT LIKE 'TERM%'");
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');
        if ($k_id) $this->db->where('k.karyawan_id', $k_id); if ($j_id) $this->db->where('k.jabatan_id', $j_id);
        $result = $this->db->group_by('k.karyawan_id, k.nama_lengkap, k.nik, j.nama_jabatan, o.operator_nama, p.nama_perusahaan, u.user_id')->get()->result();
        $wd = $this->get_working_days($bulan, $sd, $ed);
        
        $user_ids = [];
        foreach ($result as $row) {
            if ($row->user_id) $user_ids[] = $row->user_id;
            $row->total_spk = 0;
        }

        if (!empty($user_ids)) {
            $this->db->select('deskprint_id, COUNT(*) as total_spk');
            $this->db->from('tbl_pemesanan');
            $this->db->where_in('deskprint_id', $user_ids);
            $this->db->where('created_at >=', $sd . ' 00:00:00');
            $this->db->where('created_at <=', $ed . ' 23:59:59');
            $this->db->group_by('deskprint_id');
            $spk_counts = $this->db->get()->result();
            
            $spk_map = [];
            foreach ($spk_counts as $spk) {
                $spk_map[$spk->deskprint_id] = $spk->total_spk;
            }
            
            foreach ($result as $row) {
                if ($row->user_id && isset($spk_map[$row->user_id])) {
                    $row->total_spk = $spk_map[$row->user_id];
                }
            }
        }

        foreach ($result as $row) {
            $tr = $row->total_hadir + $row->total_izin + $row->total_sakit + $row->total_cuti;
            $alpha = max(0, $wd - $tr); $row->total_alpha = $alpha;
            $row->skor_kinerja = 100 - ($row->total_sakit*1) - ($row->total_izin*1) - ($alpha*5) - ($row->total_telat*1) - ($row->total_pulang_cepat*3);
        }
        return $result;
    }

    public function get_working_days($bulan, $sd = '', $ed = '')
    {
        if (empty($sd)) { $sd = $bulan.'-01'; $ed = date('Y-m-t', strtotime($sd)); }
        $holidays = array_column($this->db->select('tanggal')->where(['tanggal >=' => $sd, 'tanggal <=' => $ed])->get('hari_libur')->result_array(), 'tanggal');
        $days = 0; $t = date('Y-m-d'); $c = strtotime($sd);
        while ($c <= strtotime($ed)) {
            $d = date('Y-m-d', $c); if ($d > $t) break;
            if (date('N', $c) <= 6 && !in_array($d, $holidays)) $days++;
            $c = strtotime('+1 day', $c);
        }
        return $days;
    }

    public function get_manual_payroll($k_id, $bulan, $sd = '', $ed = '')
    {
        $m = $this->db->get_where('tbl_payroll_manual', ['karyawan_id' => $k_id, 'bulan' => $bulan.'-01'])->row();
        return (object)['reward' => $m->reward ?? 0, 'bpjs' => $m->bpjs ?? 0, 'tunjangan_extra' => $m->tunjangan_extra ?? 0, 'tunjangan_kinerja_manual' => $m->tunjangan_kinerja_manual ?? 0, 'kasbon' => $m->kasbon ?? 0, 'pinjaman' => $m->pinjaman ?? 0];
    }

    public function get_meal_allowance($k_id, $bulan, $sd, $ed)
    {
        return $this->db->select_sum('biaya_makan')->get_where('tbl_lembur', ['karyawan_id' => $k_id, 'status' => 'disetujui', 'tanggal >=' => $sd, 'tanggal <=' => $ed])->row()->biaya_makan ?? 0;
    }

    public function get_lateness_penalty($k_id, $bulan, $sd, $ed)
    {
        return $this->db->select_sum('nominal_potongan')->get_where('tbl_potongan_terlambat', ['karyawan_id' => $k_id, 'tanggal >=' => $sd, 'tanggal <=' => $ed])->row()->nominal_potongan ?? 0;
    }

    private function _get_lembur_config($key, $def = null)
    {
        static $configs = null;
        if ($configs === null) {
            $configs = [];
            foreach ($this->db->get('tbl_lembur_config')->result() as $row) $configs[$row->config_key] = $row->config_value;
        }
        return $configs[$key] ?? $def;
    }
}
