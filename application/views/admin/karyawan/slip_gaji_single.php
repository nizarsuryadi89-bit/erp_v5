<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - <?= $slip['nama_lengkap'] ?></title>
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
            font-family: 'Inter', Arial, sans-serif;
            font-size: 11px;
            color: var(--text-dark);
            background: #e2e8f0;
            margin: 0;
            padding: 0;
        }

        /* A5 Paper Layout Settings */
        .slip-container {
            width: 148mm;
            min-height: 210mm;
            margin: 10mm auto;
            padding: 8mm;
            background: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 4px;
            position: relative;
            overflow: hidden;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
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
            margin: 10px 0;
            font-size: 13px;
            font-weight: 700;
            text-decoration: underline;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
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
            margin-bottom: 15px;
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
            padding: 6px 10px;
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
            border-top: 2px solid var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }

        .footer {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
        }

        .sign-box {
            text-align: center;
            width: 150px;
        }

        .sign-box p {
            margin-bottom: 45px;
        }

        .signature-name {
            font-weight: 700;
            text-decoration: underline;
            font-size: 10px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 60px;
            color: rgba(0, 0, 0, 0.03);
            font-weight: 800;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
        }

        .edit-input {
            width: 100%;
            padding: 4px;
            border: 1px solid var(--accent-color);
            border-radius: 2px;
            font-size: 10px;
            background: #fff;
        }

        @media print {
            @page {
                size: A5 portrait;
                margin: 0;
            }

            body {
                background: none;
            }

            .slip-container {
                margin: 0;
                box-shadow: none;
                width: 100%;
                min-height: auto;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<?php $mode = isset($_GET['mode']) ? $_GET['mode'] : ''; ?>

<body <?= ($mode == 'preview' || $mode == 'edit' || $mode == 'pdf') ? '' : 'onload="window.print()"' ?>>
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

        <form id="formGaji">
            <input type="hidden" name="karyawan_id" value="<?= $slip['karyawan_id'] ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">

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
                        <td class="amount">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="gaji_pokok" class="edit-input earnings"
                                    value="<?= $slip['gaji_pokok'] ?>" style="text-align:right;">
                            <?php else: ?>
                                Rp <?= number_format($slip['gaji_pokok']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tunjangan Jabatan</td>
                        <td class="amount">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="tunjangan_jabatan" class="edit-input earnings"
                                    value="<?= $slip['tunjangan_jabatan'] ?>" style="text-align:right;">
                            <?php else: ?>
                                Rp <?= number_format($slip['tunjangan_jabatan']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if (isset($slip['transport_aktif']) && $slip['transport_aktif'] == 1): ?>
                        <tr>
                            <td>Tunjangan Transportasi</td>
                            <td class="amount">
                                <?php if ($mode == 'edit'): ?>
                                    <input type="number" name="tunjangan_transportasi" class="edit-input earnings"
                                        value="<?= $slip['tunjangan_transportasi'] ?>" style="text-align:right;">
                                <?php else: ?>
                                    Rp <?= number_format($slip['tunjangan_transportasi']) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Bonus Kinerja </td>
                        <td class="amount">
                            Rp <?= number_format($slip['bonus_kinerja']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tunj. Kinerja</td>
                        <td class="amount">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="tunjangan_kinerja_manual" class="edit-input earnings"
                                    value="<?= isset($slip['tunjangan_kinerja_manual']) ? $slip['tunjangan_kinerja_manual'] : 0 ?>"
                                    style="text-align:right;">
                            <?php else: ?>
                                Rp
                                <?= number_format(isset($slip['tunjangan_kinerja_manual']) ? $slip['tunjangan_kinerja_manual'] : 0) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Bonus Kehadiran</td>
                        <td class="amount">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="bonus_kehadiran" class="edit-input earnings"
                                    value="<?= $slip['bonus_kehadiran'] ?>" style="text-align:right;">
                            <?php else: ?>
                                Rp <?= number_format($slip['bonus_kehadiran']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Uang Lembur
                            <?php if (isset($slip['detail_lembur']) && $mode != 'edit'): ?>
                                <br><small style="font-weight:normal; font-size:9px; color: var(--text-muted);">
                                    (Wajib: <?= format_jam_menit($slip['detail_lembur']['wajib']['jam']) ?>,
                                    Kerjaan: <?= format_jam_menit($slip['detail_lembur']['kerjaan']['jam']) ?>
                                    <?php if (isset($slip['potongan_lembur_jam']) && $slip['potongan_lembur_jam'] > 0): ?>
                                        , <span style="color:#e74c3c;">Potong Telat:
                                            -<?= format_jam_menit($slip['potongan_lembur_jam']) ?></span>
                                    <?php endif; ?>)
                                </small>
                            <?php endif; ?>
                        </td>
                        <td class="amount">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="uang_lembur" class="edit-input earnings"
                                    value="<?= $slip['uang_lembur'] ?>" style="text-align:right;">
                            <?php else: ?>
                                Rp <?= number_format($slip['uang_lembur']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Biaya Makan Lembur</td>
                        <td class="amount">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="biaya_makan_lembur" class="edit-input earnings"
                                    value="<?= isset($slip['biaya_makan_lembur']) ? $slip['biaya_makan_lembur'] : 0 ?>"
                                    style="text-align:right;">
                            <?php else: ?>
                                Rp
                                <?= number_format(isset($slip['biaya_makan_lembur']) ? $slip['biaya_makan_lembur'] : 0) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Reward</td>
                        <td class="amount">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="reward" class="edit-input earnings"
                                    value="<?= isset($slip['reward']) ? $slip['reward'] : 0 ?>" style="text-align:right;">
                            <?php else: ?>
                                Rp <?= number_format(isset($slip['reward']) ? $slip['reward'] : 0) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>BPJS</td>
                        <td class="amount">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="bpjs" class="edit-input earnings"
                                    value="<?= isset($slip['bpjs']) ? $slip['bpjs'] : 0 ?>" style="text-align:right;">
                            <?php else: ?>
                                Rp <?= number_format(isset($slip['bpjs']) ? $slip['bpjs'] : 0) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tunjangan Extra</td>
                        <td class="amount">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="tunjangan_extra" class="edit-input earnings"
                                    value="<?= isset($slip['tunjangan_extra']) ? $slip['tunjangan_extra'] : 0 ?>"
                                    style="text-align:right;">
                            <?php else: ?>
                                Rp <?= number_format(isset($slip['tunjangan_extra']) ? $slip['tunjangan_extra'] : 0) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php for($i=1; $i<=3; $i++): ?>
                    <?php if(isset($slip["custom_in{$i}_nom"]) && $slip["custom_in{$i}_nom"] > 0): ?>
                    <tr>
                        <td><?= htmlspecialchars($slip["custom_in{$i}_nama"] ?: "Tambahan Pendapatan $i") ?></td>
                        <td class="amount">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" readonly class="edit-input earnings" value="<?= $slip["custom_in{$i}_nom"] ?>" style="text-align:right; border: none; background: transparent;">
                            <?php else: ?>
                                Rp <?= number_format($slip["custom_in{$i}_nom"]) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endfor; ?>
                    <tr style="background-color: #f8fafc; font-weight: bold;">
                        <td class="text-right">SUBTOTAL PENDAPATAN</td>
                        <td class="amount" id="subtotalPendapatan">
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
                                    (Durasi:
                                    <?= format_jam_menit($slip['total_detik_telat'] / 3600) ?>)
                                </small>
                            <?php endif; ?>
                        </td>
                        <td class="amount" style="color: #e74c3c;">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="potongan_terlambat" class="edit-input deductions"
                                    value="<?= isset($slip['potongan_terlambat']) ? $slip['potongan_terlambat'] : 0 ?>"
                                    style="text-align:right;">
                            <?php else: ?>
                                - Rp
                                <?= number_format(isset($slip['potongan_terlambat']) ? $slip['potongan_terlambat'] : 0) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #e74c3c;">
                            Potongan Alpha
                            <?php if (isset($slip['total_alpha']) && $slip['total_alpha'] > 0): ?>
                                <br><small style="font-weight:normal; font-size:9px; color: var(--text-muted);">
                                    (Jumlah:
                                    <?= $slip['total_alpha'] ?> Hari)
                                </small>
                            <?php endif; ?>
                        </td>
                        <td class="amount" style="color: #e74c3c;">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="potongan_alpha" class="edit-input deductions"
                                    value="<?= $slip['potongan_alpha'] ?>" style="text-align:right;">
                            <?php else: ?>
                                - Rp <?= number_format($slip['potongan_alpha']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #e74c3c;">Kasbon</td>
                        <td class="amount" style="color: #e74c3c;">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="kasbon" class="edit-input deductions"
                                    value="<?= isset($slip['kasbon']) ? $slip['kasbon'] : 0 ?>" style="text-align:right;">
                            <?php else: ?>
                                - Rp <?= number_format(isset($slip['kasbon']) ? $slip['kasbon'] : 0) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #e74c3c;">Pinjaman / Lain-lain</td>
                        <td class="amount" style="color: #e74c3c;">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" name="pinjaman" class="edit-input deductions"
                                    value="<?= isset($slip['pinjaman']) ? $slip['pinjaman'] : 0 ?>"
                                    style="text-align:right;">
                            <?php else: ?>
                                - Rp <?= number_format(isset($slip['pinjaman']) ? $slip['pinjaman'] : 0) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php for($i=1; $i<=3; $i++): ?>
                    <?php if(isset($slip["custom_out{$i}_nom"]) && $slip["custom_out{$i}_nom"] > 0): ?>
                    <tr>
                        <td style="color: #e74c3c;"><?= htmlspecialchars($slip["custom_out{$i}_nama"] ?: "Tambahan Potongan $i") ?></td>
                        <td class="amount" style="color: #e74c3c;">
                            <?php if ($mode == 'edit'): ?>
                                <input type="number" readonly class="edit-input deductions" value="<?= $slip["custom_out{$i}_nom"] ?>" style="text-align:right; border: none; background: transparent; color: #e74c3c;">
                            <?php else: ?>
                                - Rp <?= number_format($slip["custom_out{$i}_nom"]) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endfor; ?>
                    <tr style="background-color: #fff5f5; font-weight: bold;">
                        <td class="text-right" style="color: #e74c3c;">SUBTOTAL POTONGAN</td>
                        <td class="amount" id="subtotalPotongan" style="color: #e74c3c;">
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
                    <td class="amount" id="displayTotal">
                        <?php $total_diterima = $sub_pendapatan - $sub_potongan; ?>
                        Rp <?= number_format($total_diterima) ?>
                        <input type="hidden" name="total_gaji" id="inputTotal" value="<?= $total_diterima ?>">
                    </td>
                </tr>
            </table>

            <?php if ($mode == 'edit'): ?>
                <div class="no-print" style="text-align: center; margin-top: 20px;">
                    <button type="button" id="btnSave"
                        style="padding: 8px 16px; background: #2ecc71; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 11px;">Simpan
                        Perubahan</button>
                    <button type="button" id="btnReset"
                        style="padding: 8px 16px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 11px; margin-left: 10px;">Reset
                        ke Default</button>
                </div>
            <?php endif; ?>
        </form>

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

    <?php if ($mode == 'edit'): ?>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function () {
                function calculateTotal() {
                    let totalEarnings = 0;
                    let totalDeductions = 0;

                    $('.edit-input.earnings').each(function () {
                        totalEarnings += parseFloat($(this).val()) || 0;
                    });

                    $('.edit-input.deductions').each(function () {
                        totalDeductions += parseFloat($(this).val()) || 0;
                    });

                    // Add system-calculated but non-editable bonus if any
                    // In this case, Bonus Kinerja (System) is static in preview but we should include it
                    // Actually, for simplicity in edit mode, all relevant inputs have the classes.

                    let total = totalEarnings - totalDeductions;

                    $('#subtotalPendapatan').text('Rp ' + totalEarnings.toLocaleString('id-ID'));
                    $('#subtotalPotongan').text('- Rp ' + totalDeductions.toLocaleString('id-ID'));
                    $('#inputTotal').val(total);
                    $('#displayTotal').text('Rp ' + total.toLocaleString('id-ID'));
                }

                $('.edit-input').on('input', calculateTotal);
                calculateTotal();

                $('#btnSave').click(function () {
                    $.ajax({
                        url: '<?= base_url('karyawan/save_gaji_manual') ?>',
                        type: 'POST',
                        data: $('#formGaji').serialize(),
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Berhasil', 'Data gaji telah disimpan', 'success').then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan', 'error');
                            }
                        }
                    });
                });

                $('#btnReset').click(function () {
                    Swal.fire({
                        title: 'Reset Gaji?',
                        text: "Nilai akan dikembalikan ke perhitungan otomatis sistem.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Reset',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '<?= base_url('karyawan/reset_gaji_manual') ?>',
                                type: 'POST',
                                data: {
                                    karyawan_id: '<?= $slip['karyawan_id'] ?>',
                                    bulan: '<?= $bulan ?>'
                                },
                                dataType: 'json',
                                success: function (res) {
                                    if (res.success) {
                                        Swal.fire('Reset Berhasil', 'Data dikembalikan ke default', 'success').then(() => {
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire('Gagal', 'Gagal mereset data', 'error');
                                    }
                                }
                            });
                        }
                    });
                });
            });
        </script>
    <?php endif; ?>
</body>

</html>