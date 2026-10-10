</div>
<!-- /.content-wrapper -->

<!-- begin::Bottom Navigation (Mobile Only) - Ultra Modern Premium -->
<style>
  .bottom-nav {
    position: fixed;
    bottom: 0;
    width: 100%;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.05);
    display: flex;
    justify-content: space-around;
    padding: 10px 0 15px 0;
    z-index: 2000;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    border-radius: 20px 20px 0 0;
  }

  .bottom-nav a {
    text-align: center;
    flex: 1;
    color: #8E8E93; /* iOS Secondary Label Gray */
    font-size: 0.7rem;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: all 0.2s ease;
    font-weight: 600;
    letter-spacing: 0.3px;
  }

  .bottom-nav a.active {
    color: #2563eb; /* TGM Primary Blue */
  }

  .bottom-nav i {
    font-size: 1.5rem;
    margin-bottom: 2px;
    transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }

  .bottom-nav a:active i {
    transform: scale(0.85);
  }

  /* Floating Center Button */
  .nav-center-btn {
    position: relative;
    top: -25px;
    background: #2563eb !important;
    width: 55px;
    height: 55px;
    border-radius: 50% !important;
    color: white !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 8px 15px rgba(37, 99, 235, 0.3);
    border: 4px solid #fff !important;
    margin: 0 -10px;
    flex: 0 0 55px !important;
  }

  .nav-center-btn i {
    font-size: 1.6rem !important;
    margin: 0 !important;
    color: white !important;
  }

  @media (min-width: 768px) {
    .bottom-nav {
      display: none;
    }
  }
</style>

<?php if ($this->session->userdata('username')): ?>
  <?php 
    $current_uri = $this->uri->segment(1) . '/' . $this->uri->segment(2);
  ?>
  <div class="bottom-nav d-md-none">
    <a href="<?= base_url('mobile') ?>" class="<?= ($this->uri->segment(1) == 'mobile' && ($this->uri->segment(2) == 'index' || $this->uri->segment(2) == '')) ? 'active' : '' ?>">
      <i class="bi bi-grid-1x2-fill"></i>
      <span>Beranda</span>
    </a>
    <a href="<?= base_url('mobile/absen') ?>" class="<?= ($current_uri == 'mobile/absen') ? 'active' : '' ?>">
      <i class="bi bi-fingerprint"></i>
      <span>Absensi</span>
    </a>
    <a href="<?= base_url('mobile') ?>" class="nav-center-btn">
      <i class="bi bi-lightning-charge-fill"></i>
    </a>
    <a href="<?= base_url('mobile/laporan') ?>" class="<?= ($current_uri == 'mobile/laporan') ? 'active' : '' ?>">
      <i class="bi bi-clock-history"></i>
      <span>Riwayat</span>
    </a>
    <a href="<?= base_url('mobile/profile') ?>" class="<?= ($current_uri == 'mobile/profile') ? 'active' : '' ?>">
      <i class="bi bi-person-fill"></i>
      <span>Profil</span>
    </a>
  </div>
<?php endif; ?>
<!-- end::Bottom Navigation -->

<!-- Scripts -->


<script src="<?= base_url('assets/js/adminlte.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script
  src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- OverlayScrollbars Init -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const sidebarWrapper = document.querySelector('.sidebar-wrapper');
    if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
      OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
        scrollbars: {
          theme: 'os-theme-light',
          autoHide: 'leave',
          clickScroll: true
        }
      });
    }
  });
</script>

<!-- DataTables Init -->
<!-- <script>
  $(document).ready(function () {
    $('#datatable-details').DataTable({
      responsive: true,
      dom: 'Bfrtip',
      buttons: ['copy', 'excel', 'pdf', 'print'],
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        paginate: {
          first: "Pertama",
          last: "Terakhir",
          next: "Berikutnya",
          previous: "Sebelumnya"
        }
      }
    });
  });
</script> -->

<!-- Global Search Modal -->
<?php $this->load->view('layout_admin/v_global_search_modal'); ?>

<!-- Chat Widget -->
<?php if ($this->session->userdata('username')): ?>
  <div class="d-none d-md-block">
    <?php $this->load->view('admin/chat_umum'); ?>
  </div>
<?php endif; ?>

</body>

</html>