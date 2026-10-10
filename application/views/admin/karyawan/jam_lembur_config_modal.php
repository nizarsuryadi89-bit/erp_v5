<!-- Modal Edit Jam Config -->
<div class="modal fade" id="modalEditJamLembur" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-800 text-dark" id="jamModalTitle">Konfigurasi Sesi Lembur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditJamLembur">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_jam_id">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Pilih Hari</label>
                        <select name="hari" id="edit_jam_hari" class="form-select bg-light" required>
                            <option value="Senin-Jumat">Senin-Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Keterangan / Sesi</label>
                        <input type="text" name="keterangan" class="form-control" id="edit_jam_keterangan" required placeholder="Contoh: Lembur Pagi, Sesi 1, dll">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="edit_jam_mulai" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="edit_jam_selesai" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Total Efektif (Hitungan Jam)</label>
                        <div class="input-group">
                            <input type="number" step="0.5" name="total_jam" id="edit_total_jam" class="form-control"
                                required>
                            <span class="input-group-text bg-light fw-bold text-muted">Jam</span>
                        </div>
                        <small class="text-muted d-block mt-1">Masukkan hasil akhirnya (misal: lembur dari 16:00 s.d
                            19:00 = 3 Jam efektif maka ketik 3.0 / 3.5 tergantung kebijakan progresif).</small>
                    </div>

                    <div class="mb-3 border p-3 rounded bg-white">
                        <div class="form-check form-switch d-flex align-items-center gap-2">
                            <input class="form-check-input mt-0" type="checkbox" name="is_wajib" id="edit_is_wajib"
                                value="1" style="transform: scale(1.2);">
                            <label class="form-check-label fw-bold small text-dark mb-0 pt-1" for="edit_is_wajib">Tandai
                                Sebagai Lembur Wajib</label>
                        </div>
                        <small class="text-muted d-block mt-2">Centang opsi ini jika rentang jam ini sepenuhnya adalah
                            "Lembur Wajib". Jika ini rentang lembur kerjaan (tambahan), biarkan kosong.</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>