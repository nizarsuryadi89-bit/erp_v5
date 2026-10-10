<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap');

    :root {
        --fin-primary: #0f172a;
        --fin-accent: #3b82f6;
        --fin-success: #10b981;
        --fin-slate: #64748b;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
    }

    .fin-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .perm-menu-row {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 8px;
        overflow: hidden;
    }

    .perm-menu-header {
        background: #f8fafc;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: background 0.15s;
    }

    .perm-menu-header:hover {
        background: #eff6ff;
    }

    .perm-sub-row {
        padding: 8px 16px 8px 44px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid #f1f5f9;
        transition: background 0.15s;
    }

    .perm-sub-row:hover {
        background: #f8fafc;
    }

    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.3em;
        cursor: pointer;
    }
</style>

<div class="app-content mt-n4 pb-5">
    <div class="container-fluid">
        <div class="row align-items-center mb-4 pt-4">
            <div class="col-md-8">
                <a href="<?= base_url('pengaturan/user_account') ?>" class="btn btn-sm btn-light mb-2 fw-bold text-muted rounded-pill shadow-sm"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                <h3 class="fw-900 text-dark mb-1">
                    Hak Akses Spesifik: <?= htmlspecialchars($target_user['username']) ?>
                </h3>
                <p class="text-muted fw-bold mb-0">
                    Bypass Role: Atur hak akses modul secara spesifik untuk akun ini.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="badge bg-primary text-white p-2 fw-bold rounded-pill shadow-sm"><i class="fas fa-user-shield me-1"></i> Role Asli: <?= $target_user['role_id'] ?></span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="fin-card">
                    <div class="p-4 border-bottom">
                        <h5 class="fw-800 text-dark mb-1"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Daftar Modul Aplikasi</h5>
                        <p class="text-muted small mb-0">Perubahan disimpan otomatis dan langsung berlaku tanpa reload.</p>
                    </div>
                    
                    <div class="p-4">
                        <?php foreach($menus as $m): ?>
                            <?php 
                                $is_granted_menu = in_array($m->id, $user_menus_ids);
                                $my_subs = array_filter($submenus, function($s) use ($m) { return $s->menu_id == $m->id; });
                            ?>
                            <div class="perm-menu-row">
                                <div class="perm-menu-header" onclick="$('#sub-wrap-<?= $m->id ?>').slideToggle(200)">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="form-check form-switch m-0" onclick="event.stopPropagation();">
                                            <input class="form-check-input menu-toggle-chk" type="checkbox" 
                                                   data-menu-id="<?= $m->id ?>" 
                                                   <?= $is_granted_menu ? 'checked' : '' ?>>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                <i class="bi <?= $m->icon_fa ?: 'bi-circle' ?> me-2 text-primary"></i> 
                                                <?= $m->nama_menu ?>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><?= empty($m->link) ? count($my_subs).' Submenu' : 'Direct Link: '.$m->link ?></div>
                                        </div>
                                    </div>
                                    <i class="bi bi-chevron-down text-muted"></i>
                                </div>
                                
                                <div id="sub-wrap-<?= $m->id ?>" style="display: <?= $is_granted_menu ? 'block' : 'none' ?>;">
                                    <?php foreach($my_subs as $sub): ?>
                                        <?php $is_granted_sub = in_array($sub->id, $user_submenus_ids); ?>
                                        <div class="perm-sub-row">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-arrow-return-right text-muted me-2" style="font-size:0.8rem"></i>
                                                <div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;"><?= $sub->nama_submenu ?></div>
                                                    <div class="text-muted extra-small font-monospace"><?= $sub->link_submenu ?></div>
                                                </div>
                                            </div>
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input sub-toggle-chk" type="checkbox" 
                                                       data-submenu-id="<?= $sub->id ?>" 
                                                       data-menu-id="<?= $m->id ?>"
                                                       <?= $is_granted_sub ? 'checked' : '' ?>>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const _MENU_TOGGLE = '<?= base_url("pengaturan/ajax_toggle_user_menu_access") ?>';
    const _SUB_TOGGLE = '<?= base_url("pengaturan/ajax_toggle_user_submenu_access") ?>';
    const _CSRF_NAME = '<?= $this->security->get_csrf_token_name() ?>';
    const _CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
    const _USER_ID = <?= $target_user['user_id'] ?>;

    // Toast function
    function showPT(msg, type) {
        Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
            timerProgressBar: true
        }).fire({ icon: type, title: msg });
    }

    $(document).ready(function () {
        // Toggle Menu
        $(document).on('change', '.menu-toggle-chk', function () {
            const menuId = parseInt($(this).data('menu-id'));
            const is_checked = this.checked ? 1 : 0;
            const $el = $(this);
            const payload = { user_id: _USER_ID, menu_id: menuId, is_checked: is_checked };
            payload[_CSRF_NAME] = _CSRF_HASH;
            
            $.post(_MENU_TOGGLE, payload, function (res) {
                if (res.status === 'success') {
                    if (is_checked) { 
                        $('#sub-wrap-' + menuId).slideDown(200); 
                        showPT('Akses menu diberikan', 'success'); 
                    } else { 
                        $('#sub-wrap-' + menuId).slideUp(200); 
                        showPT('Akses menu dicabut', 'warning'); 
                    }
                }
            }, 'json').fail(function () { 
                showPT('Gagal menyimpan!', 'error'); 
                $el.prop('checked', !$el.prop('checked')); 
            });
        });

        // Toggle Submenu
        $(document).on('change', '.sub-toggle-chk', function () {
            const subId = parseInt($(this).data('submenu-id'));
            const menuId = parseInt($(this).data('menu-id'));
            const is_checked = this.checked ? 1 : 0;
            const $el = $(this);
            const payload = { user_id: _USER_ID, submenu_id: subId, is_checked: is_checked };
            payload[_CSRF_NAME] = _CSRF_HASH;
            
            $.post(_SUB_TOGGLE, payload, function (res) {
                if (res.status === 'success') {
                    if (is_checked) {
                        showPT('Akses submenu diberikan', 'success');
                    } else {
                        showPT('Akses submenu dicabut', 'warning');
                    }
                }
            }, 'json').fail(function () { 
                showPT('Gagal menyimpan!', 'error'); 
                $el.prop('checked', !$el.prop('checked')); 
            });
        });
    });
</script>
