<div class="row g-3">
  <!-- Header Stats -->
  <div class="col-12 mt-2">
    <div class="card border-0 bg-danger text-white rounded-4 shadow-sm position-relative overflow-hidden">
      <div class="position-absolute end-0 top-0 opacity-10 me-n3 mt-n3">
        <i class="bi bi-wallet2" style="font-size: 8rem;"></i>
      </div>
      <div class="card-body p-4 position-relative">
        <div class="d-flex align-items-center mb-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 me-2">
            <i class="bi bi-exclamation-circle-fill text-white"></i>
          </div>
          <span class="fw-bold opacity-75">TOTAL PIUTANG USAHA</span>
        </div>
        <h2 class="fw-900 display-4 mb-0">Rp <?= number_format($sisa_piutang, 0) ?></h2>
        <div class="mt-2 text-white-50 small">
          Dari <?= $count_piutang ?> Customer yang belum lunas
        </div>
      </div>
    </div>
  </div>

  <!-- Search / Action Bar -->
  <div class="col-12 mt-2">
    <div class="d-flex gap-2">
      <div class="input-group">
        <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
          <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" id="searchPiutang" class="form-control border-start-0 rounded-end-pill"
          placeholder="Cari Nama / SPK...">
      </div>
      <button class="btn btn-dark rounded-circle flex-shrink-0" style="width: 42px; height: 42px;"
        onclick="location.reload()">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
    </div>
  </div>

  <!-- List Piutang -->
  <div class="col-12">
    <div class="section-title mb-3 text-danger">
      <i class="bi bi-list-task me-2"></i> Daftar Tagihan Belum Lunas
    </div>

    <?php foreach ($list_piutang as $row):
      $jatuh_tempo = strtotime($row->jatuh_tempo);
      $today = time();
      $is_overdue = ($jatuh_tempo < $today);
      $days_diff = floor(($jatuh_tempo - $today) / (60 * 60 * 24));

      // Badge Status Check
      $badge_color = 'bg-warning text-dark';
      $badge_text = 'H-' . abs($days_diff); // H-3
    
      if ($is_overdue) {
        $badge_color = 'bg-danger text-white';
        $badge_text = 'Telat ' . abs($days_diff) . ' Hari';
      } elseif ($days_diff == 0) {
        $badge_color = 'bg-danger text-white blinking';
        $badge_text = 'HARI INI';
      }
      ?>
      <div class="card border-0 shadow-sm mb-3 piutang-card" style="border-radius: 16px;">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <span class="badge bg-light text-dark border mb-1"><?= $row->nospk ?></span>
              <div class="fw-bold text-dark text-truncate" style="max-width: 200px;"><?= $row->konsumen_nama ?></div>
            </div>
            <span class="badge <?= $badge_color ?> rounded-pill" style="font-size: 0.7rem;">
              <i class="bi bi-clock me-1"></i><?= $badge_text ?>
            </span>
          </div>

          <div class="row align-items-end mt-3">
            <div class="col-7">
              <div class="small text-muted mb-1">Total Tagihan</div>
              <div class="progress" style="height: 6px; border-radius: 10px;">
                <?php
                $percent = ($row->total_tagihan > 0) ? ($row->total_bayar / $row->total_tagihan) * 100 : 0;
                ?>
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%"></div>
              </div>
              <div class="d-flex justify-content-between mt-1" style="font-size: 0.7rem;">
                <span class="text-success fw-bold"><?= number_format($percent, 0) ?>% Lunas</span>
              </div>
            </div>
            <div class="col-5 text-end">
              <div class="small text-muted">Sisa Tagihan</div>
              <div class="h6 fw-900 text-danger mb-0">
                Rp <?= number_format($row->sisa_piutang, 0, ',', '.') ?>
              </div>
            </div>
          </div>

          <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
            <div class="small text-muted">
              <i class="bi bi-calendar-event me-1"></i> Jatuh Tempo:
              <span class="<?= $is_overdue ? 'text-danger fw-bold' : '' ?>">
                <?= date('d M Y', strtotime($row->jatuh_tempo)) ?>
              </span>
            </div>
            <a href="https://wa.me/<?= '62' . ltrim($row->konsumen_nohp, '0') ?>?text=Halo Kak <?= $row->konsumen_nama ?>, %0AKami dari TGM Print ingin menginfokan tagihan SPK <?= $row->nospk ?> sebesar Rp <?= number_format($row->sisa_piutang, 0) ?> sudah jatuh tempo tanggal <?= date('d M Y', strtotime($row->jatuh_tempo)) ?>. %0AMohon segera melakukan pembayaran. Terima kasih."
              target="_blank" class="btn btn-sm btn-success rounded-pill px-3">
              <i class="bi bi-whatsapp me-1"></i> Tagih
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <?php if (empty($list_piutang)): ?>
      <div class="text-center py-5">
        <img src="<?= base_url('assets/img/empty.svg') ?>" class="img-fluid mb-3 w-50 opacity-50">
        <p class="text-muted fw-bold">Tidak ada data piutang.</p>
        <div class="small text-muted">Semua tagihan lunas! Kerja bagus.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
  // Search Filter
  document.getElementById('searchPiutang').addEventListener('keyup', function () {
    let filter = this.value.toUpperCase();
    let cards = document.querySelectorAll('.piutang-card');

    cards.forEach(card => {
      let text = card.textContent || card.innerText;
      if (text.toUpperCase().indexOf(filter) > -1) {
        card.style.display = "";
      } else {
        card.style.display = "none";
      }
    });
  });
</script>

<style>
  @keyframes blink {
    50% {
      opacity: 0.5;
    }
  }

  .blinking {
    animation: blink 1s linear infinite;
  }
</style>