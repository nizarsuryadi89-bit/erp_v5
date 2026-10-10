<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_menu extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }

    // ==================== MENU OPERATIONS ====================

    /**
     * Get all menus with submenu count
     */
    public function getAllMenusWithSubmenuCount() {
        $this->db->select('m.*, COUNT(s.id) as submenu_count');
        $this->db->from('menu m');
        $this->db->join('submenu s', 'm.id = s.id_menu', 'left');
        $this->db->group_by('m.id');
        $this->db->order_by('m.urutan', 'ASC');
        $this->db->order_by('m.nama_menu', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get active menus only
     */
    public function getActiveMenus() {
        $this->db->where('status', 1);
        $this->db->order_by('urutan', 'ASC');
        $this->db->order_by('nama_menu', 'ASC');
        return $this->db->get('menu')->result();
    }

    /**
     * Get menu by ID with submenus
     */
    public function getMenuWithSubmenus($menu_id) {
        $menu = $this->db->get_where('menu', ['id' => $menu_id])->row();
        
        if ($menu) {
            $this->db->where('id_menu', $menu_id);
            $this->db->order_by('urutan', 'ASC');
            $menu->submenus = $this->db->get('submenu')->result();
        }
        
        return $menu;
    }

    /**
     * Check if menu name exists
     */
    public function isMenuNameExists($nama_menu, $exclude_id = null) {
        $this->db->where('nama_menu', $nama_menu);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results('menu') > 0;
    }

    /**
     * Add new menu
     */
    public function addMenu($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('menu', $data);
    }

    /**
     * Update menu
     */
    public function updateMenu($menu_id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $menu_id);
        return $this->db->update('menu', $data);
    }

    /**
     * Delete menu
     */
    public function deleteMenu($menu_id) {
        // Check if menu has submenus
        $this->db->where('id_menu', $menu_id);
        $has_submenus = $this->db->count_all_results('submenu') > 0;
        
        if ($has_submenus) {
            return false; // Cannot delete menu with submenus
        }
        
        $this->db->where('id', $menu_id);
        return $this->db->delete('menu');
    }

    /**
     * Get menu statistics
     */
    public function getMenuStatistics() {
        $stats = [
            'total_menus' => $this->db->count_all('menu'),
            'active_menus' => $this->db->where('status', 1)->count_all_results('menu'),
            'inactive_menus' => $this->db->where('status', 0)->count_all_results('menu'),
            'menus_with_submenus' => $this->db->select('COUNT(DISTINCT id_menu) as count')
                                               ->from('submenu')
                                               ->get()->row()->count
        ];
        
        return $stats;
    }

    // ==================== SUBMENU OPERATIONS ====================

    /**
     * Get all submenus with menu information
     */
    public function getAllSubmenusWithMenuInfo() {
        $this->db->select('s.*, m.nama_menu as menu_name');
        $this->db->from('submenu s');
        $this->db->join('menu m', 's.id_menu = m.id');
        $this->db->order_by('m.urutan', 'ASC');
        $this->db->order_by('s.urutan', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get submenus by menu ID
     */
    public function getSubmenusByMenuId($menu_id) {
        $this->db->where('id_menu', $menu_id);
        $this->db->order_by('urutan', 'ASC');
        $this->db->order_by('nama_submenu', 'ASC');
        return $this->db->get('submenu')->result();
    }

    /**
     * Get active submenus by menu ID
     */
    public function getActiveSubmenusByMenuId($menu_id) {
        $this->db->where('id_menu', $menu_id);
        $this->db->where('status', 1);
        $this->db->order_by('urutan', 'ASC');
        $this->db->order_by('nama_submenu', 'ASC');
        return $this->db->get('submenu')->result();
    }

    /**
     * Check if submenu name exists in menu
     */
    public function isSubmenuNameExists($menu_id, $nama_submenu, $exclude_id = null) {
        $this->db->where('id_menu', $menu_id);
        $this->db->where('nama_submenu', $nama_submenu);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results('submenu') > 0;
    }

    /**
     * Add new submenu
     */
    public function addSubmenu($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('submenu', $data);
    }

    /**
     * Update submenu
     */
    public function updateSubmenu($submenu_id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $submenu_id);
        return $this->db->update('submenu', $data);
    }

    /**
     * Delete submenu
     */
    public function deleteSubmenu($submenu_id) {
        $this->db->where('id', $submenu_id);
        return $this->db->delete('submenu');
    }

    /**
     * Get submenu statistics
     */
    public function getSubmenuStatistics($menu_id = null) {
        if ($menu_id) {
            $stats = [
                'total_submenus' => $this->db->where('id_menu', $menu_id)->count_all_results('submenu'),
                'active_submenus' => $this->db->where('id_menu', $menu_id)->where('status', 1)->count_all_results('submenu'),
                'inactive_submenus' => $this->db->where('id_menu', $menu_id)->where('status', 0)->count_all_results('submenu')
            ];
        } else {
            $stats = [
                'total_submenus' => $this->db->count_all('submenu'),
                'active_submenus' => $this->db->where('status', 1)->count_all_results('submenu'),
                'inactive_submenus' => $this->db->where('status', 0)->count_all_results('submenu')
            ];
        }
        
        return $stats;
    }

    // ==================== VALIDATION FUNCTIONS ====================

    /**
     * Validate menu data
     */
    public function validateMenuData($data, $is_update = false) {
        $errors = [];
        
        // Required fields
        if (empty($data['nama_menu'])) {
            $errors[] = 'Nama menu wajib diisi';
        }
        
        // Check name uniqueness
        if (!empty($data['nama_menu'])) {
            $exclude_id = $is_update ? $data['menu_id'] : null;
            if ($this->isMenuNameExists($data['nama_menu'], $exclude_id)) {
                $errors[] = 'Nama menu sudah digunakan';
            }
        }
        
        // Validate status
        if (isset($data['status']) && !in_array($data['status'], [0, 1])) {
            $errors[] = 'Status menu tidak valid';
        }
        
        // Validate order
        if (isset($data['urutan']) && !is_numeric($data['urutan'])) {
            $errors[] = 'Urutan menu harus berupa angka';
        }
        
        return $errors;
    }

    /**
     * Validate submenu data
     */
    public function validateSubmenuData($data, $is_update = false) {
        $errors = [];
        
        // Required fields
        if (empty($data['nama_submenu'])) {
            $errors[] = 'Nama submenu wajib diisi';
        }
        
        if (empty($data['link_submenu'])) {
            $errors[] = 'Link submenu wajib diisi';
        }
        
        if (empty($data['menu_id'])) {
            $errors[] = 'Menu wajib dipilih';
        }
        
        // Check if menu exists
        if (!empty($data['menu_id'])) {
            $menu_exists = $this->db->get_where('menu', ['id' => $data['menu_id']])->row();
            if (!$menu_exists) {
                $errors[] = 'Menu yang dipilih tidak ditemukan';
            }
        }
        
        // Check name uniqueness in menu
        if (!empty($data['nama_submenu']) && !empty($data['menu_id'])) {
            $exclude_id = $is_update ? $data['submenu_id'] : null;
            if ($this->isSubmenuNameExists($data['menu_id'], $data['nama_submenu'], $exclude_id)) {
                $errors[] = 'Nama submenu sudah digunakan dalam menu ini';
            }
        }
        
        // Validate status
        if (isset($data['status']) && !in_array($data['status'], [0, 1])) {
            $errors[] = 'Status submenu tidak valid';
        }
        
        // Validate order
        if (isset($data['urutan']) && !is_numeric($data['urutan'])) {
            $errors[] = 'Urutan submenu harus berupa angka';
        }
        
        return $errors;
    }

    // ==================== UTILITY FUNCTIONS ====================

    /**
     * Get menu hierarchy for navigation
     */
    public function getMenuHierarchy() {
        $menus = $this->getActiveMenus();
        
        foreach ($menus as $menu) {
            $menu->submenus = $this->getActiveSubmenusByMenuId($menu->id);
        }
        
        return $menus;
    }

    /**
     * Reorder menu items
     */
    public function reorderMenus($menu_orders) {
        $this->db->trans_start();
        
        foreach ($menu_orders as $order) {
            $this->db->where('id', $order['id']);
            $this->db->update('menu', ['urutan' => $order['order']]);
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Reorder submenu items
     */
    public function reorderSubmenus($submenu_orders) {
        $this->db->trans_start();
        
        foreach ($submenu_orders as $order) {
            $this->db->where('id', $order['id']);
            $this->db->update('submenu', ['urutan' => $order['order']]);
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Bulk update menu status
     */
    public function bulkUpdateMenuStatus($menu_ids, $status) {
        $this->db->where_in('id', $menu_ids);
        return $this->db->update('menu', ['status' => $status]);
    }

    /**
     * Bulk update submenu status
     */
    public function bulkUpdateSubmenuStatus($submenu_ids, $status) {
        $this->db->where_in('id', $submenu_ids);
        return $this->db->update('submenu', ['status' => $status]);
    }

    /**
     * Search menus
     */
    public function searchMenus($keyword) {
        $this->db->like('nama_menu', $keyword);
        $this->db->or_like('icon_menu', $keyword);
        $this->db->order_by('urutan', 'ASC');
        return $this->db->get('menu')->result();
    }

    /**
     * Search submenus
     */
    public function searchSubmenus($keyword, $menu_id = null) {
        $this->db->select('s.*, m.nama_menu as menu_name');
        $this->db->from('submenu s');
        $this->db->join('menu m', 's.id_menu = m.id');
        $this->db->group_start();
        $this->db->like('s.nama_submenu', $keyword);
        $this->db->or_like('s.link_submenu', $keyword);
        $this->db->or_like('m.nama_menu', $keyword);
        $this->db->group_end();
        
        if ($menu_id) {
            $this->db->where('s.id_menu', $menu_id);
        }
        
        $this->db->order_by('m.urutan', 'ASC');
        $this->db->order_by('s.urutan', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get menu usage statistics
     */
    public function getMenuUsageStats() {
        $stats = [];
        
        // Get menus with submenu count
        $menus = $this->getAllMenusWithSubmenuCount();
        
        foreach ($menus as $menu) {
            $stats[] = [
                'menu_name' => $menu->nama_menu,
                'submenu_count' => $menu->submenu_count,
                'status' => $menu->status ? 'Aktif' : 'Nonaktif',
                'created_at' => $menu->created_at
            ];
        }
        
        return $stats;
    }
}
?>
