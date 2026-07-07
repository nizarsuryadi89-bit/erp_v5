<?php
define('BASEPATH', '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/system/');
require_once '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/application/config/database.php';

$db_config = $db['default'];

try {
    $dsn = "mysql:host={$db_config['hostname']};dbname={$db_config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Cari karyawan status Kontrak/Magang yang akhir_kontrak NULL atau kosong
    $stmt = $pdo->query("SELECT karyawan_id, nama_lengkap, tanggal_masuk, status_karyawan, akhir_kontrak FROM tbl_karyawan WHERE status_karyawan IN ('Kontrak', 'Magang') AND (akhir_kontrak IS NULL OR akhir_kontrak = '0000-00-00')");
    $karyawans = $stmt->fetchAll();
    
    echo "--- MIGRASI KONTRAK KARYAWAN ---\n";
    echo "Ditemukan " . count($karyawans) . " karyawan dengan kontrak kosong.\n\n";
    
    $update_stmt = $pdo->prepare("UPDATE tbl_karyawan SET akhir_kontrak = ? WHERE karyawan_id = ?");
    
    foreach($karyawans as $k) {
        $tgl_masuk = $k['tanggal_masuk'];
        // Hitung 2 tahun dari tanggal masuk
        $akhir_kontrak_baru = date('Y-m-d', strtotime($tgl_masuk . ' + 2 years'));
        
        $update_stmt->execute([$akhir_kontrak_baru, $k['karyawan_id']]);
        
        echo "Diperbarui: {$k['nama_lengkap']} (Tanggal Masuk: {$tgl_masuk} -> Akhir Kontrak Baru: {$akhir_kontrak_baru})\n";
    }
    
    echo "\nMigrasi database sukses dilakukan.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
