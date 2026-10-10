<!-- Header Section: Clean & Consistent -->
<div class="header-main bg-white border-bottom shadow-sm mb-4">
    <div class="container-fluid py-3 px-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-soft-primary text-primary rounded-circle mr-3 d-flex align-items-center justify-content-center"
                    style="width: 42px; height: 42px;">
                    <i class="fas fa-terminal"></i>
                </div>
                <div>
                    <h1 class="h5 font-weight-bold mb-0 text-dark">Log Pengembang</h1>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Audit Trail & Pembaruan Sistem</p>
                </div>
            </div>
            <div class="text-right d-none d-md-block">
                <div class="badge badge-light border text-muted px-3 py-2 rounded-pill small">
                    <i class="fas fa-calendar-alt mr-1 text-primary"></i> <?= date('d M Y') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">

            <!-- Quick Metric & Summary -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 12px;">
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        <div class="col-md-3 bg-white p-3 d-flex align-items-center border-right">
                            <div class="px-2">
                                <h6 class="font-weight-bold text-primary mb-1">
                                    <i class="fas fa-rocket mr-1"></i> Evolusi
                                </h6>
                                <p class="text-muted mb-0" style="font-size: 0.7rem; line-height: 1.2;">
                                    Fokus stabilitas navigasi & RBAC.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-9 bg-light p-2">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h5 class="font-weight-bold text-primary mb-0" style="font-size: 1.1rem;">99%
                                    </h5>
                                    <small class="text-uppercase font-weight-bold text-muted"
                                        style="font-size: 0.6rem; letter-spacing: 0.5px;">Keamanan</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="font-weight-bold text-success mb-0" style="font-size: 1.1rem;">Cepat
                                    </h5>
                                    <small class="text-uppercase font-weight-bold text-muted"
                                        style="font-size: 0.6rem; letter-spacing: 0.5px;">Performa</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="font-weight-bold text-info mb-0" style="font-size: 1.1rem;">Live</h5>
                                    <small class="text-uppercase font-weight-bold text-muted"
                                        style="font-size: 0.6rem; letter-spacing: 0.5px;">Audit Trail</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Container -->
            <div class="row g-3">
                <?php
                $logs = [
                    [
                        'version' => 'v5.2',
                        'title' => 'Warehouse SOP 6-Step Workflow & Konsolidasi Modul',
                        'date' => 'Feb 23, 2025',
                        'badge' => 'badge-success',
                        'icon' => 'fa-project-diagram',
                        'items' => [
                            ['tag' => 'bg-soft-primary text-primary', 'label' => 'SOP 6 Langkah', 'icon' => 'fa-stream', 'desc' => 'Implementasi komponen alur kerja 6-langkah (<code>v_material_workflow.php</code>) yang terpusat dan reusable. Alur mencakup: Induk → Varian → Opname → Pembelian → Dispatch → Monitoring.'],
                            ['tag' => 'bg-soft-success text-success', 'label' => 'Integrasi Penuh', 'icon' => 'fa-check-double', 'desc' => 'Komponen workflow dipasang di seluruh 6 halaman modul gudang: <code>materials_jasa</code>, <code>materials</code>, <code>opname</code>, <code>purchase</code>, <code>dispatch</code>, dan <code>stock</code>.'],
                            ['tag' => 'bg-soft-info text-info', 'label' => 'Dashboard Update', 'icon' => 'fa-tachometer-alt', 'desc' => 'Dashboard gudang diperbarui dari tampilan 2-langkah menjadi 6-langkah SOP lengkap dengan link navigasi cepat ke setiap modul.'],
                            ['tag' => 'bg-soft-warning text-warning', 'label' => 'Konsolidasi Folder', 'icon' => 'fa-folder-open', 'desc' => 'Seluruh view modul bahan (<code>admin/bahan/*</code>) dipindahkan ke <code>admin/warehouse/*</code> untuk sentralisasi kode. Folder <code>admin/bahan</code> dihapus setelah migrasi.'],
                            ['tag' => 'bg-soft-danger text-danger', 'label' => 'Controller Update', 'icon' => 'fa-cogs', 'desc' => 'Warehouse Controller diperbarui untuk memanggil view dari lokasi baru di folder warehouse. Tidak ada perubahan struktur database.']
                        ]
                    ],
                    [
                        'version' => 'v5.1',
                        'title' => 'SDM Rebranding & UX Overhaul',
                        'date' => date('M d, Y'),
                        'badge' => 'badge-primary',
                        'icon' => 'fa-user-tie',
                        'items' => [
                            ['tag' => 'bg-soft-primary text-primary', 'label' => 'New Theme', 'icon' => 'fa-palette', 'desc' => 'Rebranding total dari warna Indigo ke Royal Blue (Ocean Blue & Slate) untuk kesan korporat yang lebih tajam.'],
                            ['tag' => 'bg-soft-success text-success', 'label' => 'Clean UI', 'icon' => 'fa-desktop', 'desc' => 'Overhaul antarmuka Database Karyawan, Penilaian Kinerja, Pengaturan Absensi, & Raport Individu dengan konsep "Ultra-Minimalist" dan grid statistik yang lebih informatif.'],
                            ['tag' => 'bg-soft-info text-info', 'label' => 'SOP Integrated', 'icon' => 'fa-journal-whills', 'desc' => 'Integrasi Panduan SOP pada modul SDM, Kinerja, Pengaturan Absensi, & Raport Individu untuk meminimalisir kesalahan interpretasi data & operasional.'],
                            ['tag' => 'bg-soft-warning text-warning', 'label' => 'Accessibility', 'icon' => 'fa-text-height', 'desc' => 'Peningkatan ukuran font di seluruh modul SDM untuk menjamin keterbacaan yang lebih baik di berbagai resolusi.'],
                            ['tag' => 'bg-soft-danger text-danger', 'label' => 'Bug Fix', 'icon' => 'fa-bug', 'desc' => 'Perbaikan critical syntax error pada AJAX Table rendering yang menghambat tampilan data record.']
                        ]
                    ],
                    [
                        'version' => 'v5.0',
                        'title' => 'Warehouse Management Overhaul',
                        'date' => date('M d, Y'),
                        'badge' => 'badge-danger',
                        'icon' => 'fa-warehouse',
                        'items' => [
                            ['tag' => 'bg-soft-primary text-primary', 'label' => 'SOP Digital', 'icon' => 'fa-book', 'desc' => 'Integrasi modul panduan (SOP) interaktif pada seluruh alur Gudang (Material, Order, Purchase, Dispatch, Opname, Ledger).'],
                            ['tag' => 'bg-soft-success text-success', 'label' => 'UI Premium v2', 'icon' => 'fa-gem', 'desc' => 'Standardisasi posisi tombol aksi High-Level (SOP/Dashboard) di pojok kanan atas untuk navigasi yang lebih intuitif.'],
                            ['tag' => 'bg-soft-info text-info', 'label' => 'Audit Real-time', 'icon' => 'fa-sync-alt', 'desc' => 'Implementasi kalkulasi deviasi otomatis & sinkronisasi stok instan pada modul Stock Opname & Audit Ledger.'],
                            ['tag' => 'bg-soft-danger text-danger', 'label' => 'Request System', 'icon' => 'fa-file-signature', 'desc' => 'Penambahan modul <code>Pengajuan Material</code> untuk memfasilitasi permintaan barang dari Operator Produksi ke Gudang Utama.'],
                            ['tag' => 'bg-soft-dark text-dark', 'label' => 'Localization', 'icon' => 'fa-language', 'desc' => 'Menerjemahkan seluruh antarmuka & informasi teknis gudang ke Bahasa Indonesia untuk kemudahan operasional.']
                        ]
                    ],
                    [
                        'version' => 'v4.7',
                        'title' => 'Monitoring Absensi & Performa',
                        'date' => date('M d, Y'),
                        'badge' => 'badge-primary',
                        'icon' => 'fa-users-cog',
                        'items' => [
                            ['tag' => 'bg-soft-primary text-primary', 'label' => 'Payroll Dinamis', 'icon' => 'fa-calendar-alt', 'desc' => 'Laporan absensi kini mengikuti <code>payroll_start_day</code> (Cutoff 27) secara otomatis dari database.'],
                            ['tag' => 'bg-soft-info text-info', 'label' => 'Edit Admin', 'icon' => 'fa-edit', 'desc' => 'Fitur Manual Correction; Admin/HR dapat mengubah status & jam kehadiran langsung dari tabel rekap.'],
                            ['tag' => 'bg-soft-success text-success', 'label' => 'Statistik Tim', 'icon' => 'fa-chart-pie', 'desc' => 'Penyempurnaan metrik Global Team: Total Omset, Avg Omset/SPK, dan Ratio SPK terbayar.'],
                            ['tag' => 'bg-soft-danger text-danger', 'label' => 'Lembur', 'icon' => 'fa-clock', 'desc' => 'Refaktor Lembur Wajib; Penyesuaian jadwal (Sabtu 15:00, Weekday 16:00) dengan pembulatan floor.']
                        ]
                    ],
                    [
                        'version' => 'v4.6',
                        'title' => 'Core Production & Machine Hub',
                        'date' => date('M d, Y'),
                        'badge' => 'badge-success',
                        'icon' => 'fa-microchip',
                        'items' => [
                            ['tag' => 'bg-soft-primary text-primary', 'label' => 'Pilih Mesin', 'icon' => 'fa-print', 'desc' => 'Integrasi Machine Assignment; Operator wajib memilih mesin & lokasi meja sebelum mulai.'],
                            ['tag' => 'bg-soft-info text-info', 'label' => 'Live Tracking', 'icon' => 'fa-map-marker-alt', 'desc' => 'Badge Lokasi Produksi pada dashboard untuk monitoring real-time posisi pengerjaan item.'],
                            ['tag' => 'bg-soft-success text-success', 'label' => 'Smart Tag', 'icon' => 'fa-tag', 'desc' => 'Refaktor Tag Produksi dengan alur pratinjau (Preview First) & otomatisasi data Deskprint.'],
                            ['tag' => 'bg-soft-danger text-danger', 'label' => 'Log Output', 'icon' => 'fa-chart-line', 'desc' => 'Implementasi Productivity Tracking; pencatatan otomatis <code>qty_output</code> per step pekerjaan.']
                        ]
                    ],
                    [
                        'version' => 'v4.5',
                        'title' => 'Sistem Tiket Support & Bug',
                        'date' => date('M d, Y'),
                        'badge' => 'badge-primary',
                        'icon' => 'fa-headset',
                        'items' => [
                            ['tag' => 'bg-soft-primary text-primary', 'label' => 'Tiket Utama', 'icon' => 'fa-ticket-alt', 'desc' => 'Sistem Support terpusat untuk BUG & Request Fitur dengan Module-Specific Tracking.'],
                            ['tag' => 'bg-soft-info text-info', 'label' => 'Alert Dev', 'icon' => 'fa-bell', 'desc' => 'Sistem Badge Notifikasi di Top Header bagi akun Developer/Admin untuk tiket OPEN/PENDING.'],
                            ['tag' => 'bg-soft-success text-success', 'label' => 'Handler Media', 'icon' => 'fa-paperclip', 'desc' => 'Engine upload file FCPATH standar dengan Image Preview pada thread percakapan tiket.'],
                            ['tag' => 'bg-soft-danger text-danger', 'label' => 'Bypass RBAC', 'icon' => 'fa-user-shield', 'desc' => 'Whitelisting modul support guna menjamin seluruh user dapat melapor kendala tanpa setup manual.']
                        ]
                    ],
                    [
                        'version' => 'v4.5',
                        'title' => 'Cockpit Live Production',
                        'date' => date('M d, Y'),
                        'badge' => 'badge-danger',
                        'icon' => 'fa-satellite-dish',
                        'items' => [
                            ['tag' => 'bg-soft-danger text-danger', 'label' => 'Alur Prod', 'icon' => 'fa-stream', 'desc' => 'Dashboard real-time untuk memantau progres operator & End-to-End Tracking (Produksi-PB).'],
                            ['tag' => 'bg-soft-success text-success', 'label' => 'Stat Bahan', 'icon' => 'fa-box-open', 'desc' => 'Rangkuman konsumsi bahan harian (Lembar/Meter) dengan otomatisasi log stok produksi.'],
                            ['tag' => 'bg-soft-warning text-warning', 'label' => 'Koreksi Admin', 'icon' => 'fa-tools', 'desc' => 'Fitur Koreksi Administratif; sinkronisasi otomatis qty produksi dengan reversal stok gudang.']
                        ]
                    ],
                    [
                        'version' => 'v4.4',
                        'title' => 'Isolasi Cabang & Pengingat',
                        'date' => date('M d, Y'),
                        'badge' => 'badge-info',
                        'icon' => 'fa-industry',
                        'items' => [
                            ['tag' => 'bg-soft-info text-info', 'label' => 'Multi-tenancy', 'icon' => 'fa-code-branch', 'desc' => 'Isolasi Cabang Ketat; tiap cabang hanya melihat data operasional lokasinya sendiri.'],
                            ['tag' => 'bg-soft-warning text-warning', 'label' => 'Handover', 'icon' => 'fa-bullhorn', 'desc' => 'Visual Handover Reminder; banner dinamis untuk mengingatkan perpindahan baris Done ke PB.'],
                            ['tag' => 'bg-soft-success text-success', 'label' => 'Invoice Otomatis', 'icon' => 'fa-file-invoice', 'desc' => 'Otomatisasi watermark LUNAS pada Tag Produksi berdasarkan API pembayaran real-time.'],
                            ['tag' => 'bg-soft-danger text-danger', 'label' => 'PIN Wajib', 'icon' => 'fa-lock', 'desc' => 'Verifikasi PIN Mandatori untuk siklus operasional (Start-Finish-Rijek) sebagai audit trail.']
                        ]
                    ],
                    [
                        'version' => 'v4.3',
                        'title' => 'Pusat Keamanan & UX Ops',
                        'date' => 'Feb 16, 2026',
                        'badge' => 'badge-success',
                        'icon' => 'fa-fingerprint',
                        'items' => [
                            ['tag' => 'bg-soft-danger text-danger', 'label' => 'PIN Unik', 'icon' => 'fa-user-lock', 'desc' => 'Sistem pencegahan duplikasi PIN per cabang untuk menjamin akuntabilitas operator.'],
                            ['tag' => 'bg-soft-primary text-primary', 'label' => 'Optimasi Input', 'icon' => 'fa-keyboard', 'desc' => 'Peningkatan UX: Autofocus modal & Enter-to-Submit pada seluruh proses verifikasi PIN.'],
                            ['tag' => 'bg-soft-info text-info', 'label' => 'Manajemen HR', 'icon' => 'fa-id-card', 'desc' => 'Integrasi kolom pin_produksi di modul HR dengan validasi AJAX real-time di Profile Modal.']
                        ]
                    ]
                ];

                foreach ($logs as $log): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                            <div class="card-header bg-white py-2 border-0 d-flex justify-content-between align-items-center"
                                style="border-radius: 12px 12px 0 0;">
                                <h6 class="font-weight-bold mb-0">
                                    <span class="badge <?= $log['badge'] ?> text-white mr-1"
                                        style="font-size: 0.65rem;"><?= $log['version'] ?></span>
                                    <span class="text-dark" style="font-size: 0.85rem;"><?= $log['title'] ?></span>
                                </h6>
                                <small class="text-muted" style="font-size: 0.75rem;"><?= $log['date'] ?></small>
                            </div>
                            <div class="card-body py-2 pt-0">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($log['items'] as $item): ?>
                                        <div class="list-group-item px-0 py-2 border-0">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="scope-tag <?= $item['tag'] ?> mr-2">
                                                    <i class=" fas <?= $item['icon'] ?>
                                                mr-1"></i><?= $item['label'] ?>
                                                </span>
                                            </div>
                                            <p class="mb-0 text-secondary"
                                                style="font-size: 0.82rem; line-height: 1.4; padding-left: 2px;">
                                                <?= $item['desc'] ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div> <!-- End of Grid -->

            <div class="alert alert-dark border-0 shadow-sm p-4 d-flex align-items-center"
                style="border-radius: 15px; background: #2d3436; color: #dfe6e9;">
                <div class="bg-primary p-3 rounded-circle mr-4">
                    <i class="fas fa-lightbulb text-white"></i>
                </div>
                <div>
                    <h6 class="font-weight-bold mb-1">Wawasan Pengembang:</h6>
                    <p class="small mb-0 opacity-75">
                        Fokus pengembangan tahun 2026 adalah <strong>Kecepatan & Keamanan</strong>.
                        Setiap modul baru harus lolos standarisasi RBAC dan mendukung pencatatan Audit Trail
                        otomatis.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Force layout to touch edges */
    .content-wrapper {
        padding: 0 !important;
    }

    .main-footer {
        margin-top: 0 !important;
    }

    /* Scope Tags Styling */
    .scope-tag {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 5px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .bg-soft-primary {
        background: #e0e7ff;
    }

    .bg-soft-info {
        background: #e0f2fe;
    }

    .bg-soft-warning {
        background: #fef3c7;
    }

    .bg-soft-danger {
        background: #fee2e2;
    }

    .bg-soft-dark {
        background: #f1f5f9;
    }

    .bg-soft-success {
        background: #dcfce7;
    }

    .border-bottom-faint {
        border-bottom: 1px solid #f8fafc;
    }

    .border-bottom-faint:last-child {
        border-bottom: none;
    }

    /* Custom Table Styling */
    .custom-table {
        border-collapse: separate;
        border-spacing: 0 4px;
    }

    .custom-table thead th {
        border: none;
        color: #94a3b8;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 5px 10px !important;
    }

    .custom-table tbody td {
        border-top: 1px solid #f1f5f9 !important;
        padding: 0.5rem 0.75rem !important;
        vertical-align: middle;
        font-size: 0.8rem;
        color: #334155;
    }

    /* Modern Timeline Styling (Disabled for Grid) */
    .changelog-timeline-modern {
        padding: 0;
        border: none;
        margin: 0;
    }

    .timeline-indicator {
        position: absolute;
        left: -46px;
        top: 0;
        width: 20px;
        height: 20px;
        background: #fff;
        border: 3px solid #0d6efd;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        font-size: 0.6rem;
        box-shadow: 0 0 0 3px #f1f5f9;
        z-index: 2;
    }

    .timeline-box {
        margin-bottom: 2rem !important;
    }

    .timeline-box.active .timeline-indicator {
        background: #0d6efd;
        color: #fff;
        box-shadow: 0 0 0 3px #e0e7ff;
    }

    code {
        background: #f1f5f9;
        color: #dc3545;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* Card Shadow & Radius */
    .shadow-lg {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03) !important;
    }
</style>