<style>
  body,
  .app-content {
    background: #f5f5f5;
    color: #222;
  }

  .konsumen-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.10);
    border: 1px solid #d1d1d1;
  }

  .konsumen-table th,
  .konsumen-table td {
    font-size: 0.97rem;
    color: #222;
    vertical-align: middle;
    background: #fff;
  }

  .konsumen-table th {
    background: #222 !important;
    color: #fff !important;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    border-bottom: 2px solid #d1d1d1;
    font-weight: 600;
    letter-spacing: 0.03em;
    text-align: center;
  }

  .konsumen-table tr {
    background: #fff;
    transition: background 0.15s;
  }

  .konsumen-table tr:hover {
    background: #ededed;
  }

  .btn-mono {
    background: #ededed;
    color: #222;
    border: 1px solid #bdbdbd;
    border-radius: 8px;
    font-size: 0.93rem;
    padding: 0.35rem 0.8rem;
    transition: background 0.2s, color 0.2s, border 0.2s;
    box-shadow: none;
  }

  .btn-mono:hover,
  .btn-mono:focus {
    background: #222;
    color: #fff;
    border: 1px solid #222;
  }

  .btn-primary {
    background: #222;
    color: #fff;
    border: 1px solid #222;
    border-radius: 8px;
    font-size: 0.97rem;
    padding: 0.4rem 1.2rem;
    transition: background 0.2s, color 0.2s, border 0.2s;
  }

  .btn-primary:hover,
  .btn-primary:focus {
    background: #444;
    color: #fff;
    border: 1px solid #444;
  }

  .modal-content {
    border-radius: 18px;
    border: 2px solid #d1d1d1;
    box-shadow: 0 4px 32px rgba(0, 0, 0, 0.13);
    background: #fff;
    color: #222;
  }

  .modal-header,
  .modal-footer {
    background: #f5f5f5;
    border-bottom: 1px solid #d1d1d1;
    border-top-left-radius: 18px;
    border-top-right-radius: 18px;
  }

  .modal-title {
    color: #222;
    font-weight: 700;
    letter-spacing: 0.02em;
  }

  .form-control,
  textarea,
  .form-select {
    background: #ededed;
    border-radius: 8px;
    border: 1px solid #bdbdbd;
    font-size: 0.97rem;
    color: #222;
  }

  .form-control:focus,
  textarea:focus,
  .form-select:focus {
    border-color: #222;
    box-shadow: 0 0 0 1px #2222;
    background: #fff;
    color: #222;
  }

  .modal-lg {
    max-width: 900px;
  }

  @media (max-width: 991.98px) {
    .modal-lg {
      max-width: 98vw;
      margin: 0.5rem auto;
    }
  }

  @media (max-width: 575.98px) {

    .konsumen-table th,
    .konsumen-table td {
      font-size: 0.89rem;
    }

    .modal-content {
      border-radius: 10px;
    }

    .modal-lg {
      max-width: 99vw;
    }
  }
</style>
<div class="app-content">
  <div class="container-fluid">
    <div class="konsumen-card card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0">
        <div class="row mb-12 w-100">
          <div class="col-sm-12 d-flex gap-2 flex-wrap">

            <button type="button" class="btn btn-mono" onclick="history.back()">Kembali</button>
            <a href="#" class="btn btn-mono disabled">Jumlah Konsumen : <?= $jml_konsumen ?></a>
            <button class="btn btn-mono" data-bs-toggle="modal" data-bs-target="#modalKonsumen">Tambah Konsumen</button>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="konsumen-table table table-bordered table-hover align-middle mb-0" id="datatable-detailss">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th width="20%">No HP</th>
                <th width="30%">Alamat</th>
                <th>Email</th>
                <th>Status</th>
                <th width="10%">Aksi</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal Tambah Konsumen -->
<div class="modal fade" id="modalKonsumen" tabindex="-1" aria-labelledby="modalKonsumenLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form id="formKonsumen" method="POST" action="<?= base_url('pengaturan/konsumen_add') ?>">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalKonsumenLabel"><i class="bi bi-person-plus me-2"></i>Tambah Konsumen</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-md-6">
            <label for="nama" class="form-label">Nama Konsumen</label>
            <input type="text" class="form-control" name="konsumen_nama" required>
          </div>
          <div class="col-md-6">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" name="konsumen_alamat" required>
          </div>
          <div class="col-md-6">
            <label for="nohp" class="form-label">Nomor WhatsApp</label>
            <input type="tel" class="form-control" id="wa_number" name="konsumen_nohp" placeholder="+6281234567890"
              required>
            <div id="wa_error" class="text-danger small mt-1"></div>
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="konsumen_email" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
          <button type="button" class="btn btn-mono" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Modal Edit Konsumen -->
<div class="modal fade" id="modalEditKonsumen" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form method="POST" action="<?= base_url('pengaturan/konsumen_edit') ?>">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditLabel"><i class="bi bi-pencil-square me-2"></i>Edit Konsumen</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body row g-3">
          <input type="hidden" name="konsumen_id" id="edit_id" required>
          <div class="col-md-6">
            <label>Nama Konsumen</label>
            <input type="text" name="konsumen_nama" id="edit_nama" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label>Alamat</label>
            <input type="text" name="konsumen_alamat" id="edit_alamat" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label>No HP</label>
            <input type="tel" name="konsumen_nohp" id="edit_nohp" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label>Email</label>
            <input type="email" name="konsumen_email" id="edit_email" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label>Status</label>
            <select name="status" id="edit_status" class="form-select" required>
              <option value="1">Aktif</option>
              <option value="0">Tidak Aktif</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update</button>
          <button type="button" class="btn btn-mono" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
  document.getElementById('formKonsumen').addEventListener('submit', function (e) {
    const waInput = document.getElementById('wa_number');
    const errorDiv = document.getElementById('wa_error');
    const waValue = waInput.value.trim();
    const valid = /^\+\d{8,15}$/.test(waValue);
    errorDiv.textContent = '';
    if (!valid) {
      e.preventDefault();
      errorDiv.textContent = 'Format nomor harus +62 dan 8–15 digit angka.';
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
        autoWidth: false,
        ajax: {
          url: "<?= base_url('pengaturan/konsumen_get_ajax') ?>",
          type: "POST"
        },
        columnDefs: [
          { targets: [0, 6], className: 'text-center' }
        ],
        columns: [
          { data: "no" },
          { data: "konsumen_nama" },
          { data: "konsumen_nohp" },
          { data: "konsumen_alamat" },
          { data: "konsumen_email" },
          { data: "status" },
          {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
              return `
                            <button type="button" class="btn btn-mono btn-edit-konsumen"
                                data-id     ="${row.konsumen_id}"
                                data-nama   ="${row.konsumen_nama}"
                                data-alamat ="${row.konsumen_alamat}"
                                data-nohp   ="${row.konsumen_nohp}"
                                data-email  ="${row.konsumen_email}"
                                data-status ="${row.status}"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditKonsumen"
                            >
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        `;
            }
          }
        ]
      });
    }
    $(document).on('click', '.btn-edit-konsumen', function () {
      $('#edit_id').val($(this).data('id'));
      $('#edit_nama').val($(this).data('nama'));
      $('#edit_alamat').val($(this).data('alamat'));
      $('#edit_nohp').val($(this).data('nohp'));
      $('#edit_email').val($(this).data('email'));
      $('#edit_status').val($(this).data('status'));
      // Modal show handled by data-bs-toggle
    });
  });
</script>