<script>
    let Toast;

    // --- Global Functions (Accessible from inline onclick) ---
    function sortBy(field, direction) {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('orderby', field);
        currentUrl.searchParams.set('sort', direction);
        currentUrl.searchParams.delete('page');
        window.location.href = currentUrl.toString();
    }

    function resetFilters() {
        $('#searchInput').val('');
        $('#filterKategori').val('');
        $('#filterStatus').val('');
        $('#filterStartsWith').val('');
        $('#filterSort').val('asc');
        $('#filterMetode').val('');

        const currentUrl = new URL(window.location.href);
        const currentPerPage = currentUrl.searchParams.get('per_page') || '20';
        $('#pageSize').val(currentPerPage);

        // Reset to first page and clear filters
        currentUrl.searchParams.delete('page');
        currentUrl.searchParams.delete('q');
        currentUrl.searchParams.delete('kategori');
        currentUrl.searchParams.delete('status');
        currentUrl.searchParams.delete('starts_with');
        currentUrl.searchParams.delete('sort');
        currentUrl.searchParams.delete('orderby');
        currentUrl.searchParams.set('per_page', currentPerPage);

        window.location.href = currentUrl.toString();
    }

    function exportData() {
        const visibleRows = $('#productTable tbody tr:visible');
        if (visibleRows.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak ada data',
                text: 'Tidak ada data yang dapat diexport'
            });
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "No,Nama Product,Kategori,Sub Kategori,Jenis,Sisi,Satuan,Metode Penjualan,Status Kelengkapan,BOM,Routing,Skala Harga\n";

        visibleRows.each(function (index) {
            const row = $(this);
            const no = index + 1;
            const nama = row.find('td:eq(1) a.fw-bold').text().trim().replace(/"/g, '""');
            const status = row.find('td:eq(1) .status-badge').text().trim();
            const kategori = row.find('td:eq(2) .fw-bold').text().trim().replace(/"/g, '""');
            const subkategori = row.find('td:eq(2) .text-slate-600').text().trim().replace(/"/g, '""');
            const jenis = row.find('td:eq(3) .d-flex.flex-wrap > div:first-child').text().trim();
            const sisi = row.find('td:eq(3) .badge').text().trim() || '-';
            const satuan = row.find('td:eq(3) .fw-bold.text-slate-700').text().trim();
            const metode = row.find('td:eq(4) .badge').text().trim();
            const bom = row.find('td:eq(5) .indicator-bom .count-badge').text().trim() || '0';
            const routing = row.find('td:eq(5) .indicator-routing .count-badge').text().trim() || '0';
            const skalaHarga = row.find('td:eq(5) .indicator-price .count-badge').text().trim() || '0';

            csvContent += `"${no}","${nama}","${kategori}","${subkategori}","${jenis}","${sisi}","${satuan}","${metode}","${status}","${bom}","${routing}","${skalaHarga}"\n`;
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `product_data_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        Swal.fire({
            icon: 'success',
            title: 'Export Berhasil',
            text: `Data ${visibleRows.length} product berhasil diexport`,
            timer: 2000,
            showConfirmButton: false
        });
    }

    $(document).ready(function () {
        Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        // Enhanced Search and Filter functionality
        // Removed auto-search on keyup to prevent frequent reloads

        $('#searchInput').on('keypress', function (e) {
            if (e.which == 13) {
                filterTable();
            }
        });

        // Search icon click listener
        $('.bi-search').parent().css('cursor', 'pointer').on('click', function () {
            filterTable();
        });

        $('#filterKategori, #filterStatus, #filterStartsWith, #filterSort, #filterMetode').on('change', function () {
            filterTable();
        });

        $('.alphabet-filter-btn').on('click', function () {
            const selectedLetter = $(this).data('starts-with') ?? '';
            $('#filterStartsWith').val(selectedLetter);
            filterTable();
        });

        // Page size change
        $('#pageSize').on('change', function () {
            const pageSize = $(this).val();
            const currentUrl = new URL(window.location.href);

            // Update per_page parameter
            currentUrl.searchParams.set('per_page', pageSize);

            // Reset to first page when changing page size
            currentUrl.searchParams.delete('page');

            // Redirect to new URL
            window.location.href = currentUrl.toString();
        });

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Set pageSize dropdown value based on current URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const currentPerPage = urlParams.get('per_page');
        if (currentPerPage) {
            $('#pageSize').val(currentPerPage);
        }

        // Add loading states
        $('.btn').on('click', function () {
            if (!$(this).hasClass('btn-export')) {
                $(this).addClass('loading');
                setTimeout(() => {
                    $(this).removeClass('loading');
                }, 1000);
            }
        });

        // Keyboard shortcuts
        $(document).on('keydown', function (e) {
            // Ctrl/Cmd + F for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                $('#searchInput').focus();
            }
            // Ctrl/Cmd + E for export
            if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
                e.preventDefault();
                exportData();
            }
        });

        // Auto-refresh data every 5 minutes
        setInterval(function () {
            // Only refresh if user is not actively interacting
            if (!$('input:focus, select:focus').length) {
                // You can add AJAX refresh here if needed
            }
        }, 300000); // 5 minutes

        function filterTable() {
            const searchTerm = $('#searchInput').val();
            const kategoriFilter = $('#filterKategori').val();
            const statusFilter = $('#filterStatus').val();
            const startsWithFilter = $('#filterStartsWith').val();
            const sortFilter = $('#filterSort').val();
            const orderbyFilter = new URL(window.location.href).searchParams.get('orderby');

            const currentUrl = new URL(window.location.href);

            // Set Search Query
            if (searchTerm) currentUrl.searchParams.set('q', searchTerm);
            else currentUrl.searchParams.delete('q');

            // Set Kategori
            if (kategoriFilter) currentUrl.searchParams.set('kategori', kategoriFilter);
            else currentUrl.searchParams.delete('kategori');

            // Set Status
            if (statusFilter) currentUrl.searchParams.set('status', statusFilter);
            else currentUrl.searchParams.delete('status');

            // Set Starting Letter
            if (startsWithFilter) currentUrl.searchParams.set('starts_with', startsWithFilter);
            else currentUrl.searchParams.delete('starts_with');

            // Set Sort
            if (sortFilter && sortFilter !== 'asc') currentUrl.searchParams.set('sort', sortFilter);
            else currentUrl.searchParams.delete('sort');

            // Set OrderBy
            if (orderbyFilter) currentUrl.searchParams.set('orderby', orderbyFilter);

            // Reset to page 1 when searching
            currentUrl.searchParams.delete('page');

            window.location.href = currentUrl.toString();
        }

        function updateVisibleCount() {
            const visibleCount = $('#productTable tbody tr:visible').length;
            const totalCount = $('#productTable tbody tr').length;

            // Add or update count display
            let countDisplay = $('.filter-count');
            if (countDisplay.length === 0) {
                countDisplay = $('<div class="filter-count mt-2 text-muted small"></div>');
                $('.search-section').append(countDisplay);
            }

            if (visibleCount !== totalCount) {
                countDisplay.html(`<i class="bi bi-info-circle me-1"></i>Menampilkan ${visibleCount} dari ${totalCount} product`);
            } else {
                countDisplay.html('');
            }
        }

        // Form validation
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Subkategori AJAX functionality
        $('#formTambahSubkategori').on('submit', function (e) {
            e.preventDefault();
            const nama = $(this).find('input[name="subkategori_nama"]').val();

            $.ajax({
                url: "<?= base_url('product/add_subkategori_ajax') ?>",
                method: "POST",
                data: { subkategori_nama: nama },
                dataType: "json",
                success: function (res) {
                    if (res.status === 'success') {
                        const row = `
                        <tr data-id="${res.data.subkategori_id}">
                            <td class="text-center">-</td>
                            <td><input type="text" class="form-control subkategori-nama" value="${res.data.subkategori_nama}"></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-primary btn-sm btn-edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                        $('table tbody').append(row);
                        $('#formTambahSubkategori')[0].reset();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Sub Kategori berhasil ditambahkan',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal menambahkan sub kategori'
                        });
                    }
                }
            });
        });

        // Edit Subkategori
        $(document).on('click', '.btn-edit', function (e) {
            e.preventDefault();
            const row = $(this).closest('tr');
            const id = row.data('id');
            const nama = row.find('.subkategori-nama').val();

            $.ajax({
                url: "<?= base_url('product/update_subkategori_ajax') ?>",
                method: "POST",
                data: { subkategori_id: id, subkategori_nama: nama },
                dataType: "json",
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Sub Kategori berhasil diperbarui',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal memperbarui data'
                        });
                    }
                }
            });
        });

        // Delete Subkategori
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const row = $(this).closest('tr');
            const id = row.data('id');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url('product/delete_subkategori_ajax') ?>",
                        method: "POST",
                        data: { subkategori_id: id },
                        dataType: "json",
                        success: function (res) {
                            if (res.status === 'success') {
                                row.remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dihapus!',
                                    text: 'Subkategori berhasil dihapus',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message || 'Gagal menghapus data'
                                });
                            }
                        }
                    });
                }
            });
        });

        // Select2 initialization
        $('#modalInputData').on('shown.bs.modal', function () {
            var $select = $(this).find('.select2-bahan-ajax');
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.select2({
                placeholder: '-- Cari & Pilih Bahan Baku --',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalInputData'),
                ajax: {
                    url: '<?= base_url('product/search_bahan_json') ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { term: params.term };
                    },
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });
        });
    });

    // BOM Modal Functions
    function openBomModal(productId, productName) {
        $('#bomProductId').text(productId);
        $('#bomProductName').text(productName);
        $('#bomProductNameHeader').text(productName);
        $('#bomProductIdInput').val(productId);



        // Load BOM data
        loadBomData(productId);

        // Initialize bahan select2
        initializeBahanSelect2();

        // Show modal
        $('#modalBom').modal('show');
    }



    function loadBomData(productId) {
        $.ajax({
            url: '<?= base_url('manufaktur/get_bom_data') ?>',
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    displayBomData(response.data);
                    
                    // Populate variations dropdown
                    let varSelect = $('#bomVariasiSelect');
                    if (varSelect.hasClass('select2-hidden-accessible')) {
                        varSelect.select2('destroy');
                    }
                    varSelect.empty();
                    if (response.variations && response.variations.length > 0) {
                        response.variations.forEach(function(v) {
                            varSelect.append(`<option value="${v.harga_id}">${v.display_name}</option>`);
                        });
                    }
                    varSelect.select2({
                        dropdownParent: $('#modalBom'),
                        width: '100%',
                        allowClear: true
                    });
                    
                } else {
                    $('#bomTableBody').html('<tr><td colspan="7" class="text-center text-muted">Tidak ada data BOM</td></tr>');
                }
            },
            error: function () {
                $('#bomTableBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }

    function displayBomData(bomData) {
        let html = '';
        if (bomData.length > 0) {
            bomData.forEach(function (item, index) {
                const netQty = parseFloat(item.quantity) || 0;
                const scrap = parseFloat(item.scrap_factor) || 0;
                const grossQty = netQty * (1 + (scrap / 100));

                const calcTypeMap = {
                    'fixed': '1. Fixed',
                    'area': '2. Area (P x L)',
                    'length': '3. Length (P)',
                    'weight': '4. Weight'
                };
                const calcType = item.calc_type || 'fixed';
                const calcTypeText = calcTypeMap[calcType] || calcTypeMap['fixed'];
                const variasiBadge = item.harga_id ? `<span class="badge bg-info-subtle text-info border border-info-subtle mt-1" style="font-size: 0.7rem;"><i class="bi bi-tag me-1"></i>${item.variasi_nama || 'Varian ' + item.harga_id}</span>` : `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle mt-1" style="font-size: 0.7rem;"><i class="bi bi-globe me-1"></i>Semua Variasi (Global)</span>`;

                html += `
                <tr id="bom-row-${item.bom_line_id}">
                    <td class="text-center fw-bold">${index + 1}</td>
                    <td>
                        <div class="fw-bold text-dark">${item.material_name || '-'}</div>
                        ${variasiBadge}
                        <div class="small text-muted mt-1" id="bom-ket-text-${item.bom_line_id}">${item.keterangan || ''}</div>
                    </td>
                    <td class="text-center fw-semibold text-primary">
                        <span id="bom-qty-text-${item.bom_line_id}">${+netQty.toFixed(4)}</span> ${item.satuan || ''}
                    </td>
                    <td class="text-center text-danger">
                        <span id="bom-scrap-text-${item.bom_line_id}">${scrap > 0 ? (+scrap.toFixed(4)) + '%' : '-'}</span>
                    </td>
                    <td class="text-center bg-light fw-bold text-dark" id="bom-gross-text-${item.bom_line_id}">${+grossQty.toFixed(4)}</td>
                    <td class="text-center text-secondary">
                        <span id="bom-dimensi-text-${item.bom_line_id}" class="badge bg-light text-dark border border-secondary">${calcTypeText}</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="editBom(${JSON.stringify(item).replace(/"/g, '&quot;')})" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteBom(${item.bom_line_id})" title="Hapus">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            });
        } else {
            html = '<tr><td colspan="7" class="text-center text-muted">Tidak ada data BOM</td></tr>';
        }
        $('#bomTableBody').html(html);
    }

    function editBom(item) {
        const netQty = parseFloat(item.quantity) || 0;
        const scrap = parseFloat(item.scrap_factor) || 0;
        const calcType = item.calc_type || 'fixed';

        const rowHtml = `
            <td class="bg-soft-warning"></td>
            <td class="bg-soft-warning">
                <select class="form-select select2-edit-bahan" id="edit-bahan-${item.bom_line_id}">
                    <option value="${item.bahan_id}" selected>${item.material_name || '-'}</option>
                </select>
                <input type="text" class="form-control form-control-sm mt-1" id="edit-ket-${item.bom_line_id}" value="${item.keterangan || ''}" placeholder="Keterangan...">
            </td>
            <td class="bg-soft-warning">
                <div class="input-group input-group-sm">
                    <input type="number" step="0.0001" class="form-control text-center fw-bold" id="edit-qty-${item.bom_line_id}" value="${netQty}">
                    <span class="input-group-text" id="edit-satuan-label-${item.bom_line_id}">${item.satuan || ''}</span>
                </div>
            </td>
            <td class="bg-soft-warning">
                <div class="input-group input-group-sm">
                    <input type="number" step="0.1" class="form-control text-center" id="edit-scrap-${item.bom_line_id}" value="${scrap}">
                    <span class="input-group-text">%</span>
                </div>
            </td>
            <td class="bg-soft-warning text-center align-middle">
                <span class="badge bg-warning text-dark">REVISI</span>
            </td>
            <td class="bg-soft-warning">
                <select class="form-select form-select-sm text-center fw-bold" id="edit-calctype-${item.bom_line_id}">
                    <option value="fixed" ${calcType === 'fixed' ? 'selected' : ''}>1. Fixed</option>
                    <option value="area" ${calcType === 'area' ? 'selected' : ''}>2. Area</option>
                    <option value="length" ${calcType === 'length' ? 'selected' : ''}>3. Length</option>
                    <option value="weight" ${calcType === 'weight' ? 'selected' : ''}>4. Weight</option>
                </select>
            </td>
            <td class="bg-soft-warning text-center">
                <div class="btn-group">
                    <button class="btn btn-sm btn-success" onclick="saveEditBom(${item.bom_line_id})" title="Simpan">
                        <i class="bi bi-check-lg"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="loadBomData($('#bomProductIdInput').val())" title="Batal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </td>
        `;

        $(`#bom-row-${item.bom_line_id}`).html(rowHtml);

        // Initialize Select2 for the edit row
        $(`#edit-bahan-${item.bom_line_id}`).select2({
            dropdownParent: $('#modalBom'),
            width: '100%',
            ajax: {
                url: '<?= base_url('manufaktur/get_bahan_options') ?>',
                dataType: 'json',
                delay: 250,
                processResults: function (data) { return { results: data.results }; }
            }
        }).on('select2:select', function (e) {
            const data = e.params.data;
            if (data.satuan) {
                $(`#edit-satuan-label-${item.bom_line_id}`).text(data.satuan);
            }
        });
    }

    function saveEditBom(bomId) {
        const data = {
            bom_line_id: bomId,
            bahan_id: $(`#edit-bahan-${bomId}`).val(),
            qty: $(`#edit-qty-${bomId}`).val(),
            scrap_factor: $(`#edit-scrap-${bomId}`).val(),
            calc_type: $(`#edit-calctype-${bomId}`).val(),
            keterangan: $(`#edit-ket-${bomId}`).val()
        };

        $.ajax({
            url: '<?= base_url('manufaktur/update_bom') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    loadBomData($('#bomProductIdInput').val());
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    }



    function initializeBahanSelect2() {
        if ($('#bahan_id').hasClass('select2-hidden-accessible')) {
            $('#bahan_id').select2('destroy');
        }

        // Badge color map matching the warehouse badge system
        const jenisBadge = {
            'bahan_baku': { label: 'Bahan Baku', color: '#166534', bg: '#dcfce7' },
            'bahan_operasional': { label: 'Bahan Operasional', color: '#854d0e', bg: '#fef3c7' },
            'barang_jadi': { label: 'Barang Jadi', color: '#1e40af', bg: '#dbeafe' },
            'spare_part': { label: 'Spare Part', color: '#6d28d9', bg: '#ede9fe' },
            'operasional': { label: 'Operasional', color: '#374151', bg: '#f3f4f6' },
        };

        function formatBahanOption(item) {
            if (!item.id) return item.text;
            const j = jenisBadge[item.jenis_barang] || { label: item.jenis_barang, color: '#64748b', bg: '#f1f5f9' };
            return $(`
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span class="fw-600 text-dark">${item.text.split(' [')[0]}</span>
                    <span style="font-size:0.75rem;font-weight:800;padding:2px 10px;border-radius:20px;
                        background:${j.bg};color:${j.color};text-transform:uppercase;letter-spacing:0.5px;">
                        ${j.label}
                    </span>
                </div>
            `);
        }

        $('#bahan_id').select2({
            placeholder: '-- Cari atau klik untuk pilih bahan --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modalBom'),
            templateResult: formatBahanOption,
            ajax: {
                url: '<?= base_url('manufaktur/get_bahan_options') ?>',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { term: params.term || '' };
                },
                processResults: function (data) {
                    return { results: data.results || [] };
                },
                cache: true
            },
            minimumInputLength: 0
        });

        // When bahan is selected — auto-fill satuan from response (no second AJAX needed)
        $('#bahan_id').off('select2:select').on('select2:select', function (e) {
            const data = e.params.data;
            if (data.satuan) {
                // Show satuan info next to qty field
                let $satuanLabel = $('#bom_satuan_label');
                if (!$satuanLabel.length) {
                    $satuanLabel = $('<span id="bom_satuan_label" class="input-group-text fw-bold text-primary"></span>');
                    $('#qty').closest('.col-md-2').find('.input-group').length
                        ? $('#qty').closest('.input-group').append($satuanLabel)
                        : $('#qty').wrap('<div class="input-group"></div>').parent().append($satuanLabel);
                }
                $satuanLabel.text(data.satuan);
            }
        });

        $('#bahan_id').off('select2:clear').on('select2:clear', function () {
            $('#bom_satuan_label').text('');
        });
    }

    function getBahanUnit(bahanId) {
        $.ajax({
            url: '<?= base_url('manufaktur/get_bahan_unit') ?>',
            type: 'POST',
            data: { bahan_id: bahanId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#satuan').val(response.data.satuan);
                }
            }
        });
    }

    // Handle BOM form submission
    $('#addBomForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('manufaktur/add_bom') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Reload BOM data
                    loadBomData($('#bomProductIdInput').val());

                    // Reset form
                    $('#addBomForm')[0].reset();
                    $('#bahan_id').val(null).trigger('change');
                    $('#satuan').val('');

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem'
                });
            }
        });
    });

    // Handle Routing form submission
    $('#addRoutingForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);



        $.ajax({
            url: '<?= base_url('manufaktur/add_routing') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Reload routing data
                    loadRoutingData($('#routingProductIdInput').val());

                    // Reset form and Select2 dropdowns
                    $('#addRoutingForm')[0].reset();
                    $('#bom_line_id').val(null).trigger('change');
                    $('#operator_id').val(null).trigger('change');

                    // Re-initialize Select2 to ensure proper functionality
                    setTimeout(function () {
                        initializeMesinSelect2();
                    }, 100);
                }
            },
            error: function () {
                // Silent error handling
            }
        });
    });



    function deleteBom(bomId) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus BOM ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('manufaktur/delete_bom') ?>',
                    type: 'POST',
                    data: { bom_id: bomId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Reload BOM data
                            loadBomData($('#bomProductIdInput').val());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan sistem'
                        });
                    }
                });
            }
        });
    }

    // ==================== ROUTING FUNCTIONS ====================

    function openRoutingModal(productId, productName) {
        // Set product info
        $('#routingProductId').text(productId);
        $('#routingProductName').text(productName);
        $('#routingProductNameHeader').text(productName);
        $('#routingProductIdInput').val(productId);

        // Load routing data
        loadRoutingData(productId);

        // Initialize mesin select2
        initializeMesinSelect2();

        // Show modal
        $('#modalRouting').modal('show');
    }

    function loadRoutingData(productId) {
        $.ajax({
            url: '<?= base_url('manufaktur/get_routing_data') ?>',
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    window.currentRoutingVariations = response.variations || [];
                    displayRoutingData(response.data);
                    updateRoutingStats(response.data);

                    let varSelect = $('#routingVariasiSelect');
                    if (varSelect.hasClass('select2-hidden-accessible')) {
                        varSelect.select2('destroy');
                    }
                    varSelect.empty();
                    if (response.variations && response.variations.length > 0) {
                        response.variations.forEach(function(v) {
                            varSelect.append(`<option value="${v.harga_id}">${v.display_name}</option>`);
                        });
                    }
                    varSelect.select2({
                        dropdownParent: $('#modalRouting'),
                        width: '100%',
                        allowClear: true
                    });
                } else {
                    window.currentRoutingVariations = [];
                    $('#routingTableBody').html('<tr><td colspan="6" class="text-center text-muted">Tidak ada data routing</td></tr>');
                    updateRoutingStats([]);
                }
            },
            error: function () {
                window.currentRoutingVariations = [];
                $('#routingTableBody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
                updateRoutingStats([]);
            }
        });
    }

    function displayRoutingData(routingData) {
        let html = '';

        if (routingData.length === 0) {
            html = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data routing</td></tr>';
        } else {
            routingData.forEach((item, index) => {
                const laborCost = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.labor_cost || 0);
                html += `
                <tr>
                    <td class="text-center fw-bold"><span class="badge bg-secondary rounded-pill px-3">${item.routing_urutan || (index + 1) * 10}</span></td>
                    <td>
                        <div class="fw-bold">${item.routing_name || '-'}</div>
                        <small class="text-muted d-block">${item.bom_material_name ? '<i class="bi bi-link-45deg"></i> ' + item.bom_material_name : ''}</small>
                    </td>
                    <td><span class="badge bg-light text-dark border"><i class="bi bi-person-workspace me-1"></i>${item.operator_name || '-'}</span></td>
                    <td class="text-center fw-semibold text-primary">${item.est_time || 0} Min</td>
                    <td class="text-end fw-bold text-success">${laborCost}</td>
                    <td class="text-center" style="white-space: nowrap;">
                        <button class="btn btn-sm btn-outline-warning me-1" onclick='openEditRoutingModalByItem(${JSON.stringify(item).replace(/'/g, "&#39;")})' title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteRouting(${item.routing_id})" title="Hapus">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </td>
                </tr>
            `;
            });
        }

        $('#routingTableBody').html(html);
    }

    function openEditRoutingModalByItem(item) {
        $('#edit_routing_id').val(item.routing_id);
        
        let varSelect = $('#edit_harga_id_routing');
        varSelect.empty();
        varSelect.append('<option value="">-- Semua Variasi (Global) --</option>');
        if (window.currentRoutingVariations && window.currentRoutingVariations.length > 0) {
            window.currentRoutingVariations.forEach(function(v) {
                varSelect.append(`<option value="${v.harga_id}">${v.display_name}</option>`);
            });
        }
        $('#edit_harga_id_routing').val(item.harga_id || '');
        
        $('#edit_routing_urutan').val(item.routing_urutan);
        $('#edit_routing_name').val(item.routing_name);
        $('#edit_est_time').val(item.est_time);
        $('#edit_labor_cost').val(new Intl.NumberFormat('id-ID').format(item.labor_cost || 0).replace(/,/g, '.'));

        // Load & Set Operator (Normal Select)
        if ($('#edit_operator_id').hasClass('select2-hidden-accessible')) {
            $('#edit_operator_id').select2('destroy');
        }
        
        // Disable temporarily while loading
        $('#edit_operator_id').prop('disabled', true);
        $.ajax({
            url: '<?= base_url('manufaktur/get_operator_options') ?>',
            dataType: 'json',
            data: { term: '' },
            success: function(res) {
                let $sel = $('#edit_operator_id');
                $sel.empty();
                $sel.append('<option value="">-- Pilih --</option>');
                let data = res.results || res || [];
                data.forEach(d => {
                    $sel.append(`<option value="${d.id}">${d.text}</option>`);
                });
                if (item.operator_id) {
                    $sel.val(item.operator_id);
                }
                $sel.prop('disabled', false);
            }
        });

        // Load & Set BOM Select (Normal Select)
        if ($('#edit_bom_line_id_routing').hasClass('select2-hidden-accessible')) {
            $('#edit_bom_line_id_routing').select2('destroy');
        }
        
        $('#edit_bom_line_id_routing').prop('disabled', true);
        $.ajax({
            url: '<?= base_url('manufaktur/get_bom_options_by_product/') ?>' + $('#routingProductIdInput').val(),
            dataType: 'json',
            data: { term: '' },
            success: function(res) {
                let $sel = $('#edit_bom_line_id_routing');
                $sel.empty();
                $sel.append('<option value="">-- Tanpa Material --</option>');
                let data = res.results || res || [];
                data.forEach(d => {
                    $sel.append(`<option value="${d.id}">${d.text}</option>`);
                });
                const bomId = item.bom_line_id || '99999';
                $sel.val(bomId);
                $sel.prop('disabled', false);
            }
        });

        $('#modalEditRouting').modal('show');
    }

    $(document).on('submit', '#editRoutingForm', function (e) {
        e.preventDefault();
        const data = $(this).serializeArray();
        const lb = data.find(i => i.name === 'labor_cost');
        if (lb) lb.value = lb.value.replace(/[^,\d]/g, '').replace(',', '.');
        $.post('<?= base_url('manufaktur/update_routing') ?>', data, () => {
            $('#modalEditRouting').modal('hide');
            loadRoutingData($('#routingProductIdInput').val());
        }, 'json');
    });

    function updateRoutingStats(routingData) {
        const totalSteps = routingData.length;

        $('#routingTotalSteps').text(totalSteps);

    }

    function initializeMesinSelect2() {
        // Destroy existing Select2 instances to prevent conflicts
        if ($('#bom_line_id').hasClass('select2-hidden-accessible')) {
            $('#bom_line_id').select2('destroy');
        }
        if ($('#operator_id').hasClass('select2-hidden-accessible')) {
            $('#operator_id').select2('destroy');
        }

        // Initialize BOM Select2
        $('#bom_line_id').select2({
            placeholder: '-- Pilih BOM --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modalRouting'),
            ajax: {
                url: '<?= base_url('manufaktur/get_bom_options') ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    const productId = $('#routingProductIdInput').val();
                    return {
                        term: params.term || '',
                        product_id: productId
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results || data || [],
                        pagination: {
                            more: false
                        }
                    };
                },
                error: function (xhr, status, error) {
                    // Error handling for BOM AJAX
                },
                cache: true
            },
            minimumInputLength: 0
        });

        // Initialize Operator Select2
        $('#operator_id').select2({
            placeholder: '-- Pilih Operator --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modalRouting'),
            minimumInputLength: 0, // Allow empty search to show all results
            ajax: {
                url: '<?= base_url('manufaktur/get_operator_options') ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { term: params.term || '' };
                },
                processResults: function (data) {
                    return {
                        results: data.results || data || [],
                        pagination: {
                            more: false
                        }
                    };
                },
                error: function (xhr, status, error) {
                    // Error handling for Operator AJAX
                },
                cache: true
            }
        });

        // Select2 initialization completed
    }

    function deleteRouting(routingId) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus routing ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('manufaktur/delete_routing') ?>',
                    type: 'POST',
                    data: { routing_id: routingId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Reload routing data
                            loadRoutingData($('#routingProductIdInput').val());
                        }
                    },
                    error: function () {
                        // Silent error handling
                    }
                });
            }
        });
    }


</script>

<script src="<?= base_url('assets/select2/js/select2.full.min.js') ?>"></script>

<script>
    $(document).ready(function () {
        // Select2 AJAX untuk bahan baku produksi
        $('.select2-bahan-ajax').select2({
            placeholder: "-- Cari & Pilih Bahan Baku --",
            allowClear: true,
            width: '100%',
            ajax: {
                url: '<?= base_url('manufaktur/get_bahan_options') ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { term: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        // Initialize Select2 when routing modal is shown
        $('#modalRouting').on('shown.bs.modal', function () {
            setTimeout(function () {
                initializeMesinSelect2();
            }, 100);
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Pastikan Select2 sudah dimuat
        if (typeof $.fn.select2 === 'undefined') {
            return;
        }

        // Initialize Select2 dengan delay untuk memastikan DOM sudah siap
        setTimeout(function () {
            $('#bahan_id').select2({
                placeholder: "-- Cari & Pilih Bahan Baku --",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalInputData'), // Pastikan dropdown muncul di dalam modal
                ajax: {
                    url: '<?= base_url("product/getBahanAjax") ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data || [],
                            pagination: {
                                more: false
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });

            // Saat bahan dipilih, isi product_nama dan bahan_deskripsi otomatis
            $('#bahan_id').on('select2:select', function (e) {
                const namaBahan = e.params.data.text;
                const bahanDeskripsi = e.params.data.deskripsi || '';

                // Isi nama produk otomatis jika kosong
                if (!$('input[name="product_nama"]').val()) {
                    $('input[name="product_nama"]').val(namaBahan);
                }

                // Isi deskripsi bahan
                $('input[name="bahan_deskripsi"]').val(bahanDeskripsi);
            });

            // Jika dibersihkan, kosongkan field terkait
            $('#bahan_id').on('select2:clear', function () {
                $('input[name="bahan_deskripsi"]').val('');
            });
        }, 100);
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
    $(document).ready(function () {
        // Re-initialize Select2 saat modal dibuka
        $('#modalInputData').on('shown.bs.modal', function () {
            setTimeout(function () {
                if ($('#bahan_id').length && typeof $.fn.select2 !== 'undefined') {
                    // Destroy existing Select2 jika ada
                    if ($('#bahan_id').hasClass('select2-hidden-accessible')) {
                        $('#bahan_id').select2('destroy');
                    }

                    // Re-initialize Select2
                    $('#bahan_id').select2({
                        placeholder: "-- Cari & Pilih Bahan Baku --",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#modalInputData'),
                        ajax: {
                            url: '<?= base_url("product/getBahanAjax") ?>',
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    search: params.term,
                                    page: params.page || 1
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data || [],
                                    pagination: {
                                        more: false
                                    }
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 1
                    });

                    // Event handler untuk select bahan
                    $('#bahan_id').off('select2:select select2:clear').on('select2:select', function (e) {
                        const namaBahan = e.params.data.text;
                        const bahanDeskripsi = e.params.data.deskripsi || '';

                        // Isi nama produk otomatis jika kosong
                        if (!$('input[name="product_nama"]').val()) {
                            $('input[name="product_nama"]').val(namaBahan);
                        }

                        // Isi deskripsi bahan
                        $('input[name="bahan_deskripsi"]').val(bahanDeskripsi);
                    });

                    $('#bahan_id').on('select2:clear', function () {
                        $('input[name="bahan_deskripsi"]').val('');
                    });
                }
            }, 200);
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Handle product delete confirmation
        $('.btn-delete-product').click(function (e) {
            e.preventDefault();

            const href = $(this).data('href');
            const productName = $(this).data('product-name');

            Swal.fire({
                title: 'Konfirmasi Hapus Produk',
                html: `
                <div class="text-start">
                    <p class="mb-3">Anda yakin ingin menghapus produk ini?</p>
                    <div class="alert alert-warning">
                        <strong>Produk:</strong> ${productName}
                    </div>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Semua data terkait akan dihapus:
                        <ul class="mb-0 mt-2">
                            <li>Data harga produk</li>
                            <li>Bill of Materials (BOM)</li>
                            <li>Data routing produksi</li>
                            <li>Data transaksi terkait</li>
                        </ul>
                    </div>
                </div>
            `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Produk!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Menghapus Produk...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect to delete URL
                    window.location.href = href;
                }
            });
        });
    });

    function openEditProductModal(productId) {
        Swal.fire({
            title: 'Memuat...',
            text: 'Sedang mengambil data produk',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: "<?= site_url('manufaktur/get_product_detail/') ?>" + productId,
            method: "GET",
            dataType: "json",
            success: function (res) {
                Swal.close();
                if (res.status === 'success') {
                    const d = res.data;
                    $('#edit_product_id').val(d.product_id);
                    $('#edit_product_nama').val(d.product_nama);
                    $('#edit_product_name_title').text(d.product_nama);
                    $('#edit_kategori_id').val(d.kategori_id);
                    $('#edit_tipe_id').val(d.tipe_id);
                    $('#edit_satuan_id').val(d.satuan_id);
                    $('#edit_side_id').val(d.side_id);
                    $('#edit_metode_id').val(d.detail_id);
                    $('#edit_product_deskripsi').val(d.product_deskripsi);

                    // Load subkategori
                    loadSubkategori(d.kategori_id, '#edit_subkategori_id', d.subkategori_id);

                    $('#modalEditProduct').modal('show');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function () {
                Swal.close();
                Swal.fire('Error', 'Gagal menghubungi server', 'error');
            }
        });
    }

    function loadSubkategori(kategoriId, targetSelector, selectedId = '') {
        if (!kategoriId) {
            $(targetSelector).html('<option value="">-- Pilih Sub Kategori --</option>');
            return;
        }

        $(targetSelector).prop('disabled', true).html('<option value="">Memuat...</option>');

        $.ajax({
            url: "<?= site_url('manufaktur/get_subkategori') ?>",
            method: "POST",
            data: { id: kategoriId },
            dataType: "json",
            success: function (data) {
                let options = '<option value="">-- Pilih Sub Kategori --</option>';
                if (data && Array.isArray(data)) {
                    data.forEach(function (item) {
                        const isSelected = (item.subkategori_id == selectedId) ? 'selected' : '';
                        options += `<option value="${item.subkategori_id}" ${isSelected}>${item.subkategori_nama}</option>`;
                    });
                }
                $(targetSelector).prop('disabled', false).html(options);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error: ", error);
                $(targetSelector).prop('disabled', false).html('<option value="">-- Gagal memuat data --</option>');
            }
        });
    }

    $('#edit_kategori_id').on('change', function () {
        loadSubkategori($(this).val(), '#edit_subkategori_id');
    });

    $('#kategori_id').on('change', function () {
        loadSubkategori($(this).val(), '.subkategori');
    });

    $('#editProductForm').on('submit', function (e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            this.classList.add('was-validated');
            return;
        }

        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: "<?= site_url('manufaktur/update_product_ajax') ?>",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (res) {
                submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Perbarui Produk');
                if (res.status === 'success') {
                    $('#modalEditProduct').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function () {
                submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Perbarui Produk');
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            }
        });
    });
</script>

<script>
    function cloneProduct(productId, productName) {
        Swal.fire({
            title: 'Clone Master Data',
            html: `
            <div class="text-start">
                <p>Duplikasi BOM, Routing, dan Skala Harga dari:</p>
                <div class="alert alert-info py-2"><strong>${productName}</strong></div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Produk Baru</label>
                    <input type="text" id="new_product_name" class="form-control" value="${productName} - Copy">
                </div>
            </div>
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            confirmButtonText: '<i class="bi bi-files me-2"></i>Mulai Duplikasi',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const newName = Swal.getPopup().querySelector('#new_product_name').value;
                if (!newName) {
                    Swal.showValidationMessage(`Nama produk baru tidak boleh kosong`);
                }
                return { newName: newName };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang Menyalin...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: '<?= base_url("manufaktur/clone_product") ?>',
                    type: 'POST',
                    data: {
                        source_id: productId,
                        new_name: result.value.newName
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Produk berhasil diduplikasi.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Duplikasi',
                                text: response.message || 'Terjadi kesalahan sistem'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Koneksi server terputus'
                        });
                    }
                });
            }
        });
    }
</script>

<script>
    $(document).ready(function () {
        // Event handler untuk modal routing
        $('#modalRouting').on('shown.bs.modal', function () {
            // Pastikan product ID sudah diset
            const productId = $('#routingProductIdInput').val();

            // Initialize Select2 dengan delay
            setTimeout(function () {
                if (typeof $.fn.select2 !== 'undefined') {
                    initializeMesinSelect2();
                }

                // Initialize Bootstrap Tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }, 300);
        });

        // Event handler untuk form routing
        $('#addRoutingForm').on('submit', function (e) {
            e.preventDefault();

            const formData = {
                product_id: $('#routingProductIdInput').val(),
                routing_urutan: $('#routing_urutan').val(),
                routing_name: $('#routing_name').val(),
                bom_line_id: $('#bom_line_id').val(),
                operator_id: $('#operator_id').val(),
                est_time: $('input[name="est_time"]').val(),
                labor_cost: $('input[name="labor_cost"]').val()
            };

            // Validasi
            if (!formData.routing_urutan || !formData.routing_name || !formData.bom_line_id || !formData.operator_id) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap',
                    text: 'Mohon isi semua field yang bertanda bintang (*)'
                });
                return;
            }

            // Submit form
            $.ajax({
                url: '<?= base_url("manufaktur/add_routing") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Reset form
                        $('#addRoutingForm')[0].reset();

                        // Reload routing data
                        loadRoutingData(formData.product_id);

                        // Clear Select2
                        $('#bom_line_id').val(null).trigger('change');
                        $('#operator_id').val(null).trigger('change');
                    }
                },
                error: function (xhr, status, error) {
                    // Silent error handling
                }
            });
        });

        // ==================== BULK SETUP FUNCTIONS ====================
        function initializeBulkSelect2() {
            // Destroy existing to prevent conflicts
            if ($('#bulk_template_id').hasClass('select2-hidden-accessible')) {
                $('#bulk_template_id').select2('destroy');
            }
            if ($('#bulk_target_ids').hasClass('select2-hidden-accessible')) {
                $('#bulk_target_ids').select2('destroy');
            }

            // Template Search
            $('#bulk_template_id').select2({
                placeholder: '-- Cari Produk --',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalBulkSetup'),
                ajax: {
                    url: '<?= base_url("manufaktur/search_product") ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { term: params.term };
                    },
                    processResults: function (data) {
                        return { results: data.results || [] };
                    }
                },
                minimumInputLength: 0,
                minimumResultsForSearch: 0
            });

            // Targets Search
            $('#bulk_target_ids').select2({
                placeholder: '-- Pilih Produk Target --',
                width: '100%',
                allowClear: true,
                dropdownParent: $('#modalBulkSetup'),
                closeOnSelect: false,
                minimumResultsForSearch: 0
            });
        }

        $('#bulk_target_subkategori').on('change', function () {
            const subId = $(this).val();
            if (!subId) return;

            $.get('<?= base_url("manufaktur/get_products_by_subkategori") ?>', { subkategori_id: subId }, function (data) {
                let options = '';
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.text}</option>`;
                });
                $('#bulk_target_ids').html(options).trigger('change');
            }, 'json');
        });

        // Event handler untuk modal bulk setup
        $('#modalBulkSetup').on('shown.bs.modal', function () {
            initializeBulkSelect2();
        });

        // Auto-focus Select2 search field when opened
        $(document).on('select2:open', function (e) {
            const searchField = document.querySelector('.select2-search__field');
            if (searchField) {
                searchField.focus();
            }
        });

        // Export submit function to global for onclick
        window.submitBulkSetup = function () {
            const form = $('#bulkSetupForm');
            const templateId = $('#bulk_template_id').val();
            const targetIds = $('#bulk_target_ids').val();

            if (!templateId || !targetIds || targetIds.length === 0) {
                Swal.fire('Peringatan', 'Silakan pilih Template dan Minimal 1 Produk Target', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Bulk Setup',
                text: "Data BOM/Routing pada produk target akan digantikan. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Terapkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.post('<?= base_url("manufaktur/bulk_apply_template") ?>', form.serialize(), function (res) {
                        if (res.success) {
                            Swal.fire('Berhasil', res.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    }, 'json');
                }
            });
        };

        window.bulkSelectAllTargets = function () {
            $('#bulk_target_ids option').prop('selected', true).trigger('change');
        };

        window.bulkClearTargets = function () {
            $('#bulk_target_ids').val(null).trigger('change');
        };

        // ==================== BULK PRICE UPDATE ====================
        $(document).ready(function () {
            $('.select2-bulk-price').select2({
                dropdownParent: $('#modalBulkPriceUpdate'),
                width: '100%'
            });

            $('input[name="adj_method"]').on('change', function () {
                const method = $(this).val();
                $('#adj_symbol').text(method === 'percent' ? '%' : 'Rp');
            });
        });

        window.submitBulkPriceUpdate = function () {
            const form = $('#bulkPriceForm');
            const data = form.serialize();
            const adjValue = form.find('input[name="adj_value"]').val();
            const targets = form.find('input[name="targets[]"]:checked').length;

            if (!adjValue) {
                return Swal.fire('Error', 'Harap masukkan nilai kenaikan!', 'warning');
            }

            if (targets === 0) {
                return Swal.fire('Error', 'Harap pilih target perubahan (Harga Jual/HPP)!', 'warning');
            }

            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: "Apakah Anda yakin ingin memperbarui harga untuk kategori terpilih? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading() }
                    });

                    $.ajax({
                        url: "<?= base_url('manufaktur/bulk_price_adjustment_ajax') ?>",
                        type: "POST",
                        data: data,
                        dataType: "json",
                        success: function (res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message,
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Terjadi kesalahan sistem saat menghubungi server', 'error');
                        }
                    });
                }
            });
        };

        // ==================== PRICING FUNCTIONS ====================
        window.lastCalculatedHPP = 0;

        window.openPricingModal = function (productId, productName) {
            $('#pricingProductName').text(productName);
            $('#pricingProductIdInput').val(productId);

            // Initial Loading State
            $('#pricingModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Memuat matriks harga...</p></div>');
            $('#modalPricing').modal('show');

            // Load Calculate HPP from backend for the "Magic Button"
            $.get('<?= base_url('manufaktur/get_calculated_hpp_ajax/') ?>' + productId, function (res) {
                if (res.success) {
                    window.lastCalculatedHPP = res.hpp;
                }
            }, 'json');

            // Load HTML Partial
            $.get('<?= base_url('manufaktur/get_pricing_partial/') ?>' + productId, function (html) {
                $('#pricingModalBody').html(html);

                // Override Form Action for AJAX inside modal
                $('#formSavePrices').on('submit', function (e) {
                    e.preventDefault();
                    savePricingMatrix($(this));
                });
            });
        }

        window.savePricingMatrix = function (form) {
            const btn = form.find('button[type="submit"]');
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function (res) {
                    btn.prop('disabled', false).html(originalHtml);
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function () {
                    btn.prop('disabled', false).html(originalHtml);
                    Swal.fire('Error', 'Terjadi kesalahan sistem saat menyimpan harga', 'error');
                }
            });
        }

        // ==================== FORM BUILDER MODAL ====================
        window.openFormBuilderModal = function (productId, productName) {
            $('#formBuilderProductNameHeader').text(productName);
            $('#iframeFormBuilder').attr('src', '<?= base_url('pengaturan/product_form_builder/') ?>' + productId + '/modal');
            $('#modalFormBuilder').modal('show');
        }

        $('#modalFormBuilder').on('hidden.bs.modal', function () {
            $('#iframeFormBuilder').attr('src', '');
            // Optional: Reload window to update badge color if user saved schema
            // window.location.reload(); 
        });

        // ==================== MASS DELETE LOGIC ====================
        const checkAll = $('#checkAll');
        const checkItems = $('.checkItem');
        const btnDeleteSelected = $('#btnDeleteSelected');
        const selectedCount = $('#selectedCount');

        function updateDeleteButton() {
            const count = $('.checkItem:checked').length;
            selectedCount.text(count);
            if (count > 0) {
                btnDeleteSelected.fadeIn(200);
            } else {
                btnDeleteSelected.fadeOut(200);
            }
        }

        checkAll.on('change', function() {
            checkItems.prop('checked', $(this).prop('checked'));
            updateDeleteButton();
        });

        checkItems.on('change', function() {
            if (!$(this).prop('checked')) {
                checkAll.prop('checked', false);
            } else if ($('.checkItem:checked').length === checkItems.length) {
                checkAll.prop('checked', true);
            }
            updateDeleteButton();
        });

        btnDeleteSelected.on('click', function() {
            const selectedIds = [];
            $('.checkItem:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            Swal.fire({
                title: 'Hapus ' + selectedIds.length + ' Produk?',
                text: "Anda yakin ingin menghapus produk yang dipilih beserta semua datanya?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus Data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '<?= base_url('manufaktur/delete_bulk_product') ?>',
                        type: 'POST',
                        data: { ids: selectedIds },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                window.location.reload();
                            } else {
                                Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Gagal terhubung ke server', 'error');
                        }
                    });
                }
            });
        });

    });
</script>

