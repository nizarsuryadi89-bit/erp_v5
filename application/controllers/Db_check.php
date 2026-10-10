<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Db_check extends CI_Controller {
    public function index() {
        $this->load->model('Warehouse_model', 'warehouse');
        
        $low_stock = $this->warehouse->get_low_stock_materials();
        echo "get_low_stock_materials Count: " . count($low_stock) . "\n";
        if (count($low_stock) > 0) {
            echo "First 3 items:\n";
            print_r(array_slice($low_stock, 0, 3));
        }

        $critical = $this->warehouse->get_critical_stock();
        echo "\nget_critical_stock Count: " . count($critical) . "\n";
        if (count($critical) > 0) {
            echo "First 3 items:\n";
            print_r(array_slice($critical, 0, 3));
        }
    }
}
