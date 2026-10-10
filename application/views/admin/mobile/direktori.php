<!-- Page Header -->
<div class="custom-page-header">
    <h5><i class="bi bi-person-lines-fill me-2 text-info"></i>Direktori Karyawan</h5>
    <p>Cari & hubungi rekan kerja Anda</p>

    <div class="mt-4 px-2">
        <div class="position-relative">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" id="searchEmp"
                class="form-control form-control-lg bg-white bg-opacity-10 border-0 text-white shadow-none ps-5 rounded-pill placeholder-white"
                placeholder="Ketik nama spesifik..." style="backdrop-filter: blur(10px); font-size: 0.95rem;">
        </div>
    </div>
</div>

<style>
    .placeholder-white::placeholder {
        color: rgba(255, 255, 255, 0.6) !important;
    }

    .emp-card {
        background: #fff;
        border-radius: 20px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: all 0.2s;
    }

    .emp-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f1f5f9;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        flex-shrink: 0;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
    }

    .emp-info {
        flex: 1;
        min-width: 0;
    }

    .emp-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.05rem;
        margin-bottom: 0.15rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .emp-role {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .emp-div {
        font-size: 0.65rem;
        font-weight: 700;
        color: #0ea5e9;
        background: #e0f2fe;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        display: inline-block;
    }

    /* Action Buttons Configuration */
    .emp-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-contact {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-contact:active {
        transform: scale(0.9);
    }

    .btn-wa {
        background: #dcfce7;
        color: #16a34a;
    }

    .btn-call {
        background: #f1f5f9;
        color: #475569;
    }

    /* Contact Details Modal Styling */
    .detail-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
        display: block;
    }

    .detail-val {
        font-size: 0.95rem;
        color: #334155;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px dashed #e2e8f0;
    }
</style>

<!-- Main Content -->
<div class="content-container">

    <!-- Online Stats Row -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <span class="text-muted fw-bold" style="font-size: 0.85rem;">
            <?= count($direktori) ?> Karyawan Terdaftar
        </span>
        <span class="badge bg-success-soft text-success rounded-pill px-3 py-1 bg-opacity-10"
            style="font-size: 0.7rem; background: #dcfce7;">
            <i class="bi bi-circle-fill me-1" style="font-size: 0.4rem;"></i> REAL-TIME
        </span>
    </div>

    <!-- Employee List Container -->
    <div id="employeeList">
        <?php foreach ($direktori as $emp): ?>
            <?php 
                $emp_foto = !empty($emp->foto) && file_exists('assets/images/karyawan/' . $emp->foto) 
                    ? base_url('assets/images/karyawan/' . $emp->foto) 
                    : base_url('assets/images/karyawan/default.png');
                    
                $jdata = htmlspecialchars(json_encode([
                    'nama' => $emp->nama_lengkap,
                    'jabatan' => $emp->nama_jabatan ?? 'Staff',
                    'divisi' => $emp->divisi_nama,
                    'telepon' => $emp->telepon,
                    'email' => $emp->email,
                    'foto' => $emp_foto
                ]), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="emp-card" data-name="<?= strtolower($emp->nama_lengkap) ?>">
                <!-- Avatar -->
                <div onclick="showContactDetails(<?= $jdata ?>)" style="cursor: pointer;">
                    <?php if (!empty($emp->foto) && file_exists('assets/images/karyawan/' . $emp->foto)): ?>
                        <img src="<?= $emp_foto ?>" class="emp-avatar">
                    <?php else: ?>
                        <div class="emp-avatar">
                            <i class="bi bi-person-fill fs-3"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="emp-info" onclick="showContactDetails(<?= $jdata ?>)" style="cursor: pointer;">
                    <div class="emp-name">
                        <?= $emp->nama_lengkap ?>
                    </div>
                    <div class="emp-role">
                        <?= $emp->nama_jabatan ?? 'Staff' ?>
                    </div>
                    <span class="emp-div">
                        <?= $emp->divisi_nama ?? '-' ?>
                    </span>
                </div>

                <!-- Actions -->
                <div class="emp-actions">
                    <?php
                    // WA Formatter (Convert 08 to 628)
                    $wa_number = '';
                    if (!empty($emp->telepon)) {
                        $wa_number = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $emp->telepon));
                    }
                    ?>
                    <?php if (!empty($wa_number)): ?>
                        <a href="https://wa.me/<?= $wa_number ?>" target="_blank" class="btn-contact btn-wa shadow-sm">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    <?php else: ?>
                        <a href="javascript:void(0)"
                            onclick="Swal.fire('Tidak Tersedia', 'Nomor WA Karyawan ini belum didaftarkan.', 'info')"
                            class="btn-contact btn-wa shadow-sm" style="opacity: 0.5;">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Empty State (Hidden initially) -->
        <div id="noResults" class="text-center py-5" style="display: none;">
            <i class="bi bi-search text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
            <p class="text-muted mt-3 fw-bold mb-0">Rekan Kerja Tidak Ditemukan</p>
            <p class="text-muted small">Coba ubah kata kunci pencarian Anda.</p>
        </div>
    </div>
</div>

<script>
    // Live Search Functionality
    document.getElementById('searchEmp').addEventListener('keyup', function (e) {
        const term = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.emp-card');
        let hasVisibleCard = false;

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(term)) {
                card.style.display = 'flex';
                hasVisibleCard = true;
            } else {
                card.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noResults');
        if (!hasVisibleCard) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    });

    // Contact Details Modal
    function showContactDetails(emp) {
        let phoneHtml = emp.telepon ? `<a href="tel:${emp.telepon}" class="text-decoration-none fw-bold text-dark">${emp.telepon}</a>` : `<span class="text-muted fw-normal fst-italic">Belum diisi</span>`;
        let emailHtml = emp.email ? `<a href="mailto:${emp.email}" class="text-decoration-none fw-bold text-primary">${emp.email}</a>` : `<span class="text-muted fw-normal fst-italic">Belum diisi</span>`;

        let waBtn = '';
        if (emp.telepon) {
            let wa_num = emp.telepon.replace(/^0/, '62').replace(/[^0-9]/g, '');
            waBtn = `<a href="https://wa.me/${wa_num}" target="_blank" class="action-btn w-100 py-3 mt-4 text-decoration-none shadow-sm d-flex justify-content-center" style="background: linear-gradient(135deg, #22c55e, #16a34a); color: white;">
                        <i class="bi bi-whatsapp me-2"></i> Krm Pesan WhatsApp
                    </a>`;
        }

        Swal.fire({
            html: `
                <div class="text-center mb-4 mt-2">
                    <img src="${emp.foto}" class="rounded-circle border border-4 border-white shadow-sm mb-3" style="width: 100px; height: 100px; object-fit: cover; background: #e2e8f0;">
                    <h5 class="fw-bold text-dark mb-1">${emp.nama}</h5>
                    <div class="text-muted small fw-bold mb-2">${emp.jabatan}</div>
                    <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-1 bg-opacity-10" style="background: #dbeafe;">
                        <i class="bi bi-diagram-3 me-1"></i> ${emp.divisi || '-'}
                    </span>
                </div>

                <div class="text-start px-2 mt-4">
                    <div class="bg-light rounded-4 p-3 border border-light">
                        <span class="detail-label"><i class="bi bi-telephone text-primary me-1"></i> Telepon Utama</span>
                        <div class="detail-val">${phoneHtml}</div>
                        
                        <span class="detail-label mt-2"><i class="bi bi-envelope text-danger me-1"></i> Email Kerja</span>
                        <div class="detail-val border-0 pb-0 mb-0">${emailHtml}</div>
                    </div>
                </div>

                ${waBtn}
            `,
            showConfirmButton: false,
            showCloseButton: true,
            padding: '1.5rem',
            borderRadius: '25px',
            customClass: {
                closeButton: 'bg-light text-dark shadow-sm mt-3 me-3 rounded-circle border-0'
            },
            showClass: { popup: 'animate__animated animate__fadeInUp' }
        });
    }
</script>