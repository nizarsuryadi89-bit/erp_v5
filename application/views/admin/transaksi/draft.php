<div class="app-content d-block">
    <div class="container-fluid py-4">

        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm animate__animated animate__fadeIn"
                    style="border-radius: 15px; background: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="text-white mb-1 fw-bold">Draft SPK Saya</h4>
                                <p class="text-white-50 mb-0">Kelola dan lanjutkan pengerjaan SPK yang belum sempat Anda
                                    selesaikan.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="<?= base_url('transaksi/createspk') ?>"
                                    class="btn btn-primary btn-lg shadow-sm border-0 px-4"
                                    style="border-radius: 10px; background: #3498db;">
                                    <i class="fas fa-plus-circle me-2"></i>Buat SPK Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 animate__animated animate__slideInDown"
                role="alert" style="border-radius: 10px; border-left: 5px solid #2ecc71 !important;">
                <i class="fas fa-check-circle me-2"></i>
                <?= $this->session->flashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 animate__animated animate__shakeX"
                role="alert" style="border-radius: 10px; border-left: 5px solid #e74c3c !important;">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= $this->session->flashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm animate__animated animate__fadeInUp"
            style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0 fw-bold text-dark me-3"><i class="fas fa-list me-2 text-primary"></i>Daftar
                                Draft Tersimpan</h6>
                            <!-- Bulk Action Container -->
                            <div id="bulkActionDraft" style="display: none;" class="animate__animated animate__fadeIn">
                                <button type="button" id="btnHapusDraftMassal"
                                    class="btn btn-sm btn-danger px-3 shadow-sm border-0"
                                    style="border-radius: 20px; background: #e74c3c;">
                                    <i class="fas fa-trash-alt me-2"></i>Hapus Terpilih (<span
                                        id="selectedDraftCount">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-light text-muted border px-3 py-2" style="border-radius: 20px;">
                            <?= count($drafts) ?> Total Draft
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-4 py-3 text-center" style="width: 50px;">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="checkAllDraft">
                                    </div>
                                </th>
                                <th class="py-3 text-muted small fw-bold text-uppercase" style="width: 150px;">
                                    Tanggal & Waktu</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Nomor SPK</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Pelanggan</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Metode Pesan</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-center">Jumlah Item</th>
                                <th class="pe-4 py-3 text-muted small fw-bold text-uppercase text-end">Aksi Lanjutan
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($drafts)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-5">
                                            <div class="mb-3">
                                                <i class="fas fa-folder-open fa-4x text-light-emphasis opacity-25"></i>
                                            </div>
                                            <h5 class="text-secondary fw-bold">Belum ada Draft</h5>
                                            <p class="text-muted small">Semua SPK yang Anda buat telah diselesaikan atau
                                                Anda belum membuat SPK baru.</p>
                                            <a href="<?= base_url('transaksi/createspk') ?>"
                                                class="btn btn-outline-primary btn-sm mt-2 px-4"
                                                style="border-radius: 20px;">
                                                Mulai Buat Sekarang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($drafts as $draft): ?>
                                    <tr>
                                        <td class="ps-4 text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input check-draft" type="checkbox"
                                                    value="<?= $draft->nospk ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                <?= date('d M Y', strtotime($draft->spk_time)) ?>
                                            </div>
                                            <small class="text-muted"><i class="far fa-clock me-1"></i>
                                                <?= date('H:i', strtotime($draft->spk_time)) ?> WIB
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-2"
                                                    style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <span class="fw-bold text-dark">#
                                                    <?= $draft->nospk ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="fw-bold text-dark" id="konsumen-nama-<?= $draft->nospk ?>">
                                                        <?= $draft->konsumen_nama ?>
                                                    </div>
                                                    <small class="text-muted">ID: <?= $draft->konsumen_id ?></small>
                                                </div>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary border-0 rounded-circle p-1 ms-2"
                                                    onclick="openEditKonsumen('<?= $draft->nospk ?>', '<?= $draft->konsumen_id ?>', '<?= htmlspecialchars($draft->konsumen_nama) ?>')"
                                                    title="Ganti Konsumen">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = 'bg-info';
                                            $icon = 'fa-shopping-cart';
                                            if (stripos($draft->nama_jenis_transaksi, 'Marketplace') !== false) {
                                                $badgeClass = 'bg-primary';
                                                $icon = 'fa-store';
                                            }
                                            ?>
                                            <span
                                                class="badge <?= $badgeClass ?> bg-opacity-10 text-<?= str_replace('bg-', '', $badgeClass) ?> px-3 py-2"
                                                style="border-radius: 6px;">
                                                <i class="fas <?= $icon ?> me-1"></i>
                                                <?= $draft->nama_jenis_transaksi ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn btn-sm btn-light fw-bold border-0 px-3"
                                                style="border-radius: 20px; pointer-events: none;">
                                                <?= $draft->total_items ?> Items
                                            </div>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                                <a href="<?= base_url('transaksi/createspk/' . $draft->nospk) ?>"
                                                    class="btn btn-success px-3 py-2" title="Lanjutkan Pengerjaan">
                                                    <i class="fas fa-play me-2"></i>Lanjutkan
                                                </a>
                                                <button type="button" onclick="confirmDelete('<?= $draft->nospk ?>')"
                                                    class="btn btn-danger px-3 py-2" title="Hapus Draft">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1 text-primary"></i>
                    Draft SPK hanya dapat dilihat dan dikelola oleh Deskprint yang membuatnya.
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ganti Konsumen -->
<div class="modal fade" id="modalEditKonsumen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Ganti Konsumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-4">Pilih konsumen baru untuk SPK <strong id="modal-nospk-label"></strong>
                </p>
                <form id="formEditKonsumen">
                    <input type="hidden" name="nospk" id="edit-nospk">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Cari Konsumen</label>
                        <select name="konsumen_id" id="konsumenSelectEdit" class="form-control"
                            style="width:100%;"></select>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                    style="border-radius: 10px;">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanKonsumen" style="border-radius: 10px;">
                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-top: none;
    }

    .table tbody td {
        padding-top: 1rem;
        padding-bottom: 1rem;
        border-bottom-color: #f8f9fa;
    }

    .btn-group .btn {
        font-size: 0.85rem;
        font-weight: 600;
        border: none;
    }

    .card-hover:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    .badge {
        font-weight: 600;
    }
</style>

<!-- Load SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(nospk) {
        Swal.fire({
            title: 'Hapus Draft?',
            html: "Draft SPK <strong>#" + nospk + "</strong> akan dihapus permanen beserta item didalamnya.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-trash-alt me-2"></i>Ya, Hapus!',
            cancelButtonText: 'Batal',
            borderRadius: '15px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('transaksi/delete_draft/') ?>" + nospk;
            }
        });
    }

    // Modal Edit Konsumen Logic
    let modalEditKonsumen;

    function openEditKonsumen(nospk, id, nama) {
        $('#edit-nospk').val(nospk);
        $('#modal-nospk-label').text('#' + nospk);

        // Clear and initialize select2
        $('#konsumenSelectEdit').empty().append(new Option(nama, id, true, true)).trigger('change');

        if (modalEditKonsumen) {
            modalEditKonsumen.show();
        }
    }

    $(document).ready(function () {
        // Initialize Modal
        modalEditKonsumen = new bootstrap.Modal(document.getElementById('modalEditKonsumen'));

        $('#konsumenSelectEdit').select2({
            dropdownParent: $('#modalEditKonsumen'),
            placeholder: 'Cari konsumen...',
            minimumInputLength: 1,
            ajax: {
                url: '<?= site_url("pengaturan/konsumen_search_ajax") ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || ''
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.konsumen_id || item.id,
                                text: (item.konsumen_nama || item.nama || '') + (item.konsumen_nohp ? ' (' + item.konsumen_nohp + ')' : '')
                            };
                        })
                    };
                },
                cache: true
            }
        });

        $('#btnSimpanKonsumen').click(function () {
            const formData = $('#formEditKonsumen').serialize();
            const nospk = $('#edit-nospk').val();
            const selectedData = $('#konsumenSelectEdit').select2('data')[0];

            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

            $.ajax({
                url: '<?= base_url("transaksi/update_spk_konsumen") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (res) {
                    $('#btnSimpanKonsumen').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan Perubahan');
                    if (res.status === 'success') {
                        modalEditKonsumen.hide();
                        // Update UI
                        $('#konsumen-nama-' + nospk).text(selectedData.text.split(' (')[0]);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                },
                error: function () {
                    $('#btnSimpanKonsumen').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan Perubahan');
                    Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                }
            });
        });
    });

    // Bulk Delete logic
    function updateBulkActionDraftUI() {
        const checkedCount = $('.check-draft:checked').length;
        if (checkedCount > 0) {
            $('#bulkActionDraft').fadeIn();
            $('#selectedDraftCount').text(checkedCount);
        } else {
            $('#bulkActionDraft').fadeOut();
            $('#checkAllDraft').prop('checked', false);
        }
    }

    $(document).on('change', '#checkAllDraft', function () {
        $('.check-draft').prop('checked', $(this).is(':checked'));
        updateBulkActionDraftUI();
    });

    $(document).on('change', '.check-draft', function () {
        updateBulkActionDraftUI();
    });

    $('#btnHapusDraftMassal').click(function () {
        const selectedDrafts = [];
        $('.check-draft:checked').each(function () {
            selectedDrafts.push($(this).val());
        });

        if (selectedDrafts.length === 0) return;

        Swal.fire({
            title: 'Hapus ' + selectedDrafts.length + ' Draft?',
            text: "Draft yang dipilih akan dihapus permanen beserta isinya.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-trash-alt me-2"></i>Ya, Hapus Semua!',
            cancelButtonText: 'Batal',
            borderRadius: '15px'
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menghapus...');

                $.ajax({
                    url: '<?= base_url("transaksi/bulk_delete_draft") ?>',
                    type: 'POST',
                    data: { nospk: selectedDrafts },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            $('#btnHapusDraftMassal').prop('disabled', false).html('<i class="fas fa-trash-alt me-2"></i>Hapus Terpilih');
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    },
                    error: function () {
                        $('#btnHapusDraftMassal').prop('disabled', false).html('<i class="fas fa-trash-alt me-2"></i>Hapus Terpilih');
                        Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                    }
                });
            }
        });
    });
</script>