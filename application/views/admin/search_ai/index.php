<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* Custom Styles for AI Search Dashboard */
    .ai-search-wrapper {
        background: #f8fafc;
        min-height: 80vh;
        padding-bottom: 5rem;
        font-family: 'Outfit', sans-serif;
    }

    .search-hero {
        padding: 4rem 1rem;
        text-align: center;
        background: white;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .search-container-ai {
        max-width: 800px;
        margin: 0 auto;
        position: relative;
    }

    .search-box-ai {
        display: flex;
        align-items: center;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 20px;
        padding: 8px 10px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .search-box-ai:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.1);
        transform: translateY(-2px);
    }

    .search-input-ai {
        flex: 1;
        border: none;
        outline: none;
        padding: 12px 20px;
        font-size: 1.15rem;
        font-weight: 500;
        color: #1e293b;
    }

    .btn-ai-analyze {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s;
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
    }

    .btn-ai-analyze:hover:not(:disabled) {
        transform: scale(1.03);
        box-shadow: 0 10px 15px rgba(37, 99, 235, 0.3);
    }

    .btn-ai-analyze:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .example-chips {
        margin-top: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
    }

    .chip {
        background: #f1f5f9;
        color: #64748b;
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .chip:hover {
        background: #e2e8f0;
        color: #334155;
        border-color: #cbd5e1;
    }

    /* Result Aesthetics */
    .results-area {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 1rem;
        display: none;
        animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-results {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .table-ai th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        font-weight: 800;
        color: #64748b;
        padding: 15px;
        border: none;
    }

    .table-ai td {
        padding: 15px;
        font-size: 0.9rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 500;
        color: #334155;
    }

    .loader-container {
        display: none;
        padding: 4rem 1rem;
    }

    .ai-brain-icon {
        font-size: 3rem;
        color: #3b82f6;
        animation: brainPulse 2s infinite ease-in-out;
    }

    @keyframes brainPulse {

        0%,
        100% {
            transform: scale(1);
            filter: drop-shadow(0 0 0 rgba(59, 130, 246, 0));
        }

        50% {
            transform: scale(1.1);
            filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.4));
        }
    }

    .sql-debug-box {
        background: #0f172a;
        color: #38bdf8;
        padding: 1rem;
        border-radius: 12px;
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        font-size: 0.8rem;
        margin-top: 1.5rem;
        display: none;
        border: 1px solid #1e293b;
        overflow-x: auto;
    }
</style>

<div class="ai-search-wrapper">
    <!-- Hero Search Section -->
    <div class="search-hero">
        <div class="container">
            <h1 class="fw-900 text-dark mb-2 d-flex justify-content-center align-items-center gap-3"
                style="font-size: 2.5rem; letter-spacing: -0.02em;">
                <i class="bi bi-stars text-primary"
                    style="font-size: 3rem; filter: drop-shadow(0 0 10px rgba(59,130,246,0.3));"></i>
                <span>COREMIND <span class="text-primary">AI</span></span>
            </h1>
            <p class="text-muted fw-500 mb-5 fs-5">Tanyakan apapun pada database Anda dengan bahasa sehari-hari.</p>

            <div class="search-container-ai">
                <div class="search-box-ai">
                    <i class="bi bi-stars text-primary ms-3 fs-5"></i>
                    <input type="text" id="queryInput" class="search-input-ai"
                        placeholder="Contoh: Siapa karyawan paling banyak Alpha bulan ini?" autocomplete="off">
                    <button id="btnAnalyze" class="btn-ai-analyze">
                        <i class="fas fa-bolt"></i> ANALISA
                    </button>
                </div>

                <div class="example-chips">
                    <span class="chip" data-query="Berapa total omset penjualan bulan ini?"><i class="bi bi-cash-coin me-1 text-success"></i> Omset Bulan Ini</span>
                    <span class="chip" data-query="Tampilkan 5 produk paling laris tahun ini berdasarkan jumlah terjual"><i class="bi bi-graph-up-arrow me-1 text-primary"></i> Produk Terlaris</span>
                    <span class="chip" data-query="Tampilkan daftar konsumen dengan tagihan piutang terbesar yang belum lunas"><i class="bi bi-exclamation-octagon me-1 text-danger"></i> Daftar Piutang</span>
                    <span class="chip" data-query="Tampilkan daftar material yang stoknya sudah di bawah batas minimal"><i class="bi bi-box-seam me-1 text-warning"></i> Restock Material</span>
                    <span class="chip" data-query="Siapa 5 konsumen VIP dengan total transaksi terbesar tahun ini?"><i class="bi bi-star-fill me-1 text-warning"></i> Top Konsumen</span>
                    <span class="chip" data-query="Berapa perkiraan total pengeluaran untuk gaji karyawan bulan ini?"><i class="bi bi-wallet2 me-1 text-info"></i> Beban Gaji</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Loader Section -->
    <div class="loader-container text-center" id="loaderAI">
        <i class="bi bi-stars ai-brain-icon mb-4"></i>
        <h4 class="fw-800 text-dark">Menganalisa Database...</h4>
        <p class="text-muted fw-500">Menghubungkan pola bahasa ke query SQL struktural.</p>
    </div>

    <!-- Results Section -->
    <div class="results-area" id="resultsSection">
        <div class="card-results">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-800 text-primary mb-1"><i class="fas fa-database me-2"></i>DATA HASIL ANALISA</h5>
                    <p class="text-muted small mb-0 fw-600" id="resultMeta">Menampilkan data berdasarkan permintaan
                        Anda.</p>
                </div>
                <div class="d-flex gap-2">
                    <span
                        class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-800 border border-primary border-opacity-25"
                        id="badgeRows">0 Baris</span>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill fw-bold" id="btnToggleSql">
                        <i class="fas fa-terminal me-1"></i> LOG SQL
                    </button>
                </div>
            </div>

            <div id="debugSql" class="sql-debug-box"></div>

            <div class="table-responsive mt-3">
                <table class="table table-ai" id="tableResults">
                    <thead>
                        <tr id="theadResults"></tr>
                    </thead>
                    <tbody id="tbodyResults"></tbody>
                </table>
            </div>

            <div id="noDataState" class="text-center py-5 d-none">
                <img src="<?= base_url('assets/img/empty-data.png') ?>" style="width: 150px; opacity: 0.5;"
                    class="mb-3">
                <h5 class="fw-800 text-muted">Data Tidak Ditemukan</h5>
                <p class="text-muted small">AI tidak menemukan baris data yang sesuai dengan kriteria tersebut.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const queryInput = document.getElementById('queryInput');
        const btnAnalyze = document.getElementById('btnAnalyze');
        const loaderAI = document.getElementById('loaderAI');
        const resultsSection = document.getElementById('resultsSection');
        const btnToggleSql = document.getElementById('btnToggleSql');
        const debugSql = document.getElementById('debugSql');

        // Typing Effect / Chip handling
        document.querySelectorAll('.chip').forEach(chip => {
            chip.addEventListener('click', function () {
                queryInput.value = this.getAttribute('data-query');
                executeAnalysis();
            });
        });

        // Trigger on Enter
        queryInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') executeAnalysis();
        });

        btnAnalyze.addEventListener('click', executeAnalysis);

        btnToggleSql.addEventListener('click', function () {
            if (debugSql.style.display === 'none' || debugSql.style.display === '') {
                debugSql.style.display = 'block';
            } else {
                debugSql.style.display = 'none';
            }
        });

        function executeAnalysis() {
            const query = queryInput.value.trim();
            if (!query) return;

            // UI State: Loading
            resultsSection.style.display = 'none';
            loaderAI.style.display = 'block';
            btnAnalyze.disabled = true;
            btnAnalyze.innerHTML = '<span class="spinner-border spinner-border-sm"></span> ANALISA...';

            // Menggunakan jQuery yang biasanya sudah ada di CI3 template
            $.ajax({
                url: '<?= site_url("SearchAI/process_query") ?>',
                type: 'POST',
                data: { query: query },
                dataType: 'json',
                success: function (res) {
                    renderResponse(res);
                },
                error: function (xhr) {
                    let msg = 'Terjadi kesalahan sistem.';
                    if (xhr.status === 401) msg = 'Sesi Anda telah berakhir. Silakan login kembali.';
                    Swal.fire('Error', msg, 'error');
                },
                complete: function () {
                    loaderAI.style.display = 'none';
                    btnAnalyze.disabled = false;
                    btnAnalyze.innerHTML = '<i class="fas fa-bolt"></i> ANALISA';
                }
            });
        }

        function renderResponse(res) {
            const thead = document.getElementById('theadResults');
            const tbody = document.getElementById('tbodyResults');
            const badgeRows = document.getElementById('badgeRows');
            const noDataState = document.getElementById('noDataState');
            const tableElement = document.getElementById('tableResults');

            thead.innerHTML = '';
            tbody.innerHTML = '';
            debugSql.innerHTML = '-- generated sql at ' + new Date().toLocaleTimeString() + '\n' + (res.debug_sql || '-- no sql provided');

            if (res.status === 'error') {
                Swal.fire('AI Message', res.message, 'warning');
                return;
            }

            resultsSection.style.display = 'block';
            badgeRows.innerText = (res.num_rows || 0) + ' Baris';

            if (!res.data || res.data.length === 0) {
                noDataState.classList.remove('d-none');
                tableElement.style.display = 'none';
                return;
            }

            noDataState.classList.add('d-none');
            tableElement.style.display = 'table';

            // Create Headers
            res.fields.forEach(field => {
                const th = document.createElement('th');
                th.innerText = field.replace(/_/g, ' ').toUpperCase();
                thead.appendChild(th);
            });

            // Create Rows
            res.data.forEach(row => {
                const tr = document.createElement('tr');
                res.fields.forEach(field => {
                    const td = document.createElement('td');
                    let val = row[field];

                    // Simple Formatter for Currency / Numbers
                    if (typeof val === 'number' || (!isNaN(val) && val !== '' && val !== null)) {
                        if (field.toLowerCase().includes('harga') || field.toLowerCase().includes('total') || field.toLowerCase().includes('bayar')) {
                            val = 'Rp ' + parseFloat(val).toLocaleString('id-ID');
                        }
                    }

                    td.innerText = (val === null) ? '-' : val;
                    tr.appendChild(td);
                });
                tbody.appendChild(tr);
            });
        }
    });
</script>