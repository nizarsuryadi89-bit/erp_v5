<div class="container-fluid py-4">
    <div class="row">
        <!-- Ticket Information -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 1rem;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold border-bottom pb-2">Ticket Info</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted d-block">Nomor Ticket</label>
                        <span class="fw-bold text-primary">
                            <?= $ticket->ticket_number ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block">Status</label>
                        <?php
                        $status_badge = 'bg-secondary';
                        if ($ticket->status == 'OPEN')
                            $status_badge = 'bg-info';
                        elseif ($ticket->status == 'IN-PROGRESS')
                            $status_badge = 'bg-warning text-dark';
                        elseif ($ticket->status == 'RESOLVED')
                            $status_badge = 'bg-success';
                        elseif ($ticket->status == 'CLOSED')
                            $status_badge = 'bg-dark';
                        ?>
                        <span class="badge <?= $status_badge ?>">
                            <?= $ticket->status ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block">Modul Terkait</label>
                        <span class="fw-bold text-dark"><i class="bi bi-box me-1"></i>
                            <?= $ticket->module_name ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block">Kategori / Prioritas</label>
                        <span class="small border px-2 py-1 rounded bg-light">
                            <?= $ticket->category ?>
                        </span>
                        <span class="small border px-2 py-1 rounded bg-light">
                            <?= $ticket->priority ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block">Pelapor</label>
                        <span class="fw-bold">
                            <?= $ticket->nama_lengkap ?? $ticket->username ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block">Tanggal Laporan</label>
                        <span class="small">
                            <?= date('d M Y, H:i', strtotime($ticket->created_at)) ?>
                        </span>
                    </div>

                    <?php if ($ticket->attachment):
                        $ext = pathinfo($ticket->attachment, PATHINFO_EXTENSION);
                        $is_image = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                        ?>
                        <div class="mt-4 pt-3 border-top">
                            <label class="small text-muted d-block mb-2">Lampiran Original</label>
                            <?php if ($is_image): ?>
                                <a href="<?= base_url('assets/uploads/support/' . $ticket->attachment) ?>" target="_blank">
                                    <img src="<?= base_url('assets/uploads/support/' . $ticket->attachment) ?>"
                                        class="img-fluid rounded border mb-2 shadow-sm" style="max-height: 200px;">
                                </a>
                            <?php endif; ?>
                            <a href="<?= base_url('assets/uploads/support/' . $ticket->attachment) ?>"
                                class="btn btn-sm btn-outline-dark w-100" target="_blank">
                                <i class="bi bi-paperclip me-1"></i> Lihat / Download Lampiran
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($is_admin): ?>
                        <div class="mt-4 pt-3 border-top bg-light p-3 rounded">
                            <h6 class="small fw-bold mb-3"><i class="bi bi-gear-fill me-1"></i> Admin Action</h6>
                            <form action="<?= base_url('support/reply/' . $ticket->id) ?>" method="POST">
                                <div class="mb-3">
                                    <label class="small fw-bold">Update Status</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="OPEN" <?= $ticket->status == 'OPEN' ? 'selected' : '' ?>>OPEN</option>
                                        <option value="IN-PROGRESS" <?= $ticket->status == 'IN-PROGRESS' ? 'selected' : '' ?>>
                                            IN-PROGRESS</option>
                                        <option value="PENDING" <?= $ticket->status == 'PENDING' ? 'selected' : '' ?>>PENDING
                                            (Tunggu Jawaban User)</option>
                                        <option value="RESOLVED" <?= $ticket->status == 'RESOLVED' ? 'selected' : '' ?>>
                                            RESOLVED (Selesai)</option>
                                        <option value="CLOSED" <?= $ticket->status == 'CLOSED' ? 'selected' : '' ?>>CLOSED
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">Developer Notes (Hidden from User)</label>
                                    <textarea name="developer_notes" class="form-control form-control-sm"
                                        rows="3"><?= $ticket->developer_notes ?></textarea>
                                </div>
                                <div class="small text-muted mb-2">Internal notes only.</div>
                                <button type="submit" class="btn btn-dark btn-sm w-100 shadow-sm">Update System
                                    Info</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <?php if ($ticket->developer_notes && false): // Hidden from user as per admin form above ?>
                            <div class="mt-4 pt-3 border-top">
                                <label class="small text-muted d-block">Catatan Developer</label>
                                <div class="p-2 bg-light rounded small">
                                    <?= nl2br($ticket->developer_notes) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Conversations -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <?= $ticket->subject ?>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Original Message -->
                    <div class="d-flex mb-4">
                        <div class="avatar-sm me-3 bg-primary text-white text-center rounded-circle"
                            style="width: 40px; height: 40px; line-height:40px; flex-shrink: 0;">
                            <?= strtoupper(substr($ticket->nama_lengkap ?? '?', 0, 1)) ?>
                        </div>
                        <div class="flex-grow-1 border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">
                                    <?= $ticket->nama_lengkap ?? $ticket->username ?>
                                </span>
                                <span class="small text-muted">
                                    <?= date('d/m/Y H:i', strtotime($ticket->created_at)) ?>
                                </span>
                            </div>
                            <div class="message-content">
                                <?= nl2br($ticket->description) ?>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Replies -->
                    <?php foreach ($replies as $r):
                        $is_me = ($r->user_id == $this->session->userdata('id'));
                        $is_admin_reply = (isset($r->role_name) && ($r->role_name == 'Super Admin' || $r->username == 'nizarsuryadi')); // Adjust logic as needed
                        ?>
                        <div class="d-flex mb-4 <?= $is_me ? 'flex-row-reverse' : '' ?>">
                            <div class="<?= $is_me ? 'ms-3' : 'me-3' ?> bg-<?= $is_admin_reply ? 'dark' : 'primary' ?> text-white text-center rounded-circle"
                                style="width: 40px; height: 40px; line-height:40px; flex-shrink: 0;">
                                <?= strtoupper(substr($r->nama_lengkap ?? '?', 0, 1)) ?>
                            </div>
                            <div class="flex-grow-1 border rounded p-3 <?= $is_me ? 'bg-white border-primary' : 'bg-light' ?>"
                                style="max-width: 85%;">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold small">
                                        <?= $r->nama_lengkap ?? $r->username ?>
                                        <?= $is_admin_reply ? '<span class="badge bg-dark ms-1">Staff</span>' : '' ?>
                                    </span>
                                    <span class="small text-muted" style="font-size: 0.75rem;">
                                        <?= date('d/m/Y H:i', strtotime($r->created_at)) ?>
                                    </span>
                                </div>
                                <div class="message-content small">
                                    <?= nl2br($r->message) ?>
                                </div>
                                <?php if ($r->attachment): 
                                    $r_ext = pathinfo($r->attachment, PATHINFO_EXTENSION);
                                    $r_is_image = in_array(strtolower($r_ext), ['jpg', 'jpeg', 'png', 'gif']);
                                ?>
                                    <div class="mt-2 pt-2 border-top">
                                        <?php if ($r_is_image): ?>
                                            <a href="<?= base_url('assets/uploads/support/' . $r->attachment) ?>" target="_blank">
                                                <img src="<?= base_url('assets/uploads/support/' . $r->attachment) ?>" class="img-fluid rounded border mb-2 d-block" style="max-height: 120px;">
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('assets/uploads/support/' . $r->attachment) ?>" target="_blank"
                                            class="small text-decoration-none">
                                            <i class="bi bi-paperclip"></i> Lihat Lampiran
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($replies)): ?>
                        <div class="text-center text-muted my-4 small">
                            Belum ada tanggapan.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Reply Form -->
                <?php if ($ticket->status != 'CLOSED'): ?>
                    <div class="card-footer bg-white py-4 border-top">
                        <h6 class="fw-bold mb-3">Tulis Balasan</h6>
                        <form action="<?= base_url('support/reply/' . $ticket->id) ?>" method="POST"
                            enctype="multipart/form-data">
                            <div class="mb-3">
                                <textarea name="message" class="form-control" rows="4"
                                    placeholder="Ketik balasan Anda di sini..." required></textarea>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="small fw-bold">Lampiran (Jika ada)</label>
                                    <input type="file" name="attachment" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                        <i class="bi bi-reply-fill me-1"></i> Balas Ticket
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="card-footer bg-light text-center py-3">
                        <span class="text-muted small"><i class="bi bi-lock-fill me-1"></i> Ticket ini telah ditutup.
                            Silakan buat ticket baru jika masalah berlanjut.</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="text-start">
                <a href="<?= base_url('support') ?>" class="btn btn-link text-decoration-none p-0">
                    <i class="bi bi-arrow-left"></i> Kembali ke daftar ticket
                </a>
            </div>
        </div>
    </div>
</div>