<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji Semua Karyawan - <?= date('F Y', strtotime($bulan . '-01')) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --text-dark: #333;
            --text-muted: #666;
            --border-color: #e1e8ed;
            --bg-light: #f8fafc;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        body {
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 11px;
            color: var(--text-dark);
            background: #e2e8f0;
            margin: 0;
            padding: 0;
        }

        /* A4 Paper Layout Settings */
        .slip-container {
            width: 210mm;
            height: 297mm;
            /* Fixed A4 height */
            margin: 0 auto;
            padding: 10mm;
            /* Reduced padding */
            background: #fff;
            position: relative;
            overflow: hidden;
            page-break-after: always;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .slip-title {
            text-align: center;
            margin: 8px 0;
            font-size: 14px;
            font-weight: 700;
            text-decoration: underline;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
            background: var(--bg-light);
            padding: 8px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 0;
            font-size: 10px;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .salary-table th {
            background: var(--primary-color);
            color: #fff;
            padding: 6px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }

        .salary-table td {
            padding: 4px 10px;
            /* Compact row height */
            border-bottom: 1px solid var(--border-color);
            font-size: 10px;
        }

        .salary-table tr:nth-child(even) {
            background: #fafafa;
        }

        .amount {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
            font-size: 11px;
        }

        .total-row {
            background: #f1f5f9 !important;
            color: var(--primary-color);
        }

        .total-row td {
            font-weight: 700;
            font-size: 12px;
            padding: 10px !important;
            border-top: 2px solid var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }

        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .sign-box {
            text-align: center;
            width: 180px;
        }

        .sign-box p {
            margin-bottom: 40px;
            font-size: 10px;
        }

        .signature-name {
            font-weight: 700;
            text-decoration: underline;
            font-size: 11px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.03);
            font-weight: 800;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            body {
                background: none;
                margin: 0;
                padding: 0;
            }

            .slip-container {
                margin: 0;
                box-shadow: none;
                width: 210mm;
                height: 297mm;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <?php
    if (!function_exists('format_jam_menit')) {
        function format_jam_menit($val)
        {
            if (!$val)
                return '0j 0m';
            $jam = floor($val);
            $menit = round(($val - $jam) * 60);
            if ($menit >= 60) {
                $jam++;
                $menit = 0;
            }
            return "{$jam}j {$menit}m";
        }
    }
    ?>

    <?php foreach ($salary_data as $slip): ?>
        <div class="slip-container">
            <div class="watermark">TGM ERP SYSTEM</div>

            <div class="header">
                <h2><?= isset($perusahaan->nama_perusahaan) ? $perusahaan->nama_perusahaan : 'TGM ERP SYSTEM' ?></h2>
                <p><?= isset($perusahaan->alamat) ? $perusahaan->alamat : 'Professional Business Solutions' ?></p>
                <p>Telp: <?= isset($perusahaan->telepon) ? $perusahaan->telepon : '-' ?> | Email:
                    <?= isset($perusahaan->email) ? $perusahaan->email : '-' ?>
                </p>
            </div>

            <div class="slip-title">SLIP GAJI KARYAWAN</div>

            <div class="info-grid">
                <table class="info-table">
                    <tr>
                        <td width="30%"><strong>NIK</strong></td>
                        <td>: <?= $slip['nik'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td>: <?= $slip['nama_lengkap'] ?></td>
                    </tr>
                </table>
                <table class="info-table">
                    <tr>
                        <td width="35%"><strong>Jabatan</strong></td>
                        <td>: <?= $slip['nama_jabatan'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Periode</strong></td>
                        <td>: <?= date('F Y', strtotime($bulan . '-01')) ?></td>
                    </tr>
                </table>
            </div>

            <!-- PENDAPATAN SECTION -->
            <table class="salary-table" style="margin-bottom: 5px;">
                <thead>
                    <tr>
                        <th width="70%">PENDAPATAN</th>
                        <th class="text-right">JUMLAH</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gaji Pokok</td>
                        <td class="amount">Rp <?= number_format($slip['gaji_pokok']) ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Jabatan</td>
                        <td class="amount">Rp <?= number_format($slip['tunjangan_jabatan']) ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Transportasi</td>
                        <td class="amount">Rp <?= number_format($slip['tunjangan_transportasi']) ?></td>
                    </tr>
                    <tr>
                        <td>Bonus Kinerja</td>
                        <td class="amount">Rp <?= number_format($slip['bonus_kinerja']) ?></td>
                    </tr>
                    <tr>
                        <td>Tunj. Kinerja</td>
                        <td class="amount">Rp
                            <?= number_format(isset($slip['tunjangan_kinerja_manual']) ? $slip['tunjangan_kinerja_manual'] : 0) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Bonus Kehadiran</td>
                        <td class="amount">Rp <?= number_format($slip['bonus_kehadiran']) ?></td>
                    </tr>
                    <tr>
                        <td>
                            Uang Lembur
                            <?php if (isset($slip['detail_lembur'])): ?>
                                <br><small style="font-weight:normal; font-size:9px; color: var(--text-muted);">
                                    (Wajib: <?= format_jam_menit($slip['detail_lembur']['wajib']['jam']) ?>, Kerjaan:
                                    <?= format_jam_menit($slip['detail_lembur']['kerjaan']['jam']) ?>)
                                </small>
                            <?php endif; ?>
                        </td>
                        <td class="amount">Rp <?= number_format($slip['uang_lembur']) ?></td>
                    </tr>
                    <tr>
                        <td>Biaya Makan Lembur</td>
                        <td class="amount">Rp
                            <?= number_format(isset($slip['biaya_makan_lembur']) ? $slip['biaya_makan_lembur'] : 0) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Reward</td>
                        <td class="amount">Rp <?= number_format(isset($slip['reward']) ? $slip['reward'] : 0) ?></td>
                    </tr>
                    <tr>
                        <td>BPJS</td>
                        <td class="amount">Rp <?= number_format(isset($slip['bpjs']) ? $slip['bpjs'] : 0) ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Extra</td>
                        <td class="amount">Rp
                            <?= number_format(isset($slip['tunjangan_extra']) ? $slip['tunjangan_extra'] : 0) ?>
                        </td>
                    </tr>
                    <?php for($i=1; $i<=3; $i++): ?>
                    <?php if(isset($slip["custom_in{$i}_nom"]) && $slip["custom_in{$i}_nom"] > 0): ?>
                    <tr>
                        <td><?= htmlspecialchars($slip["custom_in{$i}_nama"] ?: "Tambahan Pendapatan $i") ?></td>
                        <td class="amount">Rp <?= number_format($slip["custom_in{$i}_nom"]) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php endfor; ?>
                    <tr style="background-color: #f8fafc; font-weight: bold;">
                        <td class="text-right">SUBTOTAL PENDAPATAN</td>
                        <td class="amount">
                            <?php
                            $sub_pendapatan = $slip['gaji_pokok'] + $slip['tunjangan_jabatan'] + $slip['tunjangan_transportasi'] + $slip['bonus_kinerja'] + (isset($slip['tunjangan_kinerja_manual']) ? $slip['tunjangan_kinerja_manual'] : 0) + $slip['bonus_kehadiran'] + $slip['uang_lembur'] + (isset($slip['biaya_makan_lembur']) ? $slip['biaya_makan_lembur'] : 0) + (isset($slip['reward']) ? $slip['reward'] : 0) + (isset($slip['bpjs']) ? $slip['bpjs'] : 0) + (isset($slip['tunjangan_extra']) ? $slip['tunjangan_extra'] : 0) + (isset($slip['custom_in1_nom']) ? $slip['custom_in1_nom'] : 0) + (isset($slip['custom_in2_nom']) ? $slip['custom_in2_nom'] : 0) + (isset($slip['custom_in3_nom']) ? $slip['custom_in3_nom'] : 0);
                            ?>
                            Rp <?= number_format($sub_pendapatan) ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- POTONGAN SECTION -->
            <table class="salary-table">
                <thead>
                    <tr>
                        <th width="70%" style="background-color: #e74c3c;">POTONGAN</th>
                        <th class="text-right" style="background-color: #e74c3c;">JUMLAH</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="color: #e74c3c;">
                            Potongan Terlambat
                            <?php if (isset($slip['total_detik_telat']) && $slip['total_detik_telat'] > 0): ?>
                                <br><small style="font-weight:normal; font-size:9px; color: var(--text-muted);">
                                    (Durasi: <?= format_jam_menit($slip['total_detik_telat'] / 3600) ?>)
                                </small>
                            <?php endif; ?>
                        </td>
                        <td class="amount" style="color: #e74c3c;">
                            - Rp <?= number_format(isset($slip['potongan_terlambat']) ? $slip['potongan_terlambat'] : 0) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #e74c3c;">
                            Potongan Alpha
                            <?php if (isset($slip['total_alpha']) && $slip['total_alpha'] > 0): ?>
                                <br><small style="font-weight:normal; font-size:9px; color: var(--text-muted);">
                                    (Jumlah: <?= $slip['total_alpha'] ?> Hari)
                                </small>
                            <?php endif; ?>
                        </td>
                        <td class="amount" style="color: #e74c3c;">
                            - Rp <?= number_format($slip['potongan_alpha']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #e74c3c;">Kasbon</td>
                        <td class="amount" style="color: #e74c3c;">
                            - Rp <?= number_format(isset($slip['kasbon']) ? $slip['kasbon'] : 0) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #e74c3c;">Pinjaman / Lain-lain</td>
                        <td class="amount" style="color: #e74c3c;">
                            - Rp <?= number_format(isset($slip['pinjaman']) ? $slip['pinjaman'] : 0) ?>
                        </td>
                    </tr>
                    <?php for($i=1; $i<=3; $i++): ?>
                    <?php if(isset($slip["custom_out{$i}_nom"]) && $slip["custom_out{$i}_nom"] > 0): ?>
                    <tr>
                        <td style="color: #e74c3c;"><?= htmlspecialchars($slip["custom_out{$i}_nama"] ?: "Tambahan Potongan $i") ?></td>
                        <td class="amount" style="color: #e74c3c;">- Rp <?= number_format($slip["custom_out{$i}_nom"]) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php endfor; ?>
                    <tr style="background-color: #fff5f5; font-weight: bold;">
                        <td class="text-right" style="color: #e74c3c;">SUBTOTAL POTONGAN</td>
                        <td class="amount" style="color: #e74c3c;">
                            <?php
                            $sub_potongan = $slip['potongan_alpha'] + (isset($slip['potongan_terlambat']) ? $slip['potongan_terlambat'] : 0) + (isset($slip['kasbon']) ? $slip['kasbon'] : 0) + (isset($slip['pinjaman']) ? $slip['pinjaman'] : 0) + (isset($slip['custom_out1_nom']) ? $slip['custom_out1_nom'] : 0) + (isset($slip['custom_out2_nom']) ? $slip['custom_out2_nom'] : 0) + (isset($slip['custom_out3_nom']) ? $slip['custom_out3_nom'] : 0);
                            ?>
                            - Rp <?= number_format($sub_potongan) ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- TAKE HOME PAY -->
            <table class="salary-table" style="margin-top: 10px;">
                <tr class="total-row">
                    <td width="70%">TOTAL DITERIMA (TAKE HOME PAY)</td>
                    <td class="amount">
                        Rp <?= number_format($sub_pendapatan - $sub_potongan) ?>
                    </td>
                </tr>
            </table>

            <div class="footer">
                <div class="sign-box">
                    <p>Penerima,</p>
                    <div class="signature-name"><?= $slip['nama_lengkap'] ?></div>
                </div>
                <div class="sign-box">
                    <p>Bekasi, <?= date('d F Y') ?><br>Bagian Keuangan,</p>
                    <div class="signature-name">Rahmi Irwan</div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</body>

</html>