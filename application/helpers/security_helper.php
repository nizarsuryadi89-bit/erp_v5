<?php if (!defined("BASEPATH")) exit("No direct script access allowed");

function encrypt_url($string) {

    $output = false;
    /*
    * read security.ini file & get encryption_key | iv | encryption_mechanism value for generating encryption code
    */        
    $security       = parse_ini_file(__DIR__ . "/security.ini");
    $secret_key     = $security["encryption_key"];
    $secret_iv      = $security["iv"];
    $encrypt_method = $security["encryption_mechanism"];

    // hash
    $key    = hash("sha256", $secret_key);

    // iv – encrypt method AES-256-CBC expects 16 bytes – else you will get a warning
    $iv     = substr(hash("sha256", $secret_iv), 0, 16);

    //do the encryption given text/string/number
    $result = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($result);
    return $output;
}



function decrypt_url($string) {

    $output = false;
    /*
    * read security.ini file & get encryption_key | iv | encryption_mechanism value for generating encryption code
    */

    $security       = parse_ini_file(__DIR__ . "/security.ini");
    $secret_key     = $security["encryption_key"];
    $secret_iv      = $security["iv"];
    $encrypt_method = $security["encryption_mechanism"];

    // hash
    $key    = hash("sha256", $secret_key);

    // iv – encrypt method AES-256-CBC expects 16 bytes – else you will get a warning
    $iv = substr(hash("sha256", $secret_iv), 0, 16);

    //do the decryption given text/string/number

    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    return $output;
}

function encrypt_spk($string) {
    $output = false;
    $security       = parse_ini_file(__DIR__ . "/security.ini");
    $secret_key     = $security["encryption_key"];
    $secret_iv      = $security["iv"];
    $encrypt_method = $security["encryption_mechanism"];

    $key    = hash("sha256", $secret_key);
    $iv     = substr(hash("sha256", $secret_iv), 0, 16);

    $result = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    // Base64 standard to URL Safe
    $output = str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($result));
    return $output;
}

function decrypt_spk($string) {
    $output = false;
    $security       = parse_ini_file(__DIR__ . "/security.ini");
    $secret_key     = $security["encryption_key"];
    $secret_iv      = $security["iv"];
    $encrypt_method = $security["encryption_mechanism"];

    $key    = hash("sha256", $secret_key);
    $iv = substr(hash("sha256", $secret_iv), 0, 16);

    // URL Safe back to Base64 standard
    $b64 = str_replace(array('-', '_'), array('+', '/'), $string);
    // Padding is optional for base64_decode but good practice
    $pad = strlen($b64) % 4;
    if ($pad) {
        $b64 .= str_repeat('=', 4 - $pad);
    }
    
    $output = openssl_decrypt(base64_decode($b64), $encrypt_method, $key, 0, $iv);
    return $output;
}
