<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ErrorLog extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('file');
        $this->load->library('pagination');
    }

    /**
     * Display error logs with filtering and pagination
     */
    public function index()
    {
        $data = array_merge($this->data, array(
            'title' => 'Error Logs',
            'function' => 'index'
        ));

        // Get filter parameters
        $date_filter = $this->input->get('date', true);
        $severity_filter = $this->input->get('severity', true);
        $search = $this->input->get('search', true);

        // Get log files
        $log_files = $this->get_log_files();
        $logs = array();

        foreach ($log_files as $file) {
            $file_logs = $this->parse_log_file($file);

            // Apply filters
            if ($date_filter) {
                $file_logs = $this->filter_by_date($file_logs, $date_filter);
            }

            if ($severity_filter) {
                $file_logs = $this->filter_by_severity($file_logs, $severity_filter);
            }

            if ($search) {
                $file_logs = $this->filter_by_search($file_logs, $search);
            }

            $logs = array_merge($logs, $file_logs);
        }

        // Sort logs by date (newest first)
        usort($logs, function ($a, $b) {
            return strtotime($b['datetime']) - strtotime($a['datetime']);
        });

        // Pagination
        $per_page = 50;
        $page = $this->input->get('page', true) ? $this->input->get('page', true) : 1;
        $offset = ($page - 1) * $per_page;

        $total_logs = count($logs);
        $paginated_logs = array_slice($logs, $offset, $per_page);

        // Pagination configuration
        $config['base_url'] = base_url('errorlog/index');
        $config['total_rows'] = $total_logs;
        $config['per_page'] = $per_page;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;

        // Pagination styling
        $config['full_tag_open'] = '<ul class="pagination pagination-sm justify-content-center">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['anchor_class'] = 'page-link';

        $this->pagination->initialize($config);

        $data['logs'] = $paginated_logs;
        $data['total_logs'] = $total_logs;
        $data['pagination'] = $this->pagination->create_links();
        $data['filters'] = array(
            'date' => $date_filter,
            'severity' => $severity_filter,
            'search' => $search
        );

        $this->load_admin_view('admin/errorlog/index', $data);
    }

    /**
     * View detailed error log
     */
    public function view($filename = null)
    {
        if (!$filename) {
            redirect('errorlog');
        }

        $log_file = APPPATH . 'logs/' . $filename;

        if (!file_exists($log_file)) {
            $this->session->set_flashdata('error', 'Log file not found.');
            redirect('errorlog');
        }

        $logs = $this->parse_log_file($filename);

        $data = array_merge($this->data, array(
            'title' => 'Error Log Details - ' . $filename,
            'function' => 'view',
            'filename' => $filename,
            'logs' => $logs,
            'total_errors' => count($logs)
        ));

        $this->load_admin_view('admin/errorlog/view', $data);
    }

    /**
     * Download log file
     */
    public function download($filename = null)
    {
        if (!$filename) {
            redirect('errorlog');
        }

        $log_file = APPPATH . 'logs/' . $filename;

        if (!file_exists($log_file)) {
            $this->session->set_flashdata('error', 'Log file not found.');
            redirect('errorlog');
        }

        $this->load->helper('download');
        force_download($filename, file_get_contents($log_file));
    }

    /**
     * Clear log files
     */
    public function clear()
    {
        if ($this->input->post('confirm', true) === 'yes') {
            $log_files = $this->get_log_files();
            $cleared_count = 0;

            foreach ($log_files as $file) {
                $log_file = APPPATH . 'logs/' . $file;
                if (file_exists($log_file) && unlink($log_file)) {
                    $cleared_count++;
                }
            }

            $this->session->set_flashdata('success', $cleared_count . ' log files cleared successfully.');
        } else {
            $this->session->set_flashdata('error', 'Please confirm to clear log files.');
        }

        redirect('ErrorLog');
    }

    /**
     * Get all log files
     */
    private function get_log_files()
    {
        $log_dir = APPPATH . 'logs/';
        $files = array();

        if (!is_dir($log_dir)) {
            return $files;
        }

        $log_files = scandir($log_dir);
        if ($log_files === false) {
            return $files;
        }

        foreach ($log_files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php' && $file !== 'index.html' && $file !== '.' && $file !== '..') {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Parse log file and extract error information
     */
    private function parse_log_file($filename)
    {
        $log_file = APPPATH . 'logs/' . $filename;
        $logs = array();

        if (!file_exists($log_file)) {
            return $logs;
        }

        if (!is_readable($log_file)) {
            return $logs;
        }

        $content = file_get_contents($log_file);
        if ($content === false) {
            return $logs;
        }

        $lines = explode("\n", $content);
        $current_log = null;

        foreach ($lines as $line) {
            // Skip the PHP safety header
            if (strpos($line, '<?php') !== false) {
                continue;
            }

            // Match ERROR lines (with or without Severity header)
            // Format 1: ERROR - 2026-02-13 08:31:01 --> Message
            // Format 2: ERROR - 2026-02-13 08:31:01 --> Severity: error --> Message
            if (preg_match('/^ERROR - (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) --> (?:Severity: (\w+) --> )?(.+)$/', $line, $matches)) {
                if ($current_log) {
                    $logs[] = $current_log;
                }

                $analysis = $this->_translate_error($matches[3]);
                $current_log = array(
                    'datetime' => $matches[1],
                    'severity' => (isset($matches[2]) && !empty($matches[2])) ? $matches[2] : 'Error',
                    'message' => $matches[3],
                    'filename' => $filename,
                    'full_line' => $line,
                    'indo_message' => $analysis['message'],
                    'simple_level' => $analysis['level'],
                    'solution' => $analysis['solution']
                );
            } elseif ($current_log && !empty(trim($line))) {
                // If the line doesn't start with ERROR -, it's likely a continuation (stack trace or multiline query)
                $current_log['message'] .= "\n" . $line;
                $current_log['full_line'] .= "\n" . $line;

                // Update analysis for longer messages
                $analysis = $this->_translate_error($current_log['message']);
                $current_log['indo_message'] = $analysis['message'];
                $current_log['simple_level'] = $analysis['level'];
                $current_log['solution'] = $analysis['solution'];
            }
        }

        if ($current_log) {
            $logs[] = $current_log;
        }

        return $logs;
    }

    /**
     * Filter logs by date
     */
    private function filter_by_date($logs, $date)
    {
        $filtered = array();
        $filter_date = date('Y-m-d', strtotime($date));

        foreach ($logs as $log) {
            $log_date = date('Y-m-d', strtotime($log['datetime']));
            if ($log_date === $filter_date) {
                $filtered[] = $log;
            }
        }

        return $filtered;
    }

    /**
     * Filter logs by severity
     */
    private function filter_by_severity($logs, $severity)
    {
        $filtered = array();

        foreach ($logs as $log) {
            if (strtolower($log['severity']) === strtolower($severity)) {
                $filtered[] = $log;
            }
        }

        return $filtered;
    }

    /**
     * Filter logs by search term
     */
    private function filter_by_search($logs, $search)
    {
        $filtered = array();
        $search = strtolower($search);

        foreach ($logs as $log) {
            if (
                strpos(strtolower($log['message']), $search) !== false ||
                strpos(strtolower($log['severity']), $search) !== false
            ) {
                $filtered[] = $log;
            }
        }

        return $filtered;
    }

    /**
     * Get error statistics
     */
    public function statistics()
    {
        $log_files = $this->get_log_files();
        $stats = array(
            'total_errors' => 0,
            'errors_by_severity' => array(),
            'errors_by_date' => array(),
            'recent_errors' => array()
        );

        foreach ($log_files as $file) {
            $logs = $this->parse_log_file($file);

            foreach ($logs as $log) {
                $stats['total_errors']++;

                // Count by severity
                $severity = $log['severity'];
                if (!isset($stats['errors_by_severity'][$severity])) {
                    $stats['errors_by_severity'][$severity] = 0;
                }
                $stats['errors_by_severity'][$severity]++;

                // Count by date
                $date = date('Y-m-d', strtotime($log['datetime']));
                if (!isset($stats['errors_by_date'][$date])) {
                    $stats['errors_by_date'][$date] = 0;
                }
                $stats['errors_by_date'][$date]++;

                // Recent errors (last 7 days)
                $log_time = strtotime($log['datetime']);
                if ($log_time > strtotime('-7 days')) {
                    $stats['recent_errors'][] = $log;
                }
            }
        }

        $data = array_merge($this->data, array(
            'title' => 'Error Log Statistics',
            'function' => 'statistics',
            'stats' => $stats
        ));

        $this->load_admin_view('admin/errorlog/statistics', $data);
    }

    /**
     * Translate technical logs to user-friendly Indonesian and categorize severity
     */
    private function _translate_error($msg)
    {
        $res = [
            'message' => 'Masalah sistem tidak teridentifikasi secara spesifik.',
            'level' => 'INFO', // DEFAULT
            'solution' => 'Mohon hubungi tim teknis jika fitur terkait tidak berfungsi.'
        ];

        // 1. Critical Errors (System Break / Database)
        if (stripos($msg, 'Query error') !== false || stripos($msg, 'Database error') !== false) {
            $res['message'] = 'Gagal memproses data ke database. Kemungkinan struktur tabel tidak cocok atau database sibuk.';
            $res['level'] = 'KRITIS';
            $res['solution'] = 'Periksa apakah ada perubahan tabel terbaru atau kolom yang kurang.';
        } elseif (stripos($msg, 'Undefined variable') !== false || stripos($msg, 'Undefined index') !== false) {
            $res['message'] = 'Sistem mencoba memanggil data yang tidak ada atau belum terisi.';
            $res['level'] = 'RINGAN';
            $res['solution'] = 'Pastikan semua formulir diisi dengan lengkap sebelum disimpan.';
        } elseif (stripos($msg, 'syntax error') !== false || stripos($msg, 'Parse error') !== false) {
            $res['message'] = 'Terjadi kesalahan pengetikan pada struktur kode sistem.';
            $res['level'] = 'KRITIS';
            $res['solution'] = 'Segera lapor ke pengembang (Nizar) untuk perbaikan baris kode tersebut.';
        }

        // 2. Navigation / Access (Usually Minor)
        elseif (stripos($msg, '404 Page Not Found') !== false) {
            $res['message'] = 'User mencoba mengakses halaman yang tidak ada atau salah ketik URL.';
            $res['level'] = 'INFO';
            $res['solution'] = 'Abaikan jika hanya sekali, periksa rute (routes.php) jika terjadi berulang kali.';
        } elseif (stripos($msg, 'AJAX Toggle') !== false) {
            $res['message'] = 'Pencatatan perubahan hak akses (Informasi Umum).';
            $res['level'] = 'INFO';
            $res['solution'] = 'Abaikan, ini hanya laporan bahwa Anda baru saja mengubah pengaturan di Matriks Audit.';
        } elseif (stripos($msg, 'Unauthorized Access') !== false) {
            $res['message'] = 'Percobaan akses ke menu yang tidak diizinkan untuk level user tersebut.';
            $res['level'] = 'RINGAN';
            $res['solution'] = 'Periksa pengaturan Role-Based Access Control (Matriks Audit) jika user tersebut seharusnya memiliki akses.';
        }

        // 3. Framework Issues
        elseif (stripos($msg, 'Language line not found') !== false) {
            $res['message'] = 'Label bahasa sistem tidak ditemukan.';
            $res['level'] = 'INFO';
            $res['solution'] = 'Dapat diabaikan, hanya berarti ada teks yang belum diterjemahkan.';
        }

        return $res;
    }
}
