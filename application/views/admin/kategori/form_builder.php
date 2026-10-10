<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- HEADER SECTION -->
        <?php
        // Set back URL explicitly for the header module
        if (!isset($is_modal) && !isset($is_tab)) {
            if (isset($is_product)) {
                $back_url = base_url('manufaktur');
            } else if (isset($is_subcategory)) {
                $back_url = base_url('pengaturan/subkategori');
            } else {
                $back_url = base_url('pengaturan/kategori');
            }
            $this->load->view('layout_admin/header_modul', ['back_url' => $back_url]);
        } elseif (isset($is_modal) && $is_modal) {
            echo '<style>.app-content { padding-top: 0 !important; margin-left: 0 !important; } .container-fluid { padding-top: 0 !important; } .report-section-card { box-shadow: none !important; border: none !important; } body { background: #f8f9fa !important; } .footer { display: none !important; } </style>';
        }
        ?>

        <!-- Flash Messages -->
        <div id="alert-container">
            <?= $this->session->flashdata('message'); ?>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle fs-5"></i>
                    <div class="fw-bold"><?= $this->session->flashdata('success') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- BUILDER CONTENT -->
        <div class="row g-4 animate-up">
            <!-- LEFT: VISUAL BUILDER -->
            <div class="col-lg-7">
                <div class="report-section-card h-100">
                    <div class="section-header">
                        <h5><i class="fas fa-layer-group me-2"></i> Visual Editor - <?= $kategori['kategori_nama'] ?>
                        </h5>
                        <div class="d-flex gap-2">
                            <!-- Export/Import Actions -->
                            <div class="btn-group me-1">
                                <button type="button"
                                    class="btn btn-outline-secondary btn-sm rounded-start-pill px-3 fw-bold shadow-sm"
                                    onclick="exportSchema()">
                                    <i class="fas fa-download me-1"></i> EXPORT
                                </button>
                                <button type="button"
                                    class="btn btn-outline-secondary btn-sm rounded-end-pill px-3 fw-bold shadow-sm border-start-0"
                                    onclick="triggerImport()">
                                    <i class="fas fa-upload me-1"></i> IMPORT
                                </button>
                                <input type="file" id="importFile" class="d-none" accept=".json"
                                    onchange="importSchema(this)">
                            </div>

                            <button type="button"
                                class="btn btn-outline-info btn-sm rounded-pill px-3 fw-bold shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#modalSopBuilder">
                                <i class="fas fa-question-circle me-1"></i> SOP
                            </button>
                            <button type="button"
                                class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-primary shadow-sm"
                                onclick="addNewField()">
                                <i class="fas fa-plus me-1"></i> FIELD BARU
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4 bg-light">
                        <p class="text-muted small mb-4 fw-bold">Atur struktur field input untuk kategori ini
                            menggunakan editor visual di bawah.</p>

                        <div id="field-list" class="d-flex flex-column gap-3">
                            <!-- Fields will be appended here -->
                        </div>

                        <div id="empty-state-builder" class="text-center py-5 d-none">
                            <i class="fas fa-laptop-code fs-1 text-muted opacity-25 mb-3"></i>
                            <h6 class="fw-bold text-muted">Belum ada field konfigurasi.</h6>
                            <p class="small text-muted mb-4">Klik tombol "Field Baru" untuk mulai mendesain form.</p>
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-bold"
                                onclick="addNewField()">
                                <i class="fas fa-plus me-1"></i> Tambah Field Pertama
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: PREVIEW & JSON -->
            <div class="col-lg-5">
                <div class="sticky-top" style="top: 1rem; z-index: 10;">

                    <!-- PREVIEW CARD -->
                    <div class="report-section-card mb-4">
                        <div class="section-header bg-white text-dark border-bottom">
                            <h5 class="mb-0 text-dark"><i class="fas fa-eye me-2 text-success"></i> Live Preview</h5>
                        </div>
                        <div class="card-body p-0 bg-white">
                            <div class="p-4">
                                <form onsubmit="return false;">
                                    <div id="form-preview">
                                        <!-- Preview rendered here -->
                                        <div class="text-center text-muted py-5">
                                            <i class="fas fa-eye fs-2 mb-2 opacity-25"></i>
                                            <p class="small mb-0 fw-bold">Preview form akan muncul di sini.</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-top text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-light rounded-pill px-3 fw-bold btn-sm disabled"
                                                disabled>Reset</button>
                                            <button class="btn btn-primary rounded-pill px-4 fw-bold btn-sm disabled"
                                                disabled>Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- JSON & SAVE CONFIG -->
                    <div class="report-section-card">
                        <div class="section-header">
                            <h5><i class="fas fa-save me-2"></i> Simpan Konfigurasi</h5>
                        </div>
                        <div class="p-4">
                            <?php
                            if (isset($is_product)) {
                                $save_url = 'pengaturan/product_save_schema';
                            } else {
                                $save_url = isset($is_subcategory) ? 'pengaturan/subkategori_save_schema' : 'pengaturan/kategori_save_schema';
                            }
                            ?>
                            <form action="<?= base_url($save_url) ?>" method="post">
                                <input type="hidden" name="kategori_id" value="<?= $kategori['kategori_id'] ?>">
                                <?php if(isset($is_modal) && $is_modal): ?>
                                    <input type="hidden" name="is_modal" value="1">
                                <?php endif; ?>
                                <?php if (isset($is_tab) && $is_tab): ?>
                                    <input type="hidden" name="is_tab" value="1">
                                <?php endif; ?>

                                <div class="accordion shadow-sm rounded-3 overflow-hidden border mb-3"
                                    id="accordionJson">
                                    <div class="accordion-item border-0">
                                        <h2 class="accordion-header">
                                            <button
                                                class="accordion-button collapsed fw-bold small text-uppercase bg-light text-dark"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseJson">
                                                <i class="fas fa-code me-2 text-info"></i> Advanced (JSON Schema)
                                            </button>
                                        </h2>
                                        <div id="collapseJson" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionJson">
                                            <div class="accordion-body p-0 bg-dark">
                                                <textarea name="form_schema" id="json-editor"
                                                    class="form-control bg-dark text-info border-0 p-3 font-monospace shadow-none small"
                                                    style="height: 250px; font-size: 12px; resize: none; border-radius: 0;"><?= $kategori['form_schema'] ?? '[]' ?></textarea>
                                                <div class="p-2 border-top border-secondary text-end bg-dark">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-light rounded-pill px-3"
                                                        onclick="formatJSON()"><i class="fas fa-align-left me-1"></i>
                                                        Prettify</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit"
                                        class="btn btn-primary text-white rounded-pill fw-bold py-3 shadow-sm hover-scale"
                                        style="background: #0d6efd; border: none;">
                                        <i class="fas fa-save me-2"></i> PERBARUI SCHEMA
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL SOP BUILDER -->
<div class="modal fade" id="modalSopBuilder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-info text-white border-0 p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center"
                        style="width: 50px; height: 50px;">
                        <i class="fas fa-book-open fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Panduan Penggunaan Form Builder</h5>
                        <p class="small mb-0 opacity-75">Panduan langkah demi langkah mengelola form kategori.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-tasks me-2"></i> Langkah-Langkah
                                </h6>
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex gap-3">
                                        <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                            style="width: 25px; height: 25px; font-size: 12px;">1</div>
                                        <p class="small mb-0">Klik tombol <b>FIELD BARU</b> untuk menambah inputan.</p>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                            style="width: 25px; height: 25px; font-size: 12px;">2</div>
                                        <p class="small mb-0">Isi <b>LABEL INPUT</b> (Contoh: Jenis Bahan) & <b>VARIABLE
                                                ID</b> (Contoh: bahan). <br><span class="text-danger small">Penting:
                                                Variable ID harus unik & tanpa spasi!</span></p>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                            style="width: 25px; height: 25px; font-size: 12px;">3</div>
                                        <p class="small mb-0">Atur <b>TIPE INPUT</b> sesuai kebutuhan (Cek daftar di
                                            kanan).</p>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                            style="width: 25px; height: 25px; font-size: 12px;">4</div>
                                        <p class="small mb-0">Klik <b>PERBARUI SCHEMA</b> untuk menyimpan perubahan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-success mb-3"><i class="fas fa-info-circle me-2"></i> Mengenal
                                    Tipe Input</h6>
                                <div class="small d-flex flex-column gap-2">
                                    <div class="p-2 bg-light rounded-3 border-start border-3 border-info">
                                        <b class="text-dark">Dropdown</b>: Gunakan format <code>key:Label</code>
                                        (Contoh: <code>matte:Laminasi Matte</code>).
                                    </div>
                                    <div class="p-2 bg-light rounded-3 border-start border-3 border-warning">
                                        <b class="text-dark">Dimensi</b>: Otomatis memunculkan input P x L (cm).
                                    </div>
                                    <div class="p-2 bg-light rounded-3 border-start border-3 border-success">
                                        <b class="text-dark">Meter Lari</b>: Input Panjang saja, Lebar akan otomatis
                                        terisi secara tersembunyi.
                                    </div>
                                    <div class="p-2 bg-light rounded-3 border-start border-3 border-danger">
                                        <b class="text-dark">Aturan Variable ID Khusus (Penting untuk Matriks Harga!)</b>
                                        <div class="mt-1 small">
                                            Sistem membaca urutan <b>Dropdown</b> untuk menentukan kolom harga:
                                        </div>
                                        <ul class="mb-0 mt-1 ps-3 small">
                                            <li><code>panjang</code> / <code>lebar</code>: Otomatis masuk hitungan dimensi area.</li>
                                            <li><b>Dropdown Ke-1</b>: Selalu tersimpan di database sebagai <code>ket_1</code>. (Gunakan ID <code>ket_1</code> untuk Sisi / Varian Utama).</li>
                                            <li><b>Dropdown Ke-2</b>: Selalu tersimpan sebagai <code>jenisprint_id</code>. (Gunakan ID <code>jenisprint_id</code> untuk Mesin Produksi).</li>
                                            <li><b>Dropdown Ke-3</b>: Selalu tersimpan sebagai <code>side_id</code>. (Gunakan ID <code>side_id</code> atau varian standar lain).</li>
                                            <li><b>Dropdown Ke-4 dst</b>: BEBAS! Gunakan ID apapun (contoh: <code>warna</code>, <code>laminasi</code>). Sistem otomatis menjadikannya variasi tanpa batas <i>(Dynamic Varians)</i> di Matriks Harga.</li>
                                        </ul>
                                        <div class="mt-2 p-1 bg-white text-danger rounded border small fst-italic fw-bold">
                                            <i class="fas fa-exclamation-circle me-1"></i> Jangan sampai salah urutan 3 dropdown pertama, agar Master Harga tidak rusak!
                                        </div>
                                    </div>
                                    <div class="p-2 bg-light rounded-3 border-start border-3 border-secondary">
                                        <b class="text-dark">Hidden</b>: Tidak terlihat oleh operator, berguna untuk
                                        tanda internal sistem.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning border-0 shadow-sm mt-4 rounded-4 mb-0">
                    <div class="d-flex gap-2 align-items-center">
                        <i class="fas fa-exclamation-triangle fs-4 text-warning"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Tips Keamanan:</h6>
                            <p class="small mb-0">Pastikan <b>Variable ID</b> tidak berubah jika formulir kategori
                                tersebut sudah pernah diisi datanya, agar data lama tetap terpanggil dengan benar.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-4 pt-0">
                <button type="button" class="btn btn-info text-white rounded-pill px-4 fw-bold shadow-sm"
                    data-bs-dismiss="modal">SAYA MENGERTI</button>
            </div>
        </div>
    </div>
</div>

<!-- FIELD TEMPLATE (VIRTUAL) -->
<div id="field-template" class="d-none">
    <div class="field-item card border shadow-sm mb-0 position-relative animate__animated animate__fadeIn" style="border-radius: 8px; border-color: #e2e8f0 !important;">
        <!-- Card Header -->
        <div class="card-header d-flex align-items-center justify-content-between py-2 px-3 drag-handle bg-white" style="cursor: move; border-radius: 8px 8px 0 0; border-bottom: 1px solid #e9ecef;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-grip-vertical text-muted" style="opacity: 0.3; font-size: 0.9rem;"></i>
                <span class="badge bg-primary rounded-2 px-2 py-1 field-number-badge" style="font-size: 0.65rem; letter-spacing: 0.5px;">FIELD 1</span>
            </div>
            <button type="button"
                class="btn btn-sm btn-outline-danger rounded-2 px-2 py-0 d-flex align-items-center gap-1"
                style="font-size: 0.7rem; height: 24px;" onclick="removeField(this)" title="Hapus Field">
                <i class="fas fa-times" style="font-size: 0.65rem;"></i> Hapus
            </button>
        </div>

        <!-- Card Body -->
        <div class="card-body p-3">
            <div class="row g-2">
                <!-- Label & Variable ID on same row -->
                <div class="col-8">
                    <label class="small fw-semibold text-muted mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px;">LABEL TAMPILAN</label>
                    <input type="text"
                        class="form-control form-control-sm rounded-2 field-label"
                        placeholder="Contoh: Jenis Kertas" onkeyup="autoSlug(this); syncToJson()">
                </div>
                <div class="col-4">
                    <label class="small fw-semibold text-muted mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px;">VARIABLE ID</label>
                    <input type="text" list="standard-variables"
                        class="form-control form-control-sm rounded-2 field-name text-primary font-monospace"
                        style="font-size: 0.8rem;"
                        placeholder="jenis_bahan" onkeyup="validateId(this)" onchange="this.dataset.manual='true'; syncToJson();">
                    <datalist id="standard-variables">
                        <option value="sisi">Pilihan Sisi</option>
                        <option value="mesin">Mesin Print</option>
                        <option value="bahan">Bahan / Material</option>
                        <option value="ukuran">Ukuran</option>
                        <option value="finishing">Finishing</option>
                        <option value="laminasi">Laminasi</option>
                        <option value="keterangan">Keterangan</option>
                    </datalist>
                </div>

                <!-- Tipe Input -->
                <div class="col-12">
                    <label class="small fw-semibold text-muted mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px;">TIPE INPUT</label>
                    <select class="form-select form-select-sm rounded-2 field-type" onchange="syncToJson()" style="font-size: 0.83rem;">
                        <option value="text">📝 Text Pendek</option>
                        <option value="textarea">📄 Textarea (Keterangan)</option>
                        <option value="number">🔢 Angka (Numeric)</option>
                        <option value="select">📋 Pilihan Dropdown</option>
                        <option value="dimensions">📐 Dimensi (P × L)</option>
                        <option value="meter_lari">📏 Meter Lari</option>
                        <option value="hidden">🔒 Hidden (Nilai Tetap)</option>
                        <option value="date">📅 Tanggal</option>
                        <option value="time">⏰ Waktu</option>
                        <option value="checkbox">🔘 Toggle On/Off</option>
                    </select>
                </div>
            </div>

            <!-- HIDDEN VALUE -->
            <div class="hidden-container mt-3 p-3 rounded-2 border-start border-4 border-secondary d-none" style="background: #f8f9fa;">
                <label class="small fw-semibold text-secondary mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px;"><i class="fas fa-lock me-1"></i>NILAI TETAP (VALUE)</label>
                <input type="text" class="form-control form-control-sm rounded-2 field-value"
                    placeholder="Masukkan nilai hidden..." onkeyup="syncToJson()">
            </div>

            <!-- METER LARI EXTRA -->
            <div class="meter-lari-container mt-3 p-3 rounded-2 border-start border-4 border-success d-none" style="background: #f0fdf4;">
                <label class="small fw-semibold text-success mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px;"><i class="fas fa-ruler me-1"></i>LEBAR TETAP</label>
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control rounded-2 field-fixed-width"
                        placeholder="Contoh: 0.60" step="0.01" onkeyup="syncToJson()">
                    <span class="input-group-text bg-white text-success fw-bold border-start-0">Meter</span>
                </div>
                <div class="form-text mt-1" style="font-size: 0.65rem;">Lebar ini otomatis terisi secara hidden.</div>
            </div>

            <!-- SELECT OPTIONS -->
            <div class="options-container mt-3 p-3 rounded-2 border-start border-4 border-warning d-none" style="background: #fffbeb;">
                <label class="small fw-semibold text-warning-emphasis mb-2 d-flex align-items-center gap-1" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                    <i class="fas fa-list-ul text-warning"></i> OPSI DROPDOWN
                </label>
                <input type="text"
                    class="form-control form-control-sm rounded-2 field-options"
                    placeholder="1:Label Satu, 2:Label Dua, 3:Label Tiga" onkeyup="syncToJson()">
                <div class="mt-2">
                    <span class="text-muted fst-italic" style="font-size: 0.7rem;">Format: <code class="px-1 rounded bg-white border">value:Label</code>, pisahkan dengan koma.</span>
                </div>
            </div>

            <!-- NUMBER EXTRA -->
            <div class="number-container mt-3 p-3 rounded-2 border-start border-4 border-info d-none" style="background: #eff6ff;">
                <label class="small fw-semibold text-info-emphasis mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px;"><i class="fas fa-ruler-horizontal me-1 text-info"></i>KONFIGURASI ANGKA</label>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="small text-muted mb-1" style="font-size: 0.68rem;">Satuan</label>
                        <input type="text"
                            class="form-control form-control-sm rounded-2 field-unit"
                            placeholder="cm, kg, pcs..." onkeyup="syncToJson()">
                    </div>
                    <div class="col-6">
                        <label class="small text-muted mb-1" style="font-size: 0.68rem;">Step / Kelipatan</label>
                        <input type="text"
                            class="form-control form-control-sm rounded-2 field-step"
                            placeholder="1" onkeyup="syncToJson()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale {
        transition: transform 0.2s;
    }

    .hover-scale:hover {
        transform: translateY(-2px);
    }

    .field-item {
        transition: all 0.2s ease;
    }

    .field-item:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: none;
        border-color: var(--tgm-primary);
        background-color: #fff !important;

        .form-control.is-invalid {
            background-color: #fff1f1 !important;
            border: 1px solid #ff4d4d !important;
        }
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const initialJson = document.getElementById('json-editor').value;
        try {
            const schema = JSON.parse(initialJson);
            if (Array.isArray(schema) && schema.length > 0) {
                schema.forEach(field => addFieldFromData(field));
            } else {
                document.getElementById('empty-state-builder').classList.remove('d-none');
            }
            updatePreview();
        } catch (e) {
            console.error("Invalid initial JSON", e);
            document.getElementById('empty-state-builder').classList.remove('d-none');
        }

        // Initialize Sortable for drag-and-drop
        const fieldList = document.getElementById('field-list');
        if (fieldList) {
            new Sortable(fieldList, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'bg-light',
                onEnd: function () {
                    syncToJson();
                }
            });
        }
    });

    function formatJSON() {
        try {
            const val = document.getElementById('json-editor').value;
            const obj = JSON.parse(val);
            document.getElementById('json-editor').value = JSON.stringify(obj, null, 4);
        } catch (e) {
            alert('Invalid JSON');
        }
    }

    function addNewField() {
        document.getElementById('empty-state-builder').classList.add('d-none');
        const list = document.getElementById('field-list');
        const temp = document.getElementById('field-template').cloneNode(true).firstElementChild;
        list.appendChild(temp);
        syncToJson();

        // Scroll to new field
        temp.scrollIntoView({ behavior: 'smooth', block: 'center' });
        temp.querySelector('.field-label').focus();
    }

    function autoSlug(labelInput) {
        const item = labelInput.closest('.field-item');
        const nameInput = item.querySelector('.field-name');
        
        if (!nameInput.dataset.manual) {
            let slug = labelInput.value.toLowerCase().replace(/[^a-z0-9_]/g, '_');
            slug = slug.replace(/_+/g, '_').replace(/^_|_$/g, '');
            nameInput.value = slug;
        }
    }

    function validateId(input) {
        let slug = input.value.toLowerCase().replace(/[^a-z0-9_]/g, '_');
        slug = slug.replace(/_+/g, '_').replace(/^_|_$/g, '');
        input.value = slug;
        syncToJson();
    }

    function addFieldFromData(data) {
        document.getElementById('empty-state-builder').classList.add('d-none');
        const list = document.getElementById('field-list');
        const temp = document.getElementById('field-template').cloneNode(true).firstElementChild;

        temp.querySelector('.field-label').value = data.label || '';
        temp.querySelector('.field-type').value = data.type || 'text';
        
        const nameInput = temp.querySelector('.field-name');
        nameInput.value = data.name || '';
        if (data.name) nameInput.dataset.manual = 'true';

        if (data.type === 'select') {
            temp.querySelector('.options-container').classList.remove('d-none');
            if (data.options) {
                // If options is object, convert to string
                let optString = '';
                if (typeof data.options === 'object' && !Array.isArray(data.options)) {
                    optString = Object.entries(data.options).map(([k, v]) => `${k}:${v}`).join(', ');
                }
                temp.querySelector('.field-options').value = optString;
            }
        }

        if (data.type === 'number') {
            temp.querySelector('.number-container').classList.remove('d-none');
            temp.querySelector('.field-unit').value = data.unit || '';
            temp.querySelector('.field-step').value = data.step || '';
        }

        if (data.type === 'hidden') {
            temp.querySelector('.hidden-container').classList.remove('d-none');
            temp.querySelector('.field-value').value = data.value || '';
        }

        if (data.type === 'meter_lari') {
            temp.querySelector('.meter-lari-container').classList.remove('d-none');
            temp.querySelector('.field-fixed-width').value = data.fixed_width || '0.60';
        }

        list.appendChild(temp);
    }

    function removeField(btn) {
        btn.closest('.field-item').remove();
        if (document.getElementById('field-list').children.length === 0) {
            document.getElementById('empty-state-builder').classList.remove('d-none');
        }
        syncToJson();
    }

    function syncToJson() {
        const list = document.querySelectorAll('#field-list .field-item');
        const schema = [];

        list.forEach((item, index) => {
            const type = item.querySelector('.field-type').value;
            const autoId = item.querySelector('.field-name').value;

            const field = {
                name: autoId,
                label: item.querySelector('.field-label').value,
                type: type
            };

            // Toggle sub-inputs
            item.querySelector('.options-container').classList.toggle('d-none', type !== 'select');
            item.querySelector('.number-container').classList.toggle('d-none', type !== 'number');
            item.querySelector('.hidden-container').classList.toggle('d-none', type !== 'hidden');
            item.querySelector('.meter-lari-container').classList.toggle('d-none', type !== 'meter_lari');

            if (type === 'select') {
                const optInput = item.querySelector('.field-options').value;
                const options = {};
                if (optInput) {
                    optInput.split(',').forEach(part => {
                        const s = part.split(':');
                        if (s.length >= 2) {
                            const val = s[0].trim();
                            const lab = s.slice(1).join(':').trim(); // Handle label containing colon if any
                            if (val && lab) options[val] = lab;
                        }
                    });
                }
                field.options = options;
            }

            if (type === 'number') {
                field.unit = item.querySelector('.field-unit').value;
                field.step = item.querySelector('.field-step').value;
            }

            if (type === 'hidden') {
                field.value = item.querySelector('.field-value').value;
            }

            if (type === 'meter_lari') {
                field.fixed_width = item.querySelector('.field-fixed-width').value;
            }

            schema.push(field);
        });

        const json = JSON.stringify(schema, null, 4);
        document.getElementById('json-editor').value = json;
        updatePreview();
    }

    // JSON Editor on change -> Visual Builder
    document.getElementById('json-editor').addEventListener('input', function () {
        try {
            const schema = JSON.parse(this.value);
            document.getElementById('field-list').innerHTML = '';
            if (Array.isArray(schema) && schema.length > 0) {
                document.getElementById('empty-state-builder').classList.add('d-none');
                schema.forEach(field => addFieldFromData(field));
            } else {
                document.getElementById('empty-state-builder').classList.remove('d-none');
            }
            updatePreview();
        } catch (e) {
            // Wait for valid JSON
        }
    });

    function updatePreview() {
        const json = document.getElementById('json-editor').value;
        const preview = document.getElementById('form-preview');

        try {
            const schema = JSON.parse(json);
            if (!Array.isArray(schema) || schema.length === 0) {
                preview.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-eye fs-2 mb-2 opacity-25"></i><p class="small mb-0 fw-bold">Belum ada field untuk ditampilkan.</p></div>';
                return;
            }

            let html = '';
            schema.forEach(f => {
                html += `<div class="mb-3">
                    <label class="small fw-bold text-dark d-block mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">${f.label}</label>`;

                if (f.type === 'select') {
                    html += `<select class="form-select form-select-sm fw-bold border-2 bg-light text-dark" style="border-radius: 8px;">`;
                    if (f.options) {
                        Object.entries(f.options).forEach(([k, v]) => {
                            html += `<option value="${k}">${v}</option>`;
                        });
                    }
                    html += `</select>`;
                } else if (f.type === 'dimensions') {
                    html += `<div class="input-group input-group-sm">
                        <input type="text" class="form-control fw-bold border-2 bg-light" placeholder="P" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                        <span class="input-group-text bg-white text-muted small border-2 border-start-0 border-end-0">x</span>
                        <input type="text" class="form-control fw-bold border-2 bg-light" placeholder="L">
                        <span class="input-group-text bg-white text-muted small border-2 border-start-0 border-end-0" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">cm</span>
                    </div>`;
                } else if (f.type === 'meter_lari') {
                    html += `<div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted small border-2 border-end-0" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;"><i class="fas fa-ruler-horizontal"></i></span>
                        <input type="text" class="form-control fw-bold border-2 bg-light text-center" placeholder="Masukkan Panjang Saja">
                        <span class="input-group-text bg-white text-muted small border-2 border-start-0" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">L: ${f.fixed_width || '0.6x'} m</span>
                    </div>`;
                } else if (f.type === 'hidden') {
                    html += `<div class="p-2 border border-dashed rounded-3 text-center bg-light">
                        <span class="badge bg-secondary">HIDDEN: ${f.name}</span>
                        <div class="small text-muted font-monospace mt-1" style="font-size: 10px;">Value: ${f.value || '(empty)'}</div>
                    </div>`;
                } else if (f.type === 'textarea') {
                    html += `<textarea class="form-control fw-bold border-2 bg-light text-dark" style="border-radius: 8px;" rows="2" placeholder="Masukan keterangan..."></textarea>`;
                } else if (f.type === 'checkbox') {
                    html += `<div class="form-check form-switch pt-1">
                        <input class="form-check-input" type="checkbox" role="switch" checked>
                        <label class="form-check-label small fw-bold text-muted">Aktif / Ya</label>
                    </div>`;
                } else if (f.type === 'date') {
                    html += `<div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-2 border-end-0" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;"><i class="fas fa-calendar-alt text-muted"></i></span>
                        <input type="date" class="form-control fw-bold border-2 bg-light text-dark" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                    </div>`;
                } else if (f.type === 'time') {
                    html += `<div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-2 border-end-0" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;"><i class="fas fa-clock text-muted"></i></span>
                        <input type="time" class="form-control fw-bold border-2 bg-light text-dark" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                    </div>`;
                } else {
                    html += `<div class="input-group input-group-sm">
                        <input type="${f.type}" class="form-control fw-bold border-2 bg-light text-dark" placeholder="${f.placeholder || ''}" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px; ${!f.unit ? 'border-radius: 8px;' : ''}">
                        ${f.unit ? `<span class="input-group-text bg-white border-2 border-start-0 text-muted small fw-bold" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">${f.unit}</span>` : ''}
                    </div>`;
                }
                html += `</div>`;
            });
            preview.innerHTML = html;
        } catch (e) {
            preview.innerHTML = '<div class="alert alert-danger small rounded-pill"><i class="fas fa-exclamation-triangle me-2"></i>Format JSON tidak valid.</div>';
        }
    }

    // EXPORT & IMPORT UTILITIES
    function exportSchema() {
        const jsonContent = document.getElementById('json-editor').value;
        const blob = new Blob([jsonContent], { type: "application/json" });
        const url = URL.createObjectURL(blob);
        const fileName = "schema_<?= url_title($kategori['kategori_nama'], 'underscore', TRUE) ?>_" + new Date().getTime() + ".json";

        const link = document.createElement("a");
        link.href = url;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);

        Swal.fire({
            icon: 'success',
            title: 'Export Berhasil',
            text: 'File ' + fileName + ' telah diunduh.',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function triggerImport() {
        document.getElementById('importFile').click();
    }

    function importSchema(input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            try {
                const content = e.target.result;
                // Validate JSON
                const parsed = JSON.parse(content);

                if (!Array.isArray(parsed)) {
                    throw new Error("Format schema harus berupa Array!");
                }

                Swal.fire({
                    title: 'Konfirmasi Import',
                    text: "Seluruh konfigurasi saat ini akan digantikan oleh file '" + file.name + "'. Lanjutkan?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Ya, Gantikan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Update JSON Editor
                        document.getElementById('json-editor').value = JSON.stringify(parsed, null, 4);

                        // Rebuild Visual Builder
                        document.getElementById('field-list').innerHTML = '';
                        parsed.forEach(field => addFieldFromData(field));

                        // Update Preview
                        updatePreview();

                        Swal.fire({
                            icon: 'success',
                            title: 'Import Berhasil',
                            text: 'Konfigurasi telah dimuat ke editor.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                    // Reset input file value to allow re-importing same file
                    input.value = '';
                });

            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Import',
                    text: 'File tidak valid: ' + err.message
                });
                input.value = '';
            }
        };

        reader.readAsText(file);
    }
</script>