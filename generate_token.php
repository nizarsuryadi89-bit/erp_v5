<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;

$payload = [
    "iss" => 'http://localhost/',
    "iat" => time(),
    "exp" => time() + (86400),
    "data" => [
        "user_id" => 1,
        "karyawan_id" => 1,
        "role_id" => 1,
        "username" => 'admin',
        "perusahaan_id1" => 1
    ]
];

$secret_key = 'RahasiaNegara123!@#';
$jwt = JWT::encode($payload, $secret_key, 'HS256');
echo $jwt;
