<!-- Floating Chat-Style Queue Control Widget -->
<style>
    /* Floating Trigger Button */
    #queue-fab {
        position: fixed;
        bottom: 30px;
        left: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(30, 58, 138, 0.4);
        cursor: pointer;
        z-index: 9999;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    #queue-fab:hover {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 6px 20px rgba(30, 58, 138, 0.6);
    }

    #queue-fab .badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ff4757;
        color: white;
        border: 2px solid white;
    }

    /* Chat-like Panel */
    #queue-chat-panel {
        position: fixed;
        bottom: 100px;
        left: 30px;
        width: 350px;
        max-height: 500px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 9998;
        border: 1px solid rgba(0, 0, 0, 0.05);
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .q-chat-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .q-chat-body {
        padding: 15px;
        overflow-y: auto;
        flex-grow: 1;
        background: #f8f9fa;
    }

    /* Current Queue Bubble */
    .q-current-bubble {
        background: white;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #1e3a8a;
        text-align: center;
    }

    .q-current-num {
        font-size: 2rem;
        font-weight: 900;
        color: #1e3a8a;
        line-height: 1;
        margin-bottom: 5px;
    }

    /* Waiting Item - Chat Style */
    .q-msg {
        display: flex;
        margin-bottom: 12px;
        align-items: flex-start;
    }

    .q-msg-bubble {
        background: white;
        padding: 10px 14px;
        border-radius: 14px;
        border-top-left-radius: 2px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
        max-width: 85%;
        position: relative;
        cursor: pointer;
        transition: all 0.2s;
    }

    .q-msg-bubble:hover {
        background: #f1f3f9;
        transform: translateX(3px);
    }

    .q-msg-num {
        font-weight: 800;
        color: #1e3a8a;
        font-size: 1.1rem;
    }

    .q-msg-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: #2d3436;
    }

    .q-msg-time {
        font-size: 0.7rem;
        color: #a4b0be;
        margin-top: 4px;
        display: block;
        text-align: right;
    }

    .q-chat-footer {
        padding: 10px 15px;
        background: white;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-around;
        font-size: 0.8rem;
    }

    .q-stat {
        text-align: center;
    }

    .q-stat-val {
        font-weight: 800;
        color: #1e3a8a;
        display: block;
    }

    .q-stat-label {
        font-size: 0.7rem;
        color: #636e72;
        text-transform: uppercase;
    }
</style>

<!-- Floating FAB -->
<div id="queue-fab" onclick="queueWidget.toggle()">
    <i class="fas fa-users-cog fa-lg"></i>
    <span class="badge rounded-pill" id="q-badge-count" style="display:none;">0</span>
</div>

<!-- Chat Panel -->
<div id="queue-chat-panel">
    <div class="q-chat-header">
        <span><i class="fas fa-headset me-2"></i>Antrian Cabang</span>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-light p-1 px-2" onclick="queueWidget.refresh()" title="Refresh">
                <i class="fas fa-sync-alt fa-xs"></i>
            </button>
            <button class="btn btn-sm btn-light p-1 px-2" onclick="queueWidget.toggle()">
                <i class="fas fa-times fa-xs"></i>
            </button>
        </div>
    </div>

    <div class="q-chat-body">
        <!-- Identity Settings Area -->
        <div class="mb-3 p-2 bg-white rounded shadow-sm border" style="font-size: 0.75rem;">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted fw-bold">ID DESKPRINT:</span>
                <select id="q-deskprint-id" class="form-select form-select-sm py-0 h-auto"
                    style="width: auto; font-size: 0.75rem;" onchange="queueWidget.saveIdentity()">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="Deskprint <?= $i ?>">Deskprint <?= $i ?></option>
                    <?php endfor; ?>
                    <option value="Operator">Operator (General)</option>
                </select>
            </div>
        </div>
        <div id="q-content-area">
            <!-- Content dynamic -->
        </div>
    </div>

    <div class="q-chat-footer">
        <div class="q-stat">
            <span class="q-stat-val" id="q-stat-waiting">0</span>
            <span class="q-stat-label">Antri</span>
        </div>
        <div class="q-stat">
            <span class="q-stat-val" id="q-stat-served">0</span>
            <span class="q-stat-label">Selesai</span>
        </div>
        <a href="<?= site_url('kiosk/queue_control') ?>" target="_blank"
            class="btn btn-link btn-sm p-0 d-flex align-items-center" style="font-size: 0.75rem; color: #1e3a8a;">
            FULL <i class="fas fa-external-link-alt ms-1" style="font-size: 0.6rem;"></i>
        </a>
    </div>
</div>

<script>
    const queueWidget = {
        isOpen: false,
        currentQueue: null,

        identityKey: 'tgm_deskprint_identity',

        init: function () {
            this.loadIdentity();
            this.refresh();
            setInterval(() => this.refresh(), 10000);
        },

        loadIdentity: function () {
            const saved = localStorage.getItem(this.identityKey);
            if (saved) $('#q-deskprint-id').val(saved);
        },

        saveIdentity: function () {
            const val = $('#q-deskprint-id').val();
            localStorage.setItem(this.identityKey, val);
            Toast.fire({ icon: 'info', title: 'Identitas: ' + val });
        },

        toggle: function () {
            this.isOpen = !this.isOpen;
            $('#queue-chat-panel').toggle();
            if (this.isOpen) this.refresh();
        },

        refresh: function () {
            $.ajax({
                url: '<?= site_url("kiosk/get_queue_status") ?>',
                type: 'GET',
                dataType: 'json',
                success: (res) => {
                    if (res && res.status === 'success') {
                        this.updateUI(res);
                    } else {
                        console.error('Queue Status Error:', res ? res.msg : 'Unknown');
                    }
                },
                error: (xhr) => {
                    console.error('Queue AJAX Failed');
                }
            });
        },

        updateUI: function (data) {
            this.currentQueue = data.current;
            const body = $('#q-content-area');
            const badge = $('#q-badge-count');

            // Update Badge
            if (data.waiting_count > 0) {
                badge.text(data.waiting_count).show();
            } else {
                badge.hide();
            }

            // Update Stats
            $('#q-stat-waiting').text(data.waiting_count);
            $('#q-stat-served').text(data.served_count || 0);

            let html = '';

            // 1. Current Queue Bubble
            if (data.current) {
                html += `
                    <div class="q-current-bubble">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Sedang Dilayani</small>
                        <div class="q-current-num">${data.current.queue_number}</div>
                        <div class="fw-bold small mb-2">${data.current.konsumen_nama || 'Customer'}</div>
                        <button class="btn btn-success btn-sm w-100 rounded-pill" onclick="queueWidget.complete()">
                            <i class="fas fa-check-circle me-1"></i>Selesaikan
                        </button>
                    </div>
                `;
            }

            // 2. Waiting Messages
            if (data.waiting && data.waiting.length > 0) {
                html += `<div class="mb-2 text-center"><span class="badge bg-light text-muted fw-normal" style="font-size: 0.65rem;">Antrian Menunggu</span></div>`;
                data.waiting.slice(0, 5).forEach(q => {
                    html += `
                        <div class="q-msg" onclick="queueWidget.call('${q.queue_number}')">
                            <div class="q-msg-bubble">
                                <div class="d-flex justify-content-between align-items-center gap-3">
                                    <span class="q-msg-num">${q.queue_number}</span>
                                    <span class="q-msg-name">${q.konsumen_nama || 'Customer'}</span>
                                </div>
                                <span class="q-msg-time"><i class="far fa-clock me-1"></i>${this.formatTime(q.created_at)}</span>
                            </div>
                        </div>
                    `;
                });
                if (data.waiting.length > 5) {
                    html += `<div class="text-center mt-1"><small class="text-muted" style="font-size: 0.7rem;">+${data.waiting.length - 5} lainnya</small></div>`;
                }
            } else if (!data.current) {
                html = `
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-coffee fa-3x mb-3 opacity-25"></i>
                        <p class="small">Belum ada antrian masuk</p>
                    </div>
                `;
            }

            body.html(html);
        },

        call: function (num) {
            const loketId = $('#q-deskprint-id').val();
            Swal.fire({
                title: 'Panggil Antrian?',
                text: 'Nomor: ' + num + ' ke ' + loketId,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1e3a8a',
                confirmButtonText: 'Panggil Sekarang'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('<?= site_url("kiosk/call_queue") ?>', {
                        queue_number: num,
                        loket: loketId
                    }, (res) => {
                        const r = typeof res === 'string' ? JSON.parse(res) : res;
                        if (r.status === 'success') {
                            this.refresh();
                            Toast.fire({ icon: 'success', title: 'Antrian ' + num + ' dipanggil ke ' + loketId });
                        } else {
                            Swal.fire('Error', r.msg, 'error');
                        }
                    });
                }
            });
        },

        complete: function () {
            if (!this.currentQueue) return;
            const num = this.currentQueue.queue_number;

            Swal.fire({
                title: 'Selesaikan Antrian?',
                text: 'Antrian #' + num + ' sudah selesai dilayani?',
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Ya, Selesai'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('<?= site_url("kiosk/complete_queue") ?>', { queue_number: num }, (res) => {
                        const r = typeof res === 'string' ? JSON.parse(res) : res;
                        if (r.status === 'success') {
                            this.refresh();
                            Toast.fire({ icon: 'success', title: 'Layanan Selesai' });
                        }
                    });
                }
            });
        },

        formatTime: function (datetime) {
            const date = new Date(datetime);
            return String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
        }
    };

    $(document).ready(() => queueWidget.init());
</script>