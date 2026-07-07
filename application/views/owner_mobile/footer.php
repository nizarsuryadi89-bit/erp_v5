<!-- Bottom Navigation -->
<div class="bottom-nav">
    <a href="<?= base_url('Owner/dashboard/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
        class="nav-item <?= ($title == 'Dashboard Owner' || $title == 'Pusat Laporan Keuangan') ? 'active' : '' ?>">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="javascript:void(0)" onclick="alert('Fitur Notifikasi segera hadir!');" class="nav-item">
        <i class="fas fa-bell"></i>
        <span>Notifikasi</span>
    </a>
    <a href="<?= base_url('auth/') ?>" class="nav-item text-muted">
        <i class="fas fa-sign-in-alt"></i>
        <span>Login ERP</span>
    </a>
    <a href="<?= base_url('Owner/logout') ?>" class="nav-item" style="color: #ef4444;">
        <i class="fas fa-sign-out-alt"></i>
        <span style="color: #ef4444;">Keluar</span>
    </a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Add slight padding if viewing on device with notch (iOS safe area) -->
<script>
    if (CSS.supports('padding-bottom: env(safe-area-inset-bottom)')) {
        document.querySelector('.bottom-nav').style.paddingBottom = 'calc(0.75rem + env(safe-area-inset-bottom))';
    }
</script>

</body>

</html>