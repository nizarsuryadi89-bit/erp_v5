<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tracking extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Produksi_model');
    }

    // Default redirect to tracking SPK
    public function index()
    {
        redirect('tracking/spk');
    }

    // Tracking SPK untuk semua orang (public access)
    public function spk()
    {
        // Get SPK from URL parameter or search
        $spk = $this->input->get('spk', true) ?: $this->input->post('spk', true);
        
        if ($spk) {
            // Search by SPK number
            $data['produksi'] = $this->Produksi_model->get_produksi_by_spk($spk);
            if ($data['produksi']) {
                $data['detail_produksi'] = $this->Produksi_model->get_detail_produksi_by_id($data['produksi']->produksi_id);
                $data['spk_found'] = true;
            } else {
                $data['spk_found'] = false;
                $data['error_message'] = 'SPK #' . $spk . ' tidak ditemukan.';
            }
        } else {
            $data['spk_found'] = false;
        }
        
        $data['search_spk'] = $spk;
        
        // Load public view (no admin layout)
        $this->load->view('public/spk_tracking', $data);
    }

    // API endpoint untuk tracking SPK (AJAX)
    public function get_spk_status()
    {
        $spk = $this->input->post('spk', true);
        
        if (!$spk) {
            $response = array('success' => false, 'message' => 'SPK number is required');
        } else {
            $produksi = $this->Produksi_model->get_produksi_by_spk($spk);
            
            if ($produksi) {
                $detail_produksi = $this->Produksi_model->get_detail_produksi_by_id($produksi->produksi_id);
                
                // Calculate progress
                $total_routing = count($detail_produksi);
                $completed = 0;
                $in_progress = 0;
                $in_pb = 0;
                $delivered = 0;
                
                foreach ($detail_produksi as $d) {
                    $status = isset($d->status_produksi) ? (string)$d->status_produksi : '0';
                    if ($status === '2' || $status === '3' || $status === '4') $completed++;
                    if ($status === '1') $in_progress++;
                    if ($status === '3') $in_pb++;
                    if ($status === '4') $delivered++;
                }
                
                $overall_progress = $total_routing > 0 ? round(($completed / $total_routing) * 100) : 0;
                
                $response = array(
                    'success' => true,
                    'data' => array(
                        'produksi' => $produksi,
                        'detail_produksi' => $detail_produksi,
                        'progress' => array(
                            'total_routing' => $total_routing,
                            'completed' => $completed,
                            'in_progress' => $in_progress,
                            'in_pb' => $in_pb,
                            'delivered' => $delivered,
                            'overall_progress' => $overall_progress
                        )
                    )
                );
            } else {
                $response = array('success' => false, 'message' => 'SPK #' . $spk . ' tidak ditemukan');
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
