<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kinerja User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Laporan Kinerja Harian</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Tanggal</th>
                <th>Total Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($daily_summary as $row): ?>
            <tr>
                <td><?= $row->username ?></td>
                <td><?= $row->tanggal ?></td>
                <td><?= $row->total_aktivitas ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3 class="mt-5">Skor Kinerja User</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Username</th>
                <th>Skor Kinerja</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($skor_kinerja as $row): ?>
            <tr>
                <td><?= $row->username ?></td>
                <td><?= $row->skor_kinerja ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
