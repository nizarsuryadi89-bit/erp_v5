<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Existing session role/level values that may assign/close branch tickets.
$config['complaint_manager_roles'] = array(1, 8, 17, 22);
$config['complaint_ai_enabled'] = getenv('TGM_COMPLAINT_AI_ENABLED') === '1';
$config['complaint_ai_key'] = getenv('OPENAI_API_KEY') ?: '';
$config['complaint_ai_model'] = getenv('TGM_COMPLAINT_AI_MODEL') ?: '';
$config['complaint_ai_timeout'] = 20;
$config['complaint_max_import_bytes'] = 2097152;
$config['complaint_max_import_messages'] = 500;
$config['complaint_offline_batch'] = 50;

// A gateway adapter POSTs the documented normalized payload. Never trust a
// company ID supplied inside the body. Configure each source on the server.
$config['complaint_webhook_sources'] = array();
// Example (replace IDs and load the secret from server environment):
// $config['complaint_webhook_sources']['tgm-cabang-1'] = array(
//     'perusahaan_id' => 1, 'secret' => getenv('TGM_WA_WEBHOOK_SECRET'),
//     'allowed_chats' => array('operasional-tgm')
// );
