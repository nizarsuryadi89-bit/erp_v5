<div class="row mb-3">
    <div class="col-12">
        <div class="card card-outline card-primary shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-2">
                <ul class="nav nav-pills nav-fill text-sm" style="font-weight: 500;">
                    <?php
                    $current_url = current_url();
                    
                    $tabs = [
                        ['url' => base_url('pengaturan'), 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
                        ['url' => base_url('pengaturan/perusahaan'), 'icon' => 'bi-building', 'label' => 'Perusahaan'],
                        ['url' => base_url('pengaturan/kategori'), 'icon' => 'bi-tags', 'label' => 'Kategori'],
                        ['url' => base_url('pengaturan/satuan'), 'icon' => 'bi-rulers', 'label' => 'Satuan'],
                        ['url' => base_url('pengaturan/supplier'), 'icon' => 'bi-truck', 'label' => 'Supplier'],
                        ['url' => base_url('pengaturan/mesin'), 'icon' => 'bi-printer', 'label' => 'Mesin'],
                        ['url' => base_url('pengaturan/harilibur'), 'icon' => 'bi-calendar-x', 'label' => 'Hari Libur'],
                        ['url' => base_url('pengaturan/rekening'), 'icon' => 'bi-credit-card', 'label' => 'Rekening'],
                        ['url' => base_url('pengaturan/ewallet'), 'icon' => 'bi-wallet2', 'label' => 'E-Wallet'],
                    ];
                    
                    foreach ($tabs as $tab) {
                        // Check exact match for dashboard, otherwise check prefix
                        if ($tab['url'] === base_url('pengaturan')) {
                            $is_active = ($current_url == base_url('pengaturan') || $current_url == base_url('pengaturan/index')) ? 'active bg-primary' : '';
                        } else {
                            $is_active = (strpos($current_url, $tab['url']) === 0) ? 'active bg-primary' : '';
                        }
                        
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link rounded-pill px-3 py-2 ' . $is_active . '" href="' . $tab['url'] . '">';
                        echo '<i class="bi ' . $tab['icon'] . ' me-1"></i> ' . $tab['label'];
                        echo '</a>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
