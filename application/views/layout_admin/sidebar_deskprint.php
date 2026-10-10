<div class="inner-wrapper">
    <!-- start: sidebar -->
    <aside id="sidebar-left" class="sidebar-left">

        <div class="sidebar-header">
            <div class="sidebar-title">
                <?= $level['role_nama'] ?>
            </div>
            <div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html"
                data-fire-event="sidebar-left-toggle">
                <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
            </div>
        </div>

        <div class="nano">
            <div class="nano-content">
                <nav id="menu" class="nav-main" role="navigation">
                    <ul class="nav nav-main">
                        <li>
                            <a href="<?= base_url('deskprint') ?>">
                                <i class="fa fa-print" aria-hidden="true"></i>
                                <span>Deskprint Center</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('dashboard') ?>">
                                <i class="fa fa-dashboard" aria-hidden="true"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <?php
                        if ($this->session->userdata('level') == '1') {
                            foreach ($menu as $mn) {
                                ?>
                                <li>
                                    <a href="<?= base_url($mn->link) ?>">
                                        <i class="<?= $mn->icon ?>"></i>
                                        <span><?= $mn->alias ?></span>
                                    </a>
                                </li>
                            <?php
                            }
                        }
                        ?>


                        <li>
                            <a href="<?= base_url('mobile') ?>">
                                <i class="fa fa-mobile" aria-hidden="true"></i>
                                <span>Mobile</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.tgmprint.com/" target="blank">
                                <i class="fa fa-external-link" aria-hidden="true"></i>
                                <span>Website</span>
                            </a>
                        </li>
                        <hr class="separator" />
                        <li>
                            <a href="<?= base_url('konfigurasi') ?>">
                                <i class="fa fa-power-off" aria-hidden="true"></i>
                                <span>Keluar Aplikasi</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <hr class="separator" />
            </div>

            <script>
                // Maintain Scroll Position
                if (typeof localStorage !== 'undefined') {
                    if (localStorage.getItem('sidebar-left-position') !== null) {
                        var initialPosition = localStorage.getItem('sidebar-left-position'),
                            sidebarLeft = document.querySelector('#sidebar-left .nano-content');

                        sidebarLeft.scrollTop = initialPosition;
                    }
                }
            </script>


        </div>

    </aside>
    <!-- end: sidebar -->
    <section role="main" class="content-body">