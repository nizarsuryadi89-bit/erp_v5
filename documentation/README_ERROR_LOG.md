# Error Log Controller

A comprehensive error log management system for the TGM ERP application.

## Features

### 🔍 **Error Log Monitoring**
- View all application error logs in a centralized dashboard
- Real-time error tracking and monitoring
- Comprehensive error statistics and analytics

### 📊 **Advanced Filtering & Search**
- Filter by date range
- Filter by error severity (Error, Warning, Info, Debug)
- Search through error messages
- Pagination support for large log files

### 📈 **Statistics & Analytics**
- Error distribution by severity
- Error trends over time (last 30 days)
- Recent errors summary (last 7 days)
- Interactive charts and graphs

### 🛠 **Management Tools**
- Download individual log files
- Clear all log files (with confirmation)
- Detailed error information viewing
- Export functionality

## Controller Structure

### Main Controller: `ErrorLog.php`

**Location:** `application/controllers/ErrorLog.php`

**Key Methods:**
- `index()` - Main dashboard with filtering and pagination
- `view($filename)` - Detailed view of specific log file
- `download($filename)` - Download log file
- `clear()` - Clear all log files
- `statistics()` - Error statistics and analytics

## Views

### 1. Main Dashboard (`index.php`)
**Location:** `application/views/admin/errorlog/index.php`

**Features:**
- Summary cards showing total errors, critical errors, warnings, and log files
- Advanced filtering options (date, severity, search)
- Paginated error log table
- Quick actions (view details, download)
- Clear logs functionality

### 2. Detailed View (`view.php`)
**Location:** `application/views/admin/errorlog/view.php`

**Features:**
- Detailed view of specific log file
- Error statistics for the file
- Complete error log table
- Download functionality

### 3. Statistics (`statistics.php`)
**Location:** `application/views/admin/errorlog/statistics.php`

**Features:**
- Error distribution charts
- Time-based error trends
- Recent errors summary
- Interactive visualizations

## Usage

### Accessing Error Logs

1. **Navigate to Error Logs:**
   ```
   http://your-domain/errorlog
   ```

2. **View Specific Log File:**
   ```
   http://your-domain/errorlog/view/filename.php
   ```

3. **Download Log File:**
   ```
   http://your-domain/errorlog/download/filename.php
   ```

4. **View Statistics:**
   ```
   http://your-domain/errorlog/statistics
   ```

### Filtering Options

#### Date Filter
- Select specific date to view errors from that day only
- Format: YYYY-MM-DD

#### Severity Filter
- **Error:** Critical system errors
- **Warning:** System warnings
- **Info:** Informational messages
- **Debug:** Debug information

#### Search Filter
- Search through error messages
- Case-insensitive search
- Partial matching supported

## Security

### Access Control
- Only users with `level = 1` (administrators) can access error logs
- Automatic redirect to dashboard for unauthorized users
- Session-based authentication required

### Data Protection
- Error logs are read-only for viewing
- Clear logs functionality requires confirmation
- Download functionality restricted to administrators

## Error Log Format

The controller parses error logs in the following format:

```
ERROR - 2025-08-09 06:47:10 --> Severity: error --> Exception: syntax error, unexpected '}' C:\xampp\htdocs\tgm_erp\application\views\admin\transaksi\produksi_product.php 150
```

**Components:**
- **Timestamp:** Date and time of the error
- **Severity:** Error severity level
- **Message:** Detailed error message

## Configuration

### Pagination Settings
- Default: 50 errors per page
- Configurable in controller
- Bootstrap-styled pagination

### Chart Configuration
- Uses Chart.js for visualizations
- Responsive design
- Interactive charts

## Dependencies

### Required Libraries
- CodeIgniter 3.x
- Bootstrap 5.x
- Chart.js (for statistics)
- Font Awesome (for icons)

### PHP Extensions
- File system access
- Regular expressions
- Date/time functions

## Troubleshooting

### Common Issues

1. **Permission Denied**
   - Ensure the `application/logs/` directory is readable
   - Check file permissions (755 for directories, 644 for files)

2. **No Logs Displayed**
   - Verify log files exist in `application/logs/`
   - Check log file format matches expected pattern
   - Ensure log files have `.php` extension

3. **Charts Not Loading**
   - Verify Chart.js is loaded
   - Check browser console for JavaScript errors
   - Ensure internet connection for CDN resources

### Error Handling

The controller includes comprehensive error handling:
- File not found errors
- Permission errors
- Invalid log format errors
- Database connection errors

## Maintenance

### Regular Tasks
1. **Monitor Error Logs:** Check for critical errors daily
2. **Clear Old Logs:** Periodically clear old log files
3. **Update Statistics:** Review error trends weekly
4. **Backup Logs:** Backup important log files before clearing

### Performance Considerations
- Large log files may impact performance
- Consider implementing log rotation
- Monitor disk space usage
- Optimize queries for large datasets

## Future Enhancements

### Planned Features
- Email notifications for critical errors
- Real-time error monitoring
- Error categorization and tagging
- Automated error reporting
- Integration with external monitoring services

### Customization Options
- Configurable log retention periods
- Custom error severity levels
- User-defined error categories
- Export to various formats (CSV, JSON, XML)

## Support

For issues or questions regarding the Error Log Controller:

1. Check the troubleshooting section
2. Review error logs for specific issues
3. Contact system administrator
4. Refer to CodeIgniter documentation

## License

This error log controller is part of the TGM ERP system and follows the same licensing terms.
