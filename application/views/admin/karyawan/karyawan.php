<style>
    /* Ultra-Minimalist Database Karyawan Design */
    :root {
        --tgm-primary: #2563eb;
        --tgm-primary-dark: #1d4ed8;
        --tgm-rose: #e11d48;
        --tgm-emerald: #059669;
        --tgm-slate-50: #f8fafc;
        --tgm-slate-100: #f1f5f9;
        --tgm-slate-200: #e2e8f0;
        --tgm-slate-700: #334155;
        --tgm-slate-900: #0f172a;
    }

    /* Custom Color Utilities */
    .text-primary-tgm {
        color: var(--tgm-primary) !important;
    }

    .bg-primary-tgm {
        background-color: var(--tgm-primary) !important;
    }

    .border-primary-tgm {
        border-color: var(--tgm-primary) !important;
    }

    .text-rose {
        color: #e11d48 !important;
    }

    .bg-rose {
        background-color: #e11d48 !important;
    }

    .border-rose {
        border-color: #e11d48 !important;
    }

    .text-emerald {
        color: #059669 !important;
    }

    .bg-emerald {
        background-color: #059669 !important;
    }

    .border-emerald {
        border-color: #059669 !important;
    }

    .btn-primary-tgm {
        background-color: var(--tgm-primary);
        color: white;
        border: none;
    }

    .btn-primary-tgm:hover {
        background-color: var(--tgm-primary-dark);
        color: white;
    }



    .clean-card {
        background: white;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
    }

    .stats-minimal {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        padding: 1.5rem;
        border-top: 3px solid var(--tgm-slate-900);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    .stat-box.primary {
        border-color: var(--tgm-primary);
    }

    .stat-box.success {
        border-color: #059669;
    }

    .stat-box.warning {
        border-color: #f59e0b;
    }

    .stat-box.danger {
        border-color: var(--tgm-rose);
    }

    .table-simple thead th {
        background: #fdfdfd;
        color: var(--tgm-slate-700);
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 15px 12px;
        border-top: none;
        border-bottom: 2px solid var(--tgm-slate-100);
    }

    .table-simple tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        color: var(--tgm-slate-900);
        border-bottom: 1px solid var(--tgm-slate-100);
        font-size: 0.9rem;
    }



    .avatar-clean {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid var(--tgm-slate-200);
    }

    .badge-soft {
        font-size: 0.7rem;
        padding: 5px 12px;
        font-weight: 800;
        border-radius: 5px;
        text-transform: uppercase;
    }

    .bg-slate-50 {
        background-color: #f8fafc !important;
    }

    .x-small {
        font-size: 0.75rem;
    }
</style>

<div class="app-content">
    <!-- Header -->
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-900 mb-0 ls-1">DATABASE <span class="text-primary-tgm">KARYAWAN</span></h3>
                <p class="small text-muted mb-0">Manajemen record SDM, posisi, dan riwayat administrasi.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnExportExcel"
                    class="btn btn-success rounded-pill px-4 fw-900 small shadow-sm">
                    <i class="bi bi-file-earmark-excel-fill me-2"></i> EXPORT EXCEL
                </button>
                <button type="button" id="btnExportPdf"
                    class="btn btn-danger rounded-pill px-4 fw-900 small shadow-sm">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i> EXPORT PDF
                </button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalSOPKaryawan"
                    class="btn btn-white border rounded-pill px-4 fw-900 small shadow-sm">
                    <i class="bi bi-journal-text me-2 text-primary-tgm"></i> PANDUAN SOP
                </button>
                <button type="button" onclick="addKaryawan()"
                    class="btn btn-primary-tgm rounded-pill px-4 fw-900 small shadow-sm">
                    <i class="bi bi-person-plus-fill me-2"></i> TAMBAH KARYAWAN
                </button>
            </div>
        </div>
        <!-- Advanced Filters -->
        <?php $this->load->view('admin/karyawan/components/karyawan_filters'); ?>

        <!-- Initial Filters for AJAX -->
        <div id="initial-filters" data-gender="<?= $initial_gender ?? '' ?>" data-status="<?= $initial_status ?? '' ?>"
            data-divisi="<?= $initial_divisi ?? '' ?>" data-jabatan="<?= $initial_jabatan ?? '' ?>"
            data-perusahaan="<?= $initial_perusahaan ?? '' ?>">
        </div>

        <!-- Page Content -->
        <?php $this->load->view('admin/karyawan/components/karyawan_table'); ?>
    </div>
</div>
</div>
</div>

<!-- =============================================================
     MODALS
     ============================================================= -->
<?php $this->load->view('admin/karyawan/components/karyawan_modals'); ?>

<!-- Scripts Section -->
<?php $this->load->view('admin/karyawan/components/karyawan_js'); ?>