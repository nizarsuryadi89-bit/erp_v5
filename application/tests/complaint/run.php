<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// No database, no API key, no network: php application/tests/complaint/run.php
define('BASEPATH', __DIR__.'/');
define('APPPATH', dirname(__DIR__, 3).'/application/');
date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) { throw new ErrorException($message, 0, $severity, $file, $line); });
require APPPATH.'libraries/Whatsapp_export_parser.php';
require APPPATH.'libraries/Complaint_analyzer.php';
require APPPATH.'libraries/Complaint_webhook_verifier.php';
require APPPATH.'helpers/complaint_helper.php';
$checks = 0;
function check($ok, $label) { global $checks; if (!$ok) { throw new RuntimeException('FAIL: '.$label); } $checks++; }
function rejects($call, $label) { try { $call(); } catch (InvalidArgumentException $e) { check(TRUE, $label); return; } check(FALSE, $label); }
function site_url($path) { return 'http://localhost/tgm_erp/index.php/'.$path; }
function get_instance() { return (object) array('config' => new TestConfig()); }
class TestConfig { public function item($name) { return FALSE; } }
class TestSession { public function flashdata($name) { return NULL; } }
class ViewHarness {
    public $load; public $session;
    public function __construct() { $this->load = $this; $this->session = new TestSession(); }
    public function view($path, $data) { extract($data, EXTR_SKIP); include APPPATH.'views/'.$path.'.php'; }
    public function render($path, $data) { ob_start(); try { $this->view('admin/complaint/'.$path, $data); return ob_get_clean(); } catch (Throwable $e) { ob_end_clean(); throw $e; } }
}

$parser = new Whatsapp_export_parser();
$batch = $parser->parse(file_get_contents(__DIR__.'/fixtures/whatsapp_android.txt'));
check(count($batch['messages']) === 4 && $batch['system_messages'] === 1, 'Android export/system lines');
check(strpos($batch['messages'][0]['body'], "\nSudah dibersihkan") !== FALSE, 'multiline message preserved');
$ios = $parser->parse("\xEF\xBB\xBF[10/6/26, 12:01:02 AM] Demo: trouble mesin\n[10/6/26, 1:01:02 PM] Demo: sudah normal", 'MDY');
check($ios['messages'][0]['sent_at'] === '2026-10-06 00:01:02', 'iOS/MDY/AM');
check($ios['messages'][1]['sent_at'] === '2026-10-06 13:01:02', 'PM conversion');
foreach (array('DMY', 'MDY') as $order) {
    $canonical = '';
    foreach ($batch['messages'] as $message) {
        $d = new DateTimeImmutable($message['sent_at']);
        $canonical .= $d->format($order === 'DMY' ? 'd/m/Y, H:i:s' : 'm/d/Y, H:i:s').' - '.$message['sender'].': '.$message['body']."\n";
    }
    check($parser->parse($canonical, $order)['messages'] === $batch['messages'], 'preview/save roundtrip '.$order);
}
rejects(function () use ($parser) { $parser->parse('31/02/26, 10:00 - Demo: error'); }, 'impossible date');
rejects(function () use ($parser) { $parser->parse('06/10/26, 24:00 - Demo: error'); }, 'invalid hour');
rejects(function () use ($parser) { $parser->parse("06/10/26, 10:00 - Demo: error\n2026-10-06 10:01 - Demo: error"); }, 'unsupported timestamp stops import');
rejects(function () use ($parser) { $parser->parse("06/10/26, 10:00 - Demo: error\n06/10/26, 10:01 - Demo: error", 'DMY', 1); }, 'import count bound');
rejects(function () use ($parser) { $parser->parse("06/10/26, 10:00 - Demo: \xFF"); }, 'invalid UTF8');
rejects(function () use ($parser) { $parser->parse('06/10/26, 10:00 - Demo: '.str_repeat('a', 16001)); }, 'message size bound');
$analyzer = new Complaint_analyzer();
$first = $analyzer->rules($batch['messages'][0]['body']);
check($first['classification'] === 'trouble' && $first['category'] === 'Mesin' && $first['spks'] === array('DEMO-001'), 'machine trouble and explicit SPK');
check($analyzer->rules($batch['messages'][1]['body'])['classification'] === 'complaint', 'customer complaint');
check($analyzer->rules($batch['messages'][2]['body'])['classification'] === 'follow-up', 'resolved followup not new trouble');
check($analyzer->rules('Tidak ada kendala mesin hari ini.')['classification'] === 'follow-up', 'negated problem');
check($analyzer->rules('Terima kasih, laporan diterima.')['classification'] === 'informasi', 'ordinary conversation');
check($analyzer->rules('Mesin rusak keluar asap, produksi berhenti total.')['priority'] === 'CRITICAL', 'critical hazard');
check($analyzer->rules('<Media tidak disertakan>')['classification'] === 'informasi', 'missing media not invented issue');
check($analyzer->analyze('Mesin rusak', array('enabled' => FALSE))['method'] === 'rules', 'offline classification');
check($analyzer->analyze('Mesin rusak', array('enabled' => TRUE, 'key' => '', 'model' => ''))['error'] !== NULL, 'AI configuration fallback');
Complaint_policy::transition('OPEN', 'ASSIGNED', 1, '', TRUE); check(TRUE, 'assignment');
Complaint_policy::transition('ASSIGNED', 'ON_PROGRESS', 1, '', FALSE); check(TRUE, 'PIC progress');
Complaint_policy::transition('ON_PROGRESS', 'RESOLVED', 1, 'Part diganti dan dites.', FALSE); check(TRUE, 'PIC resolution');
Complaint_policy::transition('RESOLVED', 'CLOSED', 1, 'Terverifikasi.', TRUE); check(TRUE, 'manager closure');
Complaint_policy::transition('CLOSED', 'OPEN', NULL, '', TRUE); check(TRUE, 'manager reopen');
rejects(function () { Complaint_policy::transition('OPEN', 'CLOSED', 1, 'selesai', TRUE); }, 'cannot skip workflow');
rejects(function () { Complaint_policy::transition('OPEN', 'ASSIGNED', NULL, '', TRUE); }, 'PIC required');
rejects(function () { Complaint_policy::transition('ON_PROGRESS', 'RESOLVED', 1, '', TRUE); }, 'resolution required');
rejects(function () { Complaint_policy::transition('RESOLVED', 'CLOSED', 1, 'selesai', FALSE); }, 'PIC cannot close');
rejects(function () { Complaint_policy::transition('CLOSED', 'OPEN', NULL, '', FALSE); }, 'PIC cannot reopen');
rejects(function () { Complaint_policy::date('2026-02-30'); }, 'invalid report date');

$verifier = new Complaint_webhook_verifier();
$now = 1791288000; $timestamp = (string) $now; $secret = str_repeat('test-only-', 5);
$sources = array('demo' => array('perusahaan_id' => 1, 'secret' => $secret, 'allowed_chats' => array('demo-group')));
$payload = array('message_id' => 'msg-001', 'chat_id' => 'demo-group', 'sender' => 'Demo', 'sent_at' => '2026-10-06T10:00:00Z', 'text' => 'Mesin rusak', 'perusahaan_id' => 999);
$body = json_encode($payload); $signature = hash_hmac('sha256', 'demo'."\n".$timestamp."\n".$body, $secret);
$verified = $verifier->verify($body, 'demo', $timestamp, $signature, $sources, $now);
check($verified['company'] === 1 && $verified['sent_at'] === '2026-10-06 17:00:00', 'server branch mapping and UTC to WIB');
check($verified['key'] === $verifier->verify($body, 'demo', $timestamp, $signature, $sources, $now)['key'], 'provider redelivery key stable');
rejects(function () use ($verifier, $body, $timestamp, $signature, $sources, $now) { $verifier->verify($body.' ', 'demo', $timestamp, $signature, $sources, $now); }, 'payload tampering');
rejects(function () use ($verifier, $body, $timestamp, $signature, $sources, $now) { $verifier->verify($body, 'demo', $timestamp, $signature, $sources, $now + 301); }, 'stale delivery');
rejects(function () use ($verifier, $body, $timestamp, $signature, $sources, $now) { $verifier->verify($body, 'other', $timestamp, $signature, $sources, $now); }, 'unknown source');
foreach (array(array('chat_id', 'unauthorized'), array('sent_at', '2026-02-30T10:00:00Z'), array('text', array('invalid'))) as $bad) {
    $p = $payload; $p[$bad[0]] = $bad[1]; $raw = json_encode($p); $sig = hash_hmac('sha256', 'demo'."\n".$timestamp."\n".$raw, $secret);
    rejects(function () use ($verifier, $raw, $timestamp, $sig, $sources, $now) { $verifier->verify($raw, 'demo', $timestamp, $sig, $sources, $now); }, 'bad webhook '.$bad[0]);
}
$attack = '<script>alert("demo")</script>';
check(strpos(complaint_e($attack), '<script>') === FALSE, 'HTML escape');
check(strpos(complaint_badge($attack), '<script>') === FALSE, 'badge escape');
$base = array('complaint_manager' => TRUE, 'complaint_csrf' => 'demo-token', 'is_readonly' => FALSE, 'username' => $attack,
    'perusahaan' => 'Demo branch', 'categories' => Complaint_policy::categories(), 'statuses' => Complaint_policy::statuses(), 'priorities' => Complaint_policy::priorities());
$ticket = array('complaint_id' => 1, 'ticket_no' => 'DEMO-001', 'status' => 'OPEN', 'priority' => 'HIGH', 'reported_at' => '2026-10-06 08:00:00',
    'needs_review' => 1, 'version' => 1, 'judul' => $attack, 'pelapor' => $attack, 'deskripsi' => $attack, 'kategori' => 'Mesin', 'pic_id' => NULL,
    'mesin_id' => NULL, 'resolution' => '', 'root_cause' => '', 'preventive_action' => '', 'pic_name' => NULL);
$report = array('total' => 1, 'unassigned' => 1, 'review' => 1, 'critical' => 0, 'status' => array_fill_keys(Complaint_policy::statuses(), 0), 'categories' => array(), 'machines' => array(), 'text' => $attack);
$filters = array('from' => '2026-10-01', 'to' => '2026-10-06', 'status' => '', 'kategori' => '', 'priority' => '', 'q' => $attack, 'review' => FALSE);
$listing = array('total' => 1, 'page' => 1, 'pages' => 1, 'rows' => array($ticket));
$detail = array('ticket' => $ticket, 'can_edit' => TRUE, 'spks' => array(), 'users' => array(), 'machines' => array(), 'history' => array(array('created_at' => '2026-10-06', 'username' => $attack, 'event' => 'CREATED', 'old_status' => NULL, 'new_status' => 'OPEN', 'note' => $attack)),
    'evidence' => array(array('id' => 1, 'conversation' => $attack, 'sender' => $attack, 'sent_at' => '2026-10-06', 'body' => $attack)));
$harness = new ViewHarness();
foreach (array('index' => array('report' => $report, 'filters' => $filters, 'listing' => $listing), 'create' => array('machines' => array()), 'detail' => $detail, 'summary' => array('filters' => $filters, 'report' => $report), 'import' => array()) as $view => $data) {
    $html = $harness->render($view, array_merge($base, $data));
    check(strpos($html, '<script>alert(') === FALSE, 'render escaped '.$view);
    check(strpos($html, 'Complaint & Trouble Center') !== FALSE, 'render nav '.$view);
}
$detail['can_edit'] = FALSE;
$html = $harness->render('detail', array_merge($base, $detail));
check(strpos($html, '<fieldset disabled>') !== FALSE && strpos($html, 'Simpan perkembangan') === FALSE, 'readonly detail');
$inbox_message = array('id' => 1, 'sender' => $attack, 'conversation' => $attack, 'sent_at' => '2026-10-06', 'body' => $attack,
    'state' => 'analyzed', 'classification' => 'informasi', 'summary' => $attack, 'category' => 'Lainnya', 'priority' => 'MEDIUM', 'analysis_method' => 'rules', 'analysis_error' => NULL, 'ticket_id' => NULL, 'ticket_no' => NULL);
$html = $harness->render('inbox', array_merge($base, array('inbox' => array('total' => 1, 'pending' => 0, 'page' => 1, 'pages' => 1, 'rows' => array($inbox_message)))));
check(strpos($html, '<script>alert(') === FALSE && strpos($html, 'Kotak Masuk WhatsApp') !== FALSE, 'inbox evidence escaped');

// Exercise the actual controller mutation guard without bootstrapping ERP.
class MY_Controller { public $data = array(); public $input; }
class GuardInput {
    public $verb = 'post'; public $token = 'valid-token';
    public function method() { return $this->verb; }
    public function post($key) { return $key === 'complaint_csrf' ? $this->token : NULL; }
}
function show_error($message, $status = 500) { throw new RuntimeException($message, $status); }
require APPPATH.'controllers/Complaint.php';
$reflection = new ReflectionClass('Complaint');
$controller = $reflection->newInstanceWithoutConstructor();
$controller->input = new GuardInput();
foreach (array('module_token' => 'valid-token', 'manager' => FALSE) as $name => $value) {
    $property = $reflection->getProperty($name);
    $property->setAccessible(TRUE);
    $property->setValue($controller, $value);
}
$mutation = $reflection->getMethod('mutation');
$mutation->setAccessible(TRUE);
$guard = function ($manager = FALSE) use ($mutation, $controller) { $mutation->invoke($controller, $manager); };
$denied = function ($call, $status, $label) { try { $call(); } catch (RuntimeException $e) { check($e->getCode() === $status, $label); return; } check(FALSE, $label); };
$guard(); check(TRUE, 'valid mutation POST and CSRF');
$controller->input->verb = 'get'; $denied($guard, 405, 'GET cannot mutate');
$controller->input->verb = 'post'; $controller->input->token = 'wrong'; $denied($guard, 403, 'invalid module CSRF');
$controller->input->token = NULL; $denied($guard, 403, 'missing module CSRF');
$controller->input->token = 'valid-token'; $controller->data = array('is_readonly' => TRUE); $denied($guard, 403, 'readonly cannot mutate');
$controller->data = array(); $denied(function () use ($guard) { $guard(TRUE); }, 403, 'non-manager cannot import/process/link');
$manager_property = $reflection->getProperty('manager'); $manager_property->setAccessible(TRUE);
$manager_property->setValue($controller, TRUE); $guard(TRUE); check(TRUE, 'manager mutation allowed');
echo 'PASS '.$checks." checks (offline parsing, classification, workflow, webhook validation, and HTML rendering).\n";
