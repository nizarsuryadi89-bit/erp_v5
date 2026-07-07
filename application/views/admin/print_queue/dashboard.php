<div class="container-fluid px-4 py-4" style="background-color: #f8f9fc; min-height: 100vh;">
    <!-- Dashboard Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1"><i class="fas fa-industry text-primary me-2"></i> Progress Produksi WIP</h2>
            <p class="text-muted mb-0">Lantai 2 - Operator Produksi & Mesin Cetak</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="<?= base_url('print_queue/progress_board') ?>" target="_blank" class="btn btn-dark ms-2 rounded-3 shadow-sm btn-sm fw-bold">
                <i class="fas fa-tv me-1"></i> BUKA MODE TV (LIVE BOARD)
            </a>
            <span class="badge bg-success-subtle text-success px-3 py-2 border border-success-subtle rounded-pill ms-2">
                <i class="fas fa-circle-notch fa-spin me-1"></i> Auto-Sync: 15 Detik
            </span>
            <button onclick="fetchWipData()" class="btn btn-outline-primary ms-2 rounded-3 shadow-sm btn-sm">
                <i class="fas fa-sync-alt me-1"></i> Refresh Manual
            </button>
        </div>
    </div>

    <!-- WIP Status Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small opacity-75 fw-bold text-uppercase">Antrian Menunggu (WIP)</div>
                            <div class="h2 fw-bold mb-0 mt-1" id="wip-count-pending">0</div>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="fas fa-clipboard-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small opacity-75 fw-bold text-uppercase">Sedang Diproses (WIP)</div>
                            <div class="h2 fw-bold mb-0 mt-1" id="wip-count-proses">0</div>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="fas fa-cog fa-spin fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: white;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small opacity-75 fw-bold text-uppercase">Selesai Cetak Hari Ini (WIP)</div>
                            <div class="h2 fw-bold mb-0 mt-1" id="wip-count-done">0</div>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- WIP Detailed Layout with Split Grid -->
    <div class="row">
        <!-- Left Side: Live Timeline (col-lg-8) -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-tasks text-primary me-2"></i> Linimasa Pengerjaan Operator</h5>
                    
                    <!-- Search Input -->
                    <div class="input-group input-group-sm shadow-sm" style="max-width: 250px;">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="wip-search-input" class="form-control border-start-0" placeholder="Cari SPK / Produk / Konsumen...">
                    </div>
                </div>
                <div class="card-body p-4" style="max-height: 650px; overflow-y: auto; background-color: #f8f9fc;">
                    <div class="timeline" id="wip-timeline">
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                            Sedang memuat data produksi WIP...
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Workload Breakdown & Quick Filters (col-lg-4) -->
        <div class="col-lg-4 mb-4">
            <!-- Workload Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-chart-pie text-primary me-2"></i> Beban Kerja Operator</h5>
                </div>
                <div class="card-body" id="wip-workload-breakdown">
                    <div class="text-center py-4 text-muted">
                        <div class="spinner-border text-secondary spinner-border-sm me-2" role="status"></div>
                        Menghitung beban...
                    </div>
                </div>
            </div>

            <!-- Quick Filters Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-filter text-primary me-2"></i> Saring Linimasa</h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary active text-start fw-bold py-2.5 px-3 btn-wip-filter" data-filter="all">
                            <i class="fas fa-globe me-2"></i> Tampilkan Semua
                        </button>
                        <button type="button" class="btn btn-outline-warning text-start fw-bold py-2.5 px-3 btn-wip-filter" data-filter="pending">
                            <i class="fas fa-clipboard-list me-2"></i> Antrian Menunggu (WIP)
                        </button>
                        <button type="button" class="btn btn-outline-primary text-start fw-bold py-2.5 px-3 btn-wip-filter" data-filter="proses">
                            <i class="fas fa-cog fa-spin me-2"></i> Sedang Diproses (WIP)
                        </button>
                        <button type="button" class="btn btn-outline-success text-start fw-bold py-2.5 px-3 btn-wip-filter" data-filter="done">
                            <i class="fas fa-check-circle me-2"></i> Selesai Hari Ini (WIP)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styling -->
<style>
    .rounded-4 {
        border-radius: 12px !important;
    }
    .fw-900 {
        font-weight: 900 !important;
    }
    .font-monospace {
        font-family: 'Courier New', Courier, monospace;
    }
    .badge {
        font-weight: 600;
    }
    /* Vertical Timeline CSS */
    .timeline {
        position: relative;
        padding-left: 3rem;
        margin-bottom: 2rem;
    }
    .timeline.has-line::before {
        content: '';
        position: absolute;
        left: 1.25rem;
        top: 0.5rem;
        bottom: 0.5rem;
        width: 3px;
        background: #cbd5e1;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-badge {
        position: absolute;
        left: -2.35rem;
        top: 0.25rem;
        width: 2.2rem;
        height: 2.2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.95rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        z-index: 2;
        border: 3px solid #f8f9fc;
    }
    .timeline-badge.badge-pending {
        background: #f59e0b;
    }
    .timeline-badge.badge-proses {
        background: #3b82f6;
        animation: pulse-blue-border 2s infinite;
    }
    .timeline-badge.badge-done {
        background: #10b981;
    }
    @keyframes pulse-blue-border {
        0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(59, 130, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    .timeline-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    .timeline-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }
    .timeline-time {
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        font-family: monospace;
    }
</style>

<!-- Dashboard JS Logic -->
<script>
    let wipActiveFilter = 'all'; // 'all', 'pending', 'proses', 'done'
    let wipSearchQuery = '';
    let lastWipData = null;

    $(document).ready(function() {
        // Initial fetch
        fetchWipData();

        // Polling every 15 seconds (Smart Polling)
        setInterval(fetchWipData, 15000);

        // Bind WIP Filter buttons
        $(document).on('click', '.btn-wip-filter', function() {
            $('.btn-wip-filter').removeClass('active');
            $(this).addClass('active');
            wipActiveFilter = $(this).data('filter');
            if (lastWipData) {
                renderWipTimeline(lastWipData);
            }
        });

        // Bind WIP Search input
        $(document).on('keyup input', '#wip-search-input', function() {
            wipSearchQuery = $(this).val();
            if (lastWipData) {
                renderWipTimeline(lastWipData);
            }
        });
    });

    function fetchWipData() {
        let apiUrl = '<?= base_url("print_queue/get_live_progress") ?>';
        if (window.location.protocol === 'https:') {
            apiUrl = apiUrl.replace(/^http:/, 'https:');
        }
        
        $.ajax({
            url: apiUrl,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    let newDataString = JSON.stringify(res.data);
                    let oldDataString = lastWipData ? JSON.stringify(lastWipData) : '';
                    
                    if (newDataString !== oldDataString) {
                        // Update WIP Counts
                        let pendingCount = res.data.pending.length;
                        let prosesCount = res.data.proses.length;
                        let doneCount = res.data.done.length;
                        
                        $('#wip-count-pending').text(pendingCount);
                        $('#wip-count-proses').text(prosesCount);
                        $('#wip-count-done').text(doneCount);
                        
                        // Render WIP Detailed Timeline
                        renderWipTimeline(res.data);

                        // Update Workload Breakdown
                        updateWorkloadBreakdown(res.data);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.log('Error fetching WIP data:', error);
            }
        });
    }

    function renderWipTimeline(data) {
        lastWipData = data;
        let html = '';
        let allItems = [];
        
        // Tag each item with its status group
        data.pending.forEach(item => { item.wip_status = 'pending'; allItems.push(item); });
        data.proses.forEach(item => { item.wip_status = 'proses'; allItems.push(item); });
        data.done.forEach(item => { item.wip_status = 'done'; allItems.push(item); });
        
        // Filter by status tab
        if (wipActiveFilter !== 'all') {
            allItems = allItems.filter(item => item.wip_status === wipActiveFilter);
        }
        
        // Filter by search query
        if (wipSearchQuery) {
            let q = wipSearchQuery.toLowerCase();
            allItems = allItems.filter(item => {
                return (item.spk && item.spk.toLowerCase().includes(q)) ||
                       (item.konsumen && item.konsumen.toLowerCase().includes(q)) ||
                       (item.product && item.product.toLowerCase().includes(q));
            });
        }
        
        if (allItems.length === 0) {
            html = `<div class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 opacity-50"></i><br><h5 class="fw-bold">Tidak ada pengerjaan produksi yang cocok</h5></div>`;
            $('#wip-timeline').css('padding-left', '0').removeClass('has-line').html(html);
            return;
        }

        $('#wip-timeline').css('padding-left', '3rem').addClass('has-line');
        
        allItems.forEach(item => {
            let badgeIcon = '';
            let badgeClass = '';
            let statusLabel = '';
            let detailsHtml = '';
            let borderStyle = '';
            
            if (item.wip_status === 'pending') {
                badgeIcon = '<i class="fas fa-clipboard-list"></i>';
                badgeClass = 'badge-pending';
                statusLabel = '<span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">MENUNGGU</span>';
                borderStyle = 'border-left: 4px solid #f59e0b;';
                detailsHtml = `
                    <div class="text-muted small mt-2">
                        <i class="far fa-hourglass me-1"></i> SPK baru masuk antrean tunggu produksi. Operator akan segera memprosesnya.
                    </div>`;
            } else if (item.wip_status === 'proses') {
                badgeIcon = '<i class="fas fa-cog fa-spin"></i>';
                badgeClass = 'badge-proses';
                statusLabel = '<span class="badge bg-primary text-white rounded-pill px-2.5 py-1">SEDANG DIPROSES</span>';
                borderStyle = 'border-left: 4px solid #3b82f6;';
                detailsHtml = `
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-3 d-flex align-items-center">
                            <i class="fas fa-helmet-safety me-1.5 fa-lg"></i> OPERATOR: ${item.operator}
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-3 d-flex align-items-center">
                            <i class="fas fa-layer-group me-1.5 fa-lg"></i> LANGKAH: ${item.step_name}
                        </span>
                    </div>`;
            } else if (item.wip_status === 'done') {
                badgeIcon = '<i class="fas fa-check-circle"></i>';
                badgeClass = 'badge-done';
                statusLabel = '<span class="badge bg-success text-white rounded-pill px-2.5 py-1">SELESAI</span>';
                borderStyle = 'border-left: 4px solid #10b981;';
                detailsHtml = `
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-3 d-flex align-items-center">
                            <i class="fas fa-box-check me-1.5 fa-lg"></i> STATUS: ${item.done_status}
                        </span>
                    </div>`;
            }
            
            html += `
            <div class="timeline-item animate__animated animate__fadeIn">
                <div class="timeline-badge ${badgeClass}">
                    ${badgeIcon}
                </div>
                <div class="timeline-card" style="${borderStyle}">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="timeline-time"><i class="far fa-clock me-1"></i>${item.time}</span>
                            <span class="fw-bold text-dark font-monospace" style="font-size: 1.05rem;">SPK: ${item.spk}</span>
                            ${statusLabel}
                        </div>
                        <span class="text-secondary small fw-bold font-monospace bg-light border px-2 py-0.5 rounded">PROD-${item.id}</span>
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-1 mt-1">${item.konsumen}</h5>
                    <p class="text-secondary mb-0 fw-semibold" style="font-size: 0.95rem;">
                        <i class="fas fa-box me-1.5 opacity-75"></i>${item.product}
                    </p>
                    
                    ${detailsHtml}
                </div>
            </div>`;
        });
        
        $('#wip-timeline').html(html);
    }

    function updateWorkloadBreakdown(data) {
        let totalProses = data.proses.length;
        
        let deptCounts = {
            a3_print: 0,
            a3_finish: 0,
            lf_print: 0,
            general_finish: 0,
            other: 0
        };
        
        data.proses.forEach(item => {
            let step = (item.step_name || '').toUpperCase();
            let prod = (item.product || '').toUpperCase();
            
            if (step.includes('PRINT A3') || step.includes('CETAK A3') || step.includes('A3+ PRINT') || (prod.includes('A3') && step.includes('PRINT'))) {
                deptCounts.a3_print++;
            } else if (step.includes('FINISHING A3') || step.includes('JILID') || step.includes('LAMINASI A3') || step.includes('POTONG A3')) {
                deptCounts.a3_finish++;
            } else if (step.includes('RITRAMA') || step.includes('ORAJET') || step.includes('ALBATROS') || step.includes('FLEXY') || step.includes('BANNER') || step.includes('OUTDOOR') || step.includes('INDOOR') || step.includes('LF') || step.includes('DTF') || step.includes('PRINT STIKER')) {
                deptCounts.lf_print++;
            } else if (step.includes('FINISHING UMUM') || step.includes('METERAN') || step.includes('POTONG') || step.includes('BELAH') || step.includes('FINISHING OUTDOOR')) {
                deptCounts.general_finish++;
            } else {
                deptCounts.other++;
            }
        });
        
        let html = '';
        
        const depts = [
            { id: 'a3_print', name: 'Cetak Digital A3+', count: deptCounts.a3_print, icon: 'fas fa-copy', color: '#3b82f6', bg: 'rgba(59, 130, 246, 0.1)' },
            { id: 'a3_finish', name: 'Finishing A3+', count: deptCounts.a3_finish, icon: 'fas fa-book-open', color: '#8b5cf6', bg: 'rgba(139, 92, 246, 0.1)' },
            { id: 'lf_print', name: 'Large Format / Outdoor', count: deptCounts.lf_print, icon: 'fas fa-scroll', color: '#06b6d4', bg: 'rgba(6, 182, 212, 0.1)' },
            { id: 'general_finish', name: 'Finishing & Potong', count: deptCounts.general_finish, icon: 'fas fa-cut', color: '#f97316', bg: 'rgba(249, 115, 22, 0.1)' },
            { id: 'other', name: 'Lain-lain / Produksi', count: deptCounts.other, icon: 'fas fa-cogs', color: '#64748b', bg: 'rgba(100, 116, 139, 0.1)' }
        ];
        
        depts.forEach(d => {
            let pct = totalProses > 0 ? (d.count / totalProses) * 100 : 0;
            html += `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-semibold text-secondary small d-flex align-items-center" style="font-size: 0.8rem;">
                        <span class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; background: ${d.bg}; color: ${d.color};">
                            <i class="${d.icon} fa-xs"></i>
                        </span>
                        ${d.name}
                    </span>
                    <span class="badge bg-light text-dark border fw-bold" style="font-size: 0.75rem;">${d.count} SPK</span>
                </div>
                <div class="progress rounded-pill" style="height: 6px; background-color: #e2e8f0;">
                    <div class="progress-bar rounded-pill" role="progressbar" style="width: ${pct}%; background-color: ${d.color};" aria-valuenow="${pct}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>`;
        });
        
        if (totalProses === 0) {
            html = `<div class="text-center py-4 text-muted"><i class="fas fa-tasks opacity-50 mb-2"></i><br>Tidak ada pengerjaan aktif saat ini.</div>`;
        }
        
        $('#wip-workload-breakdown').html(html);
    }
</script>
