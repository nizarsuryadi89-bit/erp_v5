# MY_Controller - Base Controller Documentation

## Overview
`MY_Controller` is a base controller that provides common functionality and configuration data to all controllers that extend it.

## Features

### 1. Automatic Configuration Loading
The base controller automatically loads:
- **Company Information** (`$this->conf['perusahaan']`)
- **User Access Rights** (`$this->conf['hakakses']`)
- **Current User Data** (`$this->conf['userid']`)

### 2. Common Data Array
Provides a pre-built data array with:
- `controller` - Current controller name
- `function` - Current function name
- `perusahaan` - Company name
- `hakakses` - User division/role name
- `logo` - Company logo
- `username` - Current username
- `level` - User level/role data
- `menu` - Menu items

### 3. Authentication Check
Automatically checks if user is logged in and redirects to auth if not.

### 4. Common Models
Automatically loads:
- `M_transaksi`
- `M_master`
- `M_gudang`

## How to Use

### 1. Extend the Base Controller
```php
<?php
class YourController extends MY_Controller {
    public function __construct() {
        parent::__construct();
        // Your additional initialization
    }
}
```

### 2. Access Configuration Data
```php
public function index() {
    // Access configuration data
    $perusahaan = $this->conf['perusahaan'];
    $hakakses = $this->conf['hakakses'];
    $userid = $this->conf['userid'];
    
    // Access common data
    $company_name = $this->data['perusahaan'];
    $user_role = $this->data['hakakses'];
}
```

### 3. Load Admin Views
```php
public function index() {
    $data = array(
        'title' => 'Your Page Title',
        'custom_data' => 'Your custom data',
        // ... other data
    );
    
    // This automatically includes all common data
    $this->load_admin_view('admin/your_view', $data);
}
```

### 4. Manual View Loading (if needed)
```php
public function custom_view() {
    $data = array(
        'title' => 'Custom View',
        'custom_data' => 'Data'
    );
    
    // Merge with common data
    $view_data = array_merge($this->data, $data);
    
    $this->load->view('layout_admin/head', $view_data);
    $this->load->view('layout_admin/top_header', $view_data);
    $this->load->view('layout_admin/sidebar', $view_data);
    $this->load->view('admin/your_view', $view_data);
    $this->load->view('layout_admin/footer');
    $this->load->view('layout_admin/alert');
}
```

## Available Methods

### `get_config()`
Returns the configuration array:
```php
$config = $this->get_config();
```

### `get_common_data()`
Returns the common data array:
```php
$common_data = $this->get_common_data();
```

### `load_admin_view($content_view, $additional_data = array())`
Loads the complete admin layout with content:
```php
$this->load_admin_view('admin/dashboard/index', $data);
```

## Benefits

1. **DRY Principle** - No need to repeat configuration code
2. **Consistency** - All controllers have the same base data
3. **Maintainability** - Changes to common data only need to be made in one place
4. **Security** - Automatic authentication check
5. **Efficiency** - Common models loaded once

## Migration Guide

### Before (Old Way)
```php
class Dashboard extends CI_Controller {
    public function index() {
        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan'=> $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id'=> $this->session->userdata('level')))->row_array(),
            'userid' => $this->db->get_where('users', array('user_id'=> $this->session->userdata('id')))->row_array(),
        );
        
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'username' => $conf['userid']['username'],
            'level' => $conf['userid'],
            'menu' => $this->db->get('menu')->result(),
            // ... your custom data
        );
        
        $this->load->view('layout_admin/head', $data);
        $this->load->view('layout_admin/top_header', $data);
        $this->load->view('layout_admin/sidebar', $data);
        $this->load->view('admin/dashboard/index', $data);
        $this->load->view('layout_admin/footer');
        $this->load->view('layout_admin/alert');
    }
}
```

### After (New Way)
```php
class Dashboard extends MY_Controller {
    public function index() {
        $data = array(
            'title' => 'Dashboard',
            // ... your custom data only
        );
        
        $this->load_admin_view('admin/dashboard/index', $data);
    }
}
```

## Notes

- All controllers should extend `MY_Controller` instead of `CI_Controller`
- The base controller automatically handles authentication
- Common data is automatically available in all views
- You can still access `$this->conf` and `$this->data` directly if needed 