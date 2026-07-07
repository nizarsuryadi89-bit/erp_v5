# Pie Chart Troubleshooting Guide

## 📋 Masalah: Grafik Pie Tidak Tampil

### 🔍 **Diagnosis Masalah**

#### **1. Debug Console Messages**
Buka Developer Tools (F12) dan periksa Console untuk melihat pesan debug:

```javascript
// Debug messages yang akan muncul:
console.log('Chart.js loaded:', typeof Chart !== 'undefined' ? 'Yes' : 'No');
console.log('Canvas element:', canvas);
console.log('Canvas context:', ctx);
console.log('Chart data:', chartData);
console.log('Pie chart created successfully');
```

#### **2. Kemungkinan Penyebab**

##### **A. Chart.js Tidak Dimuat**
```javascript
// Error: "Chart is not defined"
// Solusi: Pastikan CDN Chart.js dimuat dengan benar
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

##### **B. Canvas Element Tidak Ditemukan**
```javascript
// Error: "Canvas element not found"
// Solusi: Pastikan HTML canvas ada dan ID benar
<canvas id="incomePieChart" width="400" height="400"></canvas>
```

##### **C. Data Kosong atau Null**
```javascript
// Error: "Chart data is empty"
// Solusi: Periksa data dari controller
console.log('Chart data:', chartData);
```

##### **D. Timing Issue (DOM belum siap)**
```javascript
// Error: "Canvas context is null"
// Solusi: Gunakan setTimeout untuk delay
setTimeout(function() {
    // Chart initialization
}, 1000);
```

### 🔧 **Solusi yang Diterapkan**

#### **1. Enhanced Error Handling**
```javascript
// Sebelum
const ctx = document.getElementById('incomePieChart').getContext('2d');

// Sesudah
const canvas = document.getElementById('incomePieChart');
if (canvas && typeof Chart !== 'undefined') {
    const ctx = canvas.getContext('2d');
    // Chart creation
} else {
    console.error('Canvas element not found or Chart.js not loaded');
}
```

#### **2. Delayed Initialization**
```javascript
// Menambahkan delay untuk memastikan DOM siap
setTimeout(function() {
    // Chart initialization
}, 1000);
```

#### **3. Comprehensive Debugging**
```javascript
// Debug Chart.js loading
console.log('Chart.js loaded:', typeof Chart !== 'undefined' ? 'Yes' : 'No');

// Debug canvas element
console.log('Canvas element:', canvas);

// Debug canvas context
console.log('Canvas context:', ctx);

// Debug chart data
console.log('Chart data:', chartData);
```

#### **4. Try-Catch Error Handling**
```javascript
try {
    const incomePieChart = new Chart(ctx, chartConfig);
    console.log('Pie chart created successfully');
} catch (error) {
    console.error('Error creating pie chart:', error);
}
```

### 📊 **Struktur Data Chart**

#### **Data yang Diharapkan**
```javascript
const chartData = {
    labels: ['Cash', 'Transfer', 'Piutang', 'EDC', 'E-Wallet'],
    datasets: [{
        data: [
            <?= $cash['totalna'] ?? 0 ?>,           // Cash amount
            <?= $transfer['totalna'] ?? 0 ?>,       // Transfer amount
            <?= $total_pemasukan_piutang['totalna'] ?? 0 ?>, // Piutang amount
            <?= $debit['totalna'] ?? 0 ?>,          // EDC amount
            <?= $ewallet['totalna'] ?? 0 ?>         // E-Wallet amount
        ],
        backgroundColor: [
            '#28a745', // Cash - Green
            '#007bff', // Transfer - Blue
            '#ffc107', // Piutang - Yellow
            '#17a2b8', // EDC - Cyan
            '#fd7e14'  // E-Wallet - Orange
        ]
    }]
};
```

#### **Controller Data yang Diperlukan**
```php
// Data yang harus tersedia dari controller
$cash = ['totalna' => 1000000];
$transfer = ['totalna' => 500000];
$total_pemasukan_piutang = ['totalna' => 200000];
$debit = ['totalna' => 300000];
$ewallet = ['totalna' => 150000];
```

### 🎯 **Konfigurasi Chart**

#### **Chart Configuration**
```javascript
const chartConfig = {
    type: 'pie',
    data: chartData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false // Custom legend digunakan
            },
            tooltip: {
                backgroundColor: 'rgba(44, 62, 80, 0.9)',
                titleColor: 'white',
                bodyColor: 'white',
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
            duration: 2000
        }
    }
};
```

### 🔍 **Checklist Troubleshooting**

#### **1. Browser Console Check**
- [ ] Buka Developer Tools (F12)
- [ ] Periksa Console tab
- [ ] Cari pesan error atau debug
- [ ] Pastikan tidak ada JavaScript errors

#### **2. Network Check**
- [ ] Periksa Network tab di Developer Tools
- [ ] Pastikan Chart.js CDN dimuat dengan sukses
- [ ] Status code harus 200 OK

#### **3. HTML Structure Check**
- [ ] Pastikan canvas element ada
- [ ] ID harus `incomePieChart`
- [ ] Width dan height harus di-set

#### **4. Data Check**
- [ ] Periksa data dari controller
- [ ] Pastikan tidak ada null values
- [ ] Data harus berupa angka

#### **5. CSS Check**
- [ ] Pastikan chart container visible
- [ ] Height container harus cukup
- [ ] Tidak ada CSS yang hide chart

### 🚀 **Solusi Cepat**

#### **Jika Chart Masih Tidak Muncul:**

1. **Refresh halaman dan buka Console**
2. **Periksa pesan debug**
3. **Pastikan data tidak kosong**
4. **Coba browser berbeda**
5. **Clear browser cache**

#### **Fallback Solution**
```javascript
// Jika chart gagal, tampilkan pesan
if (!canvas) {
    document.querySelector('.chart-container').innerHTML = 
        '<div class="alert alert-warning">Grafik tidak dapat ditampilkan. Silakan refresh halaman.</div>';
}
```

### 📝 **Log Debug yang Diharapkan**

#### **Success Case:**
```
Chart.js loaded: Yes
Canvas element: <canvas id="incomePieChart" width="400" height="400">
Canvas context: CanvasRenderingContext2D
Chart data: {labels: Array(5), datasets: Array(1)}
Pie chart created successfully
```

#### **Error Case:**
```
Chart.js loaded: No
Canvas element not found or Chart.js not loaded
Error creating pie chart: [error details]
```

---

**Dengan troubleshooting ini, grafik pie seharusnya dapat ditampilkan dengan benar. Jika masih ada masalah, periksa console untuk pesan error spesifik.**
