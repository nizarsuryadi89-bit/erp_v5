<?php
/**
 * Deskprint Order Center - Landing Page
 * Provides a high-end interface for selecting product categories and starting orders.
 */
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    :root {
        --tgm-gradient: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        --tgm-accent: #3b82f6;
        --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }

    .deskprint-hero {
        background: var(--tgm-gradient);
        border-radius: 1.5rem;
        padding: 3rem 2rem;
        color: white;
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .deskprint-hero::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
        pointer-events: none;
    }

    .category-card {
        border: none;
        border-radius: 1.25rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        background: #fff;
        height: 100%;
        overflow: hidden;
    }

    .category-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: var(--tgm-accent);
    }

    .category-card .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.25rem;
        background: #f1f5f9;
        transition: all 0.3s ease;
    }

    .category-card:hover .icon-box {
        background: var(--tgm-accent);
        color: white;
    }

    .stat-pill {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1.25rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .search-container {
        position: relative;
        max-width: 600px;
        margin: -1.5rem auto 0;
        z-index: 10;
    }

    .search-input {
        height: 60px;
        border-radius: 30px !important;
        padding-left: 2rem;
        padding-right: 4rem;
        border: none;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        font-weight: 600;
        font-size: 1.1rem;
    }

    .search-btn {
        position: absolute;
        right: 10px;
        top: 10px;
        height: 40px;
        width: 40px;
        border-radius: 20px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="p-4" style="background: #f8fafc; min-height: 100vh;">
    <!-- HERO SECTION -->
    <div class="deskprint-hero animate__animated animate__fadeIn">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="stat-pill mb-3">
                    <i class="fas fa-print"></i> Deskprint Order Center
                </span>
                <h1 class="display-5 fw-900 mb-2 ls-1">Halo,
                    <?= ucfirst($username) ?>!
                </h1>
                <p class="lead opacity-75 mb-4">Silahkan pilih kategori produk atau gunakan pencarian untuk mulai
                    membuat pesanan baru.</p>
                <div class="d-flex gap-3">
                    <div class="stat-pill">
                        <i class="fas fa-shopping-basket"></i>
                        <?= $jml_order_saya ?> Pesanan Kamu Bulan Ini
                    </div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-end">
                <img src="<?= base_url('assets/images/karyawan/' . ($foto['foto'] ?? 'default.png')) ?>"
                    class="rounded-circle border border-4 border-white border-opacity-25 shadow-lg"
                    style="width: 150px; height: 150px; object-fit: cover;">
            </div>
        </div>
    </div>

    <!-- SEARCH BAR -->
    <div class="search-container animate__animated animate__fadeInUp">
        <form action="<?= base_url('transaksi/createspk') ?>" method="get" class="position-relative">
            <input type="text" name="q" class="form-control search-input"
                placeholder="Ketik nama produk atau scan barcode...">
            <button type="submit" class="btn btn-primary search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- CATEGORY GRID -->
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-800 text-dark mb-0">PRODUK BERDASARKAN KATEGORI</h4>
            <a href="<?= base_url('transaksi/transaksi_deskprint') ?>"
                class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold">
                LIHAT RIWAYAT SAYA <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php
            $icons = [
                1 => 'fa-scroll',       // Banner
                2 => 'fa-sticky-note', // Sticker
                3 => 'fa-id-card',     // Kartu Nama
                4 => 'fa-book-open',   // Brosur/Buku
                5 => 'fa-tshirt',      // Merchandise
                'default' => 'fa-box'
            ];

            foreach ($kategori as $kat):
                $icon = $icons[$kat->kategori_id] ?? $icons['default'];
                ?>
                <div class="col-md-6 col-lg-4 col-xl-3 animate__animated animate__fadeInUp">
                    <div class="card category-card p-4 shadow-sm h-100" onclick="startOrder(<?= $kat->kategori_id ?>)">
                        <div class="icon-box text-primary transition-all">
                            <i class="fas <?= $icon ?> fa-lg"></i>
                        </div>
                        <h5 class="fw-900 text-dark mb-2">
                            <?= strtoupper($kat->kategori_nama) ?>
                        </h5>
                        <p class="small text-muted mb-0">
                            <?= !empty($kat->kategori_deskripsi) ? $kat->kategori_deskripsi : 'Klik untuk mulai membuat pesanan ' . strtolower($kat->kategori_nama) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- ACTION CARD -->
            <div class="col-md-6 col-lg-4 col-xl-3 animate__animated animate__fadeInUp">
                <div class="card category-card p-4 shadow-sm h-100 bg-primary bg-opacity-10 border-dashed border-primary border-2"
                    onclick="window.location.href='<?= base_url('transaksi/createspk') ?>'">
                    <div class="icon-box bg-primary text-white mb-3">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h5 class="fw-900 text-primary mb-2">MANUAL SPK</h5>
                    <p class="small text-primary opacity-75 mb-0">Input data SPK secara manual (Langkah 1 & 2).</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Queue Widget -->
<?php $this->load->view('kiosk/queue_widget'); ?>

<script>
    function startOrder(catId) {
        // Direct to Step 1 first as it's required to create SPK Record
        window.location.href = '<?= base_url('transaksi/createspk?cat_id=') ?>' + catId;
    }
</script>