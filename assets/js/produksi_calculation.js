/**
 * Production Calculation JavaScript
 * TGM ERP System - Production Form Calculation
 * 
 * FORMULA PERHITUNGAN PRODUKSI:
 * Jumlah Produksi = qty_pesanan × quantity × (panjang × lebar)
 * 
 * LANGKAH PERHITUNGAN:
 * 1. Langkah 1: qty_pesanan × quantity = hasil_sementara
 * 2. Langkah 2: hasil_sementara × (panjang × lebar) = jumlah_produksi
 * 
 * KONDISI:
 * - Jika barang tidak mengandung panjang dan lebar (panjang=0, lebar=0), 
 *   maka panjang dan lebar menjadi 1
 * - Jika barang mengandung panjang dan lebar, gunakan nilai asli
 * 
 * CONTOH:
 * - qty_pesanan=10, quantity=2, panjang=3, lebar=4 
 *   → Langkah 1: 10 × 2 = 20
 *   → Langkah 2: 20 × (3 × 4) = 20 × 12 = 240
 * 
 * - qty_pesanan=10, quantity=2, panjang=0, lebar=0 
 *   → Langkah 1: 10 × 2 = 20
 *   → Langkah 2: 20 × (1 × 1) = 20 × 1 = 20
 */

class ProduksiCalculator {
    constructor() {
        this.init();
    }

    /**
     * Initialize the calculator
     */
    init() {
        console.log('ProduksiCalculator initialized');
        this.setupEventListeners();
        this.performInitialCalculation();
        
        // Ensure calculation runs after DOM is fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOMContentLoaded event fired');
                setTimeout(() => {
                    this.calculateTotal();
                }, 300);
            });
        } else {
            console.log('DOM already loaded, running calculation');
            setTimeout(() => {
                this.calculateTotal();
            }, 300);
        }
    }

    /**
     * Setup event listeners for form inputs
     */
    setupEventListeners() {
        console.log('Setting up event listeners');
        
        // Listen for changes in qty, panjang, and lebar inputs
        const qtyInput = document.querySelector('input[name="qty_produksi"]');
        const panjangInput = document.querySelector('input[name="panjang_produksi"]');
        const lebarInput = document.querySelector('input[name="lebar_produksi"]');
        const qtyPesananInput = document.querySelector('#qty_pesanan');
        const quantityInput = document.querySelector('#quantity');

        console.log('Found elements:', {
            qtyInput: qtyInput,
            panjangInput: panjangInput,
            lebarInput: lebarInput,
            qtyPesananInput: qtyPesananInput,
            quantityInput: quantityInput
        });

        if (qtyInput) {
            qtyInput.addEventListener('input', () => this.calculateTotal());
            console.log('Added event listener to qty_produksi');
        }

        if (panjangInput && panjangInput.type !== 'hidden') {
            panjangInput.addEventListener('input', () => this.calculateTotal());
            console.log('Added event listener to panjang_produksi');
        }

        if (lebarInput && lebarInput.type !== 'hidden') {
            lebarInput.addEventListener('input', () => this.calculateTotal());
            console.log('Added event listener to lebar_produksi');
        }

        // Listen for changes in qty_pesanan and quantity (readonly but might change)
        if (qtyPesananInput) {
            qtyPesananInput.addEventListener('change', () => this.calculateTotal());
            console.log('Added event listener to qty_pesanan');
        }

        if (quantityInput) {
            quantityInput.addEventListener('change', () => this.calculateTotal());
            console.log('Added event listener to quantity');
        }

        // Also listen for changes in readonly panjang and lebar fields
        const readonlyPanjang = document.querySelector('input[value][readonly]');
        const readonlyLebar = document.querySelectorAll('input[value][readonly]')[1];

        if (readonlyPanjang) {
            readonlyPanjang.addEventListener('change', () => this.calculateTotal());
            console.log('Added event listener to readonly panjang');
        }

        if (readonlyLebar) {
            readonlyLebar.addEventListener('change', () => this.calculateTotal());
            console.log('Added event listener to readonly lebar');
        }
    }

    /**
     * Perform initial calculation on page load
     */
    performInitialCalculation() {
        // Wait for all elements to be loaded
        setTimeout(() => {
            this.calculateTotal();
        }, 500); // Increased timeout to ensure all elements are loaded
        
        // Additional calculation after a longer delay to ensure everything is ready
        setTimeout(() => {
            this.calculateTotal();
        }, 1000);
    }

    /**
     * Calculate total based on qty_pesanan × quantity × (panjang × lebar)
     * Formula: Jumlah Produksi = qty_pesanan × quantity × (panjang × lebar)
     * Jika panjang=0 dan lebar=0, maka panjang=1 dan lebar=1
     */
    calculateTotal() {
        console.log('calculateTotal() called');
        
        const qtyInput = document.querySelector('input[name="qty_produksi"]');
        const panjangInput = document.querySelector('input[name="panjang_produksi"]');
        const lebarInput = document.querySelector('input[name="lebar_produksi"]');
        
        // Get qty_pesanan (original qty from database)
        const qtyPesananInput = document.querySelector('#qty_pesanan');
        let qtyPesanan = parseFloat(qtyPesananInput ? qtyPesananInput.value : 0) || 0;
        
        // Get quantity from BOM
        const quantityInput = document.querySelector('#quantity');
        let quantity = parseFloat(quantityInput ? quantityInput.value : 0) || 0;
        
        console.log('Initial values:', {
            qtyPesanan: qtyPesanan,
            quantity: quantity,
            qtyPesananInput: qtyPesananInput,
            quantityInput: quantityInput
        });

        // If values are not available, try to get from data attributes or other sources
        if (qtyPesanan === 0 && qtyPesananInput) {
            // Try to get from the original value attribute
            const originalValue = qtyPesananInput.getAttribute('data-original-value') || qtyPesananInput.defaultValue;
            qtyPesanan = parseFloat(originalValue) || 0;
        }
        
        if (quantity === 0 && quantityInput) {
            // Try to get from the original value attribute
            const originalValue = quantityInput.getAttribute('data-original-value') || quantityInput.defaultValue;
            quantity = parseFloat(originalValue) || 0;
        }

        // Validate that qty_pesanan and quantity are not 0
        if (qtyPesanan === 0) {
            console.warn('qty_pesanan is 0, cannot calculate production quantity');
            return;
        }
        
        if (quantity === 0) {
            console.warn('quantity is 0, cannot calculate production quantity');
            return;
        }
        
        // Get readonly panjang and lebar values
        const readonlyPanjang = document.querySelector('input[value][readonly]');
        const readonlyLebar = document.querySelectorAll('input[value][readonly]')[1];

        let panjang = parseFloat(panjangInput ? panjangInput.value : 0) || 0;
        let lebar = parseFloat(lebarInput ? lebarInput.value : 0) || 0;

        // If input fields are empty and not hidden, use readonly values
        if (panjang === 0 && readonlyPanjang && panjangInput && panjangInput.type !== 'hidden') {
            panjang = parseFloat(readonlyPanjang.value) || 0;
        }
        if (lebar === 0 && readonlyLebar && lebarInput && lebarInput.type !== 'hidden') {
            lebar = parseFloat(readonlyLebar.value) || 0;
        }

        // If input fields have value '0', use that value
        if (panjangInput && panjangInput.value === '0') {
            panjang = 0;
        }
        if (lebarInput && lebarInput.value === '0') {
            lebar = 0;
        }

        // Check if dimensi checkbox is checked
        const dimensiCheckbox = document.getElementById('memiliki_dimensi');
        const dimensiContainer = document.getElementById('dimensi_container');
        
        // Jika checkbox tidak dicentang atau container disembunyikan, gunakan panjang=1 dan lebar=1
        if (!dimensiCheckbox || !dimensiCheckbox.checked || (dimensiContainer && dimensiContainer.classList.contains('d-none'))) {
            panjang = 1;
            lebar = 1;
        }
        // Jika panjang=0 dan lebar=0, maka panjang=1 dan lebar=1
        else if (panjang === 0 && lebar === 0) {
            panjang = 1;
            lebar = 1;
        }

        // Calculate: qty_pesanan × quantity × (panjang × lebar)
        const qtyQuantity = qtyPesanan * quantity; // Langkah 1: qty_pesanan × quantity
        const total = qtyQuantity * (panjang * lebar); // Langkah 2: hasil × (panjang × lebar)
        
        console.log('Calculation steps:', {
            qtyPesanan: qtyPesanan,
            quantity: quantity,
            panjang: panjang,
            lebar: lebar,
            qtyQuantity: qtyQuantity,
            total: total
        });
        
        // Update the qty input with calculated total
        if (qtyInput) {
            qtyInput.value = total.toFixed(2);
            // Trigger change event to ensure form validation works
            qtyInput.dispatchEvent(new Event('change', { bubbles: true }));
            
            console.log('Updated qty_produksi input with value:', total.toFixed(2));
        } else {
            console.error('qty_produksi input not found!');
        }

        // Also update readonly qty_pesanan and quantity if they're empty
        if (qtyPesananInput && !qtyPesananInput.value) {
            qtyPesananInput.value = qtyPesanan.toFixed(2);
        }
        if (quantityInput && !quantityInput.value) {
            quantityInput.value = quantity.toFixed(2);
        }

        // Show calculation info
        this.showCalculationInfo(qtyPesanan, quantity, panjang, lebar, total);
    }

    /**
     * Show calculation information
     */
    showCalculationInfo(qtyPesanan, quantity, panjang, lebar, total) {
        // Remove existing calculation info
        const existingInfo = document.querySelector('.calculation-info');
        if (existingInfo) {
            existingInfo.remove();
        }

        // Create calculation info element
        const calculationInfo = document.createElement('div');
        calculationInfo.className = 'calculation-info alert alert-success mt-2';
        
        // Check if panjang and lebar were originally 0
        const readonlyPanjang = document.querySelector('input[value][readonly]');
        const readonlyLebar = document.querySelectorAll('input[value][readonly]')[1];
        const originalPanjang = parseFloat(readonlyPanjang ? readonlyPanjang.value : 0) || 0;
        const originalLebar = parseFloat(readonlyLebar ? readonlyLebar.value : 0) || 0;
        
        // Check if dimensi checkbox is checked
        const dimensiCheckbox = document.getElementById('memiliki_dimensi');
        const dimensiContainer = document.getElementById('dimensi_container');
        const isDimensiEnabled = dimensiCheckbox && dimensiCheckbox.checked && dimensiContainer && !dimensiContainer.classList.contains('d-none');
        
        if (!isDimensiEnabled) {
            // Dimensi tidak diaktifkan atau checkbox tidak dicentang
            calculationInfo.innerHTML = `
                <i class="bi bi-calculator me-2"></i>
                <strong>Perhitungan Produksi:</strong><br>
                <strong>Langkah 1:</strong> ${qtyPesanan} × ${quantity} = ${qtyQuantity.toFixed(2)}<br>
                <strong>Langkah 2:</strong> ${qtyQuantity.toFixed(2)} × (${panjang} × ${lebar}) = ${qtyQuantity.toFixed(2)} × ${panjang * lebar} = ${total.toFixed(2)} ${this.getSatuanNama()}<br>
                <small class="text-muted">Dimensi tidak diaktifkan, menggunakan panjang=1 dan lebar=1</small>
            `;
        } else if (originalPanjang === 0 && originalLebar === 0) {
            // Barang tidak mengandung panjang dan lebar
            calculationInfo.innerHTML = `
                <i class="bi bi-calculator me-2"></i>
                <strong>Perhitungan Produksi:</strong><br>
                <strong>Langkah 1:</strong> ${qtyPesanan} × ${quantity} = ${qtyQuantity.toFixed(2)}<br>
                <strong>Langkah 2:</strong> ${qtyQuantity.toFixed(2)} × (${panjang} × ${lebar}) = ${qtyQuantity.toFixed(2)} × ${panjang * lebar} = ${total.toFixed(2)} ${this.getSatuanNama()}<br>
                <small class="text-muted">Barang tidak mengandung panjang dan lebar, menggunakan panjang=1 dan lebar=1</small>
            `;
        } else {
            // Barang mengandung panjang dan lebar
            calculationInfo.innerHTML = `
                <i class="bi bi-calculator me-2"></i>
                <strong>Perhitungan Produksi:</strong><br>
                <strong>Langkah 1:</strong> ${qtyPesanan} × ${quantity} = ${qtyQuantity.toFixed(2)}<br>
                <strong>Langkah 2:</strong> ${qtyQuantity.toFixed(2)} × (${panjang} × ${lebar}) = ${qtyQuantity.toFixed(2)} × ${panjang * lebar} = ${total.toFixed(2)} ${this.getSatuanNama()}<br>
                <small class="text-muted">Menggunakan panjang dan lebar asli dari barang</small>
            `;
        }

        // Insert after qty input
        const qtyInput = document.querySelector('input[name="qty_produksi"]');
        if (qtyInput) {
            qtyInput.parentNode.appendChild(calculationInfo);
        }
    }

    /**
     * Get satuan nama from the page
     */
    getSatuanNama() {
        // Try to find satuan nama from the page content
        const satuanElements = document.querySelectorAll('input[readonly] + span, .input-group-text');
        for (let element of satuanElements) {
            if (element.textContent && element.textContent.trim()) {
                return element.textContent.trim();
            }
        }
        return 'unit'; // Default fallback
    }

    /**
     * Validate form before submission
     */
    validateForm() {
        const qtyInput = document.querySelector('input[name="qty_produksi"]');
        const qtyPesananInput = document.querySelector('#qty_pesanan');
        const quantityInput = document.querySelector('#quantity');

        // Recalculate to ensure qty_produksi is filled
        this.calculateTotal();

        if (!qtyInput || !qtyInput.value || qtyInput.value === '0') {
            alert('Jumlah Produksi harus diisi dan tidak boleh 0!');
            return false;
        }

        if (!qtyPesananInput || !qtyPesananInput.value || qtyPesananInput.value === '0') {
            alert('Qty Pesanan tidak ditemukan atau 0!');
            return false;
        }

        if (!quantityInput || !quantityInput.value || quantityInput.value === '0') {
            alert('Quantity BOM tidak ditemukan atau 0!');
            return false;
        }

        // Check if panjang and lebar are provided when they are required
        const readonlyPanjang = document.querySelector('input[value][readonly]');
        const readonlyLebar = document.querySelectorAll('input[value][readonly]')[1];

        if (readonlyPanjang && readonlyLebar) {
            const panjangValue = parseFloat(readonlyPanjang.value) || 0;
            const lebarValue = parseFloat(readonlyLebar.value) || 0;

            // Jika panjang dan lebar = 0, tidak perlu validasi khusus
            // Jika panjang dan lebar > 0, pastikan input fields terisi
            if (panjangValue > 0 || lebarValue > 0) {
                const panjangInput = document.querySelector('input[name="panjang_produksi"]');
                const lebarInput = document.querySelector('input[name="lebar_produksi"]');
                
                if (panjangInput && lebarInput) {
                    const inputPanjang = parseFloat(panjangInput.value) || 0;
                    const inputLebar = parseFloat(lebarInput.value) || 0;
                    
                    if (inputPanjang === 0 && inputLebar === 0) {
                        alert('Panjang dan lebar harus diisi untuk barang yang memiliki dimensi!');
                        return false;
                    }
                }
            }
        }

        return true;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.produksiCalculator = new ProduksiCalculator();
    
    // Additional calculation after DOM is fully loaded
    setTimeout(() => {
        if (window.produksiCalculator) {
            window.produksiCalculator.calculateTotal();
        }
    }, 1500);
});

// Also initialize if DOM is already loaded
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    window.produksiCalculator = new ProduksiCalculator();
    
    setTimeout(() => {
        if (window.produksiCalculator) {
            window.produksiCalculator.calculateTotal();
        }
    }, 1500);
}

// Export for global access
window.ProduksiCalculator = ProduksiCalculator; 