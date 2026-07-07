<?php
/**
 * LIVE PRODUCTION CONSUMPTION FEED
 */
?>

<div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 28px; background: white;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0 fw-900 text-dark">
            <i
                class="fas <?= ($monitor_type ?? 'Produksi') == 'Dispatch' ? 'fa-truck-loading text-primary' : 'fa-microchip text-danger' ?> me-2"></i>
            <?= ($monitor_type ?? 'Produksi') == 'Dispatch' ? 'Live Dispatch' : 'Live Consumption' ?>
        </h5>
        <span
            class="badge <?= ($monitor_type ?? 'Produksi') == 'Dispatch' ? 'badge-live-pulse-blue' : 'badge-live-pulse' ?> px-3 py-1 rounded-pill fw-bold">
            <?= ($monitor_type ?? 'Produksi') == 'Dispatch' ? 'INTERNAL TRANSFER' : 'PRODUCTION' ?>
        </span>
    </div>

    <?php if (empty($consumption_logs)): ?>
        <div class="text-center py-5 opacity-50">
            <i
                class="fas <?= ($monitor_type ?? 'Produksi') == 'Dispatch' ? 'fa-shipping-fast' : 'fa-history' ?> fa-3x mb-3 text-muted"></i>
            <p class="small fw-bold text-muted">Belum ada
                <?= ($monitor_type ?? 'Produksi') == 'Dispatch' ? 'pengiriman barang' : 'pemakaian produksi' ?> hari ini.
            </p>
        </div>
    <?php else: ?>
        <div class="consumption-feed">
            <?php foreach ($consumption_logs as $log): ?>
                <div class="consumption-item d-flex gap-3 mb-4">
                    <div class="consumption-icon <?= ($monitor_type ?? 'Produksi') == 'Dispatch' ? 'bg-soft-primary text-primary' : 'bg-soft-danger text-danger' ?> rounded-4 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i
                            class="fas <?= ($monitor_type ?? 'Produksi') == 'Dispatch' ? 'fa-arrow-right' : 'fa-minus-circle' ?>"></i>
                    </div>
                    <div class="consumption-body border-bottom pb-3 flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="fw-800 text-dark fs-7">
                                <?= htmlspecialchars($log->bahan_nama) ?>
                            </div>
                            <span class="text-muted" style="font-size: 0.65rem;">
                                <i class="far fa-clock me-1"></i>
                                <?= date('H:i', strtotime($log->created_at)) ?>
                            </span>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-light text-dark fw-bold" style="font-size: 0.65rem;">
                                <?= htmlspecialchars($log->varian) ?>
                            </span>

                            <?php
                            $konversi = (float) ($log->konversi ?: 1);
                            $qty_gudang = (float) ($log->qty_out ?: 0);
                            $qty_produksi = $qty_gudang * $konversi;
                            ?>

                            <span
                                class="badge <?= ($monitor_type ?? 'Produksi') == 'Dispatch' ? 'bg-soft-primary text-primary' : 'bg-soft-danger text-danger' ?> fw-800"
                                style="font-size: 0.7rem;">
                                <?= ($monitor_type ?? 'Produksi') == 'Dispatch' ? '' : '-' ?>
                                <?= number_format($qty_produksi) ?>         <?= $log->satuan_produksi ?: 'Lbr' ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 600;">
                                <i class="fas fa-user-tag me-1 text-primary"></i>
                                <?= $log->nama_lengkap ?: $log->username ?>
                            </p>
                            <small class="text-muted italic" style="font-size: 0.65rem;">
                                <?= $log->gudang_nama ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .bg-soft-danger {
        background: rgba(244, 63, 76, 0.1);
    }

    .bg-soft-primary {
        background: rgba(37, 99, 235, 0.1);
    }

    .fs-7 {
        font-size: 0.85rem !important;
    }

    .consumption-item:last-child .consumption-body {
        border-bottom: 0 !important;
    }

    .consumption-feed {
        max-height: 250px;
        overflow-y: auto;
        padding-right: 5px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .consumption-item {
        flex: 1 1 calc(33.333% - 15px);
        min-width: 300px;
        background: #f8fafc;
        padding: 15px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        margin-bottom: 0 !important;
    }

    .consumption-feed::-webkit-scrollbar {
        width: 4px;
    }

    .consumption-feed::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    @keyframes pulse-live-red {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(244, 63, 76, 0.4);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(244, 63, 76, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(244, 63, 76, 0);
        }
    }

    @keyframes pulse-live-blue {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(37, 99, 235, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0);
        }
    }

    .badge-live-pulse {
        background: #f43f4c !important;
        color: white !important;
        animation: pulse-live-red 2s infinite;
        font-size: 0.6rem !important;
        letter-spacing: 1px;
    }

    .badge-live-pulse-blue {
        background: #2563eb !important;
        color: white !important;
        animation: pulse-live-blue 2s infinite;
        font-size: 0.6rem !important;
        letter-spacing: 1px;
    }
</style>