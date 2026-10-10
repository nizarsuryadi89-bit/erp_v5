<!-- Statistics Grid Minimal -->
<div class="stats-minimal" id="statsWrapper">
    <div class="clean-card stat-box primary">
        <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.75rem; opacity: 0.8;">Total
            Karyawan</div>
        <div class="h3 fw-900 font-mono text-dark mb-0 mt-1"><?= number_format($jml) ?> <small
                class="fw-bold text-muted ls-0" style="font-size: 0.8rem;">ORANG</small></div>
    </div>
    <?php
    $colors = ['primary', 'warning', 'success', 'danger', 'indigo', 'dark'];
    foreach ($company_stats as $index => $stat):
        $color = $colors[$index % count($colors)];
        // Check if user has access to this branch
        $is_restricted = ($this->session->userdata('perusahaan_id') != 1 && $this->session->userdata('level') != 21 && $this->session->userdata('perusahaan_id') != $stat->id_perusahaan);
        if ($is_restricted)
            continue;
        ?>
        <div class="clean-card stat-box <?= $color ?>" style="cursor: pointer;"
            onclick="filterByPerusahaan(<?= $stat->id_perusahaan ?>)">
            <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.75rem; opacity: 0.8;">
                <?= $stat->nama_perusahaan ?>
            </div>
            <div
                class="h3 fw-900 font-mono text-<?= $color == 'primary' ? 'primary-tgm' : ($color == 'success' ? 'emerald' : $color) ?> mb-0 mt-1">
                <?= number_format($stat->total_karyawan) ?>
                <small class="fw-bold text-muted ls-0" style="font-size: 0.8rem;">STAFF</small>
            </div>
        </div>
    <?php endforeach; ?>
</div>