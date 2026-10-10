# Warehouse Management System

## Overview
Sistem manajemen gudang terintegrasi untuk TGM ERP yang memungkinkan pengelolaan stok, mutasi, dan lokasi di semua gudang dengan sistem yang terintegrasi dan real-time.

## Fitur Utama

### 1. Multi-Warehouse Management
- **3 Gudang Utama**:
  - Gudang Utama (ID: 1) - Bahan Baku
  - Gudang Produksi 1 (ID: 2) - Transit Produksi
  - Gudang Produksi 2 (ID: 3) - Transit Produksi

### 2. Stock Management
- Tracking stok real-time di semua gudang
- Perhitungan otomatis: `Stok Aktif = (Stok Awal + Stok Tambah) - Stok Kurang`
- Alert untuk stok minim
- Validasi ketersediaan stok sebelum mutasi

### 3. Mutation System
- **3 Jenis Mutasi**:
  - **MASUK**: Barang masuk dari supplier/eksternal
  - **KELUAR**: Barang keluar ke customer/eksternal
  - **TRANSFER**: Perpindahan antar gudang

### 4. Location Management
- Manajemen lokasi dalam gudang (Area, Rack, Shelf, Bin)
- Tracking kapasitas lokasi
- Hierarki lokasi (parent-child)

### 5. Reporting & Analytics
- Laporan stok per gudang
- Riwayat mutasi dengan filter
- Laporan stok minim
- Export ke Excel

## Database Structure

### Tables Created

#### 1. `mutasi_gudang`
```sql
- mutasi_id (PK)
- tanggal_mutasi, waktu_mutasi
- jenis_mutasi (MASUK/KELUAR/TRANSFER)
- gudang_asal, gudang_tujuan
- bahan_id, qty_mutasi, satuan_id
- keterangan, referensi_no, referensi_tipe
- supplier_id, customer_id
- created_by, created_at, updated_at
```

#### 2. `warehouse_locations`
```sql
- location_id (PK)
- gudang_id, location_name, location_code
- location_type (RACK/SHELF/BIN/AREA)
- parent_location_id, capacity, capacity_unit
- description, is_active
- created_at, updated_at
```

#### 3. `stock_movements`
```sql
- movement_id (PK)
- mutasi_id, bahan_id, gudang_id
- location_id, movement_type (IN/OUT/ADJUSTMENT)
- qty_before, qty_movement, qty_after
- unit_cost, total_cost, batch_no, expiry_date
- created_at
```

#### 4. `warehouse_settings`
```sql
- setting_id (PK)
- setting_key, setting_value, setting_description
- is_active, created_at, updated_at
```

### Existing Tables Used
- `tbl_gudang` - Data gudang
- `tbl_bahan` - Data bahan baku
- `tbl_satuan` - Data satuan
- `tbl_stok_gudang` - Stok per gudang
- `tbl_supplier` - Data supplier
- `tbl_konsumen` - Data customer
- `users` - Data user

## Installation & Setup

### 1. Database Setup
Jalankan script SQL untuk membuat tabel-tabel baru:

```bash
# Import warehouse system tables
mysql -u username -p database_name < warehouse_system_tables.sql
```

### 2. File Structure
```
application/
├── controllers/
│   └── Warehouse.php              # Main warehouse controller
├── models/
│   └── Warehouse_model.php        # Warehouse business logic
└── views/admin/warehouse/
    ├── dashboard.php              # Main dashboard
    ├── stock.php                  # Stock overview
    ├── mutations.php              # Mutation history
    ├── create_mutation.php        # Create mutation form
    ├── mutation_detail.php        # Mutation detail view
    ├── stock_adjustment.php       # Stock adjustment
    ├── locations.php              # Location management
    └── reports.php                # Reports & analytics
```

### 3. Routes
Tambahkan routes berikut di `application/config/routes.php`:

```php
// Warehouse Management Routes
$route['warehouse'] = 'warehouse/index';
$route['warehouse/stock'] = 'warehouse/stock';
$route['warehouse/mutations'] = 'warehouse/mutations';
$route['warehouse/create_mutation'] = 'warehouse/create_mutation';
$route['warehouse/mutation_detail/(:num)'] = 'warehouse/mutation_detail/$1';
$route['warehouse/locations'] = 'warehouse/locations';
$route['warehouse/stock_adjustment'] = 'warehouse/stock_adjustment';
$route['warehouse/reports'] = 'warehouse/reports';
$route['warehouse/export_stock_report'] = 'warehouse/export_stock_report';
```

## Usage Guide

### 1. Dashboard
- **URL**: `/warehouse`
- **Fitur**:
  - Overview statistik gudang
  - Quick actions untuk fungsi utama
  - Warehouse cards dengan detail stok
  - Recent mutations
  - Low stock alerts

### 2. Stock Management
- **URL**: `/warehouse/stock`
- **Fitur**:
  - View stok semua gudang
  - Filter by warehouse
  - Stock status indicators
  - Export functionality

### 3. Mutation Management
- **URL**: `/warehouse/mutations`
- **Fitur**:
  - Riwayat semua mutasi
  - Filter by type, warehouse, material, date
  - Detail view untuk setiap mutasi

#### Creating Mutations
- **URL**: `/warehouse/create_mutation`
- **Types**:
  - **MASUK**: Select destination warehouse, material, quantity
  - **KELUAR**: Select source warehouse, material, quantity
  - **TRANSFER**: Select source and destination warehouses

### 4. Stock Adjustment
- **URL**: `/warehouse/stock_adjustment`
- **Fitur**:
  - Manual stock adjustment
  - Reason tracking
  - Automatic mutation creation

### 5. Location Management
- **URL**: `/warehouse/locations`
- **Fitur**:
  - Create/edit warehouse locations
  - Location hierarchy
  - Capacity management

### 6. Reports
- **URL**: `/warehouse/reports`
- **Fitur**:
  - Stock summary by date range
  - Mutation summary
  - Low stock reports
  - Export capabilities

## API Endpoints

### AJAX Endpoints
```php
// Get stock by warehouse
POST /warehouse/ajax_get_stock_by_warehouse
{
    "warehouse_id": 1
}

// Get current stock
POST /warehouse/ajax_get_current_stock
{
    "material_id": 1,
    "warehouse_id": 1
}

// Validate stock availability
POST /warehouse/ajax_validate_stock
{
    "material_id": 1,
    "warehouse_id": 1,
    "qty_needed": 100
}
```

## Configuration

### Warehouse Settings
Settings dapat diubah melalui tabel `warehouse_settings`:

```sql
-- Default settings
INSERT INTO warehouse_settings (setting_key, setting_value, setting_description) VALUES
('default_gudang_utama', '1', 'ID Gudang Utama (Bahan Baku)'),
('default_gudang_produksi_1', '2', 'ID Gudang Produksi 1'),
('default_gudang_produksi_2', '3', 'ID Gudang Produksi 2'),
('enable_batch_tracking', '1', 'Enable batch number tracking'),
('enable_expiry_tracking', '1', 'Enable expiry date tracking'),
('enable_location_tracking', '1', 'Enable location tracking within warehouse'),
('auto_adjust_stock', '1', 'Automatically adjust stock on movement'),
('require_approval_transfer', '0', 'Require approval for inter-warehouse transfers'),
('require_approval_external', '1', 'Require approval for external movements');
```

## Business Logic

### Stock Calculation
```php
// Current Stock Formula
$current_stock = ($stok_awal + $stok_tambah) - $stok_kurang;

// Low Stock Check
$is_low_stock = $current_stock <= $stok_min;
```

### Mutation Processing
1. **Validation**: Check stock availability for OUT/TRANSFER
2. **Stock Update**: Automatically update stock based on mutation type
3. **Movement Tracking**: Create detailed movement records
4. **Audit Trail**: Track who, when, and why

### Stock Movement Types
- **IN**: Increases stock (MASUK, TRANSFER destination)
- **OUT**: Decreases stock (KELUAR, TRANSFER source)
- **ADJUSTMENT**: Manual stock correction

## Security & Permissions

### Access Control
- Requires user authentication
- Session-based access control
- User activity logging

### Data Validation
- Input validation for all forms
- Stock availability validation
- Quantity validation (positive numbers)
- Date validation

## Integration Points

### 1. Production System
- Automatic stock reduction during production
- Integration with `detail_produksi` table
- Material consumption tracking

### 2. Purchase System
- Automatic stock increase on purchase
- Integration with `tbl_pembelian` table
- Supplier tracking

### 3. Sales System
- Stock validation for sales orders
- Integration with customer data
- Delivery tracking

## Troubleshooting

### Common Issues

#### 1. Stock Mismatch
```sql
-- Check stock consistency
SELECT 
    sg.bahan_id,
    b.bahan_nama,
    sg.gudang_id,
    g.gudang_nama,
    sg.stok_awal,
    sg.stok_tambah,
    sg.stok_kurang,
    (sg.stok_awal + sg.stok_tambah - sg.stok_kurang) as calculated_stock
FROM tbl_stok_gudang sg
JOIN tbl_bahan b ON sg.bahan_id = b.bahan_id
JOIN tbl_gudang g ON sg.gudang_id = g.gudang_id
WHERE sg.gudang_id = 1;
```

#### 2. Missing Mutations
```sql
-- Check mutation history
SELECT 
    mg.*,
    b.bahan_nama,
    g_asal.gudang_nama as gudang_asal,
    g_tujuan.gudang_nama as gudang_tujuan
FROM mutasi_gudang mg
JOIN tbl_bahan b ON mg.bahan_id = b.bahan_id
LEFT JOIN tbl_gudang g_asal ON mg.gudang_asal = g_asal.gudang_id
LEFT JOIN tbl_gudang g_tujuan ON mg.gudang_tujuan = g_tujuan.gudang_id
ORDER BY mg.tanggal_mutasi DESC, mg.waktu_mutasi DESC;
```

#### 3. Stock Movement Issues
```sql
-- Check stock movements
SELECT 
    sm.*,
    mg.jenis_mutasi,
    b.bahan_nama
FROM stock_movements sm
JOIN mutasi_gudang mg ON sm.mutasi_id = mg.mutasi_id
JOIN tbl_bahan b ON sm.bahan_id = b.bahan_id
WHERE sm.bahan_id = 1
ORDER BY sm.created_at DESC;
```

### Performance Optimization

#### 1. Database Indexes
```sql
-- Add indexes for better performance
ALTER TABLE mutasi_gudang ADD INDEX idx_tanggal (tanggal_mutasi);
ALTER TABLE mutasi_gudang ADD INDEX idx_jenis_mutasi (jenis_mutasi);
ALTER TABLE mutasi_gudang ADD INDEX idx_bahan (bahan_id);
ALTER TABLE stock_movements ADD INDEX idx_bahan_created (bahan_id, created_at);
```

#### 2. Query Optimization
- Use proper JOINs instead of subqueries
- Limit result sets with pagination
- Use indexes for frequently queried columns

## Future Enhancements

### Planned Features
1. **Barcode Integration**: QR code generation and scanning
2. **Mobile App**: Warehouse management mobile application
3. **Real-time Notifications**: Stock alerts and mutation notifications
4. **Advanced Analytics**: Predictive stock management
5. **Integration APIs**: REST API for external systems
6. **Multi-currency Support**: Cost tracking in different currencies
7. **Advanced Reporting**: Custom report builder
8. **Workflow Management**: Approval workflows for mutations

### Technical Improvements
1. **Caching**: Redis/Memcached for better performance
2. **Queue System**: Background job processing
3. **Microservices**: Service-oriented architecture
4. **Containerization**: Docker deployment
5. **Monitoring**: Application performance monitoring

## Support & Maintenance

### Regular Maintenance Tasks
1. **Database Backup**: Daily automated backups
2. **Log Rotation**: Regular log file management
3. **Performance Monitoring**: Database and application monitoring
4. **Security Updates**: Regular security patches
5. **Data Archiving**: Archive old mutation data

### Contact Information
- **Developer**: TGM ERP Development Team
- **Email**: support@tgm-erp.com
- **Documentation**: This README file
- **Version**: 1.0.0

## License
This warehouse management system is proprietary software developed for TGM ERP. All rights reserved.
