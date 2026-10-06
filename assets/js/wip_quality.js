(() => {
    'use strict';
    const app = document.getElementById('quality-app');
    if (!app) return;
    const form = document.getElementById('qc-form');
    const dialog = document.getElementById('qc-dialog');
    const message = document.getElementById('qc-form-message');
    const save = document.getElementById('qc-save');
    let job, saving = false;
    const displayError = text => { message.textContent = text; message.classList.remove('d-none'); };
    const updateRequirements = () => {
        const rejected = Number(form.elements.rejected_qty.value);
        form.elements.note.required = rejected > 0 || (job && job.status === 'hold');
        form.elements.request_reprint.disabled = !(rejected > 0);
        if (!(rejected > 0)) form.elements.request_reprint.checked = false;
    };
    form.elements.rejected_qty.addEventListener('input', updateRequirements);
    app.addEventListener('click', async event => {
        const button = event.target.closest('button');
        if (!button) return;
        if (button.classList.contains('qc-close')) { if (!saving) button.closest('dialog').close(); return; }
        if (button.classList.contains('qc-inspect')) {
            job = JSON.parse(button.dataset.job);
            form.reset(); message.classList.add('d-none');
            ['produksi_id', 'version', 'request_key'].forEach(key => { form.elements[key].value = job[key]; });
            form.elements.accepted_qty.value = job.target;
            form.elements.rejected_qty.value = 0;
            document.getElementById('qc-target').textContent = job.target;
            document.getElementById('qc-job-name').textContent = job.nospk;
            updateRequirements(); dialog.showModal();
        }
        if (button.classList.contains('qc-history')) {
            const historyDialog = document.getElementById('qc-history-dialog');
            const content = document.getElementById('qc-history-content');
            content.textContent = 'Memuat riwayat…'; historyDialog.showModal();
            try {
                const url = new URL(app.dataset.historyUrl, location.href);
                url.searchParams.set('produksi_id', button.dataset.id);
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.message || 'Riwayat gagal dimuat.');
                content.replaceChildren();
                if (!data.rows.length) content.textContent = 'Belum ada hasil pemeriksaan.';
                data.rows.forEach(row => {
                    const entry = document.createElement('div'); entry.className = 'border-bottom pb-3 mb-3';
                    const title = document.createElement('strong');
                    title.textContent = `Pemeriksaan ${row.revision} · ${row.decision === 'passed' ? 'Lulus' : 'Ditahan'}`;
                    const quantities = document.createElement('div'); quantities.textContent = `Diterima: ${Number(row.accepted_qty)} · Ditolak: ${Number(row.rejected_qty)}`;
                    const by = document.createElement('small'); by.className = 'text-muted'; by.textContent = `${row.inspected_at} · ${row.inspector_name || 'User #' + row.inspector_user_id}`;
                    const note = document.createElement('p'); note.className = 'mb-1'; note.textContent = row.note;
                    entry.append(title, quantities, by, note);
                    if (Number(row.request_reprint)) { const request = document.createElement('div'); request.className = 'text-danger small'; request.textContent = `Permintaan reprint: ${Number(row.rejected_qty)}. Belum membuat pekerjaan baru.`; entry.append(request); }
                    content.append(entry);
                });
            } catch (error) { content.textContent = error.message || 'Riwayat gagal dimuat.'; }
        }
    });
    dialog.addEventListener('cancel', event => { if (saving) event.preventDefault(); });
    form.addEventListener('submit', async event => {
        event.preventDefault(); if (saving || !job || !form.reportValidity()) return;
        const good = Number(form.elements.accepted_qty.value), bad = Number(form.elements.rejected_qty.value);
        if (!Number.isFinite(good) || !Number.isFinite(bad) || good < 0 || bad < 0 || Math.abs(good + bad - Number(job.target)) > 0.00005) {
            displayError('Jumlah diterima dan ditolak harus sama dengan jumlah yang diperiksa.'); return;
        }
        saving = true; save.disabled = true; message.classList.add('d-none');
        try {
            const response = await fetch(app.dataset.saveUrl, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: new URLSearchParams(new FormData(form)) });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Hasil QC belum tersimpan.');
            location.reload();
        } catch (error) { displayError(error.message || 'Koneksi gagal. Coba simpan kembali dengan form yang sama.'); }
        finally { saving = false; save.disabled = false; }
    });
})();
