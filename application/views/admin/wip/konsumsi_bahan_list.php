<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    .report-section-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    .section-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-header h5 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .table th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
</style>

<div class="app-content mt-3">
    <div class="report-section-card">
        <div class="section-header">
            <h5>
                <i class="fas fa-boxes"></i> Rekap Konsumsi Bahan 
            </h5>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= site_url('wip/live_bahan') ?>" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                    <i class="fas fa-broadcast-tower me-1"></i> Live Material
                </a>
                <form method="GET" action="<?= site_url('wip/konsumsi_bahan') ?>" class="d-flex align-items-center me-2">
                    <input type="date" name="start_date" class="form-control form-control-sm shadow-sm" style="width: 130px; border-radius: 20px 0 0 20px; border-right: none;" value="<?= htmlspecialchars($start_date) ?>" required>
                    <span class="input-group-text bg-white border-start-0 border-end-0 py-1" style="font-size: 0.8rem;">s/d</span>
                    <input type="date" name="end_date" class="form-control form-control-sm shadow-sm" style="width: 130px; border-radius: 0 20px 20px 0; border-left: none;" value="<?= htmlspecialchars($end_date) ?>" required>
                    <button type="submit" class="btn btn-sm btn-primary ms-2 rounded-pill shadow-sm"><i class="fas fa-search"></i></button>
                    <a href="<?= site_url('wip/cetak_konsumsi_bahan?start_date='.$start_date.'&end_date='.$end_date) ?>" target="_blank" class="btn btn-sm btn-success ms-2 rounded-pill px-3 fw-bold shadow-sm">
                        <i class="fas fa-print me-1"></i> Cetak
                    </a>
                </form>
                <button type="button" class="btn btn-sm btn-info text-white rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSOP">
                    <i class="fas fa-info-circle me-1"></i> Bantuan SOP
                </button>
                <button onclick="location.reload()" class="btn btn-sm btn-light rounded-pill px-3 fw-bold shadow-sm">
                    <i class="fas fa-sync-alt me-1"></i> Refresh Data
                </button>
            </div>
        </div>

        <div class="p-4 bg-white">
            <!-- Top 10 Section -->
            <div class="mb-4 p-3 rounded" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                <h6 class="fw-bold mb-3" style="color: #475569;"><i class="fas fa-fire text-danger me-1"></i> 10 Material Paling Sering Digunakan Bulan Ini</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php if(!empty($top_10)): ?>
                        <?php foreach($top_10 as $index => $top): ?>
                            <div class="badge bg-white text-dark border p-2 shadow-sm d-flex align-items-center gap-2" style="border-radius: 12px;">
                                <span class="badge rounded-circle d-flex align-items-center justify-content-center" style="background-color: #3b82f6; width: 24px; height: 24px; font-size: 0.8rem;"><?= $index + 1 ?></span>
                                <div class="text-start">
                                    <div class="fw-bold" style="font-size: 0.85rem; color: #1e293b;"><?= htmlspecialchars($top->material_nama ?: $top->bahan_nama) ?></div>
                                    <div class="text-muted" style="font-size: 0.7rem; font-weight: 500;">Dipakai <?= $top->frekuensi ?>x &bull; <?= str_replace(',00', '', number_format($top->total_produksi, 2, ',', '.')) ?> <?= $top->satuan_produksi ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted small w-100 text-center py-2"><i class="fas fa-info-circle me-1"></i> Belum ada data konsumsi bahan bulan ini.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="tableKonsumsiBahan">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="30%">Nama Material</th>
                            <th class="text-center" width="12%">Penggunaan Pusat</th>
                            <th class="text-center" width="13%">Penggunaan Cabang</th>
                            <th class="text-center" width="15%">Total Penggunaan</th>
                            <th class="text-center" width="15%">Konversi ke Satuan Gudang</th>
                            <th class="text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($summary)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle mb-2 fa-2x opacity-50"></i><br>
                                    Belum ada data pemotongan bahan untuk bulan ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($summary as $row): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row->bahan_nama ?? '-') ?></div>
                                        <div class="small text-muted"><i class="fas fa-tag me-1 text-info"></i><?= htmlspecialchars($row->material_nama ?? '-') ?></div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="fw-bold text-primary" style="font-size: 0.9rem;">
                                            <?= str_replace(',00', '', number_format($row->total_produksi_pusat, 2, ',', '.')) ?> <?= htmlspecialchars($row->satuan_produksi ?? $row->satuan_gudang ?? '') ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="fw-bold text-success" style="font-size: 0.9rem;">
                                            <?= str_replace(',00', '', number_format($row->total_produksi_cabang, 2, ',', '.')) ?> <?= htmlspecialchars($row->satuan_produksi ?? $row->satuan_gudang ?? '') ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="fw-bold text-danger" style="font-size: 0.95rem;">
                                            <?= str_replace(',00', '', number_format($row->total_produksi, 2, ',', '.')) ?> <?= htmlspecialchars($row->satuan_produksi ?? $row->satuan_gudang ?? '-') ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if (!empty($row->satuan_produksi) && !empty($row->satuan_gudang) && strtolower(trim($row->satuan_produksi)) !== strtolower(trim($row->satuan_gudang))): ?>
                                            <div class="fw-bold text-secondary" style="font-size: 0.95rem;">
                                                <i class="fas fa-exchange-alt me-1 text-info"></i><?= str_replace(',00', '', number_format($row->total_gudang, 2, ',', '.')) ?> <?= htmlspecialchars($row->satuan_gudang) ?>
                                            </div>
                                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                                (1 <?= htmlspecialchars($row->satuan_gudang) ?> = <?= str_replace(',00', '', number_format($row->konversi ?? 1, 2, ',', '.')) ?> <?= htmlspecialchars($row->satuan_produksi) ?>)
                                            </div>
                                        <?php else: ?>
                                            <div class="text-muted small">-</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3 rounded-pill" onclick="showDetail(<?= $row->material_id ?>, '<?= htmlspecialchars($row->material_nama ?? $row->bahan_nama) ?>')">
                                            <i class="fas fa-list me-1"></i> Detail
                                        </button>
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

<!-- Modal SOP -->
<div class="modal fade" id="modalSOP" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-info-circle me-2"></i> Bantuan SOP (Standar Operasional)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-white">
                <div class="alert alert-warning border-warning shadow-sm">
                    <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-1"></i> Perhatian: Data Berasal dari BOM</h6>
                    <p class="mb-0 small">
                        Harap diingat bahwa data rekapan yang tampil di halaman ini adalah **data otomatis yang ditarik berdasarkan resep BOM (Bill of Material)** dari masing-masing produk saat operator menekan tombol selesai.
                    </p>
                </div>
                <p class="small text-muted mb-0">
                    Maka dari itu, **tolong cek juga BOM-nya** di Master Produk jika ada ketidaksesuaian jumlah atau jenis material yang terpotong. 
                    Sistem hanya akan memotong persis sesuai dengan resep BOM yang dikonfigurasi.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-history me-2 text-primary"></i> Detail Pemakaian: <span id="modalDetailMaterialName" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-white" id="modalDetailBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2 text-muted">Memuat data riwayat...</div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#tableKonsumsiBahan').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "pageLength": 25,
            "ordering": true
        });
    }
});

function showDetail(materialId, materialName) {
    $('#modalDetailMaterialName').text(materialName);
    $('#modalDetailBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2 text-muted">Memuat data riwayat...</div>
        </div>
    `);
    
    // Gunakan bootstrap 5 modal instance jika tersedia, atau cara jquery fallback
    var myModal = new bootstrap.Modal(document.getElementById('modalDetail'));
    myModal.show();
    
    let startDate = $('input[name="start_date"]').val();
    let endDate = $('input[name="end_date"]').val();

    $.ajax({
        url: '<?= site_url("wip/ajax_detail_konsumsi/") ?>' + materialId,
        type: 'GET',
        data: { start_date: startDate, end_date: endDate },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#modalDetailBody').html(response.html);
            } else {
                $('#modalDetailBody').html('<div class="alert alert-danger">Gagal memuat data.</div>');
            }
        },
        error: function() {
            $('#modalDetailBody').html('<div class="alert alert-danger">Terjadi kesalahan pada server.</div>');
        }
    });
}
</script>
