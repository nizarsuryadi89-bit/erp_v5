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

$('.check-item-nesting').prop('checked', false);
$('.check-item-nesting[data-id="1"]').data('status', 'on_pb');
app.enforceConsistency();
check(() => assert.equal($('.check-item-nesting[data-id="1"]').prop('disabled'), true, 'PB items cannot be selected for production again'));
check(() => assert.equal($('.check-item-nesting[data-id="2"]').prop('disabled'), false, 'Open sibling stays selectable'));
$('.check-item-nesting[data-id="1"]').data('status', 'proses');
app.enforceConsistency();
$('.check-item-nesting').prop('checked', true);

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

// A resolved dynamic area BOM already contains the per-item area: never multiply dimensions twice.
const variantRow = $('.check-item-nesting[data-id="1"]');
variantRow.data('kategori-id', '2').data('panjang', 1.2).data('lebar', 1.7).data('bom-lines', [{bom_line_id:'dyn_1',bom_material_id:361,bahan_id:220,bahan_nama:'Biru',bom_qty:2.04,quantity:2.04,satuan_nama:'m2',is_dynamic:true}]);
app.openCompletionModal();
check(() => assert.equal($('.bom-input-1').val(), '20.40', 'Dynamic area per item multiplied by qty once'));
variantRow.data('bom-lines', [{bom_line_id:'dyn_1',bom_material_id:361,bahan_id:220,bahan_nama:'Biru',bom_qty:0,quantity:0,satuan_nama:'m2',is_dynamic:true}]);
app.openCompletionModal();
check(() => assert.equal($('.bom-input-1').val(), '0.00', 'Zero BOM quantity never defaults to one'));
variantRow.data('kategori-id', '1').data('panjang', 0).data('lebar', 0).data('bom-lines', []);
app.openCompletionModal();

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
$('#compOperatorPin').val('0000');
app.submitCompletion(true);
check(() => assert.equal(posts.length, 1));
check(() => assert.equal(posts[0].url, '/wip/batch_selesai_dan_stok'));
check(() => assert.equal(posts[0].payload.ids.length, 2));
check(() => assert.equal(JSON.parse(posts[0].payload.outcomes)[1].qty_output, 4));
check(() => assert.equal(posts[0].payload.send_to_pb, undefined, 'Server determines PB arrival after final completion'));

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
const machineRequestsBeforeFinishing = gets.length;
app.advanceStep(1, 'next', 'F.Laminating', 0, 0, 0, 'Pcs', 1, 0);
check(() => assert.equal(gets.length, machineRequestsBeforeFinishing, 'Legacy finishing does not fetch machines'));
check(() => assert.equal($('#swal-mesin-id').length, 0, 'Finishing result form has no machine selector'));
$('#advance_good_1').val('10'); $('#swal-pin-next').val('0000');
check(() => assert.equal(finishingDialog.preConfirm().qty_output, 10));
check(() => assert.equal(finishingDialog.preConfirm().qty_pakai, '0'));
check(() => assert.equal(finishingDialog.preConfirm().mesin_id, '0'));
$('#offline-swal').remove();
window.Swal.fire = originalFire; $.get = originalGet;

$('body').append('<div id="finishingItemList"></div><input id="finOperatorPin"><div id="sectionFinMesin" hidden><select id="finMesinId"></select></div>');
const requestsBeforeBatchFinishing = gets.length;
app.openFinishingModal();
check(() => assert.equal(gets.length, requestsBeforeBatchFinishing, 'Finishing batch does not fetch machines'));
$('#finOperatorPin').val('0000');
$('#fin_good_1').val('10'); $('#fin_reject_1').val('0');
app.submitFinishing();
check(() => assert.equal(posts.length, 2, 'Finishing can be submitted without any machine controls'));
check(() => assert.equal(posts[1].payload.mesin_id, 0));
check(() => assert.equal($('#sectionFinMesin').prop('hidden'), true));

$.get = (url, callback) => {
    gets.push(url); callback({ machines: [{ mesin_id: 6, mesin_nama: 'Mesin Potong' }], is_mandatory: true });
    return { fail() { return this; } };
};
$('.check-item-nesting[data-id="1"]').data('step-name', 'F.Cutting');
$('.check-item-nesting[data-id="1"]').data('kategori-id', 2).data('product-name', 'Cutting Meteran');
app.openFinishingModal();
check(() => assert.equal($('#sectionFinMesin').prop('hidden'), false));
check(() => assert.deepEqual(new URL(gets[gets.length - 1], 'https://offline.invalid').searchParams.getAll('ids[]'), ['1'], 'Cutting machine list scoped to batch'));
$('#finOperatorPin').val('0000');
app.submitFinishing();
check(() => assert.equal(posts.length, 2, 'Cutting cannot submit without machine'));
$('#finMesinId').val('6'); app.submitFinishing();
check(() => assert.equal(posts[2].payload.mesin_id, '6', 'Cutting sends selected actual machine'));
$('#finMesinId').val('');
$('.check-item-nesting[data-id="1"]').data('step-name', 'F.Laminating');
app.openFinishingModal();
check(() => assert.equal($('#sectionFinMesin').prop('hidden'), true, 'Reopening ordinary finishing clears machine requirement'));
$('.check-item-nesting[data-id="2"]').data('step-name', 'Potong').data('kategori-id', 2).prop('checked', true);
app.openFinishingModal();
check(() => assert.equal(app.state.finishingMachineRequired, true, 'Mixed finishing batch requires machine if one item needs it'));
check(() => assert.deepEqual(new URL(gets[gets.length - 1], 'https://offline.invalid').searchParams.getAll('ids[]'), ['1', '2'], 'Machine choices follow the full frozen batch'));
$('.check-item-nesting[data-id="2"]').prop('checked', false);

let cuttingDialog;
const requestsBeforeA3 = gets.length;
$('.check-item-nesting[data-id="1"]').data('step-name', 'Cutting Kiss Cut').data('kategori-id', 16).data('product-name', 'Cutting Kiss Cut A3 (Standar)');
app.openFinishingModal();
check(() => assert.equal($('#sectionFinMesin').prop('hidden'), true, 'Standalone A3 cutting does not display a machine selector'));
check(() => assert.equal(gets.length, requestsBeforeA3, 'A3 finishing does not request machines or warn about an empty master'));
$('#finOperatorPin').val('0000'); app.submitFinishing();
check(() => assert.equal(posts[posts.length - 1].payload.mesin_id, 0, 'A3 finishing posts results without a machine'));
$('.check-item-nesting[data-id="1"]').data('product-name', 'Cutting Meteran').data('kategori-id', 2);
window.Swal.showValidationMessage = () => {};
window.Swal.fire = options => {
    if (options && options.preConfirm) {
        cuttingDialog = options; $('body').append('<div id="offline-swal">' + options.html + '</div>');
    }
    return Promise.resolve({ isConfirmed: false });
};
app.advanceStep(1, 'next', 'Potong Kertas', 0, 0, 0, 'Pcs', 2, 0);
check(() => assert.equal($('#swal-mesin-id').length, 1, 'Individual potong step displays machine selector'));
$('#swal-pin-next').val('0000'); $('#swal-mesin-id').val('');
check(() => assert.equal(cuttingDialog.preConfirm(), false, 'Individual potong blocks missing machine'));
$('#swal-mesin-id').val('6');
check(() => assert.equal(cuttingDialog.preConfirm().mesin_id, '6'));
$('#offline-swal').remove();
$('.check-item-nesting[data-id="1"]').data('product-name', 'Ongkos Potong A3');
app.advanceStep(1, 'next', 'Ongkos Potong Kertas', 0, 0, 0, 'Paket', 16, 1);
check(() => assert.equal($('#swal-mesin-id').length, 0, 'Individual A3 potong result does not request a machine'));
$('.check-item-nesting[data-id="1"]').data('product-name', 'Cutting Meteran');
$('#offline-swal').remove(); window.Swal.fire = originalFire; $.get = originalGet;

// Multiple confirmations must not post overlapping operations for one item.
const originalPost = $.post;
const advances = [];
$.post = (url, payload, callback) => {
    const request = $.Deferred();
    request.done(callback);
    advances.push({ payload, request });
    return request.promise();
};
app._postAdvance(1, 'start', {});
app._postAdvance(1, 'start', {});
app._postAdvance(1, 'cancel', {});
check(() => assert.equal(advances.length, 1, 'One pending item request blocks repeated start and concurrent cancel'));
app._postAdvance(2, 'start', {});
check(() => assert.equal(advances.length, 2, 'Different items can post independently'));
advances[0].request.resolve({ success: false, message: 'Offline rejection' });
app._postAdvance(1, 'start', {});
check(() => assert.equal(advances.length, 3, 'A finished rejected request allows retry'));
advances[1].request.resolve({ success: false, message: 'Offline rejection' });
advances[2].request.resolve({ success: false, message: 'Offline rejection' });
check(() => assert.equal(Object.keys(app.state.advanceRequests).length, 0, 'Pending guards are cleared after responses'));
$.post = originalPost;

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
