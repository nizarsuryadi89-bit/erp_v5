<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Restore extends MY_Controller
{

    private $backupDirectory;
    private $tempDirectory;

    public function __construct()
    {
        parent::__construct();

        $this->load->dbutil();
        $this->load->helper(array('file', 'download', 'restore'));
        $this->load->library('zip');

        // Default backup directory
        $this->backupDirectory = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR;
        $this->tempDirectory = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

        // Create directories if they don't exist
        if (!is_dir($this->backupDirectory)) {
            @mkdir($this->backupDirectory, 0755, true);
        }
        if (!is_dir($this->tempDirectory)) {
            @mkdir($this->tempDirectory, 0755, true);
        }
    }

    /**
     * Show restore UI with backup file selection and table analysis
     */
    public function index()
    {
        $existingBackups = $this->getExistingBackups();
        $currentTables = $this->db->list_tables();

        $data = array(
            'title' => 'Database Restore',
            'function' => 'index',
            'existing_backups' => $existingBackups,
            'current_tables' => $currentTables,
        );

        $this->load_admin_view('admin/restore/index', $data);
    }

    /**
     * Analyze backup file and show available tables for restore
     */
    public function analyze($filename = null)
    {
        if (!$filename) {
            $this->session->set_flashdata('error', 'No backup file specified.');
            redirect('restore');
        }

        $safeFile = basename($filename);
        $backupPath = $this->backupDirectory . $safeFile;

        if (!file_exists($backupPath)) {
            $this->session->set_flashdata('error', 'Backup file not found.');
            redirect('restore');
        }

        try {
            $tables = $this->extractTablesFromBackup($backupPath);
            $currentTables = $this->db->list_tables();

            $data = array(
                'title' => 'Restore Analysis',
                'function' => 'analyze',
                'backup_file' => $safeFile,
                'backup_path' => $backupPath,
                'available_tables' => $tables,
                'current_tables' => $currentTables,
                'backup_info' => $this->getBackupInfo($backupPath),
            );

            $this->load_admin_view('admin/restore/analyze', $data);

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Error analyzing backup: ' . $e->getMessage());
            redirect('restore');
        }
    }

    /**
     * Process restore request
     */
    public function process()
    {
        $backupFile = $this->input->post('backup_file', true);
        $selectedTables = $this->input->post('tables', true);
        $restoreMode = $this->input->post('restore_mode', true); // 'full' or 'tables'
        $backupMode = $this->input->post('backup_mode', true); // 'before_restore' or 'no_backup'

        if (!$backupFile) {
            $this->session->set_flashdata('error', 'No backup file specified.');
            redirect('restore');
        }

        $safeFile = basename($backupFile);
        $backupPath = $this->backupDirectory . $safeFile;

        if (!file_exists($backupPath)) {
            $this->session->set_flashdata('error', 'Backup file not found.');
            redirect('restore');
        }

        try {
            // Create backup before restore if requested
            if ($backupMode === 'before_restore') {
                $preBackupFile = $this->createPreRestoreBackup($selectedTables);
                $this->session->set_flashdata('info', 'Pre-restore backup created: ' . $preBackupFile);
            }

            if ($restoreMode === 'full') {
                $this->restoreFullDatabase($backupPath);
            } else {
                $this->restoreSelectedTables($backupPath, $selectedTables);
            }

            $this->session->set_flashdata('success', 'Database restore completed successfully.');
            redirect('restore');

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Restore failed: ' . $e->getMessage());
            redirect('restore');
        }
    }

    /**
     * Restore full database
     */
    private function restoreFullDatabase($backupPath)
    {
        $sqlContent = $this->extractSqlFromBackup($backupPath);

        if (empty($sqlContent)) {
            throw new Exception('No SQL content found in backup file.');
        }

        // Split SQL into individual statements
        $statements = $this->splitSqlStatements($sqlContent);

        // Execute each statement
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $result = $this->db->query($statement);
                    if ($result === FALSE) {
                        $error = $this->db->error();
                        throw new Exception('Error executing SQL: ' . $error['message']);
                    }
                } catch (Exception $e) {
                    // Log the error but continue with other statements
                    log_message('error', 'SQL Error: ' . $e->getMessage() . ' Statement: ' . substr($statement, 0, 100));
                }
            }
        }
    }

    /**
     * Restore selected tables only
     */
    private function restoreSelectedTables($backupPath, $selectedTables)
    {
        if (empty($selectedTables) || !is_array($selectedTables)) {
            throw new Exception('No tables selected for restore.');
        }

        $sqlContent = $this->extractSqlFromBackup($backupPath);

        if (empty($sqlContent)) {
            throw new Exception('No SQL content found in backup file.');
        }

        // Split SQL into individual statements
        $statements = $this->splitSqlStatements($sqlContent);

        // Filter statements for selected tables
        $filteredStatements = $this->filterStatementsForTables($statements, $selectedTables);

        if (empty($filteredStatements)) {
            throw new Exception('No SQL statements found for selected tables.');
        }

        // Execute filtered statements
        foreach ($filteredStatements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $result = $this->db->query($statement);
                    if ($result === FALSE) {
                        $error = $this->db->error();
                        throw new Exception('Error executing SQL: ' . $error['message']);
                    }
                } catch (Exception $e) {
                    // Log the error but continue with other statements
                    log_message('error', 'SQL Error: ' . $e->getMessage() . ' Statement: ' . substr($statement, 0, 100));
                }
            }
        }
    }

    /**
     * Extract SQL content from backup file
     */
    private function extractSqlFromBackup($backupPath)
    {
        $ext = strtolower(pathinfo($backupPath, PATHINFO_EXTENSION));

        if ($ext === 'zip') {
            return $this->extractFromZip($backupPath);
        } elseif ($ext === 'gz' || $ext === 'gzip') {
            return $this->extractFromGzip($backupPath);
        } else {
            return file_get_contents($backupPath);
        }
    }

    /**
     * Extract from ZIP file
     */
    private function extractFromZip($zipPath)
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception('ZipArchive extension is not enabled on this server.');
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            $sqlContent = null;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (pathinfo($filename, PATHINFO_EXTENSION) === 'sql') {
                    $sqlContent = $zip->getFromIndex($i);
                    break;
                }
            }
            $zip->close();

            if ($sqlContent !== null) {
                return $sqlContent;
            }
        }

        throw new Exception('No SQL file found in ZIP archive or failed to open ZIP.');
    }

    /**
     * Extract from GZIP file
     */
    private function extractFromGzip($gzipPath)
    {
        $content = file_get_contents($gzipPath);
        $decoded = gzdecode($content);

        if ($decoded === false) {
            throw new Exception('Failed to decode GZIP file.');
        }

        return $decoded;
    }

    /**
     * Extract table names from backup file
     */
    private function extractTablesFromBackup($backupPath)
    {
        $sqlContent = $this->extractSqlFromBackup($backupPath);
        $tables = array();

        // Look for CREATE TABLE statements with various patterns
        $patterns = array(
            '/CREATE TABLE\s+`?(\w+)`?\s*\(/i',
            '/CREATE TABLE\s+`?(\w+)`?\s*\(/i',
            '/DROP TABLE\s+`?(\w+)`?/i',
            '/INSERT INTO\s+`?(\w+)`?\s*\(/i'
        );

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $sqlContent, $matches);
            if (isset($matches[1])) {
                $tables = array_merge($tables, $matches[1]);
            }
        }

        return array_unique($tables);
    }

    /**
     * Split SQL content into individual statements
     */
    private function splitSqlStatements($sqlContent)
    {
        // Remove comments
        $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);
        $sqlContent = preg_replace('/--.*$/m', '', $sqlContent);

        // Handle multi-line statements properly
        $statements = array();
        $currentStatement = '';
        $inString = false;
        $stringChar = '';

        for ($i = 0; $i < strlen($sqlContent); $i++) {
            $char = $sqlContent[$i];

            // Handle string literals
            if (($char === "'" || $char === '"') && ($i === 0 || $sqlContent[$i - 1] !== '\\')) {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === $stringChar) {
                    $inString = false;
                }
            }

            // Only split on semicolon if not inside a string
            if ($char === ';' && !$inString) {
                $currentStatement .= $char;
                $statements[] = trim($currentStatement);
                $currentStatement = '';
            } else {
                $currentStatement .= $char;
            }
        }

        // Add the last statement if it exists
        if (!empty(trim($currentStatement))) {
            $statements[] = trim($currentStatement);
        }

        return array_filter($statements, function ($stmt) {
            return !empty(trim($stmt)) && strlen(trim($stmt)) > 5;
        });
    }

    /**
     * Filter SQL statements for specific tables
     */
    private function filterStatementsForTables($statements, $selectedTables)
    {
        $filtered = array();

        foreach ($statements as $statement) {
            $includeStatement = false;

            foreach ($selectedTables as $table) {
                // Check if statement contains the table name (case insensitive)
                if (preg_match('/\b' . preg_quote($table, '/') . '\b/i', $statement)) {
                    $includeStatement = true;
                    break;
                }
            }

            if ($includeStatement) {
                $filtered[] = $statement;
            }
        }

        return $filtered;
    }

    /**
     * Create backup before restore
     */
    private function createPreRestoreBackup($tables = null)
    {
        $timestamp = date('Ymd_His');
        $filename = 'pre_restore_backup_' . $timestamp . '.sql';

        if (empty($tables)) {
            $tables = $this->db->list_tables();
        }

        $prefs = array(
            'tables' => $tables,
            'ignore' => array(),
            'format' => 'txt',
            'filename' => $filename,
            'add_drop' => true,
            'add_insert' => true,
            'newline' => "\n"
        );

        $backupData = $this->dbutil->backup($prefs);

        if ($backupData === FALSE) {
            throw new Exception('Failed to create pre-restore backup.');
        }

        $targetPath = $this->backupDirectory . $filename;
        if (!write_file($targetPath, $backupData)) {
            throw new Exception('Failed to save pre-restore backup.');
        }

        return $filename;
    }

    /**
     * Get backup file information
     */
    private function getBackupInfo($backupPath)
    {
        $info = array(
            'filename' => basename($backupPath),
            'size' => filesize($backupPath),
            'modified' => filemtime($backupPath),
            'type' => pathinfo($backupPath, PATHINFO_EXTENSION),
        );

        return $info;
    }

    /**
     * Helper: get list of existing backups
     */
    private function getExistingBackups()
    {
        $files = array();
        if (!is_dir($this->backupDirectory)) {
            return $files;
        }

        $dirFiles = scandir($this->backupDirectory);
        if ($dirFiles === false) {
            return $files;
        }

        foreach ($dirFiles as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array(strtolower($ext), array('zip', 'gz', 'gzip', 'sql'))) {
                $files[] = array(
                    'name' => $file,
                    'size' => filesize($this->backupDirectory . $file),
                    'mtime' => filemtime($this->backupDirectory . $file),
                );
            }
        }

        // Sort by modified time desc
        usort($files, function ($a, $b) {
            return $b['mtime'] <=> $a['mtime'];
        });

        return $files;
    }

    /**
     * AJAX endpoint to get table structure
     */
    public function get_table_structure()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $tableName = $this->input->post('table', true);
        if (!$tableName) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['error' => 'Table name required']));
            return;
        }

        try {
            $fields = $this->db->field_data($tableName);
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true, 'fields' => $fields]));
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['error' => $e->getMessage()]));
        }
    }

    /**
     * AJAX endpoint to preview backup content
     */
    public function preview_backup()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $filename = $this->input->post('filename', true);
        if (!$filename) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['error' => 'Filename required']));
            return;
        }

        try {
            $safeFile = basename($filename);
            $backupPath = $this->backupDirectory . $safeFile;

            if (!file_exists($backupPath)) {
                throw new Exception('Backup file not found.');
            }

            $sqlContent = $this->extractSqlFromBackup($backupPath);
            $preview = substr($sqlContent, 0, 2000) . (strlen($sqlContent) > 2000 ? '...' : '');

            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => true,
                'preview' => $preview,
                'total_length' => strlen($sqlContent)
            ]));
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['error' => $e->getMessage()]));
        }
    }

    /**
     * Test restore functionality
     */
    public function test()
    {
        try {
            $backupFiles = $this->getExistingBackups();
            if (empty($backupFiles)) {
                echo "No backup files found.<br>";
                return;
            }

            $testFile = $backupFiles[0]['name'];
            $backupPath = $this->backupDirectory . $testFile;

            echo "Testing backup file: " . $testFile . "<br>";

            // Test SQL extraction
            $sqlContent = $this->extractSqlFromBackup($backupPath);
            echo "SQL Content length: " . strlen($sqlContent) . "<br>";

            // Test table extraction
            $tables = $this->extractTablesFromBackup($backupPath);
            echo "Found tables: " . implode(', ', $tables) . "<br>";

            // Test SQL splitting
            $statements = $this->splitSqlStatements($sqlContent);
            echo "SQL Statements count: " . count($statements) . "<br>";

            echo "Test completed successfully!<br>";

        } catch (Exception $e) {
            echo "Test failed: " . $e->getMessage() . "<br>";
        }
    }
}
