<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-pencil-square me-2"></i> Buat Ticket Baru
                    </h5>
                    <p class="text-muted small mb-0">Berikan detail yang jelas agar developer dapat memahami kebutuhan
                        Anda.</p>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('support/store') ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject / Judul <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control"
                                placeholder="Contoh: Bug pada modul Inventory / Request fitur Multi-gudang" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Modul / Menu Terkait <span
                                    class="text-danger">*</span></label>
                            <select name="module_name" class="form-select select2" required>
                                <option value="">-- Pilih Modul --</option>
                                <option value="GENERAL / LAINNYA">GENERAL / LAINNYA</option>
                                <?php foreach ($modules as $m): ?>
                                    <option value="<?= strtoupper($m->nama_menu) ?>"><?= strtoupper($m->nama_menu) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="BUG">BUG (Kesalahan Sistem)</option>
                                    <option value="FEATURE">FEATURE REQUEST (Pengajuan Fitur)</option>
                                    <option value="SUPPORT" selected>TECHNICAL SUPPORT (Bantuan Teknis)</option>
                                    <option value="OTHER">LAINNYA</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Prioritas <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required>
                                    <option value="LOW">LOW (Rendah)</option>
                                    <option value="MEDIUM" selected>MEDIUM (Normal)</option>
                                    <option value="HIGH">HIGH (Tinggi)</option>
                                    <option value="URGENT">URGENT (Mendesak / Kritis)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi Detail <span
                                    class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="8"
                                placeholder="Jelaskan secara mendalam tentang masalah atau fitur yang diajukan. Sisipkan langkah-langkah untuk mereproduksi BUG jika ada."
                                required></textarea>
                            <div class="form-text mt-1 italic small text-muted">Sebutkan URL halaman atau menu yang
                                bermasalah.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Lampiran (Opsional)</label>
                            <input type="file" name="attachment" class="form-control">
                            <div class="form-text small">Dokumen, Foto (Screenshot), atau file ZIP. Max 5MB.</div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('support') ?>" class="btn btn-light border">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="bi bi-send me-2"></i> Kirim Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>