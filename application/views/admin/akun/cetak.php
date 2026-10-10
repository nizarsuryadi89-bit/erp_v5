<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
        }

        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #888;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <h1><?= $title ?></h1>
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th>Nama Karyawan</th>
                <th>Username</th>
                <th>Hak Akses / Role</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tampilData)):
                $no = 1;
                foreach ($tampilData as $row): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row->nama_karyawan ?: '-') ?></td>
                        <td><strong><?= htmlspecialchars($row->username) ?></strong></td>
                        <td><?= htmlspecialchars($row->role_name ?: '-') ?></td>
                    </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data akun.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem TGM ERP - Manajemen Akun Pengguna</p>
    </div>
</body>

</html>