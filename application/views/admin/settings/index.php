<div class="container-fluid px-4">
  <div class="row mb-3">
    <div class="col-12">
      <h2 class="fw-bold mb-1"><i class="bi bi-gear-wide-connected"></i> Administrator Settings</h2>
      <p class="text-muted mb-0">Panel pengaturan khusus administrator sistem: monitoring error, backup database, dan
        informasi sistem.</p>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="me-3 text-danger"><i class="bi bi-exclamation-octagon-fill fs-1"></i></div>
            <div>
              <h5 class="card-title mb-0">Error Logs</h5>
              <small class="text-muted">Pantau error aplikasi & analitik</small>
            </div>
          </div>
          <p class="mb-3">Lihat log error, filter, cari, unduh, bersihkan log, dan lihat statistik error.</p>
          <div class="d-grid gap-2">
            <a href="<?= base_url('errorlog') ?>" class="btn btn-outline-danger"><i class="bi bi-list-ul me-1"></i> Buka
              Error Logs</a>
            <a href="<?= base_url('errorlog/statistics') ?>" class="btn btn-danger"><i class="bi bi-graph-up me-1"></i>
              Statistik Error</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="me-3 text-primary"><i class="bi bi-hdd-network fs-1"></i></div>
            <div>
              <h5 class="card-title mb-0">Backup Database</h5>
              <small class="text-muted">Pilih tabel, format, dan tujuan</small>
            </div>
          </div>
          <p class="mb-3">Buat backup database dengan memilih tabel tertentu, format file (ZIP/GZIP/SQL), dan simpan
            atau download.</p>
          <div class="d-grid">
            <a href="<?= base_url('backup') ?>" class="btn btn-primary"><i class="bi bi-download me-1"></i> Buka Backup
              Database</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card h-100 border-danger border-opacity-25 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="me-3 text-danger"><i class="bi bi-shield-lock-fill fs-1"></i></div>
            <div>
              <h5 class="card-title mb-0">System Maintenance</h5>
              <small class="text-muted">Manajemen data & pembersihan</small>
            </div>
          </div>
          <p class="mb-3">Gunakan tool ini untuk menghapus data transaksi dummy (SPK, Mutasi, Invoice) sebelum memulai
            <strong>Real Count</strong>.</p>
          <div class="d-grid">
            <button type="button" class="btn btn-danger" onclick="confirmSystemReset()">
              <i class="bi bi-trash3-fill me-1"></i> Reset Transaction Data
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function confirmSystemReset() {
      Swal.fire({
        title: 'Sistem Reset (Hapus Dummy?)',
        text: "Seluruh data transaksi (SPK, Mutasi, Stok, Invoice) akan DIHAPUS PERMANEN. Data Master (Bahan, Customer, Produk) tetap aman.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semua Transaksi!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Konfirmasi Terakhir',
            text: "Ketik 'HAPUS' untuk mengonfirmasi penghapusan permanen data dummy.",
            input: 'text',
            inputAttributes: {
              autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'EKSEKUSI SEKARANG',
            confirmButtonColor: '#d33',
            preConfirm: (input) => {
              if (input !== 'HAPUS') {
                Swal.showValidationMessage('Anda harus mengetik HAPUS untuk melanjutkan')
              }
              return input;
            }
          }).then((res) => {
            if (res.isConfirmed) {
              window.location.href = "<?= base_url('settings/reset_dummy_data') ?>";
            }
          })
        }
      })
    }
  </script>

  <div class="row g-4 mt-1">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0"><i class="bi bi-tools"></i> Utilitas Lain (opsional)</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-3">
              <div class="border rounded p-3 h-100">
                <h6 class="mb-2">Cache</h6>
                <p class="small text-muted mb-3">Bersihkan cache aplikasi untuk memuat ulang konfigurasi.</p>
                <button class="btn btn-sm btn-outline-secondary" disabled>Clear Cache (Soon)</button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="border rounded p-3 h-100">
                <h6 class="mb-2">Log Rotation</h6>
                <p class="small text-muted mb-3">Atur retensi log dan rotasi berkala.</p>
                <button class="btn btn-sm btn-outline-secondary" disabled>Configure (Soon)</button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="border rounded p-3 h-100">
                <h6 class="mb-2">User Audit</h6>
                <p class="small text-muted mb-3">Pantau aktivitas admin penting.</p>
                <button class="btn btn-sm btn-outline-secondary" disabled>Open (Soon)</button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="border rounded p-3 h-100">
                <h6 class="mb-2">Konfigurasi Email</h6>
                <p class="small text-muted mb-3">Pengaturan SMTP untuk notifikasi.</p>
                <button class="btn btn-sm btn-outline-secondary" disabled>Configure (Soon)</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>