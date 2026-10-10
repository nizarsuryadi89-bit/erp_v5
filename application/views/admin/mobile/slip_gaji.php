<?php
// Parse bulan for display
$bulan_awal = $bulan_filter ?? date('Y-m');
$bulan_nama = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
$split_bulan = explode('-', $bulan_awal);
$display_bulan = $bulan_nama[$split_bulan[1]] . ' ' . $split_bulan[0];
?>

<!-- Page Header -->
<div class="custom-page-header">
  <h5><i class="bi bi-wallet2 me-2 text-danger"></i>Slip Gaji Online</h5>
  <p>Periode Keuangan <?= $display_bulan ?></p>
</div>

<!-- Main Content -->
<div class="content-container">

  <!-- Summary Cards -->
  <div class="row g-2 mb-4">
    <div class="col-6">
      <div class="stat-card p-3 mb-0 text-center shadow-sm">
        <span class="d-block h5 fw-bold text-success mb-1"><?= $slip_gaji['total_hadir'] ?? 0 ?></span>
        <span class="text-muted small fw-bold">KEHADIRAN</span>
      </div>
    </div>
    <div class="col-6">
      <div class="stat-card p-3 mb-0 text-center shadow-sm">
        <span class="d-block h5 fw-bold text-danger mb-1"><?= $slip_gaji['total_alpha'] ?? 0 ?></span>
        <span class="text-muted small fw-bold">ALPHA / IZIN</span>
      </div>
    </div>
  </div>

  <!-- Main Salary Card -->
  <?php if ($slip_gaji): ?>
    <div class="stat-card p-0 overflow-hidden shadow mb-4" style="border-radius: 25px;">
      <style>
        .salary-header {
          background: #1e293b;
          color: #fff;
          padding: 1.5rem;
          text-align: center;
        }

        .salary-row {
          padding: 1rem 1.5rem;
          display: flex;
          justify-content: space-between;
          align-items: center;
          border-bottom: 1px solid #f1f5f9;
        }

        .salary-row:last-child {
          border-bottom: none;
        }

        .salary-row span {
          font-size: 0.85rem;
          color: #64748b;
          font-weight: 500;
        }

        .salary-row strong {
          font-size: 0.95rem;
          color: #1e293b;
          font-weight: 700;
        }

        .salary-row.sub-row {
          padding-left: 3rem;
          background: #fbfcfd;
        }

        .salary-row.sub-row strong {
          font-size: 0.85rem;
          color: #475569;
        }

        .salary-total {
          background: #f8fafc;
          padding: 1.5rem;
          border-top: 2px dashed #e2e8f0;
        }
      </style>

      <div class="salary-header">
        <div class="mb-2">
          <span class="badge rounded-pill bg-success-soft text-success px-3 py-1 shadow-sm"
            style="font-size:0.65rem; backdrop-filter: blur(4px);">
            <i class="bi bi-patch-check-fill me-1"></i> REAL-TIME CALCULATION
          </span>
        </div>
        <small class="text-white-50 text-uppercase d-block mb-1" style="letter-spacing: 1px;">Take Home Pay</small>
        <h2 class="fw-bold mb-0 text-warning">Rp <?= number_format($slip_gaji['total_gaji'], 0, ',', '.') ?></h2>
      </div>

      <div class="salary-body">
        <!-- 1. Gaji Pokok -->
        <div class="salary-row">
          <span><i class="bi bi-cash-stack me-2 text-primary"></i> Gaji Pokok</span>
          <strong>Rp <?= number_format($slip_gaji['gaji_pokok'] ?? 0, 0, ',', '.') ?></strong>
        </div>

        <!-- 2. Tunjangan Jabatan -->
        <div class="salary-row">
          <span><i class="bi bi-person-badge me-2 text-info"></i> Tunj. Jabatan</span>
          <strong>Rp <?= number_format($slip_gaji['tunjangan_jabatan'] ?? 0, 0, ',', '.') ?></strong>
        </div>

        <!-- 3. Tunjangan Transportasi -->
        <div class="salary-row">
          <span><i class="bi bi-car-front me-2 text-info"></i> Tunj. Trans</span>
          <strong>Rp <?= number_format($slip_gaji['tunjangan_transportasi'] ?? 0, 0, ',', '.') ?></strong>
        </div>

        <!-- 4. Nominal Lembur Breakdown -->
        <div class="bg-light px-4 py-2"><small class="premium-label m-0"
              style="color:#0ea5e9; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Nominal Lembur</small></div>
        
        <div class="salary-row sub-row">
          <span>Pagi</span>
          <strong>Rp <?= number_format($slip_gaji['detail_lembur']['pagi']['total'] ?? 0, 0, ',', '.') ?></strong>
        </div>
        <div class="salary-row sub-row">
          <span>Wajib</span>
          <strong>Rp <?= number_format($slip_gaji['detail_lembur']['wajib']['total'] ?? 0, 0, ',', '.') ?></strong>
        </div>
        <div class="salary-row sub-row">
          <span>Kerja</span>
          <strong>Rp <?= number_format($slip_gaji['detail_lembur']['kerjaan']['total'] ?? 0, 0, ',', '.') ?></strong>
        </div>
        <div class="salary-row border-top-0 pt-0">
          <span class="fw-bold">Total Lembur</span>
          <strong class="text-primary">Rp <?= number_format($slip_gaji['uang_lembur'] ?? 0, 0, ',', '.') ?></strong>
        </div>

        <!-- 5. Uang Makan -->
        <div class="salary-row" style="background: #fff7ed;">
          <span style="color: #ea580c;"><i class="bi bi-egg-fried me-2"></i> Uang Makan</span>
          <strong style="color: #ea580c;">Rp <?= number_format($slip_gaji['biaya_makan_lembur'] ?? 0, 0, ',', '.') ?></strong>
        </div>

        <!-- 6. Reward -->
        <div class="salary-row">
          <span class="text-success"><i class="bi bi-gift me-2"></i> Reward</span>
          <strong class="text-success">Rp <?= number_format(($slip_gaji['reward'] ?? 0) + ($slip_gaji['tunjangan_extra'] ?? 0) + ($slip_gaji['tunjangan_kinerja_manual'] ?? 0), 0, ',', '.') ?></strong>
        </div>

        <?php for($i=1; $i<=3; $i++): ?>
        <?php if(isset($slip_gaji["custom_in{$i}_nom"]) && $slip_gaji["custom_in{$i}_nom"] > 0): ?>
        <div class="salary-row">
          <span class="text-success"><i class="bi bi-plus-circle me-2"></i> <?= htmlspecialchars($slip_gaji["custom_in{$i}_nama"] ?: "Tambahan Pendapatan $i") ?></span>
          <strong class="text-success">Rp <?= number_format($slip_gaji["custom_in{$i}_nom"], 0, ',', '.') ?></strong>
        </div>
        <?php endif; ?>
        <?php endfor; ?>

        <!-- 7. Deduksi / Potongan -->
        <div class="bg-light px-4 py-2"><small class="premium-label m-0"
              style="color:#ef4444; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Potongan</small></div>

        <?php 
          $total_potongan = ($slip_gaji['potongan_alpha'] ?? 0) + ($slip_gaji['potongan_terlambat'] ?? 0) + ($slip_gaji['kasbon'] ?? 0) + ($slip_gaji['pinjaman'] ?? 0) + ($slip_gaji['custom_out1_nom'] ?? 0) + ($slip_gaji['custom_out2_nom'] ?? 0) + ($slip_gaji['custom_out3_nom'] ?? 0);
        ?>
        <div class="salary-row sub-row">
          <span>Alpha / Izin</span>
          <strong class="text-danger">- Rp <?= number_format($slip_gaji['potongan_alpha'] ?? 0, 0, ',', '.') ?></strong>
        </div>
        <div class="salary-row sub-row">
          <span>Terlambat</span>
          <strong class="text-danger">- Rp <?= number_format($slip_gaji['potongan_terlambat'] ?? 0, 0, ',', '.') ?></strong>
        </div>
        <div class="salary-row sub-row">
          <span>Kasbon / Pinjaman</span>
          <strong class="text-danger">- Rp <?= number_format(($slip_gaji['kasbon'] ?? 0) + ($slip_gaji['pinjaman'] ?? 0), 0, ',', '.') ?></strong>
        </div>
        <?php for($i=1; $i<=3; $i++): ?>
        <?php if(isset($slip_gaji["custom_out{$i}_nom"]) && $slip_gaji["custom_out{$i}_nom"] > 0): ?>
        <div class="salary-row sub-row">
          <span><?= htmlspecialchars($slip_gaji["custom_out{$i}_nama"] ?: "Tambahan Potongan $i") ?></span>
          <strong class="text-danger">- Rp <?= number_format($slip_gaji["custom_out{$i}_nom"], 0, ',', '.') ?></strong>
        </div>
        <?php endif; ?>
        <?php endfor; ?>
        <div class="salary-row border-top-0 pt-0">
          <span class="fw-bold text-danger">Total Potongan</span>
          <strong class="text-danger">Rp <?= number_format($total_potongan, 0, ',', '.') ?></strong>
        </div>
      </div>

      <div class="salary-total text-center">
        <a href="<?= base_url('mobile/sdm_slip_pdf/' . $slip_gaji['karyawan_id'] . '?bulan=' . $bulan_awal) ?>"
          class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-bold shadow-sm">
          <i class="bi bi-file-earmark-pdf-fill me-2 text-danger"></i> LIHAT SLIP RESMI (PDF)
        </a>
      </div>
    </div>
  <?php else: ?>
    <div class="text-center py-5 bg-white rounded-4 border border-dashed mb-4">
      <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
      <h6 class="mt-3 text-muted">Slip Gaji Belum Tersedia</h6>
      <p class="small text-muted px-4">Silakan hubungi bagian keuangan jika Anda merasa ini adalah kesalahan.</p>
    </div>
  <?php endif; ?>

  <!-- History -->
  <div class="section-header">
    <div class="section-title"><i class="bi bi-clock-history me-2"></i>Riwayat 6 Bulan Terakhir</div>
  </div>
  <div class="history-list">
    <?php if (!empty($riwayat_slip)): ?>
      <?php foreach ($riwayat_slip as $riwayat): ?>
        <a href="<?= base_url('mobile/slip_gaji?bulan=' . $riwayat['bulan']) ?>"
          class="stat-card py-3 px-4 mb-2 shadow-sm border-0 d-flex justify-content-between align-items-center text-decoration-none">
          <div>
            <span
              class="fw-bold text-dark small d-block"><?= $bulan_nama[explode('-', $riwayat['bulan'])[1]] . ' ' . explode('-', $riwayat['bulan'])[0] ?></span>
            <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Terbayar</small>
          </div>
          <div class="text-end">
            <strong class="d-block text-dark small">Rp <?= number_format($riwayat['total_gaji'], 0, ',', '.') ?></strong>
            <i class="bi bi-chevron-right text-muted x-small"></i>
          </div>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>