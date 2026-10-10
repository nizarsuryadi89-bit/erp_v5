# Material Replacement and Print Location Feature Implementation

## Overview
This feature allows users to:
1. Replace the default material from the BOM (Bill of Materials) with available materials from the `tbl_bahan` table during production processes
2. Specify the print location (`lokasi_cetak`) to determine which warehouse stock will be reduced during stock updates

## What Has Been Implemented

### 1. Frontend Changes
- Added material selection dropdown in both Production Process Modal and Stock Update Modal
- Added print location selection dropdown in Stock Update Modal
- Added current material display showing the default BOM material
- Added JavaScript handlers for material and print location selection changes
- Added validation and user feedback for selections

### 2. Backend Changes
- Updated `Wip.php` controller to load `tbl_bahan` data
- Added validation for `bahanbaku_id` and `lokasi_cetak` fields
- Added handling for material selection in both `update_production_process()` and `complete_process()` methods
- Updated warehouse stock reduction logic to use selected material and print location

### 3. Database Changes Required
- **IMPORTANT**: The `detail_produksi` table needs two new fields:
  - `bahanbaku_id` - for material selection
  - `lokasi_cetak` - for print location selection

## Database Setup Required

### Step 1: Run the SQL Script
Execute the provided `add_bahanbaku_id_field.sql` script in your MySQL database:

```sql
-- Add bahanbaku_id field to detail_produksi table
ALTER TABLE `detail_produksi` 
ADD COLUMN `bahanbaku_id` INT(11) NULL DEFAULT NULL AFTER `lokasi_proses`,
ADD INDEX `idx_bahanbaku_id` (`bahanbaku_id`);

-- Add lokasi_cetak field to detail_produksi table
ALTER TABLE `detail_produksi` 
ADD COLUMN `lokasi_cetak` INT(11) NULL DEFAULT NULL AFTER `bahanbaku_id`,
ADD INDEX `idx_lokasi_cetak` (`lokasi_cetak`);

-- Add comments to the fields
ALTER TABLE `detail_produksi` 
MODIFY COLUMN `bahanbaku_id` INT(11) NULL DEFAULT NULL COMMENT 'ID bahan baku yang digunakan (jika berbeda dari BOM default)';

ALTER TABLE `detail_produksi` 
MODIFY COLUMN `lokasi_cetak` INT(11) NULL DEFAULT NULL COMMENT 'ID lokasi cetak untuk menentukan gudang yang akan dikurangi stoknya';
```

### Step 2: Verify the Changes
```sql
DESCRIBE `detail_produksi`;
```

You should see the new `bahanbaku_id` and `lokasi_cetak` fields in the table structure.

## How It Works

### 1. Production Process Modal
- **Material Selection**: Users can select a different material from `tbl_bahan` or use the default BOM material
- **Current Material Display**: Shows what material is currently being used
- **Validation**: Material selection is optional but properly validated

### 2. Stock Update Modal
- **Material Selection**: Same functionality as production process modal
- **Print Location Selection**: Required field to specify which warehouse location will have stock reduced
- **Stock Reduction Logic**: Uses selected material and print location to determine which warehouse stock to reduce

### 3. Default Behavior
- When no custom material is selected (`bahanbaku_id` is NULL or empty), the system uses the default material from the BOM
- When no print location is specified, the system may use a default location (if available)

### 4. Material Replacement
- Users can select a different material from the `tbl_bahan` dropdown
- When a custom material is selected, it's stored in the `bahanbaku_id` field
- The system will use this custom material instead of the BOM default for stock calculations

### 5. Print Location for Stock Management
- Users must select a print location when updating stock
- This location determines which warehouse (`gudang_id`) will have its stock reduced
- Stock reduction is calculated based on output quantity and dimensions

## User Interface

### Production Process Modal
- **"Gunakan Bahan Default"** option (value = 0): Uses the original BOM material
- **Custom material selection**: Replaces the BOM material with the selected raw material
- **Current material display**: Shows what material is currently being used

### Stock Update Modal
- **Material selection**: Same options as production process modal
- **Print location selection**: Required dropdown populated from `tbl_perusahaan`
- **Stock update information**: Clear indication of which warehouse stock will be affected

## Files Modified

### Controllers
- `application/controllers/Wip.php`
  - Added `tbl_bahan` data loading in `detail()` method
  - Added validation for `bahanbaku_id` and `lokasi_cetak` in both methods
  - Added handling for material and print location selection
  - Updated warehouse stock reduction logic

### Views
- `application/views/admin/wip/wip_detail.php`
  - Added material selection dropdowns to both modals
  - Added print location dropdown to stock update modal
  - Added current material displays
  - Added JavaScript handlers for all selections

### Database
- `add_bahanbaku_id_field.sql` - SQL script to add both required fields

## Usage Instructions

### For Production Staff
1. **Production Process Modal**:
   - Open the Production Process Modal for any routing step
   - View the current material being used (displayed above the dropdown)
   - Select a different material from the dropdown if needed
   - Choose "Gunakan Bahan Default" to use the original BOM material
   - Save the production process

2. **Stock Update Modal**:
   - Open the Stock Update Modal for completed processes
   - Select the material to be used (default or custom)
   - **Required**: Select the print location to determine warehouse stock reduction
   - Fill in output quantities and dimensions
   - Complete the stock update

### For Administrators
1. Ensure the database fields have been added
2. Verify that `tbl_bahan` table contains the required materials
3. Verify that `tbl_perusahaan` table contains the required locations
4. Test the functionality with sample production and stock update processes

## Stock Reduction Logic

### How Stock is Calculated
1. **Material Selection**: Uses selected `bahanbaku_id` if available, otherwise falls back to BOM default
2. **Location Selection**: Uses selected `lokasi_cetak` to determine which warehouse to reduce stock from
3. **Quantity Calculation**: Stock reduction is based on output quantity and dimensions
4. **Warehouse Lookup**: Finds stock record matching material ID and warehouse location
5. **Stock Update**: Reduces the stock and logs the movement

### Stock Reduction Formula
```
Stock Reduction = Output Quantity × (Panjang Output × Lebar Output) × Material Consumption Rate
```

## Validation and Error Handling

### Frontend Validation
- Material selection is optional (not required)
- Print location selection is required for stock updates
- User gets confirmation dialogs for material changes
- Clear feedback about what material and location will be used

### Backend Validation
- `bahanbaku_id` field accepts numeric values only (optional)
- `lokasi_cetak` field accepts numeric values only (required for stock updates)
- No foreign key constraints (for flexibility)
- Proper error logging for debugging

## Future Enhancements

### Potential Improvements
1. **Material Compatibility Check**: Validate that selected materials are compatible with the production process
2. **Stock Validation**: Check if the selected material has sufficient stock at the specified location
3. **Cost Tracking**: Track material costs when custom materials are used
4. **Audit Trail**: Log all material and location changes for compliance purposes
5. **Material Properties**: Display material properties (thickness, quality, etc.) in the dropdown
6. **Location-based Stock Display**: Show available stock for each location when selecting materials

### Database Optimizations
1. **Foreign Key Constraints**: Add referential integrity if needed
2. **Indexing**: Optimize queries involving material and location selection
3. **Material History**: Track material usage over time by location
4. **Stock Movement Tracking**: Enhanced logging of all stock movements

## Troubleshooting

### Common Issues
1. **Field Not Found Error**: Ensure the SQL script has been executed
2. **No Materials in Dropdown**: Check if `tbl_bahan` table has data
3. **No Locations in Dropdown**: Check if `tbl_perusahaan` table has data
4. **Material Not Saving**: Verify the controller is properly handling the fields
5. **Stock Not Reducing**: Check if the selected material and location have matching warehouse stock records

### Debug Steps
1. Check database structure: `DESCRIBE detail_produksi;`
2. Verify data loading: Check if `$bahan_baku` and `$perusahaan` arrays have data
3. Check form submission: Verify the fields are included in the form data
4. Check validation: Ensure no validation errors are blocking the save
5. Check stock reduction logs: Look for debug messages in the application logs

## Support
If you encounter any issues or need assistance with this feature, please refer to the implementation details above or contact the development team.
