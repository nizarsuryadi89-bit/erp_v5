<?php
define('ENVIRONMENT', 'development');
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/';
define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');
define('FCPATH', __DIR__ . '/');

require_once BASEPATH . 'core/CodeIgniter.php';
$CI =& get_instance();
$CI->load->model('Absensi_model');
$CI->load->database();

$karyawan_ids = [56,16,53,60,79,43,3,48,14,45];

foreach ($karyawan_ids as $karyawan_id) {
    // Calling the exact controller logic might be hard if it's private, let's just use db
    $year = 2026;
    $quota_row = $CI->db->get_where('tbl_quota_cuti', ['karyawan_id' => $karyawan_id, 'tahun' => $year])->row();
    $quota_total = $quota_row ? (($quota_row->quota_awal > 0) ? $quota_row->quota_awal : 12) : 12;

    $requests = $CI->db->where('karyawan_id', $karyawan_id)
        ->where('YEAR(tanggal_mulai)', $year)
        ->where_in('status', ['Pending', 'Approved', 'Disetujui', 'approved', 'disetujui'])
        ->get('tbl_pengajuan_izin')->result();

    $used_annual_approved = 0;
    $limits = ['cuti menikah' => 7, 'cuti duka' => 3, 'cuti keluarga sakit' => 2, 'cuti melahirkan suami' => 3];
    $usage_special_approved = ['cuti menikah' => 0, 'cuti duka' => 0, 'cuti keluarga sakit' => 0, 'cuti melahirkan suami' => 0];

    foreach ($requests as $req) {
        $type_lower = strtolower($req->tipe_izin);
        if ($type_lower == 'cuti' || $type_lower == 'cuti tahunan') {
            $used_annual_approved += $req->durasi_jumlah;
        } elseif (array_key_exists($type_lower, $limits)) {
            $usage_special_approved[$type_lower] += $req->durasi_jumlah;
        }
    }

    $spillover = 0;
    foreach ($limits as $type => $limit) {
        if ($usage_special_approved[$type] > $limit) {
            $spillover += ($usage_special_approved[$type] - $limit);
        }
    }

    $final_used_annual = $used_annual_approved + $spillover;
    $sisa_quota = max(0, $quota_total - $final_used_annual);

    $menikah_used = min($limits['cuti menikah'], $usage_special_approved['cuti menikah']);
    $duka_used = min($limits['cuti duka'], $usage_special_approved['cuti duka']);
    $sakitkel_used = min($limits['cuti keluarga sakit'], $usage_special_approved['cuti keluarga sakit']);
    $melahirkan_used = min($limits['cuti melahirkan suami'], $usage_special_approved['cuti melahirkan suami']);

    $CI->db->where(['karyawan_id' => $karyawan_id, 'tahun' => $year])
        ->update('tbl_quota_cuti', [
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
        
    echo "Synced Karyawan ID: $karyawan_id. Used: $final_used_annual. Sisa: $sisa_quota.\n";
}
?>
