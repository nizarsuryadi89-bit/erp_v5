<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repair_db extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Allow access only to logged in users/admins ideally, but for repair might need open access if login is broken
        // Assuming user acts as admin
        if (!$this->session->userdata('id')) {
            // redirect('auth'); // Uncomment if login works
        }
    }

    public function index()
    {
        echo "<h1>Database Repair Tool</h1>";
        echo "<p>Checking and creating missing tables...</p>";

        // 1. Manual Customers
        $sql_customers = "CREATE TABLE IF NOT EXISTS `manual_customers` (
            `customer_id` int(11) NOT NULL AUTO_INCREMENT,
            `customer_name` varchar(255) NOT NULL,
            `customer_phone` varchar(50) DEFAULT NULL,
            `customer_email` varchar(255) DEFAULT NULL,
            `customer_address` text,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`customer_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if ($this->db->query($sql_customers)) {
            echo "<p style='color:green'>[OK] Table `manual_customers` checked/created.</p>";
        } else {
            echo "<p style='color:red'>[ERROR] Failed to create `manual_customers`: " . $this->db->error()['message'] . "</p>";
        }

        // 2. Manual Transactions
        $sql_transactions = "CREATE TABLE IF NOT EXISTS `manual_transactions` (
            `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
            `transaction_number` varchar(50) NOT NULL,
            `customer_id` int(11) DEFAULT NULL,
            `transaction_date` date NOT NULL,
            `due_date` date DEFAULT NULL,
            `sales_person` varchar(100) DEFAULT NULL,
            `total_items` int(11) DEFAULT '0',
            `subtotal` decimal(15,2) DEFAULT '0.00',
            `discount_total` decimal(15,2) DEFAULT '0.00',
            `tax_amount` decimal(15,2) DEFAULT '0.00',
            `grand_total` decimal(15,2) DEFAULT '0.00',
            `payment_status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
            `status` enum('pending','processed','completed','cancelled') DEFAULT 'pending',
            `priority` enum('low','normal','high') DEFAULT 'normal',
            `notes` text,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`transaction_id`),
            KEY `customer_id` (`customer_id`),
            KEY `transaction_number` (`transaction_number`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if ($this->db->query($sql_transactions)) {
            echo "<p style='color:green'>[OK] Table `manual_transactions` checked/created.</p>";
        } else {
            echo "<p style='color:red'>[ERROR] Failed to create `manual_transactions`: " . $this->db->error()['message'] . "</p>";
        }

        // 3. Manual Transaction Items
        $sql_items = "CREATE TABLE IF NOT EXISTS `manual_transaction_items` (
            `item_id` int(11) NOT NULL AUTO_INCREMENT,
            `transaction_id` int(11) NOT NULL,
            `item_name` varchar(255) NOT NULL,
            `item_description` text,
            `quantity` int(11) NOT NULL DEFAULT '1',
            `unit` varchar(50) DEFAULT 'pcs',
            `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
            `discount_percent` decimal(5,2) DEFAULT '0.00',
            `discount_amount` decimal(15,2) DEFAULT '0.00',
            `total` decimal(15,2) NOT NULL DEFAULT '0.00',
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`item_id`),
            KEY `transaction_id` (`transaction_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if ($this->db->query($sql_items)) {
            echo "<p style='color:green'>[OK] Table `manual_transaction_items` checked/created.</p>";
        } else {
            echo "<p style='color:red'>[ERROR] Failed to create `manual_transaction_items`: " . $this->db->error()['message'] . "</p>";
        }

        // 4. Manual Payment History
        $sql_payments = "CREATE TABLE IF NOT EXISTS `manual_payment_history` (
            `payment_id` int(11) NOT NULL AUTO_INCREMENT,
            `transaction_id` int(11) NOT NULL,
            `payment_date` date NOT NULL,
            `payment_time` time DEFAULT NULL,
            `payment_amount` decimal(15,2) NOT NULL,
            `payment_method` varchar(50) NOT NULL,
            `payment_reference` varchar(100) DEFAULT NULL,
            `payment_status` varchar(20) DEFAULT 'completed',
            `payment_notes` text,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`payment_id`),
            KEY `transaction_id` (`transaction_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if ($this->db->query($sql_payments)) {
            echo "<p style='color:green'>[OK] Table `manual_payment_history` checked/created.</p>";
        } else {
            echo "<p style='color:red'>[ERROR] Failed to create `manual_payment_history`: " . $this->db->error()['message'] . "</p>";
        }

        echo "<hr>";
        echo "<p>Process finished. <a href='" . base_url('dashboard') . "'>Go to Dashboard</a></p>";
    }
}
