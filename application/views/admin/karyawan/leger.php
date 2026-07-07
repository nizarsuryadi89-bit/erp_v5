<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --leger-bg: #f8fafc;
        --leger-primary: #2563eb;
        --leger-success: #059669;
        --leger-warning: #d97706;
        --leger-danger: #dc2626;
        --leger-info: #0891b2;
        --leger-border: #e2e8f0;
    }

    .leger-container {
        padding: 1.5rem;
        background: var(--leger-bg);
        min-height: 100vh;
    }

    .leger-card {
        background: white;
        border: 1px solid var(--leger-border);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table-responsive-leger {
        max-height: 75vh;
        overflow: auto;
    }

    .table-leger {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.85rem;
    }

    /* Sticky Header & Columns */
    .table-leger thead th {
        position: sticky;
        top: 0;
        background: #f1f5f9;
        z-index: 10;
        padding: 12px 15px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--leger-border);
        white-space: nowrap;
    }

    /* Secondary Header for Groups */
    .header-group {
        text-align: center;
        border-left: 1px solid #cbd5e1 !important;
        background: #e2e8f0 !important;
        font-size: 0.75rem !important;
    }

    .sticky-col {
        position: sticky !important;
        left: 0;
        background: white;
        z-index: 5 !important;
        border-right: 2px solid var(--leger-border) !important;
    }

    .table-leger thead th.sticky-col {
        z-index: 15 !important;
        background: #f1f5f9 !important;
    }

    .table-leger tbody td {
        padding: 10px 15px;
        border-bottom: 1px solid var(--leger-border);
        white-space: nowrap;
    }

    .table-leger tr:hover td {
        background-color: #f8fafc;
    }

    .table-leger tr:hover .sticky-col {
        background-color: #f8fafc;
    }

    /* Column Group Styling */
    .bg-absensi { background-color: rgba(59, 130, 246, 0.03); }
    .bg-lembur { background-color: rgba(245, 158, 11, 0.03); }
    .bg-kpi { background-color: rgba(16, 185, 129, 0.03); }
    .bg-payroll { background-color: rgba(139, 92, 246, 0.03); }

    .border-absensi { border-left: 4px solid var(--leger-primary) !important; }
    .border-lembur { border-left: 4px solid var(--leger-warning) !important; }
    .border-kpi { border-left: 4px solid var(--leger-success) !important; }
    .border-payroll { border-left: 4px solid #8b5cf6 !important; }

    .font-mono { font-family: 'JetBrains Mono', 'Monaco', monospace !important; }
    
    .badge-leger {
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 0.7rem;
    }

    @media print {
        .no-print { display: none !important; }
        .table-responsive-leger { max-height: none; overflow: visible; }
        .table-leger { font-size: 7pt; }
        .sticky-col { position: static !important; }
    }
</style>

<div class="leger-container mt-n4">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <div>
                <h4 class="fw-900 text-dark mb-1">MASTER LEGER <span class="text-primary">HRD</span></h4>
                <p class="text-muted small fw-bold mb-0">Konsolidasi Data Absensi, Lembur, KPI, & Payroll • <?= date('F Y', strtotime($bulan)) ?></p>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-white border shadow-sm rounded-pill px-4 fw-bold small">
                    <i class="fas fa-print me-2"></i> CETAK LAPORAN
                </button>
                <a href="<?= site_url('hr') ?>" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold small">
                    <i class="fas fa-arrow-left me-2"></i> KEMBALI
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="leger-card p-4 mb-4 no-print">
            <form action="<?= site_url('karyawan/leger_hrd') ?>" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1 text-uppercase">Periode Bulan</label>
                    <input type="month" name="bulan" class="form-control fw-bold border-0 bg-light" value="<?= $bulan ?>" onchange="this.form.submit()">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1 text-uppercase">Cari Nama</label>
                    <input type="text" name="nama" class="form-control fw-bold border-0 bg-light" value="<?= $nama ?>" placeholder="Ketik nama karyawan...">
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1 text-uppercase">Divisi</label>
                    <select name="divisi" class="form-select fw-bold border-0 bg-light">
                        <option value="">Semua Divisi</option>
                        <?php foreach($divisi_list as $d): ?>
                            <option value="<?= $d->divisi_nama ?>" <?= $divisi == $d->divisi_nama ? 'selected' : '' ?>><?= $d->divisi_nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1 text-uppercase">Jabatan</label>
                    <select name="jabatan" class="form-select fw-bold border-0 bg-light">
                        <option value="">Semua Jabatan</option>
                        <?php foreach($jabatan_list as $j): ?>
                            <option value="<?= $j->nama_jabatan ?>" <?= $jabatan == $j->nama_jabatan ? 'selected' : '' ?>><?= $j->nama_jabatan ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill">
                        <i class="fas fa-search me-2"></i> FILTER LEGER
                    </button>
                </div>
            </form>
        </div>

        <!-- Leger Table -->
        <div class="leger-card">
            <div class="table-responsive-leger">
                <table class="table-leger">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center bg-light" width="40">NO</th>
                            <th rowspan="2" class="sticky-col text-start bg-light" width="250">IDENTITAS KARYAWAN</th>
                            <th colspan="6" class="header-group" style="background: #eff6ff !important; color: #1e40af;">MODUL ABSENSI</th>
                            <th colspan="2" class="header-group" style="background: #fffbeb !important; color: #92400e;">MODUL LEMBUR</th>
                            <th colspan="2" class="header-group" style="background: #ecfdf5 !important; color: #065f46;">MODUL KPI</th>
                            <th colspan="5" class="header-group" style="background: #f5f3ff !important; color: #5b21b6;">MODUL PAYROLL (SNAPSHOT)</th>
                        </tr>
                        <tr>
                            <!-- Absensi -->
                            <th class="text-center bg-absensi">Hadir</th>
                            <th class="text-center bg-absensi">Izin</th>
                            <th class="text-center bg-absensi">Sakit</th>
                            <th class="text-center bg-absensi">Cuti</th>
                            <th class="text-center bg-absensi">Alpha</th>
                            <th class="text-center bg-absensi">Telat (M)</th>
                            <!-- Lembur -->
                            <th class="text-center bg-lembur">Jam</th>
                            <th class="text-end bg-lembur">Nominal</th>
                            <!-- KPI -->
                            <th class="text-center bg-kpi">Skor</th>
                            <th class="text-center bg-kpi">Predikat</th>
                            <!-- Payroll -->
                            <th class="text-end bg-payroll">Pokok</th>
                            <th class="text-end bg-payroll">Tunj.</th>
                            <th class="text-end bg-payroll">Pot.</th>
                            <th class="text-end bg-payroll">THP</th>
                            <th class="text-center bg-payroll">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($rekap_data)): ?>
                            <tr>
                                <td colspan="17" class="text-center py-5 text-muted fw-bold">Data tidak ditemukan untuk periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($rekap_data as $row): ?>
                                <tr>
                                    <td class="text-center font-mono text-muted small"><?= $no++ ?></td>
                                    <td class="sticky-col">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3" style="width: 32px; height: 32px; border-radius: 6px; background: #eee; overflow: hidden;">
                                                <img src="<?= $row->foto ? base_url('assets/images/karyawan/'.$row->foto) : base_url('assets/images/user.png') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark text-uppercase" style="font-size: 0.75rem;"><?= $row->nama_lengkap ?></div>
                                                <div class="text-muted" style="font-size: 0.65rem;"><?= $row->nik ?> • <?= $row->jabatan ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Absensi -->
                                    <td class="text-center font-mono fw-bold text-success border-absensi"><?= $row->total_hadir ?></td>
                                    <td class="text-center font-mono text-info"><?= $row->total_izin ?></td>
                                    <td class="text-center font-mono text-warning"><?= $row->total_sakit ?></td>
                                    <td class="text-center font-mono text-primary"><?= $row->total_cuti ?? 0 ?></td>
                                    <td class="text-center font-mono fw-bold text-danger"><?= $row->total_alpha ?></td>
                                    <td class="text-center font-mono text-muted"><?= $row->total_telat ?></td>

                                    <!-- Lembur -->
                                    <td class="text-center font-mono fw-bold text-warning border-lembur"><?= number_format($row->total_jam_lembur, 1) ?>h</td>
                                    <td class="text-end font-mono small">Rp <?= number_format($row->uang_lembur, 0, ',', '.') ?></td>

                                    <!-- KPI -->
                                    <td class="text-center font-mono fw-bold text-success border-kpi"><?= $row->kpi_score ?></td>
                                    <td class="text-center">
                                        <span class="badge-leger bg-light text-dark border small"><?= $row->kpi_predikat ?></span>
                                    </td>

                                    <!-- Payroll -->
                                    <td class="text-end font-mono small border-payroll">Rp <?= number_format($row->gaji_pokok, 0, ',', '.') ?></td>
                                    <td class="text-end font-mono small text-success">+<?= number_format($row->tunjangan_jabatan + $row->tunjangan_transportasi + ($row->biaya_makan_lembur ?? 0), 0, ',', '.') ?></td>
                                    <td class="text-end font-mono small text-danger">-<?= number_format($row->potongan_alpha + $row->potongan_terlambat + $row->kasbon + $row->pinjaman, 0, ',', '.') ?></td>
                                    <td class="text-end font-mono fw-bold text-primary" style="background: #f5f3ff;">Rp <?= number_format($row->total_gaji, 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <?php if($row->is_synchronized): ?>
                                            <i class="fas fa-check-circle text-success" title="Sudah Sinkron"></i>
                                        <?php else: ?>
                                            <i class="fas fa-exclamation-circle text-muted" title="Belum Sinkron"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
