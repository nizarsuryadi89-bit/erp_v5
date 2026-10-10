<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| JWT configuration variables
|--------------------------------------------------------------------------
*/

$config['jwt_key'] = 'TGM_ERP_SUPER_SECRET_KEY_' . md5(uniqid(rand(), true));
$config['jwt_algorithm'] = 'HS256';
