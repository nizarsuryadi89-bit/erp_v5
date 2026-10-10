<?php 
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    if (! function_exists('hitung_umur')) {
        function hitung_umur($tgl)
        {
            $tanggal = new DateTime($tgl);
            $today = new DateTime('today');
            $y = $today->diff($tanggal)->y;
            $m = $today->diff($tanggal)->m;
            $d = $today->diff($tanggal)->d;
            return $y . "<b> Tahun </b>" . $m . " <b> bulan </b>" . $d . " <b> hari </b>";
        }
    }