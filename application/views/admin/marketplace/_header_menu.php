<?php
$seg2 = $this->uri->segment(2);

// Mapping active segments
$is_dashboard = ($seg2 == 'dashboard');
$is_pesanan   = (empty($seg2) || $seg2 == 'index' || $seg2 == 'detail');
$is_tracking  = ($seg2 == 'tracking' || $seg2 == 'tracking_batch');
$is_laporan   = ($seg2 == 'laporan_marketplace' || $seg2 == 'laporan_marketplace_harian');
?>

<a href="<?= site_url('marketplace/dashboard') ?>" class="btn <?= $is_dashboard ? 'btn-light text-primary fw-bold' : 'btn-outline-light' ?> btn-premium shadow-sm">
    <i class="fas fa-home me-1"></i> Dashboard
</a>
<a href="<?= site_url('marketplace') ?>" class="btn <?= $is_pesanan ? 'btn-light text-primary fw-bold' : 'btn-outline-light' ?> btn-premium shadow-sm">
    <i class="fas fa-list me-1"></i> Pesanan
</a>
<a href="<?= site_url('marketplace/tracking_batch') ?>" class="btn <?= $is_tracking ? 'btn-light text-primary fw-bold' : 'btn-outline-light' ?> btn-premium shadow-sm">
    <i class="fas fa-shipping-fast me-1"></i> Tracking
</a>
<a href="<?= site_url('marketplace/laporan_marketplace') ?>" class="btn <?= $is_laporan ? 'btn-light text-primary fw-bold' : 'btn-outline-light' ?> btn-premium shadow-sm">
    <i class="fas fa-chart-bar me-1"></i> Laporan
</a>



<!-- SOP Button -->
<button type="button" class="btn btn-info text-white fw-bold btn-premium shadow-sm ms-md-2 mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#modalSOPMarketplace">
    <i class="fas fa-book me-1"></i> SOP
</button>

<!-- Modal SOP Marketplace -->
<div class="modal fade text-start" id="modalSOPMarketplace" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h3 class="modal-title fw-bold text-dark" style="font-size: 1.25rem;">
                    <i class="fas fa-book text-info me-2"></i>SOP & Alur Modul Marketplace
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 shadow-sm mb-4" style="background-color: #e0f2fe; color: #0284c7;">
                    <strong><i class="fas fa-info-circle me-1"></i> Info Penting:</strong> Transaksi marketplace (Shopee, Tokopedia, TikTok, dll) memiliki alur kerja khusus (Bypass Kasir). Pesanan langsung memotong stok atau diteruskan ke tim produksi setelah diinput.
                </div>
                
                <h5 class="fw-bold text-primary mb-2" style="font-size: 1.05rem;">1. Pesanan Baru (Modul Pesanan)</h5>
                <ul class="text-muted mb-4" style="font-size: 0.95rem;">
                    <li>Pesanan yang masuk diinput ke dalam menu <strong>Pesanan</strong>.</li>
                    <li>Sistem otomatis membuatkan <strong>SPK (Surat Perintah Kerja)</strong> tanpa melalui Kasir (Bypass).</li>
                    <li>Status awal pesanan adalah <span class="badge bg-warning-lt text-warning">Proses</span> yang artinya barang sedang diproses dan pembayaran belum dicairkan oleh platform.</li>
                </ul>

                <h5 class="fw-bold text-success mb-2" style="font-size: 1.05rem;">2. Proses & Pengiriman (Modul Tracking)</h5>
                <ul class="text-muted mb-4" style="font-size: 0.95rem;">
                    <li>Gunakan menu <strong>Tracking</strong> untuk memantau status pengerjaan SPK (<em>Proses -> Packing -> Selesai</em>).</li>
                    <li>Gunakan <strong>Tracking Batch</strong> untuk memantau <em>deadline</em> atau batas waktu pengiriman ke kurir agar terhindar dari poin penalti.</li>
                    <li>Pastikan untuk mengupdate <strong>Nomor Resi</strong> di halaman Detail Pesanan agar paket siap dipickup kurir.</li>
                </ul>

                <h5 class="fw-bold text-warning mb-2" style="font-size: 1.05rem;"><i class="fas fa-coins me-1"></i> 3. Pencairan & Laporan Keuangan</h5>
                <ul class="text-muted mb-4" style="font-size: 0.95rem;">
                    <li>Saat pembeli menekan tombol "Pesanan Diterima", dana akan masuk ke Saldo Penjual di aplikasi Marketplace terkait.</li>
                    <li>Admin wajib mengupdate status pembayaran menjadi <span class="badge bg-success-lt text-success">Lunas</span> di sistem.</li>
                    <li>Pesanan yang berstatus Lunas otomatis terekap dalam <strong>Laporan Keuangan Marketplace</strong> (dihitung sebagai omset).</li>
                </ul>
                
                <h5 class="fw-bold text-dark mb-2" style="font-size: 1.05rem;">4. Manajemen Platform (Dashboard)</h5>
                <ul class="text-muted mb-0" style="font-size: 0.95rem;">
                    <li>Gunakan halaman <strong>Dashboard</strong> untuk menghidupkan/mematikan platform, mengisi URL toko, serta mengatur potongan Biaya Admin (%).</li>
                </ul>
            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Mengerti & Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pindahkan modal ke body untuk menghindari masalah z-index (tertumpuk)
    var modal = document.getElementById('modalSOPMarketplace');
    if(modal) {
        document.body.appendChild(modal);
    }
});
</script>
