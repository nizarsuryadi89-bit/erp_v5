<!-- =====================================================
     Modal: Kirim Laporan ke Owner via Link
     Include di view laporan manapun
     Requires: jQuery, Bootstrap 4
     ===================================================== -->

<!-- Trigger Button (tambahkan di hero setiap laporan) -->
<!--
<button type="button" class="btn-kirim-owner" data-toggle="modal" data-target="#modalKirimOwner"
        data-report-type="legger"
        data-params='{"perusahaan_id":1,"mulai":"2025-05-01","selesai":"2025-05-31"}'>
    <i class="fa fa-paper-plane"></i> Kirim ke Owner
</button>
-->

<!-- Modal -->
<div class="modal fade" id="modalKirimOwner" tabindex="-1" role="dialog" aria-labelledby="modalKirimOwnerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content" style="border-radius:20px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">

            <!-- Header -->
            <div style="background:linear-gradient(135deg,#1e293b,#334155);padding:1.5rem;color:white;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:44px;height:44px;background:rgba(99,102,241,.25);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#a5b4fc;border:1px solid rgba(99,102,241,.3);">
                        <i class="fa fa-paper-plane"></i>
                    </div>
                    <div>
                        <div style="font-size:1rem;font-weight:900;">Kirim Laporan ke Owner</div>
                        <div style="font-size:.75rem;color:#94a3b8;">Generate link aman & kirim via WhatsApp</div>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:.7;font-size:1.5rem;">&times;</button>
            </div>

            <div style="padding:1.5rem;">
                <!-- Report Info -->
                <div style="background:#f8fafc;border-radius:12px;padding:1rem;margin-bottom:1.25rem;border:1px solid #e2e8f0;">
                    <div style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:.4rem;">Laporan yang akan dikirim</div>
                    <div id="ko-report-label" style="font-weight:800;color:#1e293b;font-size:.95rem;">—</div>
                    <div id="ko-params-label" style="font-size:.8rem;color:#64748b;margin-top:2px;">—</div>
                </div>

                <!-- Owner WA Number -->
                <div style="margin-bottom:1rem;">
                    <label style="font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#475569;margin-bottom:.4rem;display:block;">No. WhatsApp Owner</label>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="text" id="ko-owner-nohp" class="form-control" placeholder="0812xxxx / 6212xxxx"
                               style="border-radius:10px;height:42px;border:1px solid #e2e8f0;font-weight:700;">
                        <button type="button" id="btn-load-owner-nohp" onclick="loadOwnerNohp()"
                                style="height:42px;padding:0 14px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:10px;font-weight:700;font-size:.78rem;white-space:nowrap;cursor:pointer;">
                            <i class="fa fa-user"></i> Auto
                        </button>
                    </div>
                    <div style="font-size:.72rem;color:#94a3b8;margin-top:4px;">Kosongkan jika hanya ingin generate link tanpa kirim WA.</div>
                </div>

                <!-- Expiry -->
                <div style="margin-bottom:1.25rem;">
                    <label style="font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#475569;margin-bottom:.4rem;display:block;">Masa Berlaku Link</label>
                    <div style="display:flex;gap:8px;">
                        <?php foreach ([1=>'1 Hari', 3=>'3 Hari', 7=>'1 Minggu'] as $days => $lbl): ?>
                        <label style="flex:1;cursor:pointer;">
                            <input type="radio" name="ko-expires" value="<?= $days ?>" <?= $days==3?'checked':'' ?> style="display:none;" class="ko-expires-radio">
                            <div class="ko-exp-btn" data-days="<?= $days ?>"
                                 style="text-align:center;padding:.5rem;border-radius:10px;border:2px solid <?= $days==3?'#6366f1':'#e2e8f0' ?>;background:<?= $days==3?'#eff6ff':'white' ?>;font-weight:700;font-size:.82rem;color:<?= $days==3?'#4338ca':'#64748b' ?>;transition:all .2s;">
                                <?= $lbl ?>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Result Area (initially hidden) -->
                <div id="ko-result" style="display:none;margin-bottom:1rem;">
                    <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;padding:1rem;">
                        <div style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#059669;margin-bottom:.5rem;">✅ Link Berhasil Dibuat</div>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input type="text" id="ko-link-result" readonly
                                   style="flex:1;border-radius:8px;border:1px solid #a7f3d0;padding:.5rem;font-size:.78rem;background:#f0fdf4;color:#064e3b;font-weight:700;">
                            <button onclick="copyLink()" style="height:36px;padding:0 12px;background:#059669;color:white;border:none;border-radius:8px;font-weight:700;font-size:.78rem;cursor:pointer;">
                                <i class="fa fa-copy"></i> Copy
                            </button>
                        </div>
                        <div id="ko-wa-status" style="margin-top:.5rem;font-size:.78rem;color:#065f46;"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display:flex;gap:8px;">
                    <button type="button" onclick="generateAndSend()"
                            style="flex:1;height:44px;background:linear-gradient(135deg,#6366f1,#4338ca);color:white;border:none;border-radius:12px;font-weight:700;font-size:.88rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;"
                            id="btn-ko-generate">
                        <i class="fa fa-paper-plane"></i> Generate & Kirim WA
                    </button>
                    <button type="button" onclick="generateOnly()"
                            style="height:44px;padding:0 16px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:12px;font-weight:700;font-size:.82rem;cursor:pointer;">
                        Link Saja
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-kirim-owner {
    background: linear-gradient(135deg, #6366f1, #4338ca);
    color: white; border: none; border-radius: 10px;
    padding: 0 18px; height: 38px; font-weight: 700; font-size: .82rem;
    cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
    text-decoration: none;
}
.btn-kirim-owner:hover { opacity: .88; color: white; }
</style>

<script>
var _koReportType = '';
var _koParams     = {};

// Wait for jQuery then bind events — safe regardless of load order
(function initKirimOwner() {
    if (typeof jQuery === 'undefined') { return setTimeout(initKirimOwner, 80); }
    var $ = jQuery;

    // Called when modal trigger button is clicked
    $(document).on('click', '.btn-kirim-owner', function () {
        _koReportType = $(this).data('report-type') || '';
        _koParams     = $(this).data('params') || {};

        var names = {
            legger:'Buku Besar Bulanan (Legger)', tren_keuangan:'Tren Keuangan 12 Bulan',
            aging_piutang:'Aging Piutang', arus_kas:'Laporan Arus Kas',
            hpp_margin:'HPP & Margin Produk', laba_rugi:'Laporan Laba Rugi'
        };
        $('#ko-report-label').text(names[_koReportType] || _koReportType);

        var p = [];
        if (_koParams.mulai) p.push('Periode: ' + _koParams.mulai + ' s/d ' + (_koParams.selesai || ''));
        if (_koParams.bulan) p.push('Bulan: ' + _koParams.bulan);
        $('#ko-params-label').text(p.join(' | ') || 'Semua periode');

        $('#ko-result').hide();
        $('#ko-link-result').val('');
        $('#ko-wa-status').text('');
        $('#modalKirimOwner').modal('show');
    });

    // Expiry button toggle
    $(document).on('click', '.ko-exp-btn', function () {
        $('.ko-exp-btn').css({'border-color':'#e2e8f0','background':'white','color':'#64748b'});
        $(this).css({'border-color':'#6366f1','background':'#eff6ff','color':'#4338ca'});
        $('input[name="ko-expires"][value="'+$(this).data('days')+'"]').prop('checked', true);
    });
})();

function loadOwnerNohp() {
    if (typeof jQuery === 'undefined') { return; }
    jQuery.getJSON('<?= base_url('OwnerReport/get_owner_nohp') ?>', function (res) {
        if (res.nohp) { jQuery('#ko-owner-nohp').val(res.nohp); }
        else { alert('No HP owner tidak ditemukan. Silakan isi manual.'); }
    });
}

function generateAndSend() { _doGenerate(true); }
function generateOnly()     { _doGenerate(false); }

function _doGenerate(sendWa) {
    if (typeof jQuery === 'undefined') { alert('jQuery belum siap, coba lagi.'); return; }
    var $ = jQuery;
    var expires = $('input[name="ko-expires"]:checked').val() || 3;
    var nohp    = $('#ko-owner-nohp').val().trim();
    $('#btn-ko-generate').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');

    $.post('<?= base_url('OwnerReport/generate_token') ?>', {
        report_type : _koReportType,
        params      : JSON.stringify(_koParams),
        expires_in  : expires,
        owner_nohp  : nohp,
        send_wa     : sendWa ? '1' : '0'
    }, function (res) {
        $('#btn-ko-generate').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Generate & Kirim WA');
        if (res.success) {
            $('#ko-link-result').val(res.link);
            $('#ko-result').show();
            if (sendWa && nohp) {
                $('#ko-wa-status').html(res.wa_sent
                    ? '✅ Pesan WA berhasil dikirim ke ' + nohp
                    : '⚠️ Link dibuat tapi WA gagal terkirim. Salin link manual.');
            }
        } else {
            alert('Gagal: ' + (res.message || 'Unknown error'));
        }
    }, 'json').fail(function() {
        $('#btn-ko-generate').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Generate & Kirim WA');
        alert('Koneksi gagal. Coba lagi.');
    });
}

function copyLink() {
    var el = document.getElementById('ko-link-result');
    el.select(); el.setSelectionRange(0, 99999);
    document.execCommand('copy');
    var btn = event.target.closest('button');
    var orig = btn.innerHTML;
    btn.innerHTML = '✅ Tersalin!';
    setTimeout(function(){ btn.innerHTML = orig; }, 2000);
}
</script>

