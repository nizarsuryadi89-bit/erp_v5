// Offline DOM tests. All HTTP methods are stubbed; no ERP server is contacted.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { JSDOM } = require('../../node_modules/jsdom');
const root = path.resolve(__dirname, '../..');
const dom = new JSDOM(`<!doctype html><html><body>
<div id="container-view-spk"><table id="tableSemuaProduksi"></table></div>
<div id="container-view-item"><table><tbody>
<tr class="tr-nesting-item"><td><input type="checkbox" class="check-item-nesting" checked
 data-id="1" data-step-id="11" data-step-version="version-one" data-status="proses"
 data-qty="10" data-tipe-id="1" data-kategori-id="1" data-bom-lines="[]"></td></tr>
<tr class="tr-nesting-item"><td><input type="checkbox" class="check-item-nesting" checked
 data-id="2" data-step-id="22" data-step-version="version-two" data-status="proses"
 data-qty="20" data-tipe-id="1" data-kategori-id="1" data-bom-lines="[]"></td></tr>
</tbody></table></div>
<div id="wip-pagination">old pagination</div><div class="status-tabs-container">old tabs</div>
<div id="compItemList"></div><div id="modalNestingCompletion"></div>
<span id="nestWidthSuggest"></span><span id="nestLinear">0</span>
<span id="compMesinBadge"></span><input id="compMesinId" value="3">
<input id="compMaterialId" value="5"><input id="compOperatorPin" value="0000"><input id="compActualUsage" value="50">
</body></html>`, { url: 'https://offline.invalid/wip/produksi', runScripts: 'outside-only', pretendToBeVisual: true });
const window = dom.window;
window.eval(fs.readFileSync(require.resolve('../../node_modules/jquery'), 'utf8'));
const $ = window.jQuery;
window.$ = window.jQuery = $;
$.fn.ready = function () { return this; };
$.fn.modal = function () { return this; };
window.WIP_CONFIG = { baseUrl: '/wip/', siteUrl: '/', csrfTokenName: 'csrf_test_name', csrfHash: 'initial-token', standardRolls: [1.1, 1.6], tolerance: 0 };
const messages = [];
window.Swal = { fire: (...args) => { messages.push(args); return Promise.resolve({ isConfirmed: false }); } };
window.bootstrap = { Modal: function () { this.show = () => {}; } };
const gets = [];
$.get = (url) => { gets.push(url); return { fail() { return this; } }; };
const posts = [];
$.post = (url, payload) => { posts.push({ url, payload }); return { fail() { return this; } }; };
window.eval(fs.readFileSync(path.join(root, 'assets/js/wip_produksi.js'), 'utf8'));
const app = window.ProductionApp;
let checks = 0;
function check(fn) { fn(); checks++; }

app.captureOperations([1, 2]);
check(() => assert.equal(app.getAuthData({ ids: [1, 2] }).expected_step_ids[1], 11));
check(() => assert.equal(app.getAuthData({ ids: [1, 2] }).expected_versions[2], 'version-two'));
check(() => assert.equal(app.getAuthData({ ids: [1] }).csrf_test_name, 'initial-token'));
$('.check-item-nesting[data-id="1"]').data('step-id', 12).data('step-version', 'changed');
check(() => assert.equal(app.getAuthData({ produksi_id: 1 }).expected_step_ids[1], 11, 'Submission retains opening snapshot'));
check(() => assert.equal(app.escapeText('<img src=x onerror=alert(1)>'), '&lt;img src=x onerror=alert(1)&gt;'));

check(() => assert.equal($(app.resultInputs(1, 'suggest', 2)).find('#suggest_good_1').val(), '2', 'Target two suggests two'));
check(() => assert.equal($(app.resultInputs(1, 'suggest', 2, 1)).find('#suggest_good_1').val(), '1', 'Partial good output is subtracted'));
check(() => assert.equal($(app.resultInputs(1, 'suggest', 2, 3)).find('#suggest_good_1').val(), '0', 'Suggestion never becomes negative'));
check(() => assert.equal($(app.resultInputs(1, 'suggest', 2, 0.75)).find('#suggest_good_1').val(), '1.25', 'Fractional remaining quantity is preserved'));
$('.check-item-nesting[data-id="1"]').data('step-output', 4);
check(() => assert.equal($(app.resultInputs(1, 'suggest', 10)).find('#suggest_good_1').val(), '6', 'Batch uses active-stage output'));
$('.check-item-nesting[data-id="1"]').data('step-output', 0);
app.state.operationSnapshots[99] = { target: 2, completed: 1 };
check(() => assert.equal($(app.resultInputs(99, 'suggest', '-')).find('#suggest_good_99').val(), '1', 'SPK preview without table row uses active-stage snapshot'));

app.advanceStep(1, 'start');
check(() => assert.equal(messages[messages.length - 1][0].input, undefined, 'Start confirmation has no PIN input'));

$('.check-item-nesting[data-id="1"]').data('step-output', 4).data('bom-qty', 2);
app.openCompletionModal();
check(() => assert.equal($('#compActualUsage').val(), '32', 'Material suggestion uses remaining output times BOM across selected items'));
check(() => assert.equal($('#compActualUsage').prop('readOnly'), false, 'Material estimate remains editable'));
$('.check-item-nesting[data-id="1"]').data('step-output', 0).data('bom-qty', 1);
app.openCompletionModal();
check(() => assert.equal($('#compActualUsage').val(), '30', 'Material estimate is filled when completion modal opens'));
check(() => assert.deepEqual(new URL(gets.find(url => url.includes('get_machines_ajax')), 'https://offline.invalid').searchParams.getAll('ids[]'), ['1', '2'], 'Completion fetch scopes machines to the frozen batch'));

check(() => assert.equal($('#comp_good_1').length, 1));
check(() => assert.equal($('#comp_good_2').val(), '20', 'Untouched job suggests its remaining target'));
$('#comp_good_2').val('');
check(() => assert.equal(app.resultValues([1, 2], 'comp'), null));
$('#comp_good_1').val('4'); $('#comp_reject_1').val('1');
$('#comp_good_2').val('20'); $('#comp_reject_2').val('0');
check(() => assert.equal(app.resultValues([1, 2], 'comp')[1].qty_output, 4));
$('#comp_good_1').val('-1');
check(() => assert.equal(app.resultValues([1, 2], 'comp'), null));
$('#comp_good_1').val('4');
$('input[name="o_1"]').filter('[value="success"]').prop('checked', true);
$('input[name="o_2"]').filter('[value="success"]').prop('checked', true);
// A later change to table selection must not alter an already-open result form.
$('.check-item-nesting[data-id="2"]').prop('checked', false);
$('#compActualUsage').val('50');
app.submitCompletion(true);
check(() => assert.equal(posts.length, 1));
check(() => assert.equal(posts[0].url, '/wip/batch_selesai_dan_stok'));
check(() => assert.equal(posts[0].payload.ids.length, 2));
check(() => assert.equal(JSON.parse(posts[0].payload.outcomes)[1].qty_output, 4));
check(() => assert.equal(posts[0].payload.send_to_pb, 0, 'Result posting never auto-handovers'));

// Finishing controls omit material inputs; preConfirm must still collect product results safely.
const originalGet = $.get;
$.get = (url, callback) => { gets.push(url); callback({ machines: [], is_mandatory: false }); return { fail() { return this; } }; };
let finishingDialog;
const originalFire = window.Swal.fire;
window.Swal.getPopup = () => window.document.querySelector('#offline-swal');
window.Swal.fire = options => {
    if (options && options.preConfirm) {
        finishingDialog = options;
        $('body').append('<div id="offline-swal">' + options.html + '</div>');
    }
    return Promise.resolve({ isConfirmed: false });
};
app.advanceStep(1, 'next', 'Finishing', 0, 0, 0, 'Pcs', 1, 1);
check(() => assert.deepEqual(new URL(gets[gets.length - 1], 'https://offline.invalid').searchParams.getAll('ids[]'), ['1'], 'Single result dialog scopes machines to its item'));
$('#advance_good_1').val('10'); $('#swal-pin-next').val('0000');
check(() => assert.equal(finishingDialog.preConfirm().qty_output, 10));
check(() => assert.equal(finishingDialog.preConfirm().qty_pakai, '0'));
$('#offline-swal').remove();
window.Swal.fire = originalFire; $.get = originalGet;

// Exercise the actual inline refresh script, with timers and HTTP fully stubbed.
const view = fs.readFileSync(path.join(root, 'application/views/admin/wip/produksi_list.php'), 'utf8');
const refreshCode = view.slice(view.indexOf('    // Refresh only an idle screen;'), view.lastIndexOf('</script>'));
let refreshTick;
window.setInterval = callback => { refreshTick = callback; return 1; };
window.clearInterval = () => {};
let responseHandler;
$.ajax = () => ({ done(callback) { responseHandler = callback; return this; }, always() { return this; } });
window.eval(refreshCode);
window.startAutoRefresh();
refreshTick();
check(() => assert.equal(responseHandler, undefined, 'Selected rows pause refresh'));
$('.check-item-nesting').prop('checked', false);
refreshTick();
check(() => assert.equal(typeof responseHandler, 'function'));
$('.check-item-nesting[data-id="1"]').prop('checked', true);
responseHandler('<div id="container-view-item">new rows</div>');
check(() => assert.equal($('.check-item-nesting').length, 2, 'Selection made during request survives response'));
$('.check-item-nesting').prop('checked', false);
$.active = 1;
responseHandler('<div id="container-view-item">new rows</div><div id="wip-pagination">new pagination</div><div class="status-tabs-container">new tabs</div>');
check(() => assert.equal($('#container-view-item').text(), 'new rows'));
check(() => assert.equal($('#wip-pagination').text(), 'new pagination'));
check(() => assert.equal($('.status-tabs-container').text(), 'new tabs'));
$.active = 0;
responseHandler('<html><body>login page</body></html>');
check(() => assert.equal($('#container-view-item').text(), 'new rows', 'Login/error response does not replace table'));
dom.window.close();
console.log(`PASS: ${checks} offline WIP DOM checks; all HTTP calls stubbed.`);
