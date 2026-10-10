<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Complaint_processor
{
    public function run($limit = 50, $company = NULL, $use_ai = FALSE)
    {
        $ci = get_instance();
        $ci->config->load('complaint');
        $ci->load->model('Complaint_model');
        $ci->load->library('Complaint_analyzer');
        if (!$ci->Complaint_model->ready()) { throw new RuntimeException('Pasang database/complaint_center.sql terlebih dahulu.'); }
        $options = array('enabled' => $use_ai && $ci->config->item('complaint_ai_enabled'),
            'key' => $ci->config->item('complaint_ai_key'), 'model' => $ci->config->item('complaint_ai_model'),
            'timeout' => $ci->config->item('complaint_ai_timeout'));
        $result = array('processed' => 0, 'failed' => 0);
        $started = time();
        for ($i = 0; $i < min(500, max(1, (int) $limit)); $i++) {
            // Web batches never call an external model; CLI workers may use AI.
            if (!$use_ai && time() - $started >= 15) { break; }
            $message = $ci->Complaint_model->claim($company);
            if (!$message) { break; }
            try {
                $analysis = $ci->complaint_analyzer->analyze($message['body'], $options);
                if ($ci->Complaint_model->complete_analysis($message, $analysis)) { $result['processed']++; }
            } catch (Throwable $e) {
                $ci->Complaint_model->fail_analysis($message);
                $result['failed']++;
                log_message('error', 'Complaint processing failed for message ID '.(int) $message['id']);
            }
        }
        return $result;
    }
}
