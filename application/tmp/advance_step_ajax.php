/**
* AJAX: Advance Production Step
* Handles 'Start' and 'Finish' actions for routing steps
*/
public function advance_step_ajax()
{
// Enforce JSON check or login check
if (!$this->input->is_ajax_request()) show_404();

$produksi_id = $this->input->post('produksi_id');
$operator_id = $this->session->userdata('karyawan_id') ?: 0; // Or get real operator ID

if (!$produksi_id) {
echo json_encode(['success' => false, 'message' => 'ID Produksi Invalid']);
return;
}

$this->db->trans_start();

// 1. Get current steps
$this->db->select('*');
$this->db->from('detail_produksi');
$this->db->where('produksi_id', $produksi_id);
$this->db->order_by('routing_urutan', 'ASC');
$steps = $this->db->get()->result();

if (empty($steps)) {
// Case: Single Step Item (No Routing)
// If user clicks start/finish on a single step item, just toggle main status
$this->db->where('produksi_id', $produksi_id);
$item = $this->db->get('tbl_produksi')->row();

if ($item) {
if ($item->status_produksi == 'pending' || $item->status_produksi == 'approved') {
$this->db->update('tbl_produksi', ['status_produksi' => 'proses', 'update_time' => date('Y-m-d H:i:s')]);
} elseif ($item->status_produksi == 'proses') {
$this->db->update('tbl_produksi', ['status_produksi' => 'done', 'update_time' => date('Y-m-d H:i:s')]);
// Only deduct stock on DONE for single step
// TODO: Deduct Stock Logic Here if needed
}
}
$this->db->trans_complete();
echo json_encode(['success' => true]);
return;
}

// 2. Logic for Multi-Step
$currentInProcess = null;
$nextPending = null;
$allDone = true;

foreach ($steps as $s) {
if ($s->status_produksi == '1') $currentInProcess = $s;
if ($s->status_produksi == '0' && !$nextPending) $nextPending = $s;
if ($s->status_produksi != '2') $allDone = false;
}

// ACTION: FINISH CURRENT STEP
if ($currentInProcess) {
$this->db->where('detail_produksi_id', $currentInProcess->detail_produksi_id);
$this->db->update('detail_produksi', [
'status_produksi' => '2',
'waktu_selesai' => date('Y-m-d H:i:s'),
'operator_id' => $operator_id // Record who finished it
]);

// Check if this was the last step
if (!$nextPending) {
// ALL DONE
$this->db->where('produksi_id', $produksi_id);
$this->db->update('tbl_produksi', [
'status_produksi' => 'done',
'update_time' => date('Y-m-d H:i:s')
]);
} else {
// Auto-Start Next Step? Or Leave it Pending?
// Request implied "Handover", so we leave next step as PENDING ('0')
// The next operator must click "Start"

// Update Main Status to 'proses' just in case
$this->db->where('produksi_id', $produksi_id);
$this->db->update('tbl_produksi', ['status_produksi' => 'proses', 'update_time' => date('Y-m-d H:i:s')]);
}

} else {
// ACTION: START NEXT PENDING STEP
if ($nextPending) {
$this->db->where('detail_produksi_id', $nextPending->detail_produksi_id);
$this->db->update('detail_produksi', [
'status_produksi' => '1',
'waktu_mulai' => date('Y-m-d H:i:s'),
'operator_id' => $operator_id
]);

// Ensure main status is 'proses'
$this->db->where('produksi_id', $produksi_id);
$this->db->update('tbl_produksi', ['status_produksi' => 'proses', 'update_time' => date('Y-m-d H:i:s')]);
}
}

$this->db->trans_complete();

if ($this->db->trans_status() === FALSE) {
echo json_encode(['success' => false, 'message' => 'Database Error']);
} else {
echo json_encode(['success' => true]);
}
}