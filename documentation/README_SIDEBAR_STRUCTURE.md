# Sidebar Structure Documentation

## Overview
Sidebar telah dipisahkan menjadi file-file terpisah untuk organisasi kode yang lebih baik, maintainability, dan performa.

## File Structure

### CSS Files
```
assets/css/
├── sidebar.css              # Main sidebar styles
└── sidebar-animations.css   # Animations and effects
```

### JavaScript Files
```
assets/js/
└── sidebar.js              # Sidebar functionality
```

## File Descriptions

### 1. `sidebar.css`
**Location:** `assets/css/sidebar.css`

**Purpose:** Main styling untuk sidebar

**Features:**
- Layout dan positioning
- Responsive design
- Scrollbar styling
- Basic hover effects
- Mobile navigation

**Key Classes:**
- `.app-sidebar` - Main sidebar container
- `.sidebar-brand` - Brand/logo section
- `.nav-link` - Menu item links
- `.nav-icon` - Menu icons
- `.access-denied` - Restricted menu items

### 2. `sidebar-animations.css`
**Location:** `assets/css/sidebar-animations.css`

**Purpose:** Animations dan visual effects

**Features:**
- Fade-in animations untuk menu items
- Hover effects dengan gradient
- Loading state animations
- Access denied shake animation
- Brand logo animations
- Accessibility support

**Key Animations:**
- `fadeInUp` - Menu items appear from bottom
- `spin` - Loading icon rotation
- `shake` - Access denied feedback
- `pulse` - Active menu highlighting
- `slideInLeft` - Sidebar entrance

### 3. `sidebar.js`
**Location:** `assets/js/sidebar.js`

**Purpose:** JavaScript functionality untuk sidebar

**Features:**
- Access control functions
- Mobile sidebar toggle
- Active menu highlighting
- Loading states
- Smooth scrolling
- Event handlers

**Key Functions:**
- `showAccessDenied()` - Display access restriction message
- `toggleSidebar()` - Mobile sidebar toggle
- `highlightActiveMenu()` - Mark current page
- `addLoadingState()` - Show loading feedback
- `handleMenuClick()` - Menu click handling

## Integration

### Head Include
```html
<!-- In layout_admin/head.php -->
<link rel="stylesheet" href="<?= base_url('assets/css/sidebar.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/sidebar-animations.css') ?>">
```

### Footer Include
```html
<!-- In layout_admin/footer.php -->
<script src="<?= base_url('assets/js/sidebar.js') ?>"></script>
```

## Access Control

### Level Permissions
- **Level 1 (Administrator)**: Full access
- **Level 6 (Keuangan)**: Full access  
- **Level 17 (Owner)**: Full access
- **Other Levels**: Restricted access with visual feedback

### Visual Indicators
- **Full Access**: Normal menu appearance
- **Restricted Access**: 
  - Opacity reduced (0.6)
  - Label shows "(Admin/Keuangan/Owner)"
  - Click shows access denied message
  - Shake animation on hover

## Responsive Design

### Desktop (>768px)
- Sidebar always visible
- Full width sidebar (250px)
- Main content margin-left: 250px

### Mobile (≤768px)
- Sidebar hidden by default
- Toggle button for show/hide
- Overlay background
- Smooth slide animations

## Accessibility Features

### Keyboard Navigation
- Focus states with blue outline
- Tab navigation support
- Enter key activation

### Screen Reader Support
- Proper ARIA labels
- Semantic HTML structure
- Descriptive link text

### Reduced Motion
- Respects `prefers-reduced-motion`
- Disables animations for accessibility
- Maintains functionality

### High Contrast
- Supports `prefers-contrast: high`
- Enhanced borders and contrast
- Maintains readability

## Performance Optimizations

### CSS Optimizations
- Efficient selectors
- Hardware-accelerated animations
- Minimal repaints/reflows
- Optimized transitions

### JavaScript Optimizations
- Event delegation
- Debounced resize handlers
- Efficient DOM queries
- Memory leak prevention

## Browser Support

### Modern Browsers
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

### Features Used
- CSS Grid/Flexbox
- CSS Custom Properties
- ES6+ JavaScript
- CSS Animations

## Maintenance

### Adding New Menu Items
1. Add HTML structure in `sidebar.php`
2. CSS styling automatically applies
3. JavaScript functionality auto-includes

### Modifying Access Control
1. Update level checks in `sidebar.php`
2. Modify `showAccessDenied()` message
3. Update visual indicators

### Customizing Animations
1. Edit `sidebar-animations.css`
2. Add new keyframes as needed
3. Test with reduced motion

## Troubleshooting

### Common Issues

#### Menu Not Highlighting
- Check `highlightActiveMenu()` function
- Verify URL matching logic
- Ensure proper class names

#### Animations Not Working
- Check CSS file inclusion
- Verify browser support
- Test with reduced motion disabled

#### Mobile Sidebar Issues
- Check toggle button creation
- Verify overlay functionality
- Test touch events

### Debug Mode
Enable debug logging in `sidebar.js`:
```javascript
const DEBUG = true;
if (DEBUG) console.log('Sidebar debug info');
```

## Future Enhancements

### Planned Features
- Dark/Light theme toggle
- Collapsible sidebar sections
- Search functionality
- Custom menu ordering
- Multi-level navigation

### Performance Improvements
- CSS-in-JS for dynamic styles
- Virtual scrolling for large menus
- Service worker caching
- Lazy loading for icons

## Contributing

### Code Style
- Use consistent indentation (2 spaces)
- Follow BEM naming convention
- Add comments for complex logic
- Maintain accessibility standards

### Testing
- Test on multiple browsers
- Verify mobile responsiveness
- Check accessibility compliance
- Performance testing

---

**Last Updated:** January 2025
**Version:** 1.0.0
**Author:** TGM ERP Development Team

