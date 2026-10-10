<?php
/**
 * REUSABLE COMPONENT: 6-Step Material Management Workflow
 * Usage: $this->load->view('admin/warehouse/components/v_material_workflow', ['current_step' => 2]);
 */
$step = isset($current_step) ? $current_step : 1;

// Define Steps Configuration
$steps_config = [
    1 => [
        'title' => 'Langkah 1',
        'desc' => 'Registrasi Induk & Unit Dasar',
        'icon' => 'fas fa-file-signature',
        'url' => base_url('warehouse/materials_jasa'),
        'task' => 'Daftarkan nama dasar barang dan tentukan Unit Terkecilnya (misal: cm, lembar, pcs). Ini adalah "mata uang" dasar stok Anda.'
    ],
    2 => [
        'title' => 'Langkah 2',
        'desc' => 'Registrasi Varian & Konversi',
        'icon' => 'fas fa-boxes',
        'url' => base_url('warehouse/materials'),
        'task' => 'Daftarkan tipe kemasan di gudang (Box/Roll/Pack) dan tentukan isi produk per kemasan tersebut relatif terhadap unit terkecil.'
    ],
    3 => [
        'title' => 'Langkah 3',
        'desc' => 'Koreksi Opname (Audit)',
        'icon' => 'fas fa-clipboard-check',
        'url' => base_url('warehouse/opname'),
        'task' => '⚠️ PERHATIAN: Hanya gunakan menu ini sebulan sekali untuk koreksi/audit fisik. JANGAN gunakan untuk input barang masuk baru.'
    ],
    4 => [
        'title' => 'Langkah 4',
        'desc' => 'Stok Masuk (Pembelian)',
        'icon' => 'fas fa-shopping-cart',
        'url' => base_url('warehouse/purchase'),
        'task' => 'Gunakan menu ini setiap kali ada barang masuk atau restock dari Supplier untuk menambah jumlah stok.'
    ],
    5 => [
        'title' => 'Langkah 5',
        'desc' => 'Dispatch (Stok Keluar)',
        'icon' => 'fas fa-shipping-fast',
        'url' => base_url('warehouse/dispatch'),
        'task' => 'Keluarkan bahan dari gudang utama menuju bagian produksi atau cabang. Ini akan mengurangi stok gudang utama secara otomatis.'
    ],
    6 => [
        'title' => 'Langkah 6',
        'desc' => 'Monitoring (Live Stok)',
        'icon' => 'fas fa-eye',
        'url' => base_url('warehouse/stock'),
        'task' => 'Pantau pergerakan stok harian secara real-time, cek mutasi/ledger, dan pastikan sisa stok tidak di bawah batas aman.'
    ],
];
?>

<style>
    /* SOP Workflow Styles */
    .sop-workflow-container {
        background: white;
        border-radius: 24px;
        padding: 1.5rem 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        position: relative;
    }

    .sop-steps {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
        z-index: 1;
    }

    .sop-step-item {
        flex: 1;
        text-align: center;
        position: relative;
        padding: 0 5px;
        text-decoration: none !important;
    }

    .sop-step-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #f1f5f9;
        z-index: -1;
    }

    .sop-step-item.active:not(:last-child)::after {
        background: linear-gradient(90deg, #6366f1 0%, #f1f5f9 100%);
    }

    .sop-step-item.completed:not(:last-child)::after {
        background: #6366f1;
    }

    .sop-step-icon {
        width: 40px;
        height: 40px;
        background: white;
        border: 2px solid #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 1rem;
        color: #94a3b8;
        transition: all 0.3s;
        box-shadow: 0 0 0 4px white;
    }

    .sop-step-item.active .sop-step-icon {
        background: #6366f1;
        border-color: #6366f1;
        color: white;
        box-shadow: 0 0 0 4px white, 0 10px 15px rgba(99, 102, 241, 0.2);
        transform: scale(1.1);
    }

    .sop-step-item.completed .sop-step-icon {
        background: #e0e7ff;
        border-color: #6366f1;
        color: #6366f1;
    }

    .sop-step-item.past .sop-step-icon {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #64748b;
    }

    /* Warning/Danger Step (e.g. OPNAME) */
    .sop-step-item.step-danger .sop-step-icon {
        border-color: #fecdd3;
        color: #e11d48;
        background: #fff1f2;
    }
    .sop-step-item.step-danger.active .sop-step-icon {
        background: #e11d48;
        border-color: #e11d48;
        color: white;
        box-shadow: 0 0 0 4px white, 0 10px 15px rgba(225, 29, 72, 0.2);
    }
    .sop-step-item.step-danger .sop-step-title {
        color: #e11d48;
    }

    .sop-step-title {
        font-weight: 800;
        font-size: 0.65rem;
        letter-spacing: 0.3px;
        margin-bottom: 3px;
        color: #1e293b;
        text-transform: uppercase;
    }

    .sop-step-desc {
        font-size: 0.55rem;
        color: #64748b;
        line-height: 1.2;
        max-width: 90px;
        margin: 0 auto;
    }
</style>

<div class="sop-workflow-container">
    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
        <div>
            <h6 class="fw-bold text-dark mb-0 small"><i class="fas fa-project-diagram me-2 text-primary"></i>ALUR KERJA
                INVENTORY (<?= $step ?>/6)</h6>
        </div>
        <div class="text-end">
            <span class="badge bg-soft-blue text-primary px-3 py-1 rounded-pill fw-bold"
                style="background: #eff6ff; color: #3b82f6; font-size: 0.65rem;">AKTIF:
                <?= $steps_config[$step]['desc'] ?></span>
        </div>
    </div>

    <div class="sop-steps">
        <?php foreach ($steps_config as $idx => $cfg): ?>
            <?php
            $status_class = '';
            if ($idx == $step)
                $status_class = 'active';
            elseif ($idx < $step)
                $status_class = 'completed past';
                
            if ($idx == 3) {
                $status_class .= ' step-danger';
            }
            ?>
            <a href="<?= $cfg['url'] ?>" class="sop-step-item <?= $status_class ?>">
                <div class="sop-step-icon">
                    <i class="<?= $cfg['icon'] ?>"></i>
                </div>
                <div class="sop-step-title"><?= $cfg['title'] ?></div>
                <div class="sop-step-desc"><?= $cfg['desc'] ?></div>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="mt-4 pt-3 border-top px-3">
        <div class="row align-items-center">
            <div class="col-md-9">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-circle"
                        style="width:24px; height:24px; display:flex; align-items:center; justify-content:center; background: #eff6ff;">
                        <i class="fa fa-info-circle text-primary" style="font-size: 10px;"></i>
                    </div>
                    <p class="mb-0" style="font-size: 0.7rem; color: #64748b;">
                        <strong>Task:</strong> <?= $steps_config[$step]['task'] ?>
                    </p>
                </div>
            </div>
            <div class="col-md-3 text-end">
                <?php if ($step > 1): ?>
                    <a href="<?= $steps_config[$step - 1]['url'] ?>" class="btn btn-xs btn-light border px-2 fw-bold"
                        style="border-radius: 8px; font-size: 0.65rem;">
                        <i class="fa fa-chevron-left me-1"></i> Prev
                    </a>
                <?php endif; ?>
                <?php if ($step < 6): ?>
                    <a href="<?= $steps_config[$step + 1]['url'] ?>" class="btn btn-xs btn-primary px-2 fw-bold ms-1"
                        style="border-radius: 8px; background: #6366f1; border:none; font-size: 0.65rem;">
                        Next <i class="fa fa-chevron-right ms-1"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>