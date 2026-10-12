// Offline monitor regressions: timers and HTTP are stubbed; no ERP data is touched.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const {JSDOM} = require('../../node_modules/jsdom');
const initial = {success:true,rows:[],total:0,spk_count:0,material_count:0,pages:1,page_size:50,filters:{date:'2026-10-12',warehouse:0,page:1,search:''},today:'2026-10-12',server_time:'2026-10-12 12:00:00'};
const dom = new JSDOM(`<div id="material-live" data-feed-url="/wip/ajax_live_bahan" data-detail-url="/wip/control_center/"><script id="live-initial" type="application/json">${JSON.stringify(initial)}</script><form id="live-filters"><input id="live-date" value="2026-10-12"><select id="live-warehouse"><option value="0">Semua</option><option value="3">Cabang</option></select><input id="live-search"></form><div id="live-status"></div><div id="live-error" class="d-none"></div><div id="live-updated"></div><div id="live-total"></div><div id="live-spk-count"></div><div id="live-material-count"></div><table><tbody id="live-rows"></tbody></table><div id="live-pagination-label"></div><button id="live-prev"></button><button id="live-next"></button><button id="live-pause"></button><button id="live-refresh"></button><button id="live-today"></button></div>`, {url:'https://offline.invalid/wip/live_bahan',runScripts:'outside-only',pretendToBeVisual:true});
const w = dom.window, el = id => w.document.getElementById(id);
const timers = new Map(); let tick = 0; const requests = []; let checks=0;
w.setTimeout = (fn, ms) => {timers.set(++tick,{fn,ms}); return tick;}; w.clearTimeout = id => timers.delete(id);
w.fetch = (url, options) => new Promise((resolve,reject) => {requests.push({url,options,resolve,reject}); options.signal.addEventListener('abort',()=>{const e=new Error('Aborted');e.name='AbortError';reject(e);});});
function check(test,label){checks++;assert.ok(test,label);}
async function flush(){for(let i=0;i<12;i++)await Promise.resolve();}
function fireTimer(ms){const entry=[...timers].find(([,t])=>t.ms===ms);assert.ok(entry,'scheduled timer '+ms);timers.delete(entry[0]);entry[1].fn();}
function respond(index, data, status=200){requests[index].resolve({ok:status===200,status,redirected:false,json:async()=>data});}
(async()=>{
 w.eval(fs.readFileSync(require.resolve('../../assets/js/material_live.js'),'utf8'));
 check(requests.length===0,'server initial feed does not immediately duplicate request');
 fireTimer(5000);check(requests.length===1,'auto reload every five seconds');
 el('live-refresh').click();check(requests.length===1,'no overlapping requests');
 const row={log_id:1,produksi_id:99,material_id:2,gudang_id:3,material_name:'<img src=x onerror=alert(1)>',variant:'Biru',production_qty:0.125,unit:'m',nospk:'3-1-1026-1',product_name:'Pulpen',warehouse_name:'Cabang',created_at:'2026-10-12 12:01:00'};
 const data={...initial,rows:[row],total:1,spk_count:1,material_count:1};
 respond(0,data);await flush();check(requests.length===1,'busy button does not queue duplicate HTTP requests');
 el('live-refresh').click();check(requests.length===2,'manual refresh resumes after completion');
 respond(1,data);await flush();
 check(el('live-rows').textContent.includes('0,125 m'),'base quantity rendered without rounding to whole units');
 check(!el('live-rows').querySelector('img'),'material labels cannot inject HTML');
 check(el('live-rows').querySelector('a').getAttribute('href')==='/wip/control_center/99','SPK opens the actual production reference');
 el('live-pause').click();check(![...timers.values()].some(t=>t.ms===5000),'pause stops polling');
 el('live-pause').click();check(requests.length===3,'resume refreshes immediately');
 respond(2,{...data,rows:[{...row,log_id:2}]});await flush();
 check(el('live-rows').querySelector('tr').classList.contains('live-material-new'),'new deduction highlighted');
 fireTimer(5000);respond(3,{success:false,message:'Gangguan uji'},500);await flush();
 check(el('live-rows').querySelector('tr').dataset.logId==='2','failed refresh preserves last rows');
 check(!el('live-error').classList.contains('d-none'),'connection error visible');
 el('live-warehouse').value='3';el('live-warehouse').dispatchEvent(new w.Event('change'));await flush();
 check(new URL(requests[4].url).searchParams.get('warehouse')==='3','warehouse selection sent');
 el('live-search').value='SPK-B';el('live-filters').dispatchEvent(new w.Event('submit',{cancelable:true}));await flush();
 check(requests[4].options.signal.aborted,'filter change aborts stale request');
 check(new URL(requests[5].url).searchParams.get('search')==='SPK-B','new filter replaces old response');
 respond(5,{...data,filters:{...initial.filters,warehouse:3,search:'SPK-B'},rows:[]});await flush();
 check(el('live-rows').textContent.includes('Belum ada'),'empty filter has an explicit empty state');
 fireTimer(5000);respond(6,{...initial,today:'2026-10-13'});await flush();
 check(el('live-date').value==='2026-10-13','today filter follows midnight server date');
 respond(7,{...initial,today:'2026-10-13',filters:{...initial.filters,date:'2026-10-13'}});await flush();
 fireTimer(5000);respond(8,{},401);await flush();
 check(![...timers.values()].some(t=>t.ms===5000),'expired session stops polling');
 check(el('live-error').textContent.includes('Login kembali'),'expired session gives a clear action');
 console.log(`PASS: ${checks} offline live-material UI checks.`);dom.window.close();
})().catch(e=>{console.error(e);process.exitCode=1;});
