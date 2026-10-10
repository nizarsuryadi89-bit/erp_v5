<div class="app-content mt-2">
  <div class="container-fluid">
    <!-- HEADER SECTION -->
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <?php if ($this->session->flashdata('success')): ?>
      <script>Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= $this->session->flashdata('success') ?>', timer: 1500, showConfirmButton: false });</script>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
      <script>Swal.fire({ icon: 'error', title: 'Gagal', text: '<?= $this->session->flashdata('error') ?>', timer: 2000, showConfirmButton: false });</script>
    <?php endif; ?>
    <!-- AKSI CEPAT & NAVIGASI -->
    <div class="card border-0 shadow-sm mb-4 animate-up">
      <div class="card-body p-3 d-flex flex-wrap gap-2 align-items-center">
        <h6 class="fw-bold mb-0 me-3 d-none d-md-block"><i class="fas fa-bolt text-warning me-2"></i>Aksi Cepat:</h6>

        <a href="<?= base_url('pengaturan/konsumen_dashboard') ?>"
          class="btn btn-outline-primary fw-bold text-nowrap rounded-pill px-3">
          <i class="fas fa-tachometer-alt me-1"></i> Dashboard
        </a>
        <button class="btn btn-success text-white fw-bold text-nowrap rounded-pill px-3 shadow-sm">
          <i class="fas fa-database me-1"></i> Database
        </button>
        <a href="<?= base_url('pengaturan/konsumen_aktivitas') ?>"
          class="btn btn-outline-info fw-bold text-nowrap rounded-pill px-3">
          <i class="fas fa-history me-1"></i> Aktivitas
        </a>

        <div class="vr mx-2 d-none d-md-block"></div>
        <hr class="d-md-none w-100 my-1 opacity-25">

        <button class="btn btn-primary fw-bold text-nowrap rounded-pill px-3 shadow-sm" data-bs-toggle="modal"
          data-bs-target="#modalKonsumen">
          <i class="fas fa-plus me-1"></i> Tambah
        </button>
        <button class="btn btn-outline-success fw-bold text-nowrap rounded-pill px-3" onclick="exportData()">
          <i class="fas fa-file-excel me-1"></i> Export
        </button>
        <button class="btn btn-outline-secondary fw-bold text-nowrap rounded-pill px-3" onclick="printData()">
          <i class="fas fa-print me-1"></i> Print
        </button>
        <button class="btn btn-outline-warning fw-bold text-nowrap rounded-pill px-3 text-dark"
          onclick="checkDuplicates()">
          <i class="fas fa-clone me-1"></i> Duplikasi
        </button>
      </div>
    </div>

    <!-- STATS VIEW -->
    <div class="row g-3 mb-4 animate-up" style="animation-delay: 0.1s">
      <div class="col-md-3">
        <div class="p-3 bg-white rounded-4 shadow-sm border-0 h-100 d-flex align-items-center">
          <div class="rounded-circle bg-light p-3 me-3 text-primary"><i class="fas fa-users fa-lg"></i></div>
          <div>
            <div class="text-muted small fw-bold">TOTAL</div>
            <h4 class="fw-900 mb-0"><?= number_format($total_rows) ?></h4>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-white rounded-4 shadow-sm border-0 h-100 d-flex align-items-center">
          <div class="rounded-circle bg-light p-2 me-3 text-success text-center" style="width:45px; height:45px;"><i
              class="fas fa-check"></i></div>
          <div>
            <div class="text-muted small fw-bold">AKTIF</div>
            <h4 class="fw-900 mb-0 text-success"><?= number_format($active_customers) ?></h4>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-white rounded-4 shadow-sm border-0 h-100 d-flex align-items-center">
          <div class="rounded-circle bg-light p-2 me-3 text-danger text-center" style="width:45px; height:45px;"><i
              class="fas fa-times"></i></div>
          <div>
            <div class="text-muted small fw-bold">NON-AKTIF</div>
            <h4 class="fw-900 mb-0 text-danger"><?= number_format($inactive_customers) ?></h4>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-white rounded-4 shadow-sm border-0 h-100 d-flex align-items-center">
          <div class="rounded-circle bg-light p-2 me-3 text-info text-center" style="width:45px; height:45px;"><i
              class="fas fa-envelope"></i></div>
          <div>
            <div class="text-muted small fw-bold">W/ EMAIL</div>
            <h4 class="fw-900 mb-0 text-info"><?= number_format($customers_with_email) ?></h4>
          </div>
        </div>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <h6 class="fw-bold mb-3"><i class="fas fa-search me-2 text-muted"></i>Pencarian & Filter Data Konsumen</h6>
            <div class="d-flex flex-wrap gap-2">
              <div class="flex-grow-1">
                <input type="text" class="form-control" id="searchInput"
                  placeholder="Ketik Nama / Email / Nomor HP untuk mencari...">
              </div>
              <div style="min-width: 200px;">
                <select class="form-select" id="statusFilter">
                  <option value="">Semua Status Konsumen</option>
                  <option value="1">Aktif Aktif</option>
                  <option value="0">Tidak Aktif</option>
                </select>
              </div>
              <button class="btn btn-outline-secondary fw-bold" onclick="resetFilters()">
                <i class="fas fa-sync-alt me-1"></i> Reset
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Customer Table -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list me-2"></i>Daftar Konsumen</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 w-100" id="datatable-detailss">
            <thead class="table-light">
              <tr>
                <th class="text-center" width="5%">No</th>
                <th>Konsumen</th>
                <th>Kontak</th>
                <th>Alamat</th>
                <th>Email</th>
                <th class="text-center">Status</th>
                <th class="text-center" width="15%">Aksi</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Overview -->
<div class="modal fade" id="modalKonsumen" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form id="formKonsumen" method="POST" action="<?= base_url('pengaturan/konsumen_add') ?>">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Tambah Konsumen</h6>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label small fw-bold text-primary">Tipe Konsumen</label>
              <select class="form-select border-primary" name="tipe_konsumen" id="tipe_konsumen_add" required onchange="toggleB2BFields('add')">
                <option value="B2C">B2C (Reguler/End User)</option>
                <option value="Reseller">Reseller</option>
                <option value="B2B">B2B (Corporate)</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold">Nama Lengkap / Kontak Utama</label>
              <input type="text" class="form-control" name="konsumen_nama" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold">Email (U/ Login Marketplace)</label>
              <input type="email" class="form-control" name="konsumen_email">
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">Nomor WhatsApp / HP</label>
              <input type="tel" class="form-control" id="wa_number" name="konsumen_nohp" placeholder="081..." required>
              <div id="wa_error" class="text-danger small mt-1"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">Password Login (Opsional)</label>
              <input type="password" class="form-control" name="password" placeholder="Kosongkan jika belum ingin dibuat">
            </div>

            <!-- B2B Fields (Hidden by default) -->
            <div class="col-12 b2b-fields-add" style="display:none;">
              <hr class="opacity-25 my-2">
              <h6 class="fw-bold text-primary mb-3"><i class="fas fa-building me-2"></i>Informasi Corporate (B2B)</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Nama Perusahaan (PT/CV)</label>
                  <input type="text" class="form-control" name="perusahaan" id="perusahaan_add">
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">NPWP Perusahaan</label>
                  <input type="text" class="form-control" name="npwp" id="npwp_add">
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Nama PIC (Person in Charge)</label>
                  <input type="text" class="form-control" name="pic_nama" id="pic_add">
                </div>
                <div class="col-md-3">
                  <label class="form-label small fw-bold">Kredit Limit (Rp)</label>
                  <input type="text" class="form-control text-end" name="kredit_limit" id="limit_add" value="0">
                </div>
                <div class="col-md-3">
                  <label class="form-label small fw-bold">Term (TOP Hari)</label>
                  <input type="number" class="form-control text-end" name="top_hari" id="top_add" value="0">
                </div>
              </div>
              <hr class="opacity-25 my-2">
            </div>

            <div class="col-12">
              <label class="form-label small fw-bold">Alamat Lengkap</label>
              <textarea class="form-control" name="konsumen_alamat" rows="2" required></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold">Kode Pos</label>
              <input type="text" class="form-control" name="kode_pos">
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEditKonsumen" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form method="POST" action="<?= base_url('pengaturan/konsumen_edit') ?>">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-warning text-dark">
          <h6 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Data Konsumen</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="konsumen_id" id="edit_id" required>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label small fw-bold text-warning">Tipe Konsumen</label>
              <select class="form-select border-warning" name="tipe_konsumen" id="edit_tipe_konsumen" required onchange="toggleB2BFields('edit')">
                <option value="B2C">B2C (Reguler/End User)</option>
                <option value="Reseller">Reseller</option>
                <option value="B2B">B2B (Corporate)</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold">Nama Lengkap</label>
              <input type="text" name="konsumen_nama" id="edit_nama" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold">Nomor HP</label>
              <input type="tel" name="konsumen_nohp" id="edit_nohp" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">Email</label>
              <input type="email" name="konsumen_email" id="edit_email" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">Reset Password (Opsional)</label>
              <input type="password" name="password" class="form-control" placeholder="Ketik untuk mengubah password akun">
            </div>

            <!-- B2B Fields -->
            <div class="col-12 b2b-fields-edit" style="display:none;">
              <hr class="opacity-25 my-2 border-warning">
              <h6 class="fw-bold text-warning mb-3"><i class="fas fa-building me-2"></i>Informasi Corporate (B2B)</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Nama Perusahaan</label>
                  <input type="text" class="form-control" name="perusahaan" id="edit_perusahaan">
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">NPWP</label>
                  <input type="text" class="form-control" name="npwp" id="edit_npwp">
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Nama PIC</label>
                  <input type="text" class="form-control" name="pic_nama" id="edit_pic_nama">
                </div>
                <div class="col-md-3">
                  <label class="form-label small fw-bold">Kredit Limit</label>
                  <input type="text" class="form-control text-end" name="kredit_limit" id="edit_kredit_limit">
                </div>
                <div class="col-md-3">
                  <label class="form-label small fw-bold">Term (TOP)</label>
                  <input type="number" class="form-control text-end" name="top_hari" id="edit_top_hari">
                </div>
              </div>
              <hr class="opacity-25 my-2 border-warning">
            </div>

            <div class="col-md-9">
              <label class="form-label small fw-bold">Alamat Lengkap</label>
              <textarea class="form-control" name="konsumen_alamat" id="edit_alamat" rows="2" required></textarea>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold">Kode Pos</label>
              <input type="text" class="form-control" name="kode_pos" id="edit_kode_pos">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold">Status Akun</label>
              <select name="status" id="edit_status" class="form-select" required>
                <option value="1">Aktif (Dapat Login & Order)</option>
                <option value="0">Tidak Aktif (Di-Suspend)</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Data</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Cek Duplikasi -->
<div class="modal fade" id="modalDuplikasi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-warning text-dark p-4 border-0">
        <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-exclamation-circle me-2"></i> Laporan Duplikasi
          Konsumen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div id="duplicateResults" class="table-responsive" style="max-height: 400px;">
          <div class="text-center py-5">
            <div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div>
            <p class="mt-2 fw-bold text-muted">Menganalisis data...</p>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 p-4 pt-0">
        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">TUTUP</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('formKonsumen').addEventListener('submit', function (e) {
    const waInput = document.getElementById('wa_number');
    const errorDiv = document.getElementById('wa_error');
    const waValue = waInput.value.trim();
    const valid = /^\+?\d{8,15}$/.test(waValue); // Modified regex to allow optional + and just digits
    errorDiv.textContent = '';
    if (!valid) {
      e.preventDefault();
      errorDiv.textContent = 'Format nomor minimal 8-15 digit.';
      waInput.focus();
    }
  });

  let tableKonsumen;
  $(document).ready(function () {
    if (!$.fn.DataTable.isDataTable('#datatable-detailss')) {
      tableKonsumen = $('#datatable-detailss').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        lengthChange: false, // hide default length
        pageLength: 20,
        ajax: {
          url: "<?= base_url('pengaturan/konsumen_get_ajax') ?>",
          type: "POST"
        },
        columnDefs: [
          { targets: [0, 5, 6], className: 'text-center' },
          { targets: 6, orderable: false }
        ],
        columns: [
          { data: "no" },
          {
            data: "konsumen_nama",
            render: function (data, type, row) {
              const init = data ? data.substring(0, 2).toUpperCase() : 'NA';
              return `
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold me-2" style="width: 35px; height: 35px;">${init}</div>
                                <div>
                                    <div class="fw-bold text-dark">${data}</div>
                                    <small class="text-muted">ID: ${row.konsumen_id}</small>
                                </div>
                            </div>
                        `;
            }
          },
          {
            data: "konsumen_nohp",
            render: function (data) {
              return `<span class="text-muted"><i class="fas fa-phone me-1"></i>${data}</span>`;
            }
          },
          {
            data: "konsumen_alamat",
            render: function (data) {
              return `<span class="text-truncate d-block" style="max-width: 200px;" title="${data}">${data}</span>`;
            }
          },
          {
            data: "konsumen_email",
            render: function (data) {
              return data ? `<span class="text-muted"><i class="fas fa-envelope me-1"></i>${data}</span>` : '-';
            }
          },
          {
            data: "status",
            render: function (data) {
              return (data == 1)
                ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>'
                : '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Non-Aktif</span>';
            }
          },
          {
            data: null,
            render: function (data, type, row) {
              return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-warning btn-edit-konsumen"
                                    data-id="${row.konsumen_id}"
                                    data-nama="${row.konsumen_nama || ''}"
                                    data-alamat="${row.konsumen_alamat || ''}"
                                    data-nohp="${row.konsumen_nohp || ''}"
                                    data-email="${row.konsumen_email || ''}"
                                    data-status="${row.status || ''}"
                                    data-tipe="${row.tipe_konsumen || 'B2C'}"
                                    data-perusahaan="${row.perusahaan || ''}"
                                    data-npwp="${row.npwp || ''}"
                                    data-pic="${row.pic_nama || ''}"
                                    data-kredit="${row.kredit_limit || '0'}"
                                    data-top="${row.top_hari || '0'}"
                                    data-kodepos="${row.kode_pos || ''}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditKonsumen">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <a href="<?= base_url('pengaturan/konsumen_delete/') ?>${row.konsumen_id}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        `;
            }
          }
        ],
        language: {
          search: "",
          searchPlaceholder: "Cari...",
          paginate: {
            previous: "<",
            next: ">"
          }
        },
        drawCallback: function () {
          updateStats();
          $('.dataTables_paginate > .pagination').addClass('pagination-sm');
        }
      });
    }

    $(document).on('click', '.btn-edit-konsumen', function () {
      $('#edit_id').val($(this).data('id'));
      $('#edit_nama').val($(this).data('nama'));
      $('#edit_alamat').val($(this).data('alamat'));
      $('#edit_nohp').val($(this).data('nohp'));
      $('#edit_email').val($(this).data('email'));
      $('#edit_status').val($(this).data('status'));
      $('#edit_kode_pos').val($(this).data('kodepos'));
      
      const tipe = $(this).data('tipe');
      $('#edit_tipe_konsumen').val(tipe);
      
      $('#edit_perusahaan').val($(this).data('perusahaan'));
      $('#edit_npwp').val($(this).data('npwp'));
      $('#edit_pic_nama').val($(this).data('pic'));
      $('#edit_kredit_limit').val($(this).data('kredit'));
      $('#edit_top_hari').val($(this).data('top'));

      toggleB2BFields('edit');
    });

    $('#searchInput').on('keyup', function () {
      tableKonsumen.search(this.value).draw();
    });

    $('#statusFilter').on('change', function () {
      // Note: Column index 5 is Status
      const status = $(this).val();
      tableKonsumen.column(5).search(status).draw();
    });

    // Simple stats update logic based on visible rows (limitation of server-side)
    // For specific stats like "Active/Inactive" globally, backend stats are better.
    function updateStats() {
      const data = tableKonsumen.rows().data().toArray();
      let active = 0, inactive = 0, email = 0;

      data.forEach(row => {
        if (row.status == 1) active++; else inactive++;
        if (row.konsumen_email) email++;
      });

      // Uncomment if you want live update from table view
      // $('#active-customers').text(active);
      // $('#inactive-customers').text(inactive);
      // $('#with-email').text(email);
    }
  });

  function resetFilters() {
    $('#searchInput').val('');
    $('#statusFilter').val('');
    tableKonsumen.search('').columns().search('').draw();
  }

  function exportData() {
    // Basic export from visible table
    let csv = "Nama,HP,Email,Alamat,Status\n";
    tableKonsumen.rows().data().each(function (row) {
      let status = row.status == 1 ? 'Aktif' : 'Non-Aktif';
      csv += `"${row.konsumen_nama}","${row.konsumen_nohp}","${row.konsumen_email}","${row.konsumen_alamat}","${status}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'konsumen_export.csv';
    a.click();
  }

  function checkDuplicates() {
    const modal = new bootstrap.Modal(document.getElementById('modalDuplikasi'));
    modal.show();
    const container = document.getElementById('duplicateResults');
    fetch('<?= base_url("pengaturan/konsumen_check_duplicates_ajax") ?>')
      .then(response => response.json())
      .then(data => {
        if (data.length === 0) {
          container.innerHTML = `<div class="text-center py-5"><i class="fas fa-check-circle text-success fs-1 mb-3"></i><h6 class="fw-800">Luar Biasa!</h6><p class="text-muted">Tidak ditemukan data duplikat.</p></div>`;
          return;
        }
        let html = `<table class="table table-sm table-hover align-middle"><thead class="table-light"><tr><th>Identitas Duplikat</th><th class="text-center">Jumlah</th><th>Contoh Nama / Detail</th></tr></thead><tbody>`;
        data.forEach(item => {
          html += `<tr><td><span class="badge bg-light text-dark border fw-bold">${item.key_val}</span><br><small class="text-muted text-uppercase" style="font-size: 10px;">Berdasarkan ${item.type}</small></td><td class="text-center"><span class="badge bg-danger rounded-pill">${item.jumlah}x</span></td><td><div class="small fw-bold text-truncate" style="max-width: 250px;">${item.details}</div></td></tr>`;
        });
        html += `</tbody></table>`;
        container.innerHTML = html;
      })
      .catch(error => {
        container.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan saat mengecek data.</div>`;
      });
  }

  function printData() {
    // Print window logic...
    alert('Silakan gunakan fitur Print browser (Ctrl+P) untuk hasil terbaik.');
    // Or implement window.open logic here similar to previous
  }

  function toggleB2BFields(mode) {
    const tipe = document.getElementById(mode === 'add' ? 'tipe_konsumen_add' : 'edit_tipe_konsumen').value;
    const b2bFields = document.querySelectorAll('.b2b-fields-' + mode);
    
    if (tipe === 'B2B') {
      b2bFields.forEach(el => el.style.display = 'block');
    } else {
      b2bFields.forEach(el => el.style.display = 'none');
    }
  }
</script>