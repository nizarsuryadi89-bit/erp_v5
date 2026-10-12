// Camera, QR decoding and HTTP are stubbed. Uses the actual PHP modal and QR controller script.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');
const { JSDOM } = require('../../node_modules/jsdom');
const root = path.resolve(__dirname, '../..');
const html = execFileSync('/Applications/XAMPP/xamppfiles/bin/php', ['-r', `
define('FCPATH', getcwd().'/'); function base_url($path) { return '/'.$path; }
require 'application/views/admin/wip/components/v_modals_produksi.php';
`], { cwd: root, encoding: 'utf8' });
const dom = new JSDOM(html, { url: 'https://offline.invalid/wip/produksi', runScripts: 'outside-only', pretendToBeVisual: true });
const w = dom.window;
w.eval(fs.readFileSync(require.resolve('../../node_modules/jquery'), 'utf8'));
const $ = w.jQuery;
Object.defineProperty(w, 'isSecureContext', { value: true, configurable: true });
Object.defineProperty(w.navigator, 'mediaDevices', { value: { getUserMedia() {} }, configurable: true });
let callback, decoded = '0042', startCount = 0, stopCount = 0, fileCount = 0, deniedCamera = false, releaseStart;
w.Html5QrcodeSupportedFormats = { QR_CODE: 0 };
w.Html5Qrcode = class {
    constructor() { this.isScanning = false; }
    async start(camera, config, success) {
        startCount++; callback = success;
        if (deniedCamera) throw new Error('Permission denied');
        if (releaseStart === 'delay') await new Promise(resolve => { releaseStart = resolve; });
        this.isScanning = true;
    }
    async stop() { stopCount++; this.isScanning = false; }
    clear() { assert.equal(this.isScanning, false, 'Camera stops before clearing reader'); }
    async scanFile() { fileCount++; if (decoded === null) throw new Error('Unreadable'); return decoded; }
};
w.eval(fs.readFileSync(path.join(root, 'assets/js/wip_pin_qr.js'), 'utf8'));
const qr = w.WipPinQr;
let checks = 0;
function check(fn) { fn(); checks++; }
const settle = async () => { await qr.queue; await qr.queue; };
const tick = () => new Promise(resolve => setImmediate(resolve));
(async () => {
    qr.init(); qr.init();
    check(() => assert.equal($('#sectionFinMesin').prop('hidden'), true, 'Machine selector remains hidden until cutting/potong is selected'));
    check(() => assert.equal($('#compPinQrPanel').prop('hidden'), true));
    $('#compScanPinQr').trigger('click'); await settle();
    check(() => assert.equal(startCount, 1, 'Repeated init does not bind duplicate clicks'));
    check(() => assert.equal($('#compScanPinQr').attr('aria-expanded'), 'true'));
    for (const invalid of ['123', '1234567', '12ab', 'https://qr.invalid/0042', '<img src=x>']) {
        callback(invalid);
        check(() => assert.equal($('#compOperatorPin').val(), '', 'Invalid QR never populates PIN'));
    }
    check(() => assert.equal($('#compPinQrStatus img').length, 0));
    callback('0042'); await settle();
    check(() => assert.equal($('#compOperatorPin').val(), '0042', 'Leading zeros retained'));
    check(() => assert.equal($('#compPinQrPanel').prop('hidden'), true));
    check(() => assert.equal(stopCount, 1, 'Camera released after reading'));
    callback('9999');
    check(() => assert.equal($('#compOperatorPin').val(), '0042', 'Repeated scanner callback ignored'));
    check(() => assert.match($('#compPinQrStatus').text(), /Konfirmasi Selesai/));
    $('#compOperatorPin').val('1111').trigger('input');
    check(() => assert.equal($('#compPinQrStatus').text(), '', 'Editing clears previous scan message'));
    qr.reset(); await settle();
    check(() => assert.equal($('#compOperatorPin').val(), '', 'PIN cleared between batches'));

    await qr.scanFile(new w.File(['test'], 'pin.png', { type: 'image/png' })); await settle();
    check(() => assert.equal($('#compOperatorPin').val(), '0042', 'Image QR fills existing PIN field'));
    check(() => assert.equal(fileCount, 1));
    await qr.scanFile(new w.File(['test'], 'pin.txt', { type: 'text/plain' }));
    check(() => assert.equal(fileCount, 1, 'Non-image file rejected before decoder'));
    decoded = null;
    await qr.scanFile(new w.File(['test'], 'bad.png', { type: 'image/png' }));
    check(() => assert.match($('#compPinQrStatus').text(), /tidak terbaca/));

    deniedCamera = true; await qr.open();
    check(() => assert.match($('#compPinQrStatus').text(), /izinnya ditolak/));
    check(() => assert.equal($('#compPinQrPanel').prop('hidden'), false, 'Image option remains available on camera failure'));
    Object.defineProperty(w, 'isSecureContext', { value: false, configurable: true });
    const before = startCount; await qr.open();
    check(() => assert.equal(startCount, before, 'Insecure page never starts camera'));
    check(() => assert.match($('#compPinQrStatus').text(), /HTTPS\/localhost/));

    Object.defineProperty(w, 'isSecureContext', { value: true, configurable: true });
    deniedCamera = false; releaseStart = 'delay';
    qr.open(); await tick();
    $('#modalNestingCompletion').trigger('hide.bs.modal');
    releaseStart(); await settle();
    check(() => assert.equal(qr.scanner.isScanning, false, 'Closing during permission prompt releases eventual stream'));
    check(() => assert.equal($('#compOperatorPin').val(), '', 'Closing modal clears secret'));
    check(() => assert.equal($('#compPinQrPanel').prop('hidden'), true));
    check(() => assert.equal($('#compScanPinQr').attr('aria-expanded'), 'false'));
    console.log(`PASS: ${checks} QR PIN UI checks; camera, decoding and HTTP stubbed.`);
    w.close();
})().catch(error => { console.error(error); w.close(); process.exitCode = 1; });
