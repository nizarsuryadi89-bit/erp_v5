-- Create kasir_shift table for cashier shift management
CREATE TABLE `kasir_shift` (
    `shift_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,                 -- ID kasir (relasi ke tabel user)
    `tanggal_shift` DATE NOT NULL,              -- Tanggal shift
    `jam_open` DATETIME NOT NULL,               -- Waktu buka kasir
    `jam_close` DATETIME DEFAULT NULL,          -- Waktu tutup kasir
    `modal_awal` DECIMAL(15,2) NOT NULL,         -- Modal awal di laci kasir
    `total_penjualan_tunai` DECIMAL(15,2) DEFAULT 0.00,
    `total_penjualan_non_tunai` DECIMAL(15,2) DEFAULT 0.00,
    `total_pengeluaran` DECIMAL(15,2) DEFAULT 0.00, -- Kas kecil yang keluar
    `uang_fisik_tutup` DECIMAL(15,2) DEFAULT 0.00,  -- Uang fisik yang dihitung saat tutup
    `selisih` DECIMAL(15,2) DEFAULT 0.00,       -- Selisih kas (uang fisik - (modal + penjualan tunai - pengeluaran))
    `status` ENUM('open','closed') DEFAULT 'open',
    `catatan` TEXT,
    PRIMARY KEY (`shift_id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_tanggal_shift` (`tanggal_shift`),
    INDEX `idx_status` (`status`),
    INDEX `idx_jam_open` (`jam_open`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add foreign key constraint to users table
ALTER TABLE `kasir_shift` 
ADD CONSTRAINT `fk_kasir_shift_user` 
FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) 
ON DELETE CASCADE ON UPDATE CASCADE;
