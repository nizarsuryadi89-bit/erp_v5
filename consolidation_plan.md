# Controller Consolidation Plan

## Objective

Reduce the number of controllers by merging smaller/redundant controllers into main functional controllers.

- **Done**: `Pembelian.php` -> `Warehouse.php`
- **Done**: `Settings.php` -> `Pengaturan.php`
- **Done**: `Penjualan_manual.php` -> `Penjualan.php`

## Changes Executed

### 1. Settings Migration

- **Source**: `Settings.php` (Deleted)
- **Destination**: `Pengaturan.php`
- **Actions**:
  - Moved `reset_dummy_data` method to `Pengaturan.php`.
  - Merged dashboard UI components logic.
  - Deleted `application/controllers/Settings.php`.
  - Deleted `application/views/admin/settings/`.
  - Updated `admin/pengaturan/index.php` to include System Reset UI.
  - Updated `admin/dashboard/hrd_summary.php` (fixed broken `settings/branch` link).

### 2. Manual Sales Migration

- **Source**: `Penjualan_manual.php` (Deleted)
- **Destination**: `Penjualan.php`
- **Actions**:
  - Moved `index` (as `create`), `simpan`, `cetak_invoice`, and helper methods to `Penjualan.php`.
  - Moved `manual_list`, `manual_detail`, `manual_delete`, `manual_export` from `Transaksi.php` logic (replicated/adapted) to `Penjualan.php` (to fully own Manual Sales).
  - Deleted `application/controllers/Penjualan_manual.php`.
  - Updated `admin/transaksi/manual.php` links to point to `Penjualan` controller.
  - Updated `admin/transaksi/manual_list.php` links to point to `Penjualan` controller.

### 3. Purchase Migration (Previous Session)

- **Source**: `Pembelian.php` (Deleted)
- **Destination**: `Warehouse.php`
- **Actions**:
  - Moved methods `add_barang_masuk`, `action_add_barang_masuk`, `opname`, `syncronisasi_stok`, `belanjabahan` to `Warehouse.php`.
  - Updated related routes (implicitly handled via method movement, though explicit route check is recommended).

## Next Steps

- Verify all manual transaction flows (Create -> Save -> Invoice -> List -> Detail -> Delete).
- Verify System Reset functionality in `Pengaturan` dashboard.
- Verify `Warehouse` purchase flows.
