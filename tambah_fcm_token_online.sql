-- Script untuk menambahkan kolom fcm_token pada database online

-- Tambahkan di tabel tbl_karyawan
ALTER TABLE tbl_karyawan ADD COLUMN fcm_token TEXT DEFAULT NULL;

-- Tambahkan di tabel users
ALTER TABLE users ADD COLUMN fcm_token TEXT DEFAULT NULL;

-- Anda bisa meng-copy paste script ini di menu "SQL" phpMyAdmin cPanel server online Anda.
