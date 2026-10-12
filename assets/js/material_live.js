(function () {
    'use strict';
    const root = document.getElementById('material-live');
    if (!root) return;
    const find = id => document.getElementById(id);
    const initial = JSON.parse(find('live-initial').textContent);
    const date = find('live-date'), warehouse = find('live-warehouse'), search = find('live-search');
    let filters = Object.assign({}, initial.filters), today = initial.today;
    let followToday = filters.date === today, paused = false, busy = false, pending = false, timer = null;
    let generation = 0, controller = null, previousIds = new Set(initial.rows.map(row => row.log_id));
    let latest = initial;
    const formatQty = number => Number(number).toLocaleString('id-ID', { maximumFractionDigits: 4 });
    function status(text, color) { const el = find('live-status'); el.textContent = text; el.className = 'badge bg-' + color; }
    function error(message) { const el = find('live-error'); el.textContent = message; el.classList.toggle('d-none', !message); }
    function pagination(data) {
        find('live-prev').disabled = data.filters.page <= 1;
        find('live-next').disabled = data.filters.page >= data.pages;
        find('live-pagination-label').textContent = 'Halaman ' + data.filters.page + ' dari ' + data.pages + ' · ' + data.total + ' mutasi';
    }
    function cell(row, text, className) { const td = document.createElement('td'); td.textContent = text; if (className) td.className = className; row.appendChild(td); return td; }
    function small(parent, text) { const el = document.createElement('div'); el.className = 'small text-muted'; el.textContent = text; parent.appendChild(el); }
    function render(data) {
        const body = document.createDocumentFragment();
        data.rows.forEach(item => {
            const tr = document.createElement('tr'); tr.dataset.logId = item.log_id;
            if (!previousIds.has(item.log_id) && filters.page === 1) tr.className = 'live-material-new';
            const material = cell(tr, item.material_name || 'Material #' + item.material_id, 'fw-bold');
            small(material, item.warehouse_name || 'Gudang #' + item.gudang_id);
            cell(tr, item.variant || '—');
            cell(tr, formatQty(item.production_qty) + ' ' + (item.unit || 'unit'), 'text-end fw-bold');
            const spk = cell(tr, '');
            if (item.nospk) {
                const link = document.createElement('a'); link.textContent = item.nospk;
                link.href = root.dataset.detailUrl.replace(/\/$/, '') + '/' + Number(item.produksi_id);
                link.target = '_blank'; link.rel = 'noopener'; spk.appendChild(link);
            } else spk.textContent = 'Referensi produksi #' + item.produksi_id;
            small(spk, item.created_at + ' WIB');
            cell(tr, item.product_name || 'Produk tidak tersedia'); body.appendChild(tr);
        });
        if (!data.rows.length) { const tr = document.createElement('tr'); const td = cell(tr, 'Belum ada pengurangan bahan produksi untuk filter ini.', 'text-center text-muted py-5'); td.colSpan = 5; body.appendChild(tr); }
        find('live-rows').replaceChildren(body);
        previousIds = new Set(data.rows.map(row => row.log_id)); latest = data; filters.page = data.filters.page;
        find('live-total').textContent = data.total; find('live-spk-count').textContent = data.spk_count;
        find('live-material-count').textContent = data.material_count;
        find('live-updated').textContent = 'Diperbarui ' + data.server_time + ' WIB';
        pagination(data);
    }
    function schedule() {
        clearTimeout(timer);
        if (!paused && !document.hidden) timer = setTimeout(refresh, 5000);
    }
    async function refresh() {
        clearTimeout(timer);
        if (busy) { pending = true; return; }
        busy = true; const requestGeneration = generation;
        controller = new AbortController();
        let timedOut = false;
        const timeout = setTimeout(() => { timedOut = true; if (controller) controller.abort(); }, 15000);
        find('live-refresh').disabled = true;
        try {
            const url = new URL(root.dataset.feedUrl, window.location.href);
            Object.entries(filters).forEach(([key, value]) => url.searchParams.set(key, value));
            const response = await fetch(url.toString(), { credentials: 'same-origin', cache: 'no-store', signal: controller.signal,
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (requestGeneration !== generation) return;
            if (response.status === 401 || response.status === 403 || response.redirected) {
                paused = true; find('live-pause').textContent = 'Lanjut otomatis';
                throw new Error('Sesi atau akses berakhir. Login kembali lalu perbarui halaman.');
            }
            const data = await response.json();
            if (requestGeneration !== generation) return;
            if (!response.ok || data.success !== true || !Array.isArray(data.rows)) throw new Error(data.message || 'Respons data belum dapat dibaca.');
            today = data.today;
            if (followToday && filters.date !== today) {
                date.value = today; filters.date = today; filters.page = 1; generation++; pending = true; return;
            }
            render(data); error(''); status(paused ? 'DIJEDA' : 'LIVE', paused ? 'secondary' : 'success');
        } catch (e) {
            if (requestGeneration !== generation || (e.name === 'AbortError' && !timedOut)) return;
            error(timedOut ? 'Koneksi terlalu lama. Data terakhir tetap ditampilkan; pembaruan akan dicoba kembali.' : (e.message || 'Koneksi terputus. Data terakhir tetap ditampilkan.'));
            status(paused ? 'DIJEDA' : 'KONEKSI TERGANGGU', paused ? 'secondary' : 'warning');
        } finally {
            clearTimeout(timeout);
            busy = false; controller = null; find('live-refresh').disabled = false;
            if (pending) { pending = false; refresh(); } else schedule();
        }
    }
    function apply() {
        if (!find('live-filters').reportValidity()) return;
        filters = { date: date.value, warehouse: warehouse.value || filters.warehouse, search: search.value.trim(), page: 1 };
        followToday = date.value === today; previousIds = new Set(); generation++;
        if (controller) controller.abort(); refresh();
    }
    find('live-filters').addEventListener('submit', e => { e.preventDefault(); apply(); });
    date.addEventListener('change', apply); warehouse.addEventListener('change', apply);
    find('live-today').addEventListener('click', () => { date.value = today; apply(); });
    find('live-refresh').addEventListener('click', refresh);
    find('live-pause').addEventListener('click', () => {
        paused = !paused; find('live-pause').textContent = paused ? 'Lanjut otomatis' : 'Jeda otomatis';
        status(paused ? 'DIJEDA' : 'LIVE', paused ? 'secondary' : 'success'); clearTimeout(timer);
        if (!paused) refresh();
    });
    find('live-prev').addEventListener('click', () => { if (filters.page > 1) { filters.page--; generation++; if (controller) controller.abort(); refresh(); } });
    find('live-next').addEventListener('click', () => { if (filters.page < latest.pages) { filters.page++; generation++; if (controller) controller.abort(); refresh(); } });
    document.addEventListener('visibilitychange', () => { clearTimeout(timer); if (!document.hidden && !paused) refresh(); });
    window.addEventListener('pagehide', () => { paused = true; pending = false; generation++; clearTimeout(timer); if (controller) controller.abort(); });
    find('live-updated').textContent = 'Diperbarui ' + initial.server_time + ' WIB';
    pagination(initial); schedule();
}());
