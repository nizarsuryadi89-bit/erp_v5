<?php
$file_path = '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/application/controllers/Dashboard.php';
$content = file_get_contents($file_path);

// Replace finance queries
$content = str_replace(
    '$data[\'income_today\'] = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE DATE(tgl_bayar) = ?", [$hariini])->row()->total ?? 0;',
    '$data[\'income_today\'] = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE DATE(tgl_bayar) = ?" . ($pid ? " AND perusahaan_id = \'$pid\'" : ""), [$hariini])->row()->total ?? 0;',
    $content
);

$content = str_replace(
    '$data[\'omzet_bulan\'] = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE DATE_FORMAT(tgl_bayar, \'%Y-%m\') = ?", [$bulanini])->row()->total ?? 0;',
    '$data[\'omzet_bulan\'] = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE DATE_FORMAT(tgl_bayar, \'%Y-%m\') = ?" . ($pid ? " AND perusahaan_id = \'$pid\'" : ""), [$bulanini])->row()->total ?? 0;',
    $content
);

$content = str_replace(
    '$data[\'piutang_total\'] = $this->db->query("SELECT SUM(total_tagihan - total_bayar) as sisa FROM tbl_pembayaran WHERE status_lunas = \'0\'")->row()->sisa ?? 0;',
    '$data[\'piutang_total\'] = $this->db->query("SELECT SUM(total_tagihan - total_bayar) as sisa FROM tbl_pembayaran WHERE status_lunas = \'0\'" . ($pid ? " AND perusahaan_id = \'$pid\'" : ""))->row()->sisa ?? 0;',
    $content
);

$content = str_replace(
    '$data[\'expense_bulan\'] = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE DATE_FORMAT(pengeluaran_tgl, \'%Y-%m\') = ?", [$bulanini])->row()->total ?? 0;',
    '$data[\'expense_bulan\'] = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE DATE_FORMAT(pengeluaran_tgl, \'%Y-%m\') = ?" . ($pid ? " AND perusahaan_id = \'$pid\'" : ""), [$bulanini])->row()->total ?? 0;',
    $content
);

$content = str_replace(
    '$gmv_curr = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?", [$curr_start, $curr_end])->row()->total ?? 0;',
    '$gmv_curr = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?" . ($pid ? " AND perusahaan_id = \'$pid\'" : ""), [$curr_start, $curr_end])->row()->total ?? 0;',
    $content
);

$content = str_replace(
    '$gmv_prev = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?", [$prev_start, $prev_end])->row()->total ?? 0;',
    '$gmv_prev = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?" . ($pid ? " AND perusahaan_id = \'$pid\'" : ""), [$prev_start, $prev_end])->row()->total ?? 0;',
    $content
);

$content = str_replace(
    '$cust_curr = $this->db->query("SELECT COUNT(DISTINCT konsumen_id) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?", [$curr_start, $curr_end])->row()->total ?? 0;',
    '$cust_curr = $this->db->query("SELECT COUNT(DISTINCT konsumen_id) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?" . ($pid ? " AND perusahaan_id = \'$pid\'" : ""), [$curr_start, $curr_end])->row()->total ?? 0;',
    $content
);

$content = str_replace(
    '$cust_prev = $this->db->query("SELECT COUNT(DISTINCT konsumen_id) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?", [$prev_start, $prev_end])->row()->total ?? 0;',
    '$cust_prev = $this->db->query("SELECT COUNT(DISTINCT konsumen_id) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?" . ($pid ? " AND perusahaan_id = \'$pid\'" : ""), [$prev_start, $prev_end])->row()->total ?? 0;',
    $content
);

$content = str_replace(
    '$sku_curr = $this->db->query("SELECT COUNT(p.produksi_id) as total FROM tbl_produksi p JOIN tbl_pemesanan o ON p.nospk = o.nospk WHERE o.created_at BETWEEN ? AND ?", [$curr_start . \' 00:00:00\', $curr_end . \' 23:59:59\'])->row()->total ?? 0;',
    '$sku_curr = $this->db->query("SELECT COUNT(p.produksi_id) as total FROM tbl_produksi p JOIN tbl_pemesanan o ON p.nospk = o.nospk WHERE o.created_at BETWEEN ? AND ?" . ($pid ? " AND (p.lokasi_cetak = \'$pid\' OR (p.lokasi_cetak = \'\' AND p.perusahaan_id = \'$pid\'))" : ""), [$curr_start . \' 00:00:00\', $curr_end . \' 23:59:59\'])->row()->total ?? 0;',
    $content
);

$content = str_replace(
    '$sku_prev = $this->db->query("SELECT COUNT(p.produksi_id) as total FROM tbl_produksi p JOIN tbl_pemesanan o ON p.nospk = o.nospk WHERE o.created_at BETWEEN ? AND ?", [$prev_start . \' 00:00:00\', $prev_end . \' 23:59:59\'])->row()->total ?? 0;',
    '$sku_prev = $this->db->query("SELECT COUNT(p.produksi_id) as total FROM tbl_produksi p JOIN tbl_pemesanan o ON p.nospk = o.nospk WHERE o.created_at BETWEEN ? AND ?" . ($pid ? " AND (p.lokasi_cetak = \'$pid\' OR (p.lokasi_cetak = \'\' AND p.perusahaan_id = \'$pid\'))" : ""), [$prev_start . \' 00:00:00\', $prev_end . \' 23:59:59\'])->row()->total ?? 0;',
    $content
);

// For `_prepare_admin_dashboard_data`
$content = str_replace(
    '$data[\'pending_approvals\'] = $this->db->where(\'approve\', 1)',
    'if ($pid) { $this->db->group_start()->where(\'lokasi_cetak\', $pid)->or_group_start()->where(\'lokasi_cetak\', \'\')->where(\'perusahaan_id\', $pid)->group_end()->group_end(); } $data[\'pending_approvals\'] = $this->db->where(\'approve\', 1)',
    $content
);

$content = str_replace(
    '$data[\'daily_consumption\'] = $this->M_gudang->get_daily_consumption($harian);',
    '$data[\'daily_consumption\'] = $this->M_gudang->get_daily_consumption($harian, $pid);',
    $content
);

file_put_contents($file_path, $content);
