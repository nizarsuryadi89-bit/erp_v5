<?php
/**
 * Laporan Penjualan per Kategori
 */
$current_perusahaan_id = $this->input->get('perusahaan_id') ?: $this->session->userdata('perusahaan_id');
?>

<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<style>
    :root {
        --fin-primary: #1e293b;
        --fin-accent: #3b82f6;
        --fin-success: #10b981;
        --fin-bg: #f8fafc;
        --card-radius: 20px;
    }

    .app-content {
        background-color: var(--fin-bg);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    .filter-hud {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 2rem;
    }

    .filter-hud .form-select,
    .filter-hud .form-control {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 1rem;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .cash-stat-card {
        background: white;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .cash-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .cash-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
    }

    .cash-stat-value {
        font-size: 1.4rem;
        font-weight: 900;
        color: var(--fin-primary);
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .cash-stat-label {
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.25rem;
    }

    .summary-card {
        background: white;
        border-radius: var(--card-radius);
        border: 1px solid #e2e8f0;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .summary-header {
        background: var(--fin-primary);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-modern {
        margin: 0;
    }

    .table-modern thead th {
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
        border: none;
    }

    .table-modern tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
    }
    
    .text-success-modern {
        color: var(--fin-success);
    }
</style>

<div class="app-content mt-3 pb-5">
    <?php $this->load->view('layout_admin/header_modul'); ?>
    <div class="content-header pt-4 pb-2">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 fw-900 text-dark" style="font-size: 1.75rem; letter-spacing: -1px;">
                        Penjualan Kategori
                    </h1>
                    <p class="text-muted fw-600 mb-0" style="font-size: 0.85rem;">Analisis Penjualan berdasarkan Kategori Produk</p>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Filter HUD -->
            <form action="<?= base_url('laporan/penjualan_kategori') ?>" method="GET" class="filter-hud">
                <div class="row align-items-end g-3">
                    <?php if (in_array($this->session->userdata('level'), [1, 2])): ?>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold mb-1" style="font-size:0.75rem;">PILIH CABANG</label>
                        <select class="form-select" name="perusahaan_id" onchange="this.form.submit()">
                            <option value="all">Semua Cabang (Global)</option>
                            <?php foreach ($this->db->get('tbl_perusahaan')->result() as $p): ?>
                                <option value="<?= $p->id_perusahaan ?>" <?= $current_perusahaan_id == $p->id_perusahaan ? 'selected' : '' ?>>
                                    <?= $p->nama_perusahaan ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold mb-1" style="font-size:0.75rem;">TANGGAL AWAL</label>
                        <input type="date" class="form-control" name="start_date" value="<?= $start_date ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold mb-1" style="font-size:0.75rem;">TANGGAL AKHIR</label>
                        <input type="date" class="form-control" name="end_date" value="<?= $end_date ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold" style="border-radius:10px; padding: 0.6rem 1rem;">
                            <i class="fas fa-filter me-2"></i> Filter
                        </button>
                    </div>
                </div>
            </form>

            <?php
            $total_qty_global = 0;
            $total_omset_global = 0;
            foreach($penjualan_kategori as $row) {
                $total_qty_global += $row['total_qty'];
                $total_omset_global += $row['total_omset'];
            }
            ?>

            <!-- Top Stats -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="cash-stat-card">
                        <div class="cash-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--fin-success);">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="cash-stat-label">Total Qty Terjual</div>
                        <div class="cash-stat-value"><?= number_format($total_qty_global, 0, ',', '.') ?> <span style="font-size: 0.9rem">Items</span></div>
                        <div class="cash-stat-desc mt-1">Total produk terjual dalam rentang tanggal.</div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="cash-stat-card">
                        <div class="cash-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--fin-accent);">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="cash-stat-label">Total Omset Produk</div>
                        <div class="cash-stat-value">Rp <?= number_format($total_omset_global, 0, ',', '.') ?></div>
                        <div class="cash-stat-desc mt-1">Total nilai penjualan dari seluruh kategori.</div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="summary-card">
                <div class="summary-header">
                    <div>
                        <h5 class="mb-0 fw-bold"><i class="fas fa-chart-pie me-2"></i> Breakdown Penjualan per Kategori</h5>
                        <small style="opacity:0.8">Menampilkan penjualan kategori yang paling laris hingga terendah.</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kategori Produk</th>
                                <th class="text-center">Total Qty Terjual</th>
                                <th class="text-end">Total Omset</th>
                                <th class="text-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($penjualan_kategori)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada data penjualan pada rentang tanggal ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($penjualan_kategori as $row): 
                                    $persentase = ($total_omset_global > 0) ? ($row['total_omset'] / $total_omset_global) * 100 : 0;
                                ?>
                                    <tr>
                                        <td class="text-muted fw-bold"><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded p-2 me-3" style="background: rgba(59, 130, 246, 0.1); color: var(--fin-accent);">
                                                    <i class="fas fa-tags"></i>
                                                </div>
                                                <span class="fw-bold fs-6 text-dark"><?= $row['kategori_nama'] ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold fs-6">
                                            <?= number_format($row['total_qty'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end fw-bold text-success-modern fs-6">
                                            Rp <?= number_format($row['total_omset'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span class="badge bg-primary" style="font-size:0.8rem; padding: 0.4rem 0.6rem; border-radius: 8px;">
                                                    <?= number_format($persentase, 1) ?>%
                                                </span>
                                                <button type="button" class="btn btn-sm btn-outline-info rounded-3 px-2 py-1 btn-view-tx" 
                                                        data-kategori="<?= htmlspecialchars($row['kategori_nama']) ?>"
                                                        data-tx='<?= htmlspecialchars(json_encode($row['transactions']), ENT_QUOTES, 'UTF-8') ?>'>
                                                    <i class="fas fa-eye"></i>
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

        </div>
    </section>
</div>

<!-- Modal Transaksi Kategori -->
<div class="modal fade" id="modalTxKategori" tabindex="-1" aria-labelledby="modalTxKategoriLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="fas fa-list text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0" id="modalTxKategoriLabel">Detail Transaksi</h5>
                        <small class="text-white-50 fw-bold" id="modalKategoriName">Kategori</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tableTxDetail">
                        <thead class="bg-white">
                            <tr>
                                <th class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Tanggal</th>
                                <th class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">No. SPK</th>
                                <th class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Produk</th>
                                <th class="text-uppercase text-muted text-center" style="font-size: 0.75rem; letter-spacing: 1px;">Qty</th>
                                <th class="text-uppercase text-muted text-end" style="font-size: 0.75rem; letter-spacing: 1px;">Omset</th>
                            </tr>
                        </thead>
                        <tbody id="txTableBody">
                            <!-- Injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-white border-top p-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-800 shadow-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formatRp = (angka) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(angka);
    };

    const txButtons = document.querySelectorAll('.btn-view-tx');
    const modalEl = document.getElementById('modalTxKategori');
    let dataTableInstance = null;
    
    if(txButtons && modalEl) {
        const txModal = new bootstrap.Modal(modalEl);
        
        txButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const kategori = this.getAttribute('data-kategori');
                const rawData = this.getAttribute('data-tx');
                let txData = [];
                
                try {
                    txData = JSON.parse(rawData);
                } catch(e) {
                    console.error("Gagal parsing JSON transaksi", e);
                }
                
                document.getElementById('modalKategoriName').innerText = 'Kategori: ' + kategori;
                
                // Clear existing DataTable if any
                if(dataTableInstance !== null) {
                    dataTableInstance.destroy();
                }
                
                const tbody = document.getElementById('txTableBody');
                tbody.innerHTML = '';
                
                if(txData && txData.length > 0) {
                    txData.forEach(tx => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="align-middle fw-bold text-muted" style="font-size: 0.85rem;">${tx.tgl_pesan}</td>
                            <td class="align-middle"><span class="badge bg-light text-dark border fw-bold px-2 py-1">${tx.nospk}</span></td>
                            <td class="align-middle">
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">${tx.product_nama}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">${tx.detail_product ? tx.detail_product : '-'}</div>
                            </td>
                            <td class="align-middle text-center fw-bold" style="font-size: 0.9rem;">${tx.qty}</td>
                            <td class="align-middle text-end fw-bold text-success" style="font-size: 0.9rem;">${formatRp(tx.subtotal)}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted fw-bold">Tidak ada rincian transaksi</td></tr>`;
                }
                
                // Initialize DataTables
                if (typeof $ !== 'undefined' && $.fn.DataTable) {
                    dataTableInstance = $('#tableTxDetail').DataTable({
                        "pageLength": 10,
                        "ordering": true,
                        "order": [[4, "desc"]], // sort by omset desc
                        "lengthChange": false,
                        "searching": true,
                        "dom": "<'row mb-3 px-4 pt-3'<'col-sm-12'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row p-4'<'col-sm-5 small fw-bold text-muted'i><'col-sm-7'p>>",
                        "language": {
                            "search": "",
                            "searchPlaceholder": "Cari SPK atau Produk...",
                            "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                            "paginate": { "previous": "<i class='fas fa-chevron-left'></i>", "next": "<i class='fas fa-chevron-right'></i>" }
                        }
                    });
                }
                
                txModal.show();
            });
        });
    }
});
</script>
