<div class="container py-4">
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
      <h5 class="mb-0">Daftar Karyawan Divisi Administrator</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle mb-0">
          <thead class="table-dark text-center">
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>NIK</th>
              <th>Jabatan</th>
              <th>Divisi</th>
              <th>Telepon</th>
              <th>Email</th>
              <th>Tanggal Masuk</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($tampilData)): ?>
              <tr><td colspan="8" class="text-center">Tidak ada data karyawan divisi deskprint.</td></tr>
            <?php else: $no=1; foreach ($tampilData as $row): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= $row->nama_lengkap ?></td>
                <td><?= $row->nik ?></td>
                <td><?= $row->jabatan_id ?></td>
                <td><?= $row->divisi_id ?></td>
                <td><?= $row->telepon ?></td>
                <td><?= $row->email ?></td>
                <td><?= date('d-m-Y', strtotime($row->tanggal_masuk)) ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div> 