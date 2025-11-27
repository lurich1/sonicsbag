<?php
require_once 'config.php';

$pageTitle = 'Shopping Cart';
$pageDescription = 'Review your shopping cart and proceed to checkout.';

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background pt-16 sm:pt-20">
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="mb-6 text-sm text-muted-foreground">
                <a href="<?php echo url(); ?>" class="hover:text-primary">Home</a>
                <span class="mx-1">/</span>
                <span class="hover:text-primary">Pages</span>
                <span class="mx-1">/</span>
                <span class="text-foreground">Cart</span>
            </div>

            <h1 class="text-4xl md:text-5xl font-bold mb-12 uppercase">Cart</h1>
            
            <div id="cart-container" class="min-h-[200px] w-full">
                <!-- Filled by JS -->
            </div>
        </div>
    </section>

    <?php include INCLUDES_PATH . '/newsletter-section.php'; ?>
</main>

<script>
const SHOP_URL = '<?php echo url('shop.php'); ?>';
const CHECKOUT_URL = '<?php echo url('checkout.php'); ?>';
const BASE_URL = '<?php echo url(); ?>';

// JavaScript function to get image URL (mimics PHP getImageUrl)
function getImageUrl(imagePath) {
    if (!imagePath || imagePath === '') {
        return BASE_URL + 'assets/images/placeholder.jpg';
    }
    // If already an absolute URL, return as-is
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
        return imagePath;
    }
    // If path starts with /assets/, use it as is
    if (imagePath.startsWith('/assets/')) {
        return BASE_URL + imagePath.substring(1);
    }
    // Default: assume it's in assets/images/
    const filename = imagePath.split('/').pop();
    return BASE_URL + 'assets/images/' + filename;
}

const formatCurrency = (value) => {
    return '₵' + Number(value || 0).toLocaleString('en-GH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const parsePriceString = (priceString) => {
    if (!priceString) return 0;
    return parseFloat(String(priceString).replace(/[₵$,]/g, '')) || 0;
};

function loadCart() {
    const cart = JSON.parse(localStorage.getItem('soncis-cart') || '[]');
    const container = document.getElementById('cart-container');
    
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center py-16 border border-dashed border-border rounded-lg">
                <p class="text-xl text-muted-foreground mb-4">Your cart is empty</p>
                <a href="${SHOP_URL}" class="inline-block px-6 py-3 bg-primary text-primary-foreground rounded hover:opacity-90 transition uppercase font-semibold">
                    Continue Shopping
                </a>
            </div>
        `;
        return;
    }
    
    const subtotal = cart.reduce((sum, item) => {
        const price = parsePriceString(item.price);
        return sum + price * (item.quantity || 1);
    }, 0);
    
    let html = '<div class="grid grid-cols-1 lg:grid-cols-3 gap-12">';
    html += '<div class="lg:col-span-2 space-y-6">';
    
    cart.forEach((item) => {
        const price = parsePriceString(item.price);
        const itemSubtotal = price * (item.quantity || 1);
        const imageUrl = getImageUrl(item.image || item.imageUrl || '');
        html += `
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 p-4 sm:p-6 border border-border rounded-lg bg-background">
                <div class="relative w-full sm:w-32 h-48 sm:h-32 rounded overflow-hidden bg-muted flex-shrink-0">
                    <img src="${imageUrl}" alt="${item.name.replace(/"/g, '&quot;')}" class="w-full h-full object-cover" onerror="this.src='${BASE_URL}assets/images/placeholder.jpg'" />
                </div>
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-semibold uppercase mb-2 text-foreground">${item.name}</h3>
                        <p class="text-muted-foreground mb-4">${item.price}</p>
                    </div>
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-4 flex-wrap">
                            <span class="text-sm text-muted-foreground">Quantity:</span>
                            <div class="flex items-center gap-0 border border-border rounded bg-background overflow-hidden">
                                <button onclick="changeQuantity(${item.id}, -1)" type="button" class="px-3 py-2 hover:bg-muted transition border-r border-border bg-background" aria-label="Decrease quantity">
                                    <iconify-icon icon="mdi:minus" width="16" height="16"></iconify-icon>
                                </button>
                                <span class="px-4 py-2 min-w-[60px] text-center font-semibold bg-background">${item.quantity || 1}</span>
                                <button onclick="changeQuantity(${item.id}, 1)" type="button" class="px-3 py-2 hover:bg-muted transition border-l border-border bg-background" aria-label="Increase quantity">
                                    <iconify-icon icon="mdi:plus" width="16" height="16"></iconify-icon>
                                </button>
                            </div>
                            <button onclick="removeItem(${item.id})" type="button" class="p-2 hover:text-destructive transition" aria-label="Remove item">
                                <iconify-icon icon="mdi:delete" width="20" height="20"></iconify-icon>
                            </button>
                        </div>
                        <p class="font-semibold text-foreground text-lg">
                            ${formatCurrency(itemSubtotal)}
                        </p>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    html += '<div class="lg:col-span-1">';
    html += '<div class="lg:sticky lg:top-24 p-4 sm:p-6 border border-primary rounded-lg bg-muted/30">';
    html += '<h2 class="text-xl font-bold uppercase mb-6 text-foreground">Cart Total</h2>';
    html += '<div class="space-y-4 mb-6 pb-4 border-b border-border">';
    html += `<div class="flex justify-between items-center"><span class="text-foreground">Subtotal</span><span class="font-semibold text-foreground">${formatCurrency(subtotal)}</span></div>`;
    html += `<div class="flex justify-between items-center pt-2"><span class="text-lg font-bold text-foreground">Total</span><span class="text-lg font-bold text-primary">${formatCurrency(subtotal)}</span></div>`;
    html += '</div>';
    html += '<div class="space-y-3">';
    html += `<button class="w-full px-6 py-3 bg-primary text-white rounded hover:opacity-90 transition font-bold uppercase text-sm" onclick="loadCart()">
            Update Cart
        </button>`;
    html += '<div class="grid grid-cols-2 gap-3">';
    html += `<a href="${SHOP_URL}" class="block w-full text-center px-4 py-3 border-2 border-primary rounded hover:opacity-90 transition font-semibold uppercase text-sm bg-background text-foreground">
            Continue Shopping
        </a>`;
    html += `<a href="${CHECKOUT_URL}" class="block w-full text-center px-4 py-3 bg-primary text-white rounded hover:opacity-90 transition font-bold uppercase text-sm">
            Proceed to Checkout
        </a>`;
    html += '</div></div></div></div></div>';
    
    container.innerHTML = html;
}

function changeQuantity(id, delta) {
    if (!window.SoncisStore) {
        console.error('SoncisStore not loaded');
        return;
    }
    // Convert id to number for comparison
    const itemId = typeof id === 'string' ? parseInt(id, 10) : id;
    const cart = window.SoncisStore.getCart();
    const item = cart.find(i => Number(i.id) === itemId);
    if (item) {
        const newQuantity = Math.max(1, (item.quantity || 1) + delta);
        window.SoncisStore.updateCartQuantity(itemId, newQuantity);
        loadCart();
    } else {
        console.error('Item not found in cart:', itemId);
    }
}

function updateQuantityInput(id, value) {
    if (!window.SoncisStore) {
        console.error('SoncisStore not loaded');
        return;
    }
    const itemId = typeof id === 'string' ? parseInt(id, 10) : id;
    const quantity = Math.max(1, parseInt(value) || 1);
    window.SoncisStore.updateCartQuantity(itemId, quantity);
    loadCart();
}

function removeItem(id) {
    if (!window.SoncisStore) {
        console.error('SoncisStore not loaded');
        return;
    }
    const itemId = typeof id === 'string' ? parseInt(id, 10) : id;
    window.SoncisStore.removeProductFromCart(itemId);
    loadCart();
}

document.addEventListener('DOMContentLoaded', loadCart);
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>

