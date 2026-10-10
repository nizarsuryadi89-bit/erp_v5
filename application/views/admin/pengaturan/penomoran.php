<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="app-content mt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 animate-in">
                <div class="base-card shadow-sm mb-4">
                    <div class="card-header-clean bg-white">
                        <i class="fas fa-file-invoice text-primary"></i>
                        <h6>Konfigurasi Penomoran Dokumen (Sequencing)</h6>
                        <div class="ms-auto">
                            <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                                <i class="fas fa-plus me-1"></i> Tambah Dokumen
                            </button>
                        </div>
                    </div>
                    <div class="p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Nama Dokumen</th>
                                        <th>Kode (Prefix)</th>
                                        <th>Format/Template</th>
                                        <th>Next Sequence</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prefixes as $p): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold">
                                                    <?= $p['name'] ?>
                                                </div>
                                                <span class="text-muted small">Standard Enterprise Format</span>
                                            </td>
                                            <td><span class="badge bg-secondary-subtle text-secondary">
                                                    <?= $p['code'] ?>
                                                </span></td>
                                            <td><code><?= htmlspecialchars($p['format']) ?></code></td>
                                            <td><span class="fw-bold text-primary">
                                                    <?= $p['next'] ?>
                                                </span></td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-light btn-sm rounded-circle"><i
                                                        class="fas fa-edit text-primary"></i></button>
                                                <button class="btn btn-light btn-sm rounded-circle"><i
                                                        class="fas fa-history text-muted"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center" style="border-radius: 12px;">
                    <i class="fas fa-info-circle me-3 fs-4"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">Tips Sequencing</h6>
                        <p class="mb-0 small">Gunakan placeholder <code>{YY}</code> untuk tahun, <code>{MONTH}</code>
                            untuk bulan, dan <code>{SEQ}</code> untuk nomor urut otomatis. Perubahan format akan berlaku
                            pada dokumen baru selanjutnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .base-card {
        background: #fff;
        border-radius: 15px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card-header-clean {
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .card-header-clean i {
        margin-right: 12px;
        font-size: 1.1rem;
    }

    .card-header-clean h6 {
        margin-bottom: 0;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.02em;
    }

    .animate-in {
        animation: fadeInUp 0.5s ease-out forwards;
        opacity: 0;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>