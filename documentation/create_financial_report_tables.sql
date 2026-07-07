-- =====================================================
-- FINANCIAL REPORTING SYSTEM TABLES
-- Based on tbl_pembayaran structure
-- =====================================================

-- Drop existing tables if they exist (be careful with this in production)
-- DROP TABLE IF EXISTS `tbl_pembayaran_detail`;
-- DROP TABLE IF EXISTS `tbl_pembayaran_produk`;
-- DROP TABLE IF EXISTS `tbl_pembayaran`;

-- Create main payment table
CREATE TABLE IF NOT EXISTS `tbl_pembayaran` (
  `pembayaran_id` int(11) NOT NULL AUTO_INCREMENT,
  `nospk` varchar(50) NOT NULL,
  `konsumen_id` int(11) DEFAULT NULL,
  `tgl_bayar` date NOT NULL DEFAULT curdate(),
  `jam_bayar` time NOT NULL DEFAULT curtime(),
  `total_tagihan` decimal(15,2) NOT NULL,
  `total_bayar` decimal(15,2) NOT NULL,
  `status_lunas` enum('0','1') NOT NULL DEFAULT '0',
  `kasir_id` int(11) NOT NULL,
  `perusahaan_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`pembayaran_id`),
  KEY `idx_nospk` (`nospk`),
  KEY `idx_tgl_bayar` (`tgl_bayar`),
  KEY `idx_kasir_id` (`kasir_id`),
  KEY `idx_perusahaan_id` (`perusahaan_id`),
  KEY `idx_konsumen_id` (`konsumen_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create payment detail table
CREATE TABLE IF NOT EXISTS `tbl_pembayaran_detail` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `pembayaran_id` int(11) NOT NULL,
  `metode` enum('tunai','transfer','debit','kartu_kredit','ewallet','lainnya') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `referensi` varchar(100) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `edc_id` int(11) DEFAULT NULL,
  `ewallet_id` int(11) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`detail_id`),
  KEY `idx_pembayaran_id` (`pembayaran_id`),
  KEY `idx_metode` (`metode`),
  KEY `idx_bank_id` (`bank_id`),
  KEY `idx_edc_id` (`edc_id`),
  KEY `idx_ewallet_id` (`ewallet_id`),
  CONSTRAINT `fk_pembayaran_detail_pembayaran` FOREIGN KEY (`pembayaran_id`) REFERENCES `tbl_pembayaran` (`pembayaran_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create payment product table (for product details)
CREATE TABLE IF NOT EXISTS `tbl_pembayaran_produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nospk` varchar(50) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `diskon` decimal(15,2) DEFAULT 0,
  `panjang` decimal(10,2) DEFAULT 0,
  `lebar` decimal(10,2) DEFAULT 0,
  `mesin_id` int(11) DEFAULT NULL,
  `finishing_id` int(11) DEFAULT NULL,
  `harga_id` int(11) NOT NULL,
  `dateline_tgl` date DEFAULT NULL,
  `dateline_jam` time DEFAULT NULL,
  `lokasi_cetak` int(11) DEFAULT 0,
  `approve` enum('0','1') DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_nospk` (`nospk`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_harga_id` (`harga_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create bank accounts table (if not exists)
CREATE TABLE IF NOT EXISTS `tbl_rekening` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_bank` varchar(100) NOT NULL,
  `no_rekening` varchar(50) NOT NULL,
  `atas_nama` varchar(100) NOT NULL,
  `rekening_ket` varchar(255) DEFAULT NULL,
  `is_active` enum('0','1') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create EDC machines table (if not exists)
CREATE TABLE IF NOT EXISTS `tbl_mesin_edc` (
  `edc_id` int(11) NOT NULL AUTO_INCREMENT,
  `edc_nama` varchar(100) NOT NULL,
  `edc_ket` varchar(255) DEFAULT NULL,
  `is_active` enum('0','1') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`edc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create e-wallet table (if not exists)
CREATE TABLE IF NOT EXISTS `tbl_ewallet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ewallet_nama` varchar(100) NOT NULL,
  `ewallet_ket` varchar(255) DEFAULT NULL,
  `is_active` enum('0','1') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data for bank accounts
INSERT IGNORE INTO `tbl_rekening` (`nama_bank`, `no_rekening`, `atas_nama`, `rekening_ket`) VALUES
('Bank Central Asia (BCA)', '1234567890', 'PT. TGM ERP', 'Rekening Utama'),
('Bank Rakyat Indonesia (BRI)', '0987654321', 'PT. TGM ERP', 'Rekening Operasional'),
('Bank Mandiri', '1122334455', 'PT. TGM ERP', 'Rekening Investasi');

-- Insert sample data for EDC machines
INSERT IGNORE INTO `tbl_mesin_edc` (`edc_nama`, `edc_ket`) VALUES
('EDC BCA 001', 'Mesin EDC BCA Cabang Utama'),
('EDC BRI 001', 'Mesin EDC BRI Cabang Utama'),
('EDC Mandiri 001', 'Mesin EDC Mandiri Cabang Utama');

-- Insert sample data for e-wallets
INSERT IGNORE INTO `tbl_ewallet` (`ewallet_nama`, `ewallet_ket`) VALUES
('GoPay', 'GoPay - GoJek'),
('OVO', 'OVO - Grab'),
('DANA', 'DANA - Emtek'),
('LinkAja', 'LinkAja - Telkomsel'),
('ShopeePay', 'ShopeePay - Shopee');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_pembayaran_date_range` ON `tbl_pembayaran` (`tgl_bayar`, `perusahaan_id`);
CREATE INDEX IF NOT EXISTS `idx_pembayaran_status` ON `tbl_pembayaran` (`status_lunas`, `perusahaan_id`);
CREATE INDEX IF NOT EXISTS `idx_pembayaran_kasir_date` ON `tbl_pembayaran` (`kasir_id`, `tgl_bayar`);

-- Create view for financial summary
CREATE OR REPLACE VIEW `v_financial_summary` AS
SELECT 
    p.perusahaan_id,
    DATE(p.tgl_bayar) as tanggal,
    COUNT(*) as total_transactions,
    SUM(p.total_tagihan) as total_tagihan,
    SUM(p.total_bayar) as total_bayar,
    SUM(p.total_tagihan - p.total_bayar) as total_piutang,
    SUM(CASE WHEN p.status_lunas = '1' THEN 1 ELSE 0 END) as total_lunas,
    SUM(CASE WHEN p.status_lunas = '0' THEN 1 ELSE 0 END) as total_belum_lunas
FROM tbl_pembayaran p
GROUP BY p.perusahaan_id, DATE(p.tgl_bayar);

-- Create view for payment methods summary
CREATE OR REPLACE VIEW `v_payment_methods_summary` AS
SELECT 
    p.perusahaan_id,
    pd.metode,
    COUNT(*) as jumlah_transaksi,
    SUM(pd.jumlah) as total_pembayaran
FROM tbl_pembayaran p
JOIN tbl_pembayaran_detail pd ON pd.pembayaran_id = p.pembayaran_id
GROUP BY p.perusahaan_id, pd.metode;

-- Create view for top customers
CREATE OR REPLACE VIEW `v_top_customers` AS
SELECT 
    p.perusahaan_id,
    p.konsumen_id,
    k.konsumen_nama,
    COUNT(*) as jumlah_transaksi,
    SUM(p.total_tagihan) as total_tagihan,
    SUM(p.total_bayar) as total_bayar
FROM tbl_pembayaran p
JOIN tbl_konsumen k ON k.konsumen_id = p.konsumen_id
GROUP BY p.perusahaan_id, p.konsumen_id;

-- Create view for top cashiers
CREATE OR REPLACE VIEW `v_top_cashiers` AS
SELECT 
    p.perusahaan_id,
    p.kasir_id,
    u.username as kasir_nama,
    COUNT(*) as jumlah_transaksi,
    SUM(p.total_tagihan) as total_tagihan,
    SUM(p.total_bayar) as total_bayar
FROM tbl_pembayaran p
JOIN users u ON u.user_id = p.kasir_id
GROUP BY p.perusahaan_id, p.kasir_id;

-- =====================================================
-- SAMPLE DATA FOR TESTING (Optional)
-- =====================================================

-- Insert sample payment data (uncomment if needed for testing)
/*
INSERT INTO `tbl_pembayaran` (`nospk`, `konsumen_id`, `tgl_bayar`, `jam_bayar`, `total_tagihan`, `total_bayar`, `status_lunas`, `kasir_id`, `perusahaan_id`) VALUES
('SPK-2024-001', 1, '2024-01-15', '09:30:00', 1500000.00, 1500000.00, '1', 1, 1),
('SPK-2024-002', 2, '2024-01-15', '10:15:00', 2500000.00, 2000000.00, '0', 1, 1),
('SPK-2024-003', 3, '2024-01-16', '14:20:00', 800000.00, 800000.00, '1', 2, 1),
('SPK-2024-004', 1, '2024-01-16', '16:45:00', 1200000.00, 1200000.00, '1', 2, 1),
('SPK-2024-005', 4, '2024-01-17', '11:30:00', 3000000.00, 2500000.00, '0', 1, 1);

-- Insert sample payment details
INSERT INTO `tbl_pembayaran_detail` (`pembayaran_id`, `metode`, `jumlah`, `referensi`, `bank_id`, `keterangan`) VALUES
(1, 'tunai', 1000000.00, NULL, NULL, 'Pembayaran tunai'),
(1, 'transfer', 500000.00, 'TRF-001', 1, 'Transfer BCA'),
(2, 'tunai', 1500000.00, NULL, NULL, 'Pembayaran tunai'),
(2, 'transfer', 500000.00, 'TRF-002', 2, 'Transfer BRI'),
(3, 'tunai', 800000.00, NULL, NULL, 'Pembayaran tunai'),
(4, 'transfer', 1200000.00, 'TRF-003', 1, 'Transfer BCA'),
(5, 'tunai', 2000000.00, NULL, NULL, 'Pembayaran tunai'),
(5, 'transfer', 500000.00, 'TRF-004', 2, 'Transfer BRI');
*/

-- =====================================================
-- STORED PROCEDURES FOR FINANCIAL REPORTS
-- =====================================================

DELIMITER //

-- Procedure to get financial summary by date range
CREATE PROCEDURE IF NOT EXISTS `sp_get_financial_summary`(
    IN p_perusahaan_id INT,
    IN p_tanggal_mulai DATE,
    IN p_tanggal_selesai DATE
)
BEGIN
    SELECT 
        COUNT(*) as total_transactions,
        SUM(total_tagihan) as total_tagihan,
        SUM(total_bayar) as total_bayar,
        SUM(total_tagihan - total_bayar) as total_piutang,
        SUM(CASE WHEN status_lunas = '1' THEN 1 ELSE 0 END) as total_lunas,
        SUM(CASE WHEN status_lunas = '0' THEN 1 ELSE 0 END) as total_belum_lunas,
        ROUND((SUM(CASE WHEN status_lunas = '1' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as persentase_lunas
    FROM tbl_pembayaran 
    WHERE perusahaan_id = p_perusahaan_id 
    AND tgl_bayar BETWEEN p_tanggal_mulai AND p_tanggal_selesai;
END //

-- Procedure to get daily transactions
CREATE PROCEDURE IF NOT EXISTS `sp_get_daily_transactions`(
    IN p_perusahaan_id INT,
    IN p_tanggal_mulai DATE,
    IN p_tanggal_selesai DATE
)
BEGIN
    SELECT 
        DATE(tgl_bayar) as tanggal,
        COUNT(*) as jumlah_transaksi,
        SUM(total_tagihan) as total_tagihan,
        SUM(total_bayar) as total_bayar,
        SUM(CASE WHEN status_lunas = '1' THEN 1 ELSE 0 END) as jumlah_lunas
    FROM tbl_pembayaran 
    WHERE perusahaan_id = p_perusahaan_id 
    AND tgl_bayar BETWEEN p_tanggal_mulai AND p_tanggal_selesai
    GROUP BY DATE(tgl_bayar)
    ORDER BY tanggal ASC;
END //

-- Procedure to get payment methods summary
CREATE PROCEDURE IF NOT EXISTS `sp_get_payment_methods`(
    IN p_perusahaan_id INT,
    IN p_tanggal_mulai DATE,
    IN p_tanggal_selesai DATE
)
BEGIN
    SELECT 
        pd.metode,
        COUNT(*) as jumlah_transaksi,
        SUM(pd.jumlah) as total_pembayaran
    FROM tbl_pembayaran p
    JOIN tbl_pembayaran_detail pd ON pd.pembayaran_id = p.pembayaran_id
    WHERE p.perusahaan_id = p_perusahaan_id 
    AND p.tgl_bayar BETWEEN p_tanggal_mulai AND p_tanggal_selesai
    GROUP BY pd.metode
    ORDER BY total_pembayaran DESC;
END //

DELIMITER ;

-- =====================================================
-- TRIGGERS FOR DATA INTEGRITY
-- =====================================================

DELIMITER //

-- Trigger to update status_lunas based on payment amount
CREATE TRIGGER IF NOT EXISTS `tr_update_status_lunas` 
BEFORE UPDATE ON `tbl_pembayaran`
FOR EACH ROW
BEGIN
    IF NEW.total_bayar >= NEW.total_tagihan THEN
        SET NEW.status_lunas = '1';
    ELSE
        SET NEW.status_lunas = '0';
    END IF;
END //

-- Trigger to update status_lunas on insert
CREATE TRIGGER IF NOT EXISTS `tr_insert_status_lunas` 
BEFORE INSERT ON `tbl_pembayaran`
FOR EACH ROW
BEGIN
    IF NEW.total_bayar >= NEW.total_tagihan THEN
        SET NEW.status_lunas = '1';
    ELSE
        SET NEW.status_lunas = '0';
    END IF;
END //

DELIMITER ;

-- =====================================================
-- COMMENTS AND DOCUMENTATION
-- =====================================================

/*
FINANCIAL REPORTING SYSTEM

This system provides comprehensive financial reporting based on the tbl_pembayaran table structure.

TABLES:
1. tbl_pembayaran - Main payment table
2. tbl_pembayaran_detail - Payment method details
3. tbl_pembayaran_produk - Product details for payments
4. tbl_rekening - Bank account information
5. tbl_mesin_edc - EDC machine information
6. tbl_ewallet - E-wallet information

VIEWS:
1. v_financial_summary - Daily financial summaries
2. v_payment_methods_summary - Payment method summaries
3. v_top_customers - Top customer rankings
4. v_top_cashiers - Top cashier rankings

STORED PROCEDURES:
1. sp_get_financial_summary - Get financial summary by date range
2. sp_get_daily_transactions - Get daily transaction data
3. sp_get_payment_methods - Get payment methods summary

FEATURES:
- Multi-company support (perusahaan_id)
- Payment method tracking
- Customer and cashier performance tracking
- Automatic status updates (lunas/belum lunas)
- Comprehensive reporting views
- Excel export capability
- Real-time dashboard

USAGE:
1. Run this SQL file to create all necessary tables
2. Access the financial reports through the Keuangan controller
3. Use the provided views and stored procedures for custom reports
4. Export data to Excel using the export functions
*/
