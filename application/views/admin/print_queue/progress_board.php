<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Live Production Progress' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        body {
            background-color: #0f172a; /* Slate 900 */
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
            overflow: hidden; /* Hide main scroll for TV mode */
            margin: 0;
            padding: 0;
        }

        .dashboard-header {
            background: rgba(15, 23, 42, 0.95);
            border-bottom: 1px solid #1e293b;
            padding: 1.5rem 2rem;
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(10px);
        }

        .board-container {
            display: flex;
            height: calc(100vh - 90px);
            padding: 1.5rem;
            gap: 1.5rem;
        }

        .board-column {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #1e293b; /* Slate 800 */
            border-radius: 1rem;
            border: 1px solid #334155;
            overflow: hidden;
        }

        .column-header {
            padding: 1.25rem;
            font-weight: 900;
            font-size: 1.25rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid;
        }

        .header-pending { border-color: #f59e0b; background: rgba(245, 158, 11, 0.1); color: #fbbf24; }
        .header-proses { border-color: #3b82f6; background: rgba(59, 130, 246, 0.1); color: #60a5fa; }
        .header-done { border-color: #10b981; background: rgba(16, 185, 129, 0.1); color: #34d399; }

        .column-body {
            padding: 1rem;
            overflow-y: hidden; /* We'll use auto-scroll JS */
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        /* Auto-scroll wrapper inside column */
        .scroll-wrapper {
            animation: scrollUp 40s linear infinite;
        }

        .job-card {
            background: #0f172a;
            border: 1px solid #334155;
            border-left: 4px solid #475569;
            border-radius: 0.75rem;
            padding: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .job-card.card-pending { border-left-color: #f59e0b; }
        .job-card.card-proses { border-left-color: #3b82f6; }
        .job-card.card-done { border-left-color: #10b981; }

        .job-card .spk-no {
            font-family: monospace;
            font-weight: 900;
            font-size: 1.1rem;
            color: #e2e8f0;
        }

        .job-card .time {
            font-weight: 600;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .job-card .consumer {
            font-weight: 800;
            font-size: 1.15rem;
            color: #f8fafc;
            margin-top: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .job-card .product {
            font-weight: 600;
            font-size: 0.95rem;
            color: #cbd5e1;
            margin-top: 0.25rem;
            line-height: 1.2;
        }

        .job-card .operator-badge {
            display: inline-block;
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 2rem;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 800;
            margin-top: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .job-card .done-badge {
            display: inline-block;
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 2rem;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 800;
            margin-top: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .clock-display {
            font-family: monospace;
            font-size: 1.5rem;
            font-weight: 900;
            color: #38bdf8;
            letter-spacing: 2px;
        }

        .pulse-live {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: #ef4444;
            border-radius: 50%;
            margin-right: 8px;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-red {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        /* Continuous Scroll Animation for long lists */
        .scrolling-content {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            animation: scroll 30s linear infinite;
        }
        .scrolling-content:hover {
            animation-play-state: paused;
        }
        @keyframes scroll {
            0% { transform: translateY(0); }
            100% { transform: translateY(calc(-50% - 0.375rem)); } /* Scrolls half height (we duplicate content) */
        }
        
        .no-data {
            color: #64748b;
            text-align: center;
            padding: 3rem 1rem;
            font-weight: 600;
            font-size: 1.1rem;
        }

    </style>
</head>
<body>

    <!-- Header -->
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <img src="<?= base_url('assets/img/logo_tgm.png') ?>" onerror="this.style.display='none'" alt="Logo" style="height: 40px; border-radius: 8px; background: white; padding: 2px;">
            <div>
                <h3 class="mb-0 fw-900 text-white ls-1"><span class="pulse-live"></span> LIVE PRODUCTION BOARD</h3>
                <div class="text-secondary fw-600" style="font-size: 0.85rem; letter-spacing: 1px;">STATUS PENGERJAAN SPK REAL-TIME</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="text-end">
                <div class="text-slate-400 fw-bold" style="font-size: 0.8rem; text-transform: uppercase;" id="current-date">--</div>
                <div class="clock-display" id="current-time">00:00:00</div>
            </div>
        </div>
    </div>

    <!-- Board -->
    <div class="board-container">
        <!-- Column: PENDING -->
        <div class="board-column shadow-lg">
            <div class="column-header header-pending">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-clipboard-list"></i>
                    <span>MENUNGGU</span>
                </div>
                <span class="badge bg-warning text-dark rounded-pill fs-6" id="count-pending">0</span>
            </div>
            <div class="column-body" id="col-pending">
                <!-- Cards injected here -->
            </div>
        </div>

        <!-- Column: PROSES -->
        <div class="board-column shadow-lg" style="transform: scale(1.02); z-index: 2; border-color: #3b82f6; box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.2);">
            <div class="column-header header-proses">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-cog fa-spin"></i>
                    <span>SEDANG DIPROSES</span>
                </div>
                <span class="badge bg-primary text-white rounded-pill fs-6" id="count-proses">0</span>
            </div>
            <div class="column-body" id="col-proses">
                <!-- Cards injected here -->
            </div>
        </div>

        <!-- Column: SELESAI -->
        <div class="board-column shadow-lg">
            <div class="column-header header-done">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>SELESAI / KIRIM</span>
                </div>
                <span class="badge bg-success text-white rounded-pill fs-6" id="count-done">0</span>
            </div>
            <div class="column-body" id="col-done">
                <!-- Cards injected here -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('current-time').innerText = now.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('current-date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        setInterval(updateClock, 1000);
        updateClock();

        let lastBoardData = null;
        // Data Fetching
        function fetchBoardData() {
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
                        let oldDataString = lastBoardData ? JSON.stringify(lastBoardData) : '';
                        
                        if (newDataString !== oldDataString) {
                            renderColumn('pending', res.data.pending, 'card-pending');
                            renderColumn('proses', res.data.proses, 'card-proses');
                            renderColumn('done', res.data.done, 'card-done');
                            lastBoardData = res.data;
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error fetching live board data:', error);
                }
            });
        }

        function renderColumn(colId, items, cardClass) {
            $('#count-' + colId).text(items.length);
            let html = '';
            
            if (items.length === 0) {
                html = `<div class="no-data"><i class="fas fa-inbox fa-2x mb-3 opacity-50"></i><br>TIDAK ADA DATA</div>`;
                $('#col-' + colId).html(html);
                return;
            }

            // Create cards
            let cardsHtml = items.map(item => {
                let badge = '';
                if (colId === 'proses') {
                    badge = `<div class="operator-badge"><i class="fas fa-helmet-safety me-1"></i> ${item.operator} (${item.step_name})</div>`;
                } else if (colId === 'done') {
                    badge = `<div class="done-badge"><i class="fas fa-box-check me-1"></i> ${item.done_status}</div>`;
                }

                return `
                <div class="job-card ${cardClass} animate__animated animate__fadeIn">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="spk-no"><i class="fas fa-hashtag opacity-50"></i>${item.spk}</div>
                        <div class="time"><i class="far fa-clock me-1"></i>${item.time}</div>
                    </div>
                    <div class="consumer">${item.konsumen}</div>
                    <div class="product">${item.product}</div>
                    ${badge}
                </div>`;
            }).join('');

            // If items are too many (e.g. > 5), we duplicate them and apply scrolling animation
            if (items.length > 5) {
                // Dynamically calculate speed: each item gets 4.5 seconds to ensure smooth and highly readable scrolling
                let duration = Math.max(25, items.length * 4.5);
                cardsHtml = `<div class="scrolling-content" style="animation-duration: ${duration}s;">${cardsHtml}${cardsHtml}</div>`;
            } else {
                cardsHtml = `<div class="d-flex flex-column gap-3">${cardsHtml}</div>`;
            }

            $('#col-' + colId).html(cardsHtml);
        }

        // Auto Refresh every 10 seconds
        setInterval(fetchBoardData, 10000);
        
        // Initial Fetch
        $(document).ready(function() {
            fetchBoardData();
        });
    </script>
</body>
</html>
