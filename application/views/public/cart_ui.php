<!-- Cart Offcanvas UI -->
<style>
    .cart-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
        display: none;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .cart-overlay.show {
        display: block;
        opacity: 1;
    }
    .cart-offcanvas {
        position: fixed;
        top: 0; right: -400px; bottom: 0;
        width: 100%;
        max-width: 400px;
        background: white;
        z-index: 1050;
        box-shadow: -4px 0 15px rgba(0,0,0,0.1);
        transition: right 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .cart-offcanvas.open {
        right: 0;
    }
    .cart-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e5e7e9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .cart-header h5 {
        margin: 0;
        font-weight: 800;
        color: #212121;
    }
    .btn-close-cart {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6d7588;
    }
    .cart-body {
        flex-grow: 1;
        overflow-y: auto;
        padding: 20px;
    }
    .cart-empty {
        text-align: center;
        color: #6d7588;
        margin-top: 50px;
    }
    .cart-item {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e5e7e9;
    }
    .cart-item img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7e9;
    }
    .cart-item-info {
        flex-grow: 1;
    }
    .cart-item-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #212121;
        margin-bottom: 5px;
    }
    .cart-item-price {
        font-weight: 800;
        color: #00aa5b;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }
    .cart-item-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .qty-control-cart {
        display: flex;
        align-items: center;
        border: 1px solid #e5e7e9;
        border-radius: 6px;
        overflow: hidden;
    }
    .qty-control-cart button {
        background: white;
        border: none;
        width: 28px;
        height: 28px;
        color: #00aa5b;
        font-weight: bold;
        cursor: pointer;
    }
    .qty-control-cart input {
        width: 35px;
        height: 28px;
        border: none;
        border-left: 1px solid #e5e7e9;
        border-right: 1px solid #e5e7e9;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .btn-remove-item {
        background: none;
        border: none;
        color: #ef144a;
        cursor: pointer;
        font-size: 1.2rem;
    }
    .cart-footer {
        padding: 20px;
        border-top: 1px solid #e5e7e9;
    }
    .btn-checkout-wa {
        display: block;
        width: 100%;
        background: #00aa5b;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 1rem;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-checkout-wa:hover {
        opacity: 0.9;
        color: white;
    }
</style>

<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
<div class="cart-offcanvas" id="cartOffcanvas">
    <div class="cart-header">
        <h5>Keranjang Belanja</h5>
        <button class="btn-close-cart" onclick="closeCart()">&times;</button>
    </div>
    <div class="cart-body" id="cartItemsContainer">
        <!-- Cart items will be rendered here by JS -->
    </div>
    <div class="cart-footer">
        <?php 
        $wa_number = !empty($perusahaan['no_wa']) ? $perusahaan['no_wa'] : (!empty($perusahaan['telepon']) ? $perusahaan['telepon'] : '');
        if(!empty($wa_number)): 
            $wa_number = preg_replace('/[^0-9]/', '', $wa_number);
            if(substr($wa_number, 0, 1) == '0') $wa_number = '62' . substr($wa_number, 1);
        ?>
        <button onclick="checkoutCartWA('<?= $wa_number ?>')" class="btn-checkout-wa">
            <i class="bi bi-whatsapp me-1"></i> Checkout via WhatsApp
        </button>
        <?php else: ?>
        <button class="btn-checkout-wa" disabled>
            Nomor WA tidak tersedia
        </button>
        <?php endif; ?>
    </div>
</div>

<script>
    const CART_STORAGE_KEY = 'tgm_catalog_cart';

    function getCart() {
        let cart = localStorage.getItem(CART_STORAGE_KEY);
        return cart ? JSON.parse(cart) : [];
    }

    function saveCart(cart) {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
        updateCartBadge();
        renderCartItems();
    }

    function addToCart(product) {
        let cart = getCart();
        let existing = cart.find(item => item.id === product.id);
        if(existing) {
            existing.qty += parseInt(product.qty);
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                img: product.img,
                url: product.url,
                qty: parseInt(product.qty)
            });
        }
        saveCart(cart);
        openCart(); // Show cart when added
    }

    function updateCartItemQty(id, change) {
        let cart = getCart();
        let item = cart.find(i => i.id === id);
        if(item) {
            item.qty += change;
            if(item.qty < 1) item.qty = 1;
            saveCart(cart);
        }
    }

    function removeCartItem(id) {
        let cart = getCart();
        cart = cart.filter(i => i.id !== id);
        saveCart(cart);
    }

    function updateCartBadge() {
        let cart = getCart();
        let totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
        let badges = document.querySelectorAll('.cart-badge');
        badges.forEach(badge => {
            badge.innerText = totalItems;
            badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
        });
    }

    function openCart() {
        renderCartItems();
        document.getElementById('cartOverlay').classList.add('show');
        document.getElementById('cartOffcanvas').classList.add('open');
    }

    function closeCart() {
        document.getElementById('cartOverlay').classList.remove('show');
        document.getElementById('cartOffcanvas').classList.remove('open');
    }

    function renderCartItems() {
        let cart = getCart();
        let container = document.getElementById('cartItemsContainer');
        
        if(cart.length === 0) {
            container.innerHTML = `
                <div class="cart-empty">
                    <i class="bi bi-cart-x" style="font-size: 3rem; color: #e5e7e9;"></i>
                    <p class="mt-3">Keranjang Anda masih kosong.<br>Silakan pilih produk dulu!</p>
                </div>
            `;
            return;
        }

        let html = '';
        cart.forEach(item => {
            html += `
                <div class="cart-item">
                    <img src="${item.img}" alt="${item.name}">
                    <div class="cart-item-info">
                        <div class="cart-item-title">${item.name}</div>
                        <div class="cart-item-actions">
                            <div class="qty-control-cart">
                                <button onclick="updateCartItemQty('${item.id}', -1)">&minus;</button>
                                <input type="text" value="${item.qty}" readonly>
                                <button onclick="updateCartItemQty('${item.id}', 1)">+</button>
                            </div>
                            <button class="btn-remove-item" onclick="removeCartItem('${item.id}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function checkoutCartWA(phone) {
        let cart = getCart();
        if(cart.length === 0) {
            alert('Keranjang Anda masih kosong!');
            return;
        }

        let cartParams = [];
        cart.forEach(item => {
            cartParams.push(`${item.id}-${item.qty}`);
        });
        let cartQuery = cartParams.join(',');
        
        let orderLink = `<?= base_url('katalog/pesanan?cart=') ?>${cartQuery}`;

        let text = `Halo Admin, saya ingin memesan produk.\n\n`;
        text += `Berikut adalah rincian pesanan saya:\n`;
        text += `${orderLink}\n\n`;
        text += `Mohon dibantu untuk rekap total biaya dan pembayarannya ya. Terima kasih!`;

        let waLink = `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
        window.open(waLink, '_blank');
        
        // Optional: clear cart after sending? 
        // We probably shouldn't clear it immediately in case they didn't send the WA yet.
    }

    // Initialize badges on load
    document.addEventListener('DOMContentLoaded', function() {
        updateCartBadge();
    });
</script>
