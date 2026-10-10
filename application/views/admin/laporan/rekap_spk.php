<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="bi bi-file-earmark-spreadsheet text-primary"></i> <?= $title ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $title ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Filter Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form id="filterForm" class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bi bi-search me-1"></i> Terapkan Filter
                        </button>
                        <button type="button" id="btnReset" class="btn btn-light fw-bold ms-2">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold m-0"><i class="bi bi-table text-success me-2"></i> Data Seluruh SPK</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable">
                        <thead class="bg-light text-center">
                            <tr>
                                <th>No</th>
                                <th>Tanggal / Jam</th>
                                <th>No SPK</th>
                                <th>Nama Konsumen</th>
                                <th>Status Produksi</th>
                                <th>Tagihan</th>
                                <th>Sudah Bayar</th>
                                <th>Sisa / Piutang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat melalui AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== MODAL DETAIL SPK ===================== -->
<div class="modal fade" id="modalDetailSpk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

            <!-- Loading Spinner -->
            <div id="modalSpinner" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                <p class="mt-2 text-muted">Memuat data transaksi...</p>
            </div>

            <!-- Modal Content (hidden until loaded) -->
            <div id="modalContent" style="display:none;">
                <!-- Header -->
                <div class="modal-header flex-column align-items-center border-0 pb-0 pt-4" style="background: linear-gradient(135deg, #1a1a2e, #16213e);">
                    <p class="text-white-50 text-uppercase small fw-bold letter-spacing mb-1">IDENTITAS TRANSAKSI</p>
                    <h2 id="modalNospk" class="text-white fw-900 display-6 mb-3"></h2>
                    <div class="d-flex gap-2 mb-3">
                        <span id="modalStatusBadge" class="badge fs-6 px-3 py-2"></span>
                        <a id="modalInvoiceBtn" href="#" target="_blank" class="btn btn-sm btn-light fw-bold">
                            <i class="bi bi-printer me-1"></i> Cetak Invoice
                        </a>
                    </div>
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    <!-- Detail Info Row -->
                    <div class="row g-0 border-bottom">
                        <!-- Detail Logistik -->
                        <div class="col-md-6 p-4 border-end">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-truck me-2"></i>DETAIL LOGISTIK</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr><td class="text-muted pe-3" style="width:130px">Tgl Transaksi</td><td class="fw-bold" id="modalTgl"></td></tr>
                                <tr><td class="text-muted">Jam Input</td><td class="fw-bold" id="modalJam"></td></tr>
                                <tr><td class="text-muted">Metode Utama</td><td id="modalMetode"></td></tr>
                                <tr><td class="text-muted">Terminal</td><td class="fw-bold text-primary" id="modalTerminal"></td></tr>
                                <tr><td class="text-muted">Kasir</td><td class="fw-bold" id="modalKasir"></td></tr>
                            </table>
                        </div>
                        <!-- Profil Konsumen -->
                        <div class="col-md-6 p-4">
                            <h6 class="fw-bold text-success mb-3"><i class="bi bi-person-circle me-2"></i>PROFIL KONSUMEN</h6>
                            <div class="fw-900 fs-5 mb-1" id="modalKonsumenNama"></div>
                            <div class="text-muted small mb-1" id="modalKonsumenEmail"></div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <i class="bi bi-telephone-fill text-success"></i>
                                <span class="fw-bold" id="modalKonsumenHp"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Item -->
                    <div class="p-4">
                        <h6 class="fw-bold text-warning mb-3"><i class="bi bi-list-check me-2"></i>RINCIAN ITEM PESANAN</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover" id="modalItemTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>PRODUK</th>
                                        <th class="text-center">QTY</th>
                                        <th class="text-end">HARGA</th>
                                        <th class="text-end">SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody id="modalItemBody"></tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="4" class="text-end fw-bold">SUBTOTAL BRUTO</td>
                                        <td class="text-end fw-bold" id="modalSubtotalBruto"></td>
                                    </tr>
                                    <tr style="background: #f0f9f0;">
                                        <td colspan="4" class="text-end fw-900 fs-6">NET REVENUE</td>
                                        <td class="text-end fw-900 fs-5 text-success" id="modalNetRevenue"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ==================== END MODAL DETAIL SPK ==================== -->

<style>
.fw-900 { font-weight: 900; }
.letter-spacing { letter-spacing: 2px; }
#modalDetailSpk .modal-header { border-radius: 0; }
</style>

<!-- DataTables Integration -->
<script>
var BASE_URL = '<?= base_url() ?>';

$(document).ready(function() {
    var table = $('.datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 25,
        "ajax": {
            "url": "<?= base_url('laporan/ajax_rekap_spk') ?>",
            "type": "POST",
            "data": function(d) {
                d.start_date = $('input[name="start_date"]').val();
                d.end_date = $('input[name="end_date"]').val();
            }
        },
        "columns": [
            { "data": "no", "className": "text-center" },
            { 
                "data": "spk_time",
                "render": function(data) {
                    if(!data) return '';
                    let d = new Date(data);
                    let day = ("0" + d.getDate()).slice(-2);
                    let month = ("0" + (d.getMonth() + 1)).slice(-2);
                    let year = d.getFullYear();
                    let hour = ("0" + d.getHours()).slice(-2);
                    let min = ("0" + d.getMinutes()).slice(-2);
                    return '<div class="fw-bold">'+day+'/'+month+'/'+year+'</div><div class="text-muted small">'+hour+':'+min+'</div>';
                }
            },
            { 
                "data": "nospk",
                "render": function(data) {
                    return '<span class="badge bg-light text-dark border"><code>'+data+'</code></span>';
                }
            },
            { 
                "data": "konsumen_nama",
                "render": function(data) {
                    return '<span class="fw-bold">' + (data ? data : 'Pelanggan Umum') + '</span>';
                }
            },
            { 
                "data": "status_produksi",
                "className": "text-center",
                "render": function(data) {
                    if(data == 'done') return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>';
                    else if(data == 'proses') return '<span class="badge bg-warning text-dark"><i class="bi bi-gear-fill me-1"></i>Di Proses</span>';
                    else if(data == 'approved') return '<span class="badge bg-primary"><i class="bi bi-hourglass-split me-1"></i>Antri Produksi</span>';
                    else if(data == 'on_pb') return '<span class="badge bg-info text-dark"><i class="bi bi-box-seam me-1"></i>Di Packing</span>';
                    else if(data == 'on_costumer') return '<span class="badge bg-success"><i class="bi bi-person-check-fill me-1"></i>Diserahkan</span>';
                    else return '<span class="badge bg-secondary"><i class="bi bi-clock me-1"></i>Pending</span>';
                }
            },
            { 
                "data": "total_tagihan",
                "className": "text-end fw-bold",
                "render": function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            { 
                "data": "total_bayar",
                "className": "text-end text-success fw-bold",
                "render": function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            { 
                "data": null,
                "className": "text-end fw-bold",
                "render": function(data, type, row) {
                    let sisa = row.total_tagihan - row.total_bayar;
                    let cls = sisa > 0 ? 'text-danger' : 'text-muted';
                    return '<span class="'+cls+'">Rp ' + new Intl.NumberFormat('id-ID').format(sisa) + '</span>';
                }
            },
            {
                "data": "nospk",
                "className": "text-center",
                "orderable": false,
                "render": function(data) {
                    return `
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-sm btn-primary btn-detail-spk" data-nospk="${data}" title="Lihat Detail Transaksi">
                                <i class="bi bi-receipt"></i>
                            </button>
                            <a href="${BASE_URL}transaksi/cetak_invoice/${data}" class="btn btn-sm btn-outline-secondary" target="_blank" title="Cetak Invoice">
                                <i class="bi bi-printer"></i>
                            </a>
                        </div>
                    `;
                }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [[1, "desc"]]
    });
    
    // Filter form submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });
    
    // Reset button
    $('#btnReset').on('click', function() {
        $('input[name="start_date"]').val('<?= $start_date ?>');
        $('input[name="end_date"]').val('<?= $end_date ?>');
        table.ajax.reload();
    });

    // Click tombol detail (delegasi event)
    $(document).on('click', '.btn-detail-spk', function() {
        var nospk = $(this).data('nospk');
        
        // Reset modal
        $('#modalContent').hide();
        $('#modalSpinner').show();
        
        // Buka modal
        var modal = new bootstrap.Modal(document.getElementById('modalDetailSpk'));
        modal.show();
        
        // Ambil data via AJAX
        $.ajax({
            url: BASE_URL + 'laporan/ajax_detail_spk',
            type: 'POST',
            data: { nospk: nospk },
            dataType: 'json',
            success: function(res) {
                $('#modalSpinner').hide();
                if (!res.success) {
                    alert('Gagal memuat data: ' + res.message);
                    return;
                }
                
                var spk = res.spk;
                var items = res.items;
                var fmt = function(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n || 0); };
                
                // Status Badge
                var statusMap = {
                    'done': ['bg-success', 'Selesai'],
                    'proses': ['bg-warning text-dark', 'Di Proses'],
                    'approved': ['bg-primary', 'Antri Produksi'],
                    'on_pb': ['bg-info text-dark', 'Di Packing'],
                    'on_costumer': ['bg-success', 'Diserahkan']
                };
                var statusData = statusMap[spk.status_produksi] || ['bg-secondary', 'Pending'];
                
                // Fill header
                $('#modalNospk').text(spk.nospk);
                $('#modalStatusBadge').attr('class', 'badge fs-6 px-3 py-2 ' + statusData[0]).text(statusData[1]);
                $('#modalInvoiceBtn').attr('href', BASE_URL + 'transaksi/cetak_invoice/' + spk.nospk);
                
                // Logistik
                var tgl = spk.tgl_bayar ? new Date(spk.tgl_bayar).toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'}) : '-';
                var jam = spk.jam_bayar ? spk.jam_bayar.substring(0,5) + ' WIB' : '-';
                var metode = spk.metode_bayar || '-';
                var metodeBadge = metode == 'tunai' ? '<span class="badge bg-success">TUNAI</span>' :
                                  metode == 'transfer' ? '<span class="badge bg-primary">TRANSFER</span>' :
                                  metode == 'debit' ? '<span class="badge bg-warning text-dark">EDC/DEBIT</span>' :
                                  metode == 'ewallet' ? '<span class="badge bg-info text-dark">E-WALLET</span>' :
                                  '<span class="badge bg-secondary">N/A</span>';

                $('#modalTgl').text(tgl);
                $('#modalJam').text(jam);
                $('#modalMetode').html(metodeBadge);
                $('#modalTerminal').text(spk.nama_perusahaan || '-');
                $('#modalKasir').text(spk.kasir_nama || '-');
                
                // Konsumen
                $('#modalKonsumenNama').text(spk.konsumen_nama || 'Pelanggan Umum');
                $('#modalKonsumenEmail').text(spk.konsumen_email || '-');
                $('#modalKonsumenHp').text(spk.konsumen_nohp || '-');
                
                // Items
                var tbody = '';
                var totalBruto = 0;
                $.each(items, function(i, item) {
                    var subtotal = parseFloat(item.subtotal) || 0;
                    totalBruto += subtotal;
                    tbody += '<tr>' +
                        '<td class="text-center">' + (i+1) + '</td>' +
                        '<td><div class="fw-bold">' + (item.product_nama || '-') + '</div><div class="text-muted small">SKU-N/A</div></td>' +
                        '<td class="text-center">' + (item.qty || 0) + ' ' + (item.satuan || '') + '</td>' +
                        '<td class="text-end">' + fmt(item.harga) + '</td>' +
                        '<td class="text-end fw-bold">' + fmt(item.subtotal) + '</td>' +
                        '</tr>';
                });
                
                if(items.length == 0) {
                    tbody = '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada item ditemukan</td></tr>';
                }
                
                $('#modalItemBody').html(tbody);
                $('#modalSubtotalBruto').text(fmt(totalBruto));
                $('#modalNetRevenue').text(fmt(spk.total_bayar));
                
                $('#modalContent').show();
            },
            error: function() {
                $('#modalSpinner').hide();
                alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
            }
        });
    });
});
</script>
