<!-- Employee Data Table Minimal -->
<div class="clean-card overflow-hidden">
    <div class="p-3 bg-white border-bottom">
        <h6 class="fw-900 text-dark mb-0 ls-1"><i class="bi bi-people-fill me-2 text-primary-tgm"></i>DATABASE RECORD
            KARYAWAN</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-simple mb-0" id="karyawanTable">
            <thead>
                <tr>
                    <th class="ps-3" width="5%">NO</th>
                    <th width="25%">NAMA KARYAWAN</th>
                    <th width="15%">TENURE</th>
                    <th width="20%">POSISI & LOKASI</th>
                    <th width="15%">KONTAK</th>
                    <th width="10%">STATUS</th>
                    <th class="text-center" width="10%">AKSI</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Data loaded via AJAX -->
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="spinner-border spinner-border-sm text-primary-tgm me-2"></div>
                        <span class="text-primary-tgm fw-bold font-mono">MEMUAT DATA...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="p-3 bg-white border-top no-print">
        <div class="d-flex justify-content-between align-items-center">
            <div class="small fw-900 text-muted text-uppercase ls-1 x-small" id="totalCount">
                MENAMPILKAN 0 KARYAWAN
            </div>
            <nav aria-label="Page navigation" id="paginationWrapper">
                <!-- Pagination built dynamically if needed -->
            </nav>
        </div>
    </div>
</div>