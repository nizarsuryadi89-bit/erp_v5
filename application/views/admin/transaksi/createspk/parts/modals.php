<!-- MODAL: KONSUMEN BARU -->
<div class="modal fade" id="modalTambahKonsumen" tabindex="-1" aria-labelledby="modalTambahKonsumenLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white border-bottom-0 py-3">
                <h6 class="modal-title fw-bold" id="modalTambahKonsumenLabel">
                    <i class="fas fa-user-plus me-2 text-primary"></i>TAMBAH KONSUMEN BARU
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formTambahKonsumen">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nama Lengkap
                            Konsumen</label>
                        <div class="input-group"><span class="input-group-text bg-light border-0"><i
                                    class="fas fa-user text-muted"></i></span><input type="text" name="konsumen_nama"
                                class="form-control bg-light border-0 fw-bold" placeholder="Contoh: Bpk. Sastro"
                                required></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">WhatsApp / No.
                            Telepon</label>
                        <div class="input-group"><span class="input-group-text bg-light border-0"><i
                                    class="fas fa-phone-alt text-muted"></i></span><input type="text"
                                name="konsumen_nohp" class="form-control bg-light border-0 fw-bold" placeholder="08xxxx"
                                required></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Alamat Email
                            (Opsional)</label>
                        <div class="input-group"><span class="input-group-text bg-light border-0"><i
                                    class="fas fa-envelope text-muted"></i></span><input type="email"
                                name="konsumen_email" class="form-control bg-light border-0"
                                placeholder="nama@email.com"></div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Alamat Pengiriman /
                            Instansi</label>
                        <textarea name="konsumen_alamat" class="form-control bg-light border-0" rows="3"
                            placeholder="Masukkan alamat lengkap..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4">
                    <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-premium btn-premium-primary px-5 fw-bold">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: IMPORT KIOSK -->
<div class="modal fade" id="modalImportKiosk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark border-0">
                <h6 class="modal-title fw-bold"><i class="fas fa-tablet-alt me-2"></i>Antrian Pesanan Kiosk</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">No. Ref</th>
                                <th>Konsumen</th>
                                <th class="text-center">Jml Item</th>
                                <th>Estimasi</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="kioskOrderList"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-outline-primary" onclick="loadKioskOrders()"><i
                        class="fas fa-sync-alt"></i> Refresh</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('kiosk/queue_widget'); ?>
<?php $this->load->view('admin/transaksi/components/v_modal_panduan_spk'); ?>