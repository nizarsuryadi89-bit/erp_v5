<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('rupiahToInt')) {
    function rupiahToInt($rupiah) {
        return (int) preg_replace('/[^\d]/', '', $rupiah);
    }
}
