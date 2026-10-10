<!-- Premium Header Integrasi -->
<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --tgm-primary: #4338ca;
        --tgm-success: #15803d;
        --tgm-danger: #b91c1c;
        --tgm-warning: #b45309;
        --tgm-slate-50: #f8fafc;
        --tgm-slate-100: #f1f5f9;
        --tgm-slate-200: #e2e8f0;
        --tgm-slate-700: #334155;
    }

    .detail-container {
        padding: 1.5rem;
        background-color: var(--tgm-slate-50);
        min-height: 100vh;
    }

    .premium-card {
        background: white;
        border-radius: 20px;
        border: 1px solid var(--tgm-slate-200);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .premium-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--tgm-slate-100);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
    }

    .premium-card-header h5 {
        margin: 0;
        font-weight: 800;
        color: var(--tgm-slate-700);
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.65rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
    }

    .mono {
        font-family: 'JetBrains Mono', monospace;
    }

    .table-premium-detail {
        width: 100%;
        margin-bottom: 0;
    }

    .table-premium-detail thead th {
        background: var(--tgm-slate-50);
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid var(--tgm-slate-100);
    }

    .table-premium-detail tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--tgm-slate-100);
    }

    .badge-status {
        padding: 0.35rem 0.75rem;
        border-radius: 100px;
        font-weight: 700;
        font-size: 0.65rem;
        text-transform: uppercase;
    }

    .total-section {
        background: var(--tgm-slate-50);
        padding: 1.5rem;
        border-top: 1px solid var(--tgm-slate-200);
    }

    .total-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .total-row:last-child {
        margin-bottom: 0;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px dashed var(--tgm-slate-200);
    }
</style>

<div class="detail-container">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-900 mb-1 text-dark">DETAIL TRANSAKSI MANUAL</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold mono" style="font-size: 0.8rem;">
                    <?= $transaction['transaction_number'] ?>
                </span>
                <span class="text-muted small">•</span>
                <span class="text-muted small fw-bold">
                    <i class="far fa-calendar-alt me-1"></i><?= date('d M Y', strtotime($transaction['transaction_date'])) ?>
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('transaksi/manual_list') ?>" class="btn btn-white border rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> KEMBALI
            </a>
            <a href="<?= base_url('transaksi/manual_edit/' . $transaction['transaction_id']) ?>" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm text-dark">
                <i class="fas fa-edit me-2"></i> EDIT
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Info Section -->
        <div class="col-md-8">
            <div class="premium-card">
                <div class="premium-card-header">
                    <h5><i class="fas fa-file-invoice me-2 text-primary"></i> INFORMASI TRANSAKSI</h5>
                    <?php
                    $stClass = $transaction['status'] == 'completed' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning';
                    ?>
                    <span class="badge-status <?= $stClass ?>">
                        <?= $transaction['status'] ?>
                    </span>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Waktu Transaksi</span>
                        <span class="info-value mono"><?= $transaction['transaction_time'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Metode Bayar</span>
                        <span class="info-value"><?= strtoupper($transaction['payment_method']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Sales Person</span>
                        <span class="info-value"><?= $transaction['sales_person'] ?: '-' ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Prioritas</span>
                        <?php
                        $prioClass = $transaction['priority'] == 'high' ? 'text-danger' : ($transaction['priority'] == 'medium' ? 'text-warning' : 'text-info');
                        ?>
                        <span class="info-value <?= $prioClass ?> fw-bold"><?= strtoupper($transaction['priority']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Uang Muka (DP)</span>
                        <span class="info-value mono">Rp <?= number_format($transaction['down_payment'], 0, ',', '.') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Admin Input</span>
                        <span class="info-value small"><?= $transaction['created_by'] ?></span>
                    </div>
                </div>

                <?php if (!empty($transaction['notes'])): ?>
                    <div class="mx-4 mb-4 p-3 rounded-4 bg-light border-start border-4 border-info">
                        <span class="info-label d-block text-info">CATATAN TRANSAKSI</span>
                        <p class="mb-0 small fw-bold text-dark italic">"<?= nl2br($transaction['notes']) ?>"</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Items Table -->
            <div class="premium-card">
                <div class="premium-card-header">
                    <h5><i class="fas fa-shopping-cart me-2 text-primary"></i> DAFTAR ITEM PESANAN</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-premium-detail">
                        <thead>
                            <tr>
                                <th width="60" class="text-center">NO</th>
                                <th>DETAIL ITEM</th>
                                <th class="text-center">QTY</th>
                                <th class="text-end">HARGA</th>
                                <th class="text-center">DISKON</th>
                                <th class="text-end">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($items as $item): 
                                // Fallback calculation jika total dari DB bernilai 0
                                $raw_total = $item->total;
                                if ($raw_total <= 0 && $item->unit_price > 0) {
                                    $disc_amount = ($item->unit_price * $item->quantity) * ($item->discount_percent / 100);
                                    $raw_total = ($item->unit_price * $item->quantity) - $disc_amount;
                                }
                            ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= $item->item_name ?></div>
                                    <small class="text-muted"><?= $item->item_description ?: 'Tidak ada deskripsi' ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-dark"><?= number_format($item->quantity, 0, ',', '.') ?></span>
                                    <small class="text-muted ms-1"><?= $item->unit ?></small>
                                </td>
                                <td class="text-end mono fw-bold text-dark">
                                    Rp <?= number_format($item->unit_price, 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <?php if($item->discount_percent > 0): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold rounded-pill" style="font-size: 0.6rem;">
                                            -<?= $item->discount_percent ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="text-silver">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end mono fw-bold text-primary">
                                    Rp <?= number_format($raw_total, 0, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Calculation Summary -->
                <div class="total-section">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="total-row">
                                <span class="info-label me-4">Subtotal</span>
                                <span class="mono fw-bold text-dark">Rp <?= number_format($transaction['subtotal'], 0, ',', '.') ?></span>
                            </div>
                            <?php if ($transaction['discount_total'] > 0): ?>
                            <div class="total-row">
                                <span class="info-label me-4">Potongan Diskon</span>
                                <span class="mono fw-bold text-danger">- Rp <?= number_format($transaction['discount_total'], 0, ',', '.') ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($transaction['tax_amount'] > 0): ?>
                            <div class="total-row">
                                <span class="info-label me-4">Pajak (PPN)</span>
                                <span class="mono fw-bold text-dark">+ Rp <?= number_format($transaction['tax_amount'], 0, ',', '.') ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="total-row">
                                <span class="fw-900 text-dark fs-5 me-4">GRAND TOTAL</span>
                                <span class="mono fw-900 text-primary fs-4">Rp <?= number_format($transaction['grand_total'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info Section -->
        <div class="col-md-4">
            <!-- Customer Card -->
            <div class="premium-card">
                <div class="premium-card-header">
                    <h5><i class="fas fa-user-circle me-2 text-primary"></i> PELANGGAN</h5>
                </div>
                <div class="p-4">
                    <label class="info-label">Nama Pelanggan</label>
                    <div class="fw-900 text-dark mb-3 fs-5"><?= $customer['customer_name'] ?></div>
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light rounded-3 p-2 text-primary">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <label class="info-label m-0">Telepon</label>
                                <div class="fw-bold small"><?= $customer['customer_phone'] ?: 'Tidak ada nomor' ?></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light rounded-3 p-2 text-primary">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <label class="info-label m-0">Email</label>
                                <div class="fw-bold small text-truncate"><?= $customer['customer_email'] ?: '-' ?></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light rounded-3 p-2 text-primary">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <label class="info-label m-0">Lokasi / Alamat</label>
                                <div class="fw-bold small"><?= $customer['customer_address'] ?: 'Alamat tidak diatur' ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4 border-slate-100">
                    
                    <div class="bg-primary bg-opacity-10 rounded-pill px-3 py-2 text-center text-primary fw-bold small">
                        Tipe: <?= strtoupper($customer['customer_type']) ?>
                    </div>
                </div>
            </div>

            <!-- Payment Status Summary Card -->
            <div class="premium-card bg-primary bg-opacity-10 border-primary border-opacity-25">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="info-label text-primary">STATUS PEMBAYARAN</span>
                        <?php
                        $payClass = $transaction['payment_status'] == 'paid' ? 'bg-success text-white' : ($transaction['payment_status'] == 'partial' ? 'bg-warning text-dark' : 'bg-danger text-white');
                        ?>
                        <span class="badge-status <?= $payClass ?> shadow-sm">
                            <?= strtoupper($transaction['payment_status']) ?>
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold text-muted">Dibayar:</span>
                        <span class="mono fw-bold text-success">Rp <?= number_format($transaction['grand_total'] - ($transaction['grand_total'] - $transaction['down_payment']), 0, ',', '.') ?></span>
                    </div>
                    <?php if($transaction['payment_status'] != 'paid'): ?>
                    <div class="d-flex justify-content-between">
                        <span class="small fw-bold text-muted">Sisa Tagihan:</span>
                        <span class="mono fw-bold text-danger">Rp <?= number_format($transaction['grand_total'] - $transaction['down_payment'], 0, ',', '.') ?></span>
                    </div>
                    <a href="#" class="btn btn-primary w-100 mt-4 rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPayment">
                        <i class="fas fa-coins me-2"></i> TAMBAH PEMBAYARAN
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Action Print -->
            <div class="d-grid gap-2">
                <a href="<?= base_url('transaksi/manual_invoice/' . $transaction['transaction_id']) ?>" target="_blank" class="btn btn-dark py-3 rounded-4 fw-800 shadow-sm border-0">
                    <i class="fas fa-print fa-lg me-2"></i> CETAK NOTA / INVOICE
                </a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layout_admin/footer'); ?>
<!-- Modal Tambah Pembayaran -->
<div class="modal fade" id="modalPayment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 text-start">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-800 text-dark">
                    <i class="fas fa-coins text-primary me-2"></i> TAMBAH PEMBAYARAN
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPayment">
                <input type="hidden" name="transaction_id" value="<?= $transaction['transaction_id'] ?>">
                <div class="modal-body p-4">
                    <div class="mb-4 p-3 bg-light rounded-4 border-start border-4 border-primary">
                        <label class="info-label mb-1">Total Sisa Tagihan</label>
                        <div class="fs-4 fw-900 text-primary mono">
                            Rp <?= number_format($transaction['grand_total'] - $transaction['total_bayar'], 0, ',', '.') ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Jumlah Bayar</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 fw-bold">Rp</span>
                            <input type="number" name="amount" class="form-control border-start-0 fw-bold mono" 
                                   value="<?= $transaction['grand_total'] - $transaction['total_bayar'] ?>" 
                                   max="<?= $transaction['grand_total'] - $transaction['total_bayar'] ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Metode Pembayaran</label>
                        <select name="payment_method" class="form-select fw-bold" required>
                            <option value="tunai">Tunai</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="debit">Debit / EDC</option>
                            <option value="ewallet">E-Wallet</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Catatan / Referensi</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="Contoh: Transfer BCA xxxx"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4 justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        SIMPAN PEMBAYARAN <i class="fas fa-check-circle ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Handle Form Submit
    $('#formPayment').on('submit', function(e) {
        e.preventDefault();
        
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> MEMPROSES...');

        $.ajax({
            url: '<?= base_url("penjualan/ajax_manual_add_payment") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Terjadi kesalahan pada server atau endpoint tidak ditemukan.'
                });
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
