<?php
class Penggajian_model extends CI_Model {
    // Ambil data gaji pokok, total lembur, dan bonus kinerja untuk 1 karyawan di bulan tertentu
    public function proses_gaji($id_karyawan, $bulan) {
        // 1. Ambil data karyawan & jabatan
        $this->db->select('k.*, j.gaji_pokok, j.nama_jabatan');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->where('k.karyawan_id', $id_karyawan);
        $karyawan = $this->db->get()->row();
        if (!$karyawan) return false;

        // 2. Hitung total jam lembur bulan ini
        $this->db->select('ROUND(SUM(TIME_TO_SEC(TIMEDIFF(jam_selesai, jam_mulai))/3600),2) as total_jam_lembur');
        $this->db->from('tbl_lembur');
        $this->db->where('karyawan_id', $id_karyawan);
        $this->db->where('status', 'disetujui');
        // $this->db->where('DATE_FORMAT(tanggal, "%Y-%m")', $bulan);

        $lembur = $this->db->get()->row();
        $total_jam_lembur = $lembur ? $lembur->total_jam_lembur : 0;
        $rate_lembur = 10000; // Rate per jam, bisa diatur
        $uang_lembur = $total_jam_lembur * $rate_lembur;

        // 3. Hitung bonus kinerja (dari skor aktivitas)
        $this->db->select('SUM(
            CASE 
                WHEN a.action = "input_pemesanan" THEN 1
                WHEN a.action = "input_transaksi" THEN 1
                WHEN a.action = "cetak_laporan" THEN 1
                WHEN a.action = "input_data_pembelian" THEN 1
                WHEN a.action = "input_data_pengambilan" THEN 1
                ELSE 0
            END) as skor_kinerja');
        $this->db->from('user_activity_log a');
        $this->db->where('a.user_id', $id_karyawan); // Asumsi user_id = karyawan_id, sesuaikan jika beda
       
        // $this->db->where('DATE_FORMAT(a.timestamp, "%Y-%m")', $bulan);
        $skor = $this->db->get()->row();
        $skor_kinerja = $skor ? $skor->skor_kinerja : 0;
        $bonus_kinerja = $skor_kinerja * 10000; // 10rb per skor, bisa diatur

        // 4. Total gaji
        $total_gaji = $karyawan->gaji_pokok + $uang_lembur + $bonus_kinerja;

        return [
            'karyawan' => $karyawan,
            'gaji_pokok' => $karyawan->gaji_pokok,
            'total_jam_lembur' => $total_jam_lembur,
            'uang_lembur' => $uang_lembur,
            'skor_kinerja' => $skor_kinerja,
            'bonus_kinerja' => $bonus_kinerja,
            'total_gaji' => $total_gaji,
            'bulan' => $bulan
        ];
    }
} 