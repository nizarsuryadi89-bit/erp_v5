<div class="row g-3">
    <!-- Search Bar -->
    <div class="col-12 mt-2">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-start-0 rounded-end-pill" placeholder="Cari Konsumen..."
                id="searchKonsumen">
        </div>
    </div>

    <!-- Top Spender Card -->
    <?php if (!empty($top_konsumen)): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-primary text-white" style="border-radius: 20px;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 opacity-10" style="margin-right: -20px; margin-top: -20px;">
                        <i class="fas fa-crown fa-6x"></i>
                    </div>

                    <span class="badge bg-white text-primary mb-2 rounded-pill px-3 fw-bold">PELANGGAN SULTAN (JUARA
                        1)</span>
                    <h5 class="fw-bold mb-0">
                        <?= $top_konsumen[0]->konsumen_nama ?>
                    </h5>
                    <div class="mt-2 text-white-50 small">Total Belanja Bulan Ini:</div>
                    <div class="h2 fw-900 mb-0">Rp
                        <?= number_format($top_konsumen[0]->total_belanja, 0, ',', '.') ?>
                    </div>
                    <div class="mt-2 text-white-50 small"><i class="fas fa-shopping-bag me-1"></i>
                        <?= $top_konsumen[0]->total_trx ?>x Transaksi
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-medal text-warning me-2"></i>Top 10 Pelanggan Loyal</h6>

            <?php foreach ($top_konsumen as $index => $k): ?>
                <?php if ($index > 0): // Skip Juara 1 (sudah ditampilkan di atas) ?>
                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 16px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 text-center" style="width: 40px;">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center fw-bold text-muted"
                                        style="width: 35px; height: 35px;">
                                        #
                                        <?= $index + 1 ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold text-dark mb-0">
                                        <?= $k->konsumen_nama ?>
                                    </h6>
                                    <div class="small text-muted">
                                        <?= $k->total_trx ?> Transaksi
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">Rp
                                        <?= number_format($k->total_belanja / 1000, 0) ?>rb
                                    </div>
                                    <a href="https://wa.me/<?= '62' . ltrim($k->konsumen_nohp, '0') ?>" target="_blank"
                                        class="btn btn-sm btn-success rounded-circle mt-1"
                                        style="width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <img src="<?= base_url('assets/img/empty.svg') ?>" alt="Empty" class="img-fluid mb-3"
                style="width: 150px; opacity: 0.5;">
            <p class="text-muted fw-bold">Belum ada data transaksi bulan ini.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    // Simple filter script
    document.getElementById('searchKonsumen').addEventListener('keyup', function () {
        let filter = this.value.toUpperCase();
        let cards = document.querySelectorAll('.card');

        cards.forEach(card => {
            let text = card.textContent || card.innerText;
            if (text.toUpperCase().indexOf(filter) > -1) {
                card.style.display = "";
            } else {
                if (!card.classList.contains('bg-primary')) { // Jangan sembunyikan kartu Juara 1
                    card.style.display = "none";
                }
            }
        });
    });
</script>