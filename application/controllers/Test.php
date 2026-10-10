<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
    public function excel() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'DATA KARYAWAN');
        
        $temp_file = FCPATH . 'assets/temp_export_' . time() . '.xlsx';
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($temp_file);
        
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="test_export.xlsx"');
        header('Cache-Control: max-age=0');
        header('Content-Length: ' . filesize($temp_file));
        
        readfile($temp_file);
        unlink($temp_file);
        exit;
    }
}
