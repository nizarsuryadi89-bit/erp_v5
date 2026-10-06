<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Complaint_webhook extends CI_Controller
{
    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');
        if ($this->input->method() !== 'post') { return $this->reply(405, array('error' => 'POST required')); }
        $this->config->load('complaint');
        $this->load->library('Complaint_webhook_verifier');
        $this->load->model('Complaint_model');
        if (!$this->Complaint_model->ready()) { return $this->reply(503, array('error' => 'Module unavailable')); }
        if ((int) $this->input->server('CONTENT_LENGTH') > 65536) { return $this->reply(413, array('error' => 'Payload too large')); }
        try {
            $m = $this->complaint_webhook_verifier->verify($this->input->raw_input_stream,
                $this->input->get_request_header('X-TGM-Source'), $this->input->get_request_header('X-TGM-Timestamp'),
                $this->input->get_request_header('X-TGM-Signature'), $this->config->item('complaint_webhook_sources'));
        } catch (Throwable $e) { return $this->reply(400, array('error' => 'Invalid webhook')); }
        try {
            $added = $this->Complaint_model->ingest($m['company'], 'webhook', $m['key'], $m['conversation'], $m['sender'], $m['sent_at'], $m['body']);
            return $this->reply(202, array('accepted' => TRUE, 'duplicate' => !$added));
        } catch (Throwable $e) {
            log_message('error', 'Complaint webhook storage failed.');
            return $this->reply(503, array('error' => 'Retry later'));
        }
    }

    private function reply($status, $payload)
    {
        return $this->output->set_status_header($status)->set_content_type('application/json', 'UTF-8')
            ->set_header('Cache-Control: no-store')->set_output(json_encode($payload));
    }
}
