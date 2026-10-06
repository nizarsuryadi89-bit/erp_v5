<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Backup extends MY_Controller
{
    private $backupDirectory;
    private $backupProfiles = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->dbutil();
        $this->load->helper(array('file', 'download'));

        // Default backup directory: uploads/backup/
        $this->backupDirectory = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR;
        if (!is_dir($this->backupDirectory)) {
            @mkdir($this->backupDirectory, 0755, true);
        }

        // Secure directory from direct access
        if (!file_exists($this->backupDirectory . '.htaccess')) {
            @file_put_contents($this->backupDirectory . '.htaccess', "Deny from all\nOptions -Indexes");
        }

        // Load models for cleanup if necessary
        $this->load->model('M_transaksi');
        $this->backupProfiles = $this->getBackupProfiles();
    }

    /**
     * Tampilan Utama: Daftar backup & pilihan tabel
     */
    public function index()
    {
        $allTables = $this->db->list_tables();
        $existingBackups = $this->getExistingBackups();

        $data = array(
            'title' => 'Database Management: Backup & Restore',
            'function' => 'index',
            'tables' => $allTables,
            'table_groups' => $this->getTableGroups($allTables),
            'backup_profiles' => $this->getProfileSummaries(),
            'existing_backups' => $existingBackups,
        );

        $this->load_admin_view('admin/backup/index', $data);
    }

    /**
     * 1. BACKUP SEMUA DATA (Full Backup)
     */
    public function all()
    {
        try {
            $tables = $this->db->list_tables();
            $this->_execute_backup($tables, 'FULL_BACKUP', 'download');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('backup');
        }
    }

    /**
     * 2. BACKUP BEBERAPA DATA (Partial Backup)
     */
    public function selected()
    {
        $selectedTables = $this->input->post('tables', true);

        if (empty($selectedTables) || !is_array($selectedTables)) {
            $this->session->set_flashdata('error', 'Silakan pilih setidaknya satu tabel untuk di-backup.');
            redirect('backup');
        }

        try {
            $validTables = $this->resolveExistingTables($selectedTables);
            if (empty($validTables)) {
                throw new Exception("Tabel yang dipilih tidak valid.");
            }
            $this->_execute_backup($validTables, 'PARTIAL_BACKUP', 'download');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('backup');
        }
    }

    /**
     * 3. BACKUP HARIAN (Save to Server)
     */
    public function daily()
    {
        try {
            $tables = $this->db->list_tables();
            $filename = $this->_execute_backup($tables, 'DAILY', 'save');

            $this->session->set_flashdata('success', 'Backup harian berhasil disimpan: ' . $filename);
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect('backup');
    }

    /**
     * 4. BACKUP TRANSAKSI & STOK (Offline Mode)
     * Hanya tabel-tabel krusial untuk kegiatan offline
     */
    public function backup_transaksi()
    {
        $this->runProfileBackup('offline_transaction');
    }

    /**
     * 5. BACKUP HARIAN TERFOKUS (Operational Data Only)
     * Mengamankan data yang sering berubah (Transaksi, Gudang, Absensi) tanpa menyertakan tabel statis.
     */
    public function backup_daily_operational()
    {
        $this->runProfileBackup('operational_daily');
    }

    /**
     * 6. BACKUP DATA STATIS (Core & Settings Only)
     * Mengamankan struktur menu, pengaturan, dan master data yang jarang berubah.
     */
    public function backup_static()
    {
        $this->runProfileBackup('master_periodic');
    }

    /**
     * Alias yang lebih eksplisit untuk backup operasional harian.
     */
    public function backup_operasional_harian()
    {
        $this->runProfileBackup('operational_daily');
    }

    /**
     * Backup master/referensi yang hanya berubah pada momen tertentu.
     */
    public function backup_master_periodik()
    {
        $this->runProfileBackup('master_periodic');
    }

    /**
     * CORE ENGINE: Eksekusi Backup
     */
    private function _execute_backup($tables, $prefix = 'backup', $mode = 'download')
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '1024M'); // Naikkan limit untuk fallback

        $timestamp = date('Ymd_His');
        $filename = $prefix . '_' . $timestamp . '.sql.gz';
        $targetPath = $this->backupDirectory . $filename;

        // 1. Coba gunakan mysqldump via exec() agar hemat memory (Sangat disarankan untuk DB besar)
        $mysqldumpPath = 'mysqldump'; 
        if (file_exists('/Applications/XAMPP/xamppfiles/bin/mysqldump')) {
            $mysqldumpPath = '/Applications/XAMPP/xamppfiles/bin/mysqldump';
        }

        $tablesStr = implode(' ', array_map('escapeshellarg', $tables));
        
        $command = sprintf(
            '%s -h %s -u %s %s %s %s | gzip > %s',
            $mysqldumpPath,
            escapeshellarg($this->db->hostname),
            escapeshellarg($this->db->username),
            (!empty($this->db->password) ? '-p' . escapeshellarg($this->db->password) : ''),
            escapeshellarg($this->db->database),
            $tablesStr,
            escapeshellarg($targetPath)
        );

        $resultCode = -1;
        if (function_exists('exec')) {
            @exec($command, $output, $resultCode);
        }

        // Jika exec() berhasil dan file terbuat
        if ($resultCode === 0 && file_exists($targetPath) && filesize($targetPath) > 0) {
            if ($mode === 'save') {
                return $filename;
            } else {
                // Streaming download tanpa load ke memory, lalu hapus
                header('Content-Type: application/x-gzip');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                header('Content-Length: ' . filesize($targetPath));
                readfile($targetPath);
                @unlink($targetPath); 
                exit;
            }
        } else {
            // 2. Fallback ke CI dbutil jika exec() di-disable di hosting
            $prefs = array(
                'tables' => $tables,
                'ignore' => array(),
                'format' => 'gzip',
                'filename' => $prefix . '_' . $timestamp . '.sql',
                'add_drop' => TRUE,
                'add_insert' => TRUE,
                'newline' => "\n"
            );

            $backupData = $this->dbutil->backup($prefs);

            if ($backupData === FALSE) {
                throw new Exception("Gagal membuat data backup database (memori penuh atau mysqldump tidak tersedia).");
            }

            if ($mode === 'save') {
                if (!write_file($targetPath, $backupData)) {
                    throw new Exception("Gagal menyimpan file di server. Pastikan folder 'uploads/backup' dapat ditulis (writable).");
                }
                return $filename;
            } else {
                force_download($filename, $backupData);
            }
        }
    }

    private function getBackupProfiles()
    {
        return array(
            'operational_daily' => array(
                'title' => 'Backup Operasional Harian',
                'description' => 'Transaksi, stok, produksi, keuangan, dan aktivitas user yang berubah setiap hari.',
                'prefix' => 'ERP_OPS_DAILY',
                'mode' => 'save',
                'success_message' => 'Backup operasional harian berhasil disimpan: %s',
                'error_message' => 'Gagal backup operasional harian: %s',
                'tables' => array(
                    'tbl_pemesanan',
                    'tbl_pemesanan_detail',
                    'tbl_transaksi',
                    'tbl_transaksi_detail',
                    'tbl_pembayaran',
                    'tbl_pembayaran_detail',
                    'tbl_pembayaran_produk',
                    'tbl_spk',
                    'tbl_pengeluaran',
                    'tbl_hutang',
                    'tbl_kas',
                    'tbl_arus_kas',
                    'tbl_stok_gudang',
                    'tbl_stok_log',
                    'tbl_kartu_stok',
                    'tbl_produksi',
                    'tbl_gudang',
                    'tbl_absensi',
                    'user_log',
                    'user_activity_log'
                ),
            ),
            'master_periodic' => array(
                'title' => 'Backup Master Periodik',
                'description' => 'Master data, referensi, konfigurasi, dan struktur sistem yang berubah hanya saat setup atau revisi kebijakan.',
                'prefix' => 'ERP_MASTER_PERIODIC',
                'mode' => 'download',
                'success_message' => 'Backup master periodik berhasil dibuat.',
                'error_message' => 'Gagal backup master periodik: %s',
                'tables' => array(
                    'menu',
                    'submenu',
                    'users',
                    'tbl_divisi',
                    'tbl_perusahaan',
                    'tbl_marketplace',
                    'tbl_jenis_transaksi',
                    'tbl_kategori',
                    'tbl_subkategori',
                    'tbl_produk',
                    'tbl_bahan'
                ),
            ),
            'offline_transaction' => array(
                'title' => 'Backup Offline Transaksi',
                'description' => 'Subset data paling penting untuk jaga operasional saat koneksi atau server utama bermasalah.',
                'prefix' => 'BACKUP_OFFLINE_TRANSAKSI',
                'mode' => 'download',
                'success_message' => 'Backup offline transaksi berhasil dibuat.',
                'error_message' => 'Gagal backup offline transaksi: %s',
                'tables' => array(
                    'tbl_transaksi',
                    'tbl_transaksi_detail',
                    'tbl_spk',
                    'tbl_stok_gudang',
                    'tbl_stok_log',
                    'tbl_kartu_stok',
                    'tbl_kas',
                    'tbl_arus_kas',
                    'tbl_produk',
                    'tbl_bahan'
                ),
            ),
        );
    }

    private function getProfileSummaries()
    {
        $summaries = array();
        foreach ($this->backupProfiles as $key => $profile) {
            $existingTables = $this->resolveExistingTables($profile['tables']);
            $summaries[$key] = array(
                'key' => $key,
                'title' => $profile['title'],
                'description' => $profile['description'],
                'prefix' => $profile['prefix'],
                'mode' => $profile['mode'],
                'tables' => $existingTables,
                'table_count' => count($existingTables),
            );
        }

        return $summaries;
    }

    private function getTableGroups($allTables)
    {
        $groups = array(
            'operational_daily' => array(
                'title' => 'Operasional Harian',
                'description' => 'Tabel yang paling sering berubah karena transaksi dan aktivitas harian.',
                'tables' => array(),
            ),
            'master_periodic' => array(
                'title' => 'Master / Periodik',
                'description' => 'Tabel referensi dan pengaturan yang berubah saat setup, audit, atau perubahan kebijakan.',
                'tables' => array(),
            ),
            'other' => array(
                'title' => 'Tabel Lainnya',
                'description' => 'Tabel tambahan di luar dua kelompok utama, tetap bisa dipilih manual bila dibutuhkan.',
                'tables' => array(),
            ),
        );

        $dailyMap = array_flip($this->resolveExistingTables($this->backupProfiles['operational_daily']['tables']));
        $periodicMap = array_flip($this->resolveExistingTables($this->backupProfiles['master_periodic']['tables']));

        foreach ($allTables as $table) {
            if (isset($dailyMap[$table])) {
                $groups['operational_daily']['tables'][] = $table;
            } elseif (isset($periodicMap[$table])) {
                $groups['master_periodic']['tables'][] = $table;
            } else {
                $groups['other']['tables'][] = $table;
            }
        }

        return $groups;
    }

    private function resolveExistingTables($tables)
    {
        $validTables = array();
        foreach ($tables as $table) {
            if ($this->db->table_exists($table)) {
                $validTables[] = $table;
            }
        }

        return array_values(array_unique($validTables));
    }

    private function runProfileBackup($profileKey, $overrideMode = null)
    {
        try {
            if (!isset($this->backupProfiles[$profileKey])) {
                throw new Exception('Profil backup tidak ditemukan.');
            }

            $profile = $this->backupProfiles[$profileKey];
            $validTables = $this->resolveExistingTables($profile['tables']);

            if (empty($validTables)) {
                throw new Exception('Tidak ada tabel yang cocok untuk profil ini.');
            }

            $mode = $overrideMode ?: $profile['mode'];
            $result = $this->_execute_backup($validTables, $profile['prefix'], $mode);

            if ($mode === 'save') {
                $this->session->set_flashdata('success', sprintf($profile['success_message'], $result));
                redirect('backup');
                return;
            }

        } catch (Exception $e) {
            $profile = isset($this->backupProfiles[$profileKey]) ? $this->backupProfiles[$profileKey] : null;
            $message = $profile ? sprintf($profile['error_message'], $e->getMessage()) : $e->getMessage();
            $this->session->set_flashdata('error', $message);
            redirect('backup');
        }
    }

    /**
     * Utility: Ambil daftar file backup yang ada di server
     */
    private function getExistingBackups()
    {
        $files = array();
        if (is_dir($this->backupDirectory)) {
            $dirFiles = scandir($this->backupDirectory);
            foreach ($dirFiles as $file) {
                if ($file === '.' || $file === '..' || $file === '.htaccess')
                    continue;

                $filePath = $this->backupDirectory . $file;
                if (file_exists($filePath)) {
                    $files[] = array(
                        'name' => $file,
                        'size' => filesize($filePath),
                        'mtime' => filemtime($filePath),
                    );
                }
            }
            // Urutkan berdasarkan waktu (terbaru di atas)
            usort($files, function ($a, $b) {
                return $b['mtime'] <=> $a['mtime'];
            });
        }
        return $files;
    }

    /**
     * Download file backup yang ada di server
     */
    /**
     * DATABASE LIFECYCLE MANAGEMENT (Cleanup & Optimize)
     * Best practice for high-intensity ERP performance.
     */
    public function cleanup()
    {
        set_time_limit(0);
        $report = array();

        // 1. DAILY CLEANING: Hapus transaksi temp yang SPK-nya sudah > 1 hari
        // tbl_temp_transaksi tidak punya kolom tanggal sendiri, jadi kita join ke tbl_spk
        $this->db->query("DELETE FROM tbl_temp_transaksi WHERE no_spk IN (SELECT nospk FROM tbl_spk WHERE spk_time < DATE_SUB(NOW(), INTERVAL 1 DAY))");
        $report[] = "Pembersihan Transaksi Temp Terbengkalai Selesai.";

        // 2. WEEKLY CLEANING: Hapus Draft SPK yang tidak diproses > 7 Hari
        $this->db->where('spk_time <', date('Y-m-d H:i:s', strtotime('-7 days')));
        $this->db->delete('tbl_spk');
        $report[] = "Pembersihan Draft SPK (>7 Hari) Berhasil: " . $this->db->affected_rows() . " baris.";

        // 3. LOG CLEANING (Best Practice: Keep logs for 30-60 days)
        // Hapus log login > 60 hari
        if ($this->db->table_exists('user_log')) {
            $this->db->where('login_time <', date('Y-m-d H:i:s', strtotime('-60 days')));
            $this->db->delete('user_log');
            $report[] = "Pembersihan User Login Log (>60 Hari): " . $this->db->affected_rows() . " baris.";
        }

        // Hapus log akses menu > 30 hari (High volume table)
        if ($this->db->table_exists('user_access_menu')) {
            $this->db->where('access_time <', date('Y-m-d H:i:s', strtotime('-30 days')));
            $this->db->delete('user_access_menu');
            $report[] = "Pembersihan Log Akses Menu (>30 Hari): " . $this->db->affected_rows() . " baris.";
        }

        // Hapus notifikasi lama > 30 hari
        if ($this->db->table_exists('inbox')) {
            $this->db->where('inbox_date <', date('Y-m-d H:i:s', strtotime('-30 days')));
            $this->db->delete('inbox');
            $report[] = "Pembersihan Notifikasi Lama (>30 Hari): " . $this->db->affected_rows() . " baris.";
        }

        // 4. SESSION CLEANING (Jika menggunakan database session)
        if ($this->db->table_exists('ci_sessions')) {
            $this->db->where('timestamp <', time() - 86400 * 7); // Hapus session > 7 hari
            $this->db->delete('ci_sessions');
            $report[] = "Pembersihan Session Expired Berhasil.";
        }

        // 5. DATABASE OPTIMIZATION (Reclaim space & Defrag)
        // Fokus optimasi pada tabel-tabel transaksi besar
        $heavyTables = ['tbl_transaksi', 'tbl_transaksi_detail', 'tbl_stok_log', 'user_access_menu', 'user_log'];
        foreach ($heavyTables as $table) {
            if ($this->db->table_exists($table)) {
                $this->db->query("OPTIMIZE TABLE " . $table);
            }
        }
        $report[] = "Optimasi struktur tabel transaksi (Defragmentation) Selesai.";

        $this->session->set_flashdata('success', implode('<br>', $report));
        redirect('backup');
    }

    public function download_stored($file)
    {
        $path = $this->backupDirectory . basename($file);
        if (file_exists($path)) {
            // Gunakan streaming untuk menghindari Error Memory Limit
            force_download($path, NULL);
        } else {
            $this->session->set_flashdata('error', 'File tidak ditemukan.');
            redirect('backup');
        }
    }

    /**
     * Hapus file backup di server
     */
    public function delete($file)
    {
        $path = $this->backupDirectory . basename($file);
        if (file_exists($path)) {
            unlink($path);
            $this->session->set_flashdata('success', 'File backup berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus: File tidak ditemukan.');
        }
        redirect('backup');
    }

    /**
     * Restore database dari file SQL/ZIP
     */
    public function restore()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        if (empty($_FILES['restore_file']['name'])) {
            $this->session->set_flashdata('error', 'Silakan pilih file untuk direstore.');
            redirect('backup');
        }

        $file = $_FILES['restore_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $allowed_exts = ['sql', 'zip', 'gz', 'gzip'];
        if (!in_array($ext, $allowed_exts)) {
            $this->session->set_flashdata('error', 'Format file tidak didukung. Gunakan file .sql, .zip, atau .gz');
            redirect('backup');
        }

        // Pindahkan ke folder temp dengan ekstensi .tmp (AMAN DARI RCE)
        $targetPath = $this->backupDirectory . 'restore_temp_' . time() . '.tmp';
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $this->session->set_flashdata('error', 'Gagal mengupload file.');
            redirect('backup');
        }

        $sqlContent = '';
        try {
            if ($ext === 'sql') {
                $sqlContent = file_get_contents($targetPath);
            } elseif ($ext === 'zip') {
                $zip = new ZipArchive;
                if ($zip->open($targetPath) === TRUE) {
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $zipEntry = $zip->getNameIndex($i);
                        if (strtolower(pathinfo($zipEntry, PATHINFO_EXTENSION)) === 'sql') {
                            $sqlContent = $zip->getFromIndex($i);
                            break;
                        }
                    }
                    $zip->close();
                } else {
                    throw new Exception("Gagal mengekstrak file ZIP.");
                }
            } elseif ($ext === 'gz' || $ext === 'gzip') {
                $gzipContent = file_get_contents($targetPath);
                $sqlContent = gzdecode($gzipContent);

                if ($sqlContent === false) {
                    throw new Exception("Gagal membaca file GZIP.");
                }
            } else {
                throw new Exception("Format file tidak didukung. Gunakan file .sql, .zip, atau .gz");
            }

            if (empty($sqlContent))
                throw new Exception("Isi file SQL kosong.");

            // Eksekusi SQL (Via MySQL CLI untuk performa data besar)
            $tempSqlFile = $this->backupDirectory . 'exec_' . time() . '.sql';

            // Disable Foreign Key & Strict Mode (Untuk mencegah Error 1265 Data Truncated)
            $sqlContent = "SET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE = '';\nSET NAMES utf8mb4;\n" . $sqlContent . "\nSET FOREIGN_KEY_CHECKS=1;";
            write_file($tempSqlFile, $sqlContent);

            $mysqlPath = '/Applications/XAMPP/xamppfiles/bin/mysql';
            if (!file_exists($mysqlPath))
                $mysqlPath = 'mysql';

            $command = sprintf(
                '%s -h %s -u %s %s %s < %s 2>&1',
                $mysqlPath,
                escapeshellarg($this->db->hostname),
                escapeshellarg($this->db->username),
                (!empty($this->db->password) ? '-p' . escapeshellarg($this->db->password) : ''),
                escapeshellarg($this->db->database),
                escapeshellarg($tempSqlFile)
            );

            exec($command, $output, $result);

            @unlink($targetPath);
            @unlink($tempSqlFile);

            if ($result !== 0) {
                throw new Exception("MySQL Error: " . implode("\n", $output));
            }

            $this->session->set_flashdata('success', 'Database berhasil direstore sepenuhnya.');
        } catch (Exception $e) {
            @unlink($targetPath);
            $this->session->set_flashdata('error', 'Gagal: ' . $e->getMessage());
        }
        redirect('backup');
    }
}
