<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;--accent-blue:#6366f1;}
.report-hero{background:var(--primary-gradient);color:white;padding:2.5rem 0 5.5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);}
.cards-area{margin-top:-4rem;position:relative;z-index:10;padding-bottom:2rem;}
.rcard{background:white;border-radius:20px;padding:1.75rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;height:100%;display:flex;flex-direction:column;justify-content:space-between;transition:all .3s ease;margin-bottom:1.5rem;}
.rcard:hover{transform:translateY(-6px);box-shadow:0 20px 40px rgba(0,0,0,.1);border-color:var(--accent-blue);}
.rcard-icon{width:52px;height:52px;border-radius:15px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:white;margin-bottom:1rem;}
.rcard h4{font-size:1rem;font-weight:800;color:var(--slate-dark);margin:0 0 .4rem 0;}
.rcard p{font-size:.82rem;color:var(--slate-muted);margin:0 0 1.25rem 0;line-height:1.5;}
.btn-r{display:block;width:100%;padding:.65rem;border-radius:12px;font-weight:700;font-size:.78rem;text-align:center;text-decoration:none;transition:all .3s;color:white;text-transform:uppercase;letter-spacing:.5px;}
.btn-r:hover{opacity:.88;color:white;text-decoration:none;}
.section-label{font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:var(--slate-muted);margin:1rem 0 1.25rem;padding-left:4px;border-left:3px solid #e2e8f0;padding-left:10px;}
</style>

<div class="container-fluid px-4">
    <div class="report-hero">
        <div class="container-fluid px-5">
            <div style="display:flex;align-items:center;gap:1.5rem;">
                <div style="width:80px;height:80px;background:rgba(255,255,255,.1);border-radius:22px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;border:1px solid rgba(255,255,255,.2);">
                    <i class="fa fa-file-text-o"></i>
                </div>
                <div>
                    <h1 style="font-size:2rem;font-weight:900;margin:0 0 .3rem 0;">Pusat Laporan Gudang</h1>
                    <p style="margin:0;color:#94a3b8;font-size:.95rem;">8 laporan operasional & analitik untuk owner dan manajemen</p>
                </div>
            </div>
        </div>
    </div>

    <div class="cards-area">
        <div class="container-fluid px-5">

            <!-- Operasional Harian -->
            <div class="section-label">📋 Laporan Operasional Harian</div>
            <div class="row">
                <div class="col-md-4">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#3c8dbc,#2471a3);"><i class="fa fa-calendar"></i></div>
                            <h4>Stok Harian</h4>
                            <p>Pergerakan stok masuk dan keluar per hari berdasarkan rentang tanggal dan gudang.</p>
                        </div>
                        <a href="<?= base_url('warehouse/laporan_stok_harian') ?>" class="btn-r" style="background:linear-gradient(135deg,#3c8dbc,#2471a3);">→ Lihat Laporan</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#27ae60,#1e8449);"><i class="fa fa-bar-chart"></i></div>
                            <h4>Bahan Sering Diproduksi</h4>
                            <p>Ranking bahan berdasarkan frekuensi dan total kuantitas keluar ke produksi.</p>
                        </div>
                        <a href="<?= base_url('warehouse/laporan_fast_moving') ?>" class="btn-r" style="background:linear-gradient(135deg,#27ae60,#1e8449);">→ Lihat Laporan</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#00c0ef,#0099bb);"><i class="fa fa-truck"></i></div>
                            <h4>Pembelian per Supplier</h4>
                            <p>Rekapitulasi total nilai dan jumlah transaksi pembelian untuk setiap supplier.</p>
                        </div>
                        <a href="<?= base_url('warehouse/laporan_supplier') ?>" class="btn-r" style="background:linear-gradient(135deg,#00c0ef,#0099bb);">→ Lihat Laporan</a>
                    </div>
                </div>
            </div>

            <!-- Analitik untuk Owner -->
            <div class="section-label" style="margin-top:1.5rem;">👔 Laporan Analitik untuk Owner / Manajemen</div>
            <div class="row">
                <div class="col-md-3">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#f39c12,#e67e22);"><i class="fa fa-money"></i></div>
                            <h4>Nilai Inventaris</h4>
                            <p>Total nilai aset bahan baku di gudang (stok aktif × harga beli terakhir).</p>
                        </div>
                        <a href="<?= base_url('warehouse/laporan_nilai_inventaris') ?>" class="btn-r" style="background:linear-gradient(135deg,#f39c12,#e67e22);">→ Lihat Laporan</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#f72585,#b5179e);"><i class="fa fa-exclamation-triangle"></i></div>
                            <h4>Stok Kritis</h4>
                            <p>Item yang stoknya di bawah minimum — perlu segera dilakukan pembelian.</p>
                        </div>
                        <a href="<?= base_url('warehouse/laporan_stok_kritis') ?>" class="btn-r" style="background:linear-gradient(135deg,#f72585,#b5179e);">→ Lihat Laporan</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#64748b,#475569);"><i class="fa fa-archive"></i></div>
                            <h4>Stok Tidak Bergerak</h4>
                            <p>Bahan yang menganggur di gudang dan mengikat modal. Identifikasi dead stock.</p>
                        </div>
                        <a href="<?= base_url('warehouse/laporan_dead_stock') ?>" class="btn-r" style="background:linear-gradient(135deg,#64748b,#475569);">→ Lihat Laporan</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#6366f1,#4338ca);"><i class="fa fa-line-chart"></i></div>
                            <h4>Tren Pembelian Bulanan</h4>
                            <p>Grafik dan tabel pengeluaran pembelian bahan baku selama 12 bulan terakhir.</p>
                        </div>
                        <a href="<?= base_url('warehouse/laporan_tren_pembelian') ?>" class="btn-r" style="background:linear-gradient(135deg,#6366f1,#4338ca);">→ Lihat Laporan</a>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:.5rem;">
                <div class="col-md-4">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);"><i class="fa fa-balance-scale"></i></div>
                            <h4>Pembelian vs Pemakaian</h4>
                            <p>Bandingkan jumlah bahan yang dibeli dengan yang terpakai untuk efisiensi penggunaan.</p>
                        </div>
                        <a href="<?= base_url('warehouse/laporan_pembelian_vs_pemakaian') ?>" class="btn-r" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);">→ Lihat Laporan</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
