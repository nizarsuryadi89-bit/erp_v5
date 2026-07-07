/**
 * Transaksi Deskprint JavaScript Functions
 * Handles filtering, interactions, and UI enhancements
 */

// Filter functionality for transaction status
document.addEventListener('DOMContentLoaded', function() {
    const filterAll = document.getElementById('filter-all');
    const filterSudah = document.getElementById('filter-sudah');
    const filterBelum = document.getElementById('filter-belum');
    const rows = document.querySelectorAll('#datatable-details tbody tr');
    
    // Set active button function
    function setActive(btn) {
        [filterAll, filterSudah, filterBelum].forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }
    
    // Filter all transactions
    if (filterAll) {
        filterAll.addEventListener('click', function() {
            setActive(this);
            rows.forEach(row => {
                row.style.display = '';
                row.classList.remove('filtered-out');
            });
            updateFilterCount();
        });
    }
    
    // Filter paid transactions
    if (filterSudah) {
        filterSudah.addEventListener('click', function() {
            setActive(this);
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                if (status === 'sudah') {
                    row.style.display = '';
                    row.classList.remove('filtered-out');
                } else {
                    row.style.display = 'none';
                    row.classList.add('filtered-out');
                }
            });
            updateFilterCount();
        });
    }
    
    // Filter unpaid transactions
    if (filterBelum) {
        filterBelum.addEventListener('click', function() {
            setActive(this);
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                if (status === 'belum') {
                    row.style.display = '';
                    row.classList.remove('filtered-out');
                } else {
                    row.style.display = 'none';
                    row.classList.add('filtered-out');
                }
            });
            updateFilterCount();
        });
    }
    
    // Update filter count display
    function updateFilterCount() {
        const visibleRows = document.querySelectorAll('#datatable-details tbody tr:not([style*="display: none"])');
        const totalRows = rows.length;
        
        // Update button text with counts
        if (filterAll) {
            filterAll.textContent = `Semua (${totalRows})`;
        }
        if (filterSudah) {
            const sudahCount = document.querySelectorAll('#datatable-details tbody tr[data-status="sudah"]').length;
            filterSudah.textContent = `Sudah Bayar (${sudahCount})`;
        }
        if (filterBelum) {
            const belumCount = document.querySelectorAll('#datatable-details tbody tr[data-status="belum"]').length;
            filterBelum.textContent = `Belum Bayar (${belumCount})`;
        }
    }
    
    // Initialize filter counts
    updateFilterCount();
});

// Enhanced table interactions
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('datatable-details');
    if (!table) return;
    
    // Add row hover effects
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.001)';
            this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Add click effects for buttons
    const buttons = table.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// Search functionality
function addSearchFunctionality() {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control mb-3';
    searchInput.placeholder = 'Cari transaksi...';
    searchInput.id = 'search-transaksi';
    
    const table = document.getElementById('datatable-details');
    if (table) {
        table.parentNode.insertBefore(searchInput, table);
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
}

// Export functionality
function exportTableData(format = 'csv') {
    const table = document.getElementById('datatable-details');
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    let data = [];
    
    // Get headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        if (!th.hidden) {
            headers.push(th.textContent.trim());
        }
    });
    data.push(headers);
    
    // Get visible rows data
    rows.forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach((td, index) => {
            if (!td.hidden) {
                rowData.push(td.textContent.trim());
            }
        });
        data.push(rowData);
    });
    
    if (format === 'csv') {
        downloadCSV(data, 'transaksi_deskprint.csv');
    } else if (format === 'excel') {
        downloadExcel(data, 'transaksi_deskprint.xlsx');
    }
}

// Download CSV
function downloadCSV(data, filename) {
    const csvContent = data.map(row => 
        row.map(cell => `"${cell}"`).join(',')
    ).join('\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
}

// Download Excel (requires SheetJS library)
function downloadExcel(data, filename) {
    if (typeof XLSX === 'undefined') {
        alert('SheetJS library not loaded. Falling back to CSV export.');
        downloadCSV(data, filename.replace('.xlsx', '.csv'));
        return;
    }
    
    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Transaksi');
    XLSX.writeFile(wb, filename);
}

// Print functionality
function printTable() {
    const table = document.getElementById('datatable-details');
    if (!table) return;
    
    const printWindow = window.open('', '_blank');
    const visibleRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    
    let printContent = `
        <html>
        <head>
            <title>Transaksi Deskprint</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .text-center { text-align: center; }
                .text-end { text-align: right; }
                @media print {
                    body { margin: 0; }
                    table { font-size: 12px; }
                }
            </style>
        </head>
        <body>
            <h2>Laporan Transaksi Deskprint</h2>
            <p>Tanggal: ${new Date().toLocaleDateString('id-ID')}</p>
            <table>
                <thead>
                    ${table.querySelector('thead').innerHTML}
                </thead>
                <tbody>
    `;
    
    visibleRows.forEach(row => {
        printContent += row.outerHTML;
    });
    
    printContent += `
                </tbody>
            </table>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

// Add export and print buttons
function addExportButtons() {
    const buttonContainer = document.querySelector('.btn-group');
    if (!buttonContainer) return;
    
    const exportDiv = document.createElement('div');
    exportDiv.className = 'btn-group ms-2';
    exportDiv.innerHTML = `
        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-download me-1"></i>Export
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="exportTableData('csv')">Export CSV</a></li>
            <li><a class="dropdown-item" href="#" onclick="exportTableData('excel')">Export Excel</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" onclick="printTable()">Print</a></li>
        </ul>
    `;
    
    buttonContainer.parentNode.appendChild(exportDiv);
}

// Initialize all functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add search functionality
    addSearchFunctionality();
    
    // Add export buttons
    addExportButtons();
    
    // Add loading states
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.href && !this.href.includes('#')) {
                this.classList.add('loading');
                this.disabled = true;
            }
        });
    });
});

// Utility functions
const TransaksiDeskprint = {
    // Filter functions
    filterAll: function() {
        document.getElementById('filter-all')?.click();
    },
    
    filterSudah: function() {
        document.getElementById('filter-sudah')?.click();
    },
    
    filterBelum: function() {
        document.getElementById('filter-belum')?.click();
    },
    
    // Export functions
    exportCSV: function() {
        exportTableData('csv');
    },
    
    exportExcel: function() {
        exportTableData('excel');
    },
    
    // Print function
    print: function() {
        printTable();
    },
    
    // Search function
    search: function(term) {
        const searchInput = document.getElementById('search-transaksi');
        if (searchInput) {
            searchInput.value = term;
            searchInput.dispatchEvent(new Event('input'));
        }
    }
};

// Export for global use
window.TransaksiDeskprint = TransaksiDeskprint;
