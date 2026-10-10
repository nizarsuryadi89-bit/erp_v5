<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan_kinerja_' . $bulan . '.xls"');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kinerja Karyawan - <?= date('F Y', strtotime($bulan . '-01')) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .grade-a { background-color: #d4edda; color: #155724; }
        .grade-b { background-color: #d1ecf1; color: #0c5460; }
        .grade-c { background-color: #fff3cd; color: #856404; }
        .grade-d { background-color: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .summary {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 14px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .summary-item {
            text-align: center;
            padding: 10px;
            background: white;
            border: 1px solid #dee2e6;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-label {
            font-size: 10px;
            color: #6c757d;
            text-transform: uppercase;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KINERJA KARYAWAN</h1>
        <p>Periode: <?= date('F Y', strtotime($bulan . '-01')) ?></p>
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
    </div>

    <div class="summary">
        <h3>Ringkasan Kinerja</h3>
        <div class="summary-grid">
            <?php 
            $total_employees = count($performance_data);
            $total_activities = 0;
            $total_score = 0;
            $avg_score = 0;
            $grade_a = 0;
            $grade_b = 0;
            $grade_c = 0;
            $grade_d = 0;
            
            foreach ($performance_data as $perf) {
                $total_activities += $perf->total_aktivitas;
                $total_score += $perf->skor_kinerja;
                
                if ($perf->skor_kinerja >= 1000) {
                    $grade_a++;
                } elseif ($perf->skor_kinerja >= 500) {
                    $grade_b++;
                } elseif ($perf->skor_kinerja >= 200) {
                    $grade_c++;
                } else {
                    $grade_d++;
                }
            }
            
            $avg_score = $total_employees > 0 ? $total_score / $total_employees : 0;
            ?>
            
            <div class="summary-item">
                <div class="summary-value"><?= $total_employees ?></div>
                <div class="summary-label">Total Karyawan</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?= number_format($total_activities) ?></div>
                <div class="summary-label">Total Aktivitas</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?= number_format($avg_score, 1) ?></div>
                <div class="summary-label">Rata-rata Skor</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?= number_format($total_score) ?></div>
                <div class="summary-label">Total Skor</div>
            </div>
        </div>
        
        <div style="margin-top: 15px;">
            <h4 style="margin: 0 0 10px 0; font-size: 12px;">Distribusi Grade:</h4>
            <div style="display: flex; gap: 20px; font-size: 11px;">
                <div><strong>Grade A:</strong> <?= $grade_a ?> karyawan</div>
                <div><strong>Grade B:</strong> <?= $grade_b ?> karyawan</div>
                <div><strong>Grade C:</strong> <?= $grade_c ?> karyawan</div>
                <div><strong>Grade D:</strong> <?= $grade_d ?> karyawan</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th>Divisi</th>
                <th>Perusahaan</th>
                <th>Hari Aktif</th>
                <th>Total Aktivitas</th>
                <th>Skor Kinerja</th>
                <th>Rata-rata Skor</th>
                <th>Grade</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($performance_data)): ?>
                <?php $no = 1; foreach ($performance_data as $perf): ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td><?= $perf->nik ?></td>
                    <td><?= $perf->nama_lengkap ?></td>
                    <td><?= $perf->nama_jabatan ?></td>
                    <td><?= $perf->divisi_nama ?></td>
                    <td><?= $perf->nama_perusahaan ?></td>
                    <td style="text-align: center;"><?= $perf->total_hari_aktif ?></td>
                    <td style="text-align: center;"><?= number_format($perf->total_aktivitas) ?></td>
                    <td style="text-align: center;"><strong><?= number_format($perf->skor_kinerja) ?></strong></td>
                    <td style="text-align: center;"><?= number_format($perf->rata_rata_skor, 2) ?></td>
                    <td style="text-align: center;">
                        <?php 
                        $grade = '';
                        $grade_class = '';
                        $keterangan = '';
                        
                        if ($perf->skor_kinerja >= 1000) {
                            $grade = 'A';
                            $grade_class = 'grade-a';
                            $keterangan = 'Sangat Baik';
                        } elseif ($perf->skor_kinerja >= 500) {
                            $grade = 'B';
                            $grade_class = 'grade-b';
                            $keterangan = 'Baik';
                        } elseif ($perf->skor_kinerja >= 200) {
                            $grade = 'C';
                            $grade_class = 'grade-c';
                            $keterangan = 'Cukup';
                        } else {
                            $grade = 'D';
                            $grade_class = 'grade-d';
                            $keterangan = 'Kurang';
                        }
                        ?>
                        <span class="<?= $grade_class ?>"><?= $grade ?></span>
                    </td>
                    <td><?= $keterangan ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12" style="text-align: center;">Tidak ada data kinerja</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Keterangan Skor:</strong></p>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <li>Input Pemesanan: 10 poin</li>
            <li>Input Transaksi: 8 poin</li>
            <li>Input Data Pembelian: 7 poin</li>
            <li>Input Data Pengambilan: 6 poin</li>
            <li>Cetak Laporan: 5 poin</li>
            <li>Edit Data: 4 poin</li>
            <li>Input Absensi: 3 poin</li>
            <li>Delete Data: 2 poin</li>
            <li>Aktivitas Lainnya: 1 poin</li>
        </ul>
        <p style="margin-top: 15px;">Dokumen ini dibuat secara otomatis oleh sistem</p>
    </div>
</body>
</html>
