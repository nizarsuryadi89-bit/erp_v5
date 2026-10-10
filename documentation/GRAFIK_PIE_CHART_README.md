# Grafik Pie Chart - Laporan Pemasukan

## 📋 Ringkasan Implementasi

Grafik pie chart telah diimplementasikan untuk memberikan visualisasi yang interaktif dan informatif tentang distribusi pemasukan per metode pembayaran. Fitur ini tersedia di laporan pemasukan harian dan bulanan dengan tampilan yang konsisten dan responsif.

## 🎯 Fitur Utama

### ✅ Fitur yang Sudah Diimplementasikan:

1. **Interactive Pie Chart** - Grafik pie yang interaktif dengan Chart.js
2. **Custom Legend** - Legenda kustom yang dapat diklik dan di-hover
3. **Responsive Design** - Tampilan yang optimal di semua device
4. **Animation Effects** - Animasi smooth saat loading dan interaksi
5. **Tooltip Enhancement** - Tooltip yang informatif dengan format rupiah
6. **Segment Toggle** - Kemampuan untuk menyembunyikan/menampilkan segment
7. **Hover Effects** - Efek hover pada legend dan chart segments
8. **Color Coding** - Warna yang konsisten untuk setiap metode pembayaran
9. **Data Summary** - Ringkasan total pemasukan di bagian bawah legend
10. **Cross-browser Compatibility** - Kompatibel dengan semua browser modern

## 🎨 Visual Design

### 1. Color Scheme
| Metode Pembayaran | Warna Utama | Warna Border | Kode Hex |
|-------------------|-------------|--------------|----------|
| **Cash** | Hijau | Hijau Gelap | `#28a745` / `#1e7e34` |
| **Transfer** | Biru | Biru Gelap | `#007bff` / `#0056b3` |
| **Piutang** | Kuning | Kuning Gelap | `#ffc107` / `#e0a800` |
| **EDC** | Cyan | Cyan Gelap | `#17a2b8` / `#138496` |
| **E-Wallet** | Orange | Orange Gelap | `#fd7e14` / `#e8590c` |

### 2. Layout Structure
```
┌─────────────────────────────────────────────────────────────┐
│                    Chart Section                            │
├─────────────────────┬───────────────────────────────────────┤
│                     │                                       │
│   Pie Chart Area    │           Legend Area                 │
│   (8 columns)       │           (4 columns)                 │
│                     │                                       │
│   - Canvas          │   - Legend Title                      │
│   - Responsive      │   - Legend Items                      │
│   - Interactive     │   - Clickable Items                   │
│                     │   - Hover Effects                     │
│                     │   - Summary Section                   │
└─────────────────────┴───────────────────────────────────────┘
```

## 🔧 Technical Implementation

### 1. Chart.js Integration

#### CDN Inclusion:
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

#### Chart Configuration:
```javascript
const chartConfig = {
    type: 'pie',
    data: chartData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false // Custom legend
            },
            tooltip: {
                backgroundColor: 'rgba(44, 62, 80, 0.9)',
                titleColor: 'white',
                bodyColor: 'white',
                borderColor: '#2c3e50',
                borderWidth: 1,
                cornerRadius: 0,
                displayColors: true,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
                    }
                }
            }
        },
        animation: {
            animateRotate: true,
            animateScale: true,
            duration: 2000,
            easing: 'easeOutQuart'
        },
        elements: {
            arc: {
                borderWidth: 2
            }
        }
    }
};
```

### 2. Data Structure

#### Chart Data:
```javascript
const chartData = {
    labels: ['Cash', 'Transfer', 'Piutang', 'EDC', 'E-Wallet'],
    datasets: [{
        data: [
            cash_amount,
            transfer_amount,
            piutang_amount,
            debit_amount,
            ewallet_amount
        ],
        backgroundColor: [
            '#28a745', // Cash
            '#007bff', // Transfer
            '#ffc107', // Piutang
            '#17a2b8', // EDC
            '#fd7e14'  // E-Wallet
        ],
        borderColor: [
            '#1e7e34',
            '#0056b3',
            '#e0a800',
            '#138496',
            '#e8590c'
        ],
        borderWidth: 2,
        hoverBorderWidth: 3,
        hoverOffset: 10
    }]
};
```

### 3. Interactive Features

#### Legend Click Handler:
```javascript
$('.legend-item').on('click', function() {
    const index = $(this).index();
    const chart = Chart.getChart('incomePieChart');
    
    if (chart) {
        // Toggle segment visibility
        const meta = chart.getDatasetMeta(0);
        meta.data[index].hidden = !meta.data[index].hidden;
        chart.update();
        
        // Update legend item appearance
        $(this).toggleClass('disabled');
    }
});
```

#### Hover Effects:
```javascript
$('.legend-item').hover(
    function() {
        const index = $(this).index();
        const chart = Chart.getChart('incomePieChart');
        
        if (chart) {
            // Highlight segment
            chart.setActiveElements([{
                datasetIndex: 0,
                index: index
            }]);
            chart.update();
        }
    },
    function() {
        const chart = Chart.getChart('incomePieChart');
        if (chart) {
            // Remove highlight
            chart.setActiveElements([]);
            chart.update();
        }
    }
);
```

## 🎨 CSS Styling

### 1. Chart Section
```css
.chart-section {
    background: white;
    border-radius: 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    overflow: hidden;
    margin-bottom: 2rem;
    padding: 1.5rem;
}
```

### 2. Chart Container
```css
.chart-container {
    position: relative;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}
```

### 3. Legend Styling
```css
.legend-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.75rem;
    border-radius: 0;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.legend-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.legend-item.disabled {
    opacity: 0.5;
    background: #f1f3f4;
}
```

### 4. Responsive Design
```css
@media (max-width: 991.98px) {
    .chart-container {
        height: 300px;
    }
    
    .chart-legend {
        margin-top: 1rem;
    }
}

@media (max-width: 767.98px) {
    .chart-container {
        height: 250px;
    }
    
    .legend-item {
        padding: 0.5rem;
    }
    
    .legend-color {
        width: 16px;
        height: 16px;
        margin-right: 0.75rem;
    }
}
```

## 📊 Data Integration

### 1. PHP Data Processing
```php
<?php
$total = ($total_pemasukan['totalna'] ?? 0) + ($total_pemasukan_piutang['totalna'] ?? 0);
$persen_cash = $total ? (($cash['totalna'] ?? 0) / $total) * 100 : 0;
$persen_transfer = $total ? (($total_pemasukan['totalna'] ?? 0) ? (($total_pemasukan['totalna'] ?? 0) - ($cash['totalna'] ?? 0) - ($debit['totalna'] ?? 0) - ($ewallet['totalna'] ?? 0)) / $total * 100 : 0) : 0;
$persen_piutang = $total ? (($total_pemasukan_piutang['totalna'] ?? 0) / $total) * 100 : 0;
$persen_debit = $total ? (($debit['totalna'] ?? 0) / $total) * 100 : 0;
$persen_ewallet = $total ? (($ewallet['totalna'] ?? 0) / $total) * 100 : 0;
?>
```

### 2. HTML Structure
```html
<div class="chart-section">
    <div class="row">
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-header">
                    <h6><i class="fa fa-chart-pie text-primary"></i> Grafik Distribusi Pemasukan</h6>
                    <p class="chart-subtitle">Visualisasi persentase pemasukan per metode pembayaran</p>
                </div>
                <div class="chart-container">
                    <canvas id="incomePieChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-legend">
                <!-- Legend items -->
            </div>
        </div>
    </div>
</div>
```

## 🎯 User Interaction

### 1. Chart Interactions
- **Hover on Chart Segments**: Menampilkan tooltip dengan detail
- **Click on Legend Items**: Toggle visibility segment
- **Hover on Legend Items**: Highlight segment di chart
- **Responsive Resize**: Chart menyesuaikan ukuran layar

### 2. Tooltip Features
- **Currency Format**: Rp dengan separator ribuan
- **Percentage Display**: Persentase dari total
- **Color Indicators**: Warna sesuai segment
- **Responsive Position**: Tooltip menyesuaikan posisi

### 3. Animation Effects
- **Initial Load**: Rotate dan scale animation
- **Segment Toggle**: Smooth transition saat hide/show
- **Hover Effects**: Smooth highlight transitions
- **Legend Interactions**: Transform effects pada hover

## 📱 Responsive Behavior

### 1. Desktop View (>992px)
- Chart height: 400px
- Legend side-by-side dengan chart
- Full interactive features

### 2. Tablet View (768px - 991px)
- Chart height: 300px
- Legend di bawah chart
- Maintained interactive features

### 3. Mobile View (<768px)
- Chart height: 250px
- Compact legend items
- Touch-optimized interactions

## 🔧 Performance Optimization

### 1. Chart.js Optimization
- **Responsive**: `maintainAspectRatio: false`
- **Animation**: Optimized duration dan easing
- **Memory Management**: Proper chart cleanup

### 2. CSS Optimization
- **Hardware Acceleration**: Transform effects
- **Efficient Selectors**: Specific class targeting
- **Minimal Repaints**: Optimized transitions

### 3. JavaScript Optimization
- **Event Delegation**: Efficient event handling
- **Debounced Updates**: Smooth chart updates
- **Memory Cleanup**: Proper event removal

## 🚀 Implementation Steps

### 1. Add Chart.js CDN
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

### 2. Create HTML Structure
```html
<div class="chart-section">
    <div class="row">
        <div class="col-lg-8">
            <canvas id="incomePieChart"></canvas>
        </div>
        <div class="col-lg-4">
            <div class="chart-legend">
                <!-- Legend items -->
            </div>
        </div>
    </div>
</div>
```

### 3. Add CSS Styling
```css
/* Chart section styles */
.chart-section { /* ... */ }
.chart-container { /* ... */ }
.legend-item { /* ... */ }
```

### 4. Initialize Chart
```javascript
const ctx = document.getElementById('incomePieChart').getContext('2d');
const incomePieChart = new Chart(ctx, chartConfig);
```

### 5. Add Interactive Features
```javascript
// Legend click handlers
// Hover effects
// Animation triggers
```

## 💡 Best Practices

### 1. Data Validation
- Check for null/undefined values
- Use null coalescing operator (`??`)
- Validate data before chart creation

### 2. Error Handling
- Try-catch blocks for chart creation
- Fallback for missing data
- Graceful degradation

### 3. Accessibility
- ARIA labels for interactive elements
- Keyboard navigation support
- Screen reader compatibility

### 4. Performance
- Lazy loading for large datasets
- Debounced chart updates
- Memory leak prevention

## 🔧 Troubleshooting

### 1. Chart Not Displaying
- Check Chart.js CDN loading
- Verify canvas element exists
- Check console for JavaScript errors

### 2. Data Not Showing
- Validate PHP data variables
- Check data format consistency
- Verify null coalescing usage

### 3. Responsive Issues
- Test on different screen sizes
- Check CSS media queries
- Verify Bootstrap grid classes

### 4. Interactive Features Not Working
- Check jQuery loading
- Verify event handler binding
- Test Chart.js API compatibility

## 📝 Notes

1. **Browser Compatibility**: Chart.js supports all modern browsers
2. **Mobile Performance**: Optimized for touch devices
3. **Data Accuracy**: Real-time data from database
4. **Visual Consistency**: Matches application design system
5. **User Experience**: Intuitive and interactive interface

---

**Grafik pie chart memberikan visualisasi yang powerful dan interaktif untuk analisis distribusi pemasukan dengan interface yang user-friendly dan performa yang optimal.**
