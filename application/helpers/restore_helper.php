<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper function to format bytes
 */
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

/**
 * Helper function to get total backup size
 */
if (!function_exists('getTotalBackupSize')) {
    function getTotalBackupSize() {
        $backupDir = FCPATH . 'uploads/backup/';
        $totalSize = 0;
        
        if (is_dir($backupDir)) {
            $files = scandir($backupDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $backupDir . $file;
                    if (is_file($filePath)) {
                        $totalSize += filesize($filePath);
                    }
                }
            }
        }
        
        return $totalSize;
    }
}

/**
 * Helper function to check if backup file is valid
 */
if (!function_exists('isValidBackupFile')) {
    function isValidBackupFile($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, array('zip', 'gz', 'gzip', 'sql'));
    }
}

/**
 * Helper function to get backup file info
 */
if (!function_exists('getBackupFileInfo')) {
    function getBackupFileInfo($filename) {
        $backupDir = FCPATH . 'uploads/backup/';
        $filePath = $backupDir . $filename;
        
        if (!file_exists($filePath)) {
            return false;
        }
        
        return array(
            'filename' => $filename,
            'size' => filesize($filePath),
            'modified' => filemtime($filePath),
            'type' => pathinfo($filename, PATHINFO_EXTENSION),
        );
    }
}
