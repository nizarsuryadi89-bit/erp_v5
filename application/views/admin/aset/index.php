<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- HEADER SECTION REPLACED WITH STANDARD MODULE HEADER -->
        <?php $this->load->view('layout_admin/header_modul'); ?>


        <!-- STATISTICS SECTION -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-12 col-md-4">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--info-gradient)">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Total Aset Terdaftar</div>
                        <div class="stat-v2-value">
                            <?= number_format($total_rows) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Total Nilai Aset</div>
                        <div class="stat-v2-value text-success">
                            Rp
                            <?= number_format(array_sum(array_column($tampilData ?? [], 'aset_harga')), 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-boxes me-2"></i> Daftar Aset Perusahaan</h5>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('pengaturan/aset_print_all') ?>" target="_blank"
                        class="btn btn-sm btn-dark rounded-pill px-3 fw-bold shadow-sm">
                        <i class="fas fa-print text-white me-1"></i> Cetak Semua Label
                    </a>
                    <button type="button" class="btn btn-sm btn-white text-dark rounded-pill px-3 fw-bold shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#modalAddAset">
                        <i class="fas fa-plus text-success me-1"></i> Tambah Aset Baru
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="asetTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Aset</th>
                            <th>Kategori</th>
                            <th>Lokasi / Cabang</th>
                            <th class="text-end">Nilai / Harga</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (!empty($tampilData)):
                            foreach ($tampilData as $dt):
                                ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted">
                                        <?= $no++ ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($dt->aset_foto) && file_exists(FCPATH . 'assets/images/aset/' . $dt->aset_foto)): ?>
                                                <a href="#"
                                                    onclick="showPhotoModal('<?= base_url('assets/images/aset/' . $dt->aset_foto) ?>', '<?= addslashes($dt->aset_nama) ?>'); return false;">
                                                    <img src="<?= base_url('assets/images/aset/' . $dt->aset_foto) ?>"
                                                        class="rounded-3 shadow-sm me-3"
                                                        style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #fff;">
                                                </a>
                                            <?php else: ?>
                                                <div class="rounded-3 shadow-sm me-3 bg-light d-flex align-items-center justify-content-center text-muted"
                                                    style="width: 50px; height: 50px; border: 2px solid #fff;">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-800 text-dark">
                                                    <?= strtoupper($dt->aset_nama) ?>
                                                </div>
                                                <div class="text-muted small text-truncate" style="max-width:200px">
                                                    <?= $dt->aset_keterangan ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2">
                                            <?= $dt->aset_kategori ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center text-dark fw-bold small text-uppercase ls-1">
                                            <i class="fas fa-building me-2 text-muted opacity-50"></i>
                                            <?= $dt->nama_perusahaan ?? 'Pusat' ?>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        Rp
                                        <?= number_format($dt->aset_harga, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn-action-premium"
                                                style="background:#f1f5f9; color:#0f172a;" title="Tampilkan QR Code"
                                                onclick="showQR('<?= $dt->aset_id ?>', '<?= addslashes($dt->aset_nama) ?>')">
                                                <i class="fas fa-qrcode"></i>
                                            </button>
                                            <button type="button" class="btn-action-premium btn-edit-p" title="Ubah Data"
                                                onclick="openEditModal('<?= $dt->aset_id ?>', '<?= addslashes($dt->aset_nama) ?>', '<?= addslashes($dt->aset_kategori) ?>', '<?= $dt->aset_harga ?>', '<?= addslashes($dt->aset_keterangan) ?>', '<?= $dt->perusahaan_id ?>')">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button type="button"
                                                onclick="confirmDelete('<?= base_url('pengaturan/aset_delete/' . $dt->aset_id) ?>')"
                                                class="btn-action-premium btn-delete-p" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($tampilData)): ?>
                <div class="p-5 text-center">
                    <i class="fas fa-box-open fs-1 text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-800 text-muted">Belum ada data Aset yang tersedia.</h6>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL ADD ASET -->
<div class="modal fade" id="modalAddAset" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-plus-circle me-2"></i> Tambah Data Aset
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('pengaturan/aset_simpan') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Nama Aset / Barang</label>
                            <input name="aset_nama" type="text" class="form-control"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                placeholder="Misal: AC Daikin 2 PK, Laptop Dell..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Kategori Aset</label>
                            <input name="aset_kategori" type="text" class="form-control"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                placeholder="Elektronik, Furniture, Kendaraan..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Harga Beli / Nilai (Rp)</label>
                            <input name="aset_harga" type="text" class="form-control ribuan"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Lokasi / Cabang</label>
                            <select name="perusahaan_id" class="form-select"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                required>
                                <?php foreach ($list_perusahaan as $p): ?>
                                    <option value="<?= $p->id_perusahaan ?>">
                                        <?= $p->nama_perusahaan ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Foto Aset</label>
                            <input name="aset_foto" type="file" class="form-control" accept="image/*"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                        </div>
                        <div class="col-12">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Keterangan / Serial
                                Number</label>
                            <textarea name="aset_keterangan" class="form-control" rows="2"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                placeholder="Detail aset, tahun pembelian, kondis, dll..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                        style="background: var(--primary-gradient)">
                        SIMPAN ASET
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT ASET -->
<div class="modal fade" id="modalEditAset" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-edit me-2"></i> Update Data Aset
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('pengaturan/aset_update') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="aset_id" id="edit_aset_id">
                <div class="modal-body p-4">
                    <div class="mb-3 d-flex justify-content-end">
                        <span class="badge bg-light text-dark border">ID: <span id="display_aset_id"></span></span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Nama Aset / Barang</label>
                            <input name="aset_nama" id="edit_aset_nama" type="text" class="form-control"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Kategori Aset</label>
                            <input name="aset_kategori" id="edit_aset_kategori" type="text" class="form-control"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Harga Beli / Nilai (Rp)</label>
                            <input name="aset_harga" id="edit_aset_harga" type="text" class="form-control ribuan"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Lokasi / Cabang</label>
                            <select name="perusahaan_id" id="edit_perusahaan_id" class="form-select"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                required>
                                <?php foreach ($list_perusahaan as $p): ?>
                                    <option value="<?= $p->id_perusahaan ?>">
                                        <?= $p->nama_perusahaan ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Foto Aset <span
                                    class="text-danger fw-normal" style="text-transform:none;">(Abaikan jika tidak
                                    diubah)</span></label>
                            <input name="aset_foto" type="file" class="form-control" accept="image/*"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                        </div>
                        <div class="col-12">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Keterangan</label>
                            <textarea name="aset_keterangan" id="edit_aset_keterangan" class="form-control" rows="2"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                        style="background: var(--primary-gradient)">
                        UPDATE DATA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VIEW QR CODE -->
<div class="modal fade" id="modalViewQR" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-3 border-0">
                <h6 class="modal-title fw-bold text-uppercase mx-auto" id="qr_asset_name">LABEL INVENTARIS</h6>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <img id="qr_image" src="" alt="QR Code Aset" class="img-fluid border p-2 rounded shadow-sm"
                        style="max-width: 200px;">
                </div>
                <div class="fw-bold" style="color: #0f172a; font-size: 1.1rem; letter-spacing: 1px;"><span
                        id="qr_asset_id"></span></div>
                <div class="text-muted mt-2 small fw-semibold">Scan QR untuk melihat detail aset & lokasi.</div>
            </div>
            <div class="modal-footer border-0 p-3 pt-0 justify-content-center bg-light rounded-bottom-4">
                <a href="#" id="btn_print_qr" target="_blank"
                    class="btn btn-primary rounded-pill px-4 py-2 w-100 fw-bold shadow-sm"
                    style="background: var(--primary-gradient)">
                    <i class="fas fa-print me-1"></i> CETAK STIKER LABEL
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL VIEW VIEW FOTO ASET -->
<div class="modal fade" id="modalViewFoto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white p-3 border-0">
                <h6 class="modal-title fw-bold text-uppercase mx-auto" id="foto_aset_name">FOTO ASET</h6>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-light">
                <img id="foto_image" src="" alt="Foto Aset" class="img-fluid"
                    style="max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<!-- Auto format number script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var rupiahElements = document.querySelectorAll('.ribuan');
        rupiahElements.forEach(function (element) {
            element.addEventListener('input', function (e) {
                this.value = formatNumber(this.value);
            });
        });

        function formatNumber(angka) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }
    });
</script>

<script>
    function formatRP(angka) {
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    function openEditModal(id, nama, kategori, harga, ket, perusahaan_id) {
        $('#edit_aset_id').val(id);
        $('#display_aset_id').text(id);
        $('#edit_aset_nama').val(nama);
        $('#edit_aset_kategori').val(kategori);
        $('#edit_aset_harga').val(formatRP(harga));
        $('#edit_aset_keterangan').val(ket);
        $('#edit_perusahaan_id').val(perusahaan_id);

        var myModal = new bootstrap.Modal(document.getElementById('modalEditAset'));
        myModal.show();
    }

    function showQR(id, nama) {
        $('#qr_asset_id').text('ASET-' + id.toString().padStart(4, '0'));
        $('#qr_asset_name').text(nama.toUpperCase());

        // Ensure image is refetched instead of fully cached if changed
        var qrUrl = '<?= base_url('assets/qrcode/aset_') ?>' + id + '.png?v=' + new Date().getTime();
        $('#qr_image').attr('src', qrUrl);

        // Redirect to print controller instead of raw image
        var printUrl = '<?= base_url('pengaturan/aset_print_label/') ?>' + id;
        $('#btn_print_qr').attr('href', printUrl);

        var myModal = new bootstrap.Modal(document.getElementById('modalViewQR'));
        myModal.show();
    }

    function showPhotoModal(url, nama) {
        $('#foto_aset_name').text(nama.toUpperCase());
        $('#foto_image').attr('src', url);

        var myModal = new bootstrap.Modal(document.getElementById('modalViewFoto'));
        myModal.show();
    }

    function confirmDelete(url) {
        Swal.fire({
            title: 'Hapus Aset?',
            text: "Data Aset yang dihapus tidak dapat dipulihkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f72585',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATAL',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }

    $(document).ready(function () {
        if ($.fn.DataTable) {
            $('#asetTable').DataTable({
                "pageLength": 10,
                "language": { "sSearch": "Cari:", "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" } },
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }

    });

    <?php if ($this->session->flashdata('success')): ?>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: 'success',
            title: '<?= addslashes($this->session->flashdata('success')) ?>'
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        const ToastErr = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        ToastErr.fire({
            icon: 'error',
            title: '<?= addslashes($this->session->flashdata('error')) ?>'
        });
    <?php endif; ?>
</script>