<?php
require_once 'config.php';
require_once INCLUDES_PATH . '/db-helper.php';
require_once INCLUDES_PATH . '/api-helper.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = $productId ? getProductById($productId) : null;

if (!$product) {
    header('Location: ' . url('shop.php'));
    exit;
}

$transformedProduct = $product; // Already transformed from database

// Get related products (exclude current product, limit to 4)
$allProducts = getProducts();
$relatedProducts = array_filter($allProducts, function($p) use ($productId) {
    return $p['id'] != $productId;
});
$relatedProducts = array_slice($relatedProducts, 0, 4);

$pageTitle = $transformedProduct['name'];
$pageDescription = 'View ' . htmlspecialchars($transformedProduct['name']) . ' - ' . htmlspecialchars($transformedProduct['price']);

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background pt-16 sm:pt-20">
    <section class="py-12">
        <div class="container mx-auto px-4">
            <!-- Breadcrumbs -->
            <div class="mb-6 text-sm text-muted-foreground">
                <a href="<?php echo url(); ?>" class="hover:text-primary">Home</a>
                <span class="mx-1">/</span>
                <a href="<?php echo url('shop.php'); ?>" class="hover:text-primary">Shop</a>
                <span class="mx-1">/</span>
                <span class="text-foreground"><?php echo htmlspecialchars($transformedProduct['name']); ?></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-0 mb-16 md:items-start">
                <!-- Product Image -->
                <div class="relative w-full flex justify-center md:justify-start md:pr-2">
                    <div class="relative overflow-hidden rounded-lg w-full max-w-xs mx-auto md:mx-0">
                        <img
                            src="<?php echo getImageUrl($transformedProduct['imageUrl']); ?>"
                            alt="<?php echo htmlspecialchars($transformedProduct['name']); ?>"
                            class="w-full h-auto object-cover"
                            style="max-width: 300px; height: auto;"
                        />
                        <?php if ($transformedProduct['label']): ?>
                            <span class="absolute top-4 left-4 px-3 py-1.5 text-xs font-bold uppercase rounded <?php echo getLabelColor($transformedProduct['label']); ?>">
                                <?php echo htmlspecialchars($transformedProduct['label']); ?>
                            </span>
                        <?php endif; ?>
                        <button
                            onclick="handleAddToWishlist()"
                            id="wishlist-btn"
                            class="absolute top-4 right-4 p-3 rounded-full shadow-lg transition bg-white hover:bg-secondary hover:text-white"
                            aria-label="Add to wishlist"
                        >
                            <iconify-icon icon="mdi:heart-outline" width="22" height="22"></iconify-icon>
                        </button>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="w-full">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4 uppercase text-foreground"><?php echo htmlspecialchars($transformedProduct['name']); ?></h1>
                    <p class="text-2xl md:text-3xl font-bold text-secondary mb-6"><?php echo htmlspecialchars($transformedProduct['price']); ?></p>

                    <div class="mb-6">
                        <p class="text-muted-foreground leading-relaxed mb-4">
                            <?php echo htmlspecialchars($transformedProduct['description'] ?: $transformedProduct['name'] . ' - Premium quality leather product crafted with care and attention to detail.'); ?>
                        </p>
                    </div>

                    <div class="mb-6 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-foreground">SKU:</span>
                            <span class="text-muted-foreground text-sm"><?php echo 'MB-' . str_pad($transformedProduct['id'], 3, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-foreground">Stock Status:</span>
                            <span class="text-green-600 font-medium text-sm">
                                <?php echo ($transformedProduct['inStock'] ?? true) ? 'In Stock' : 'Out of Stock'; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="mb-6">
                        <div class="flex items-center gap-0 w-fit bg-background" style="border: none !important;">
                            <button
                                onclick="updateQuantity(-1)"
                                type="button"
                                class="px-4 py-2.5 hover:bg-muted transition bg-background"
                                style="border: none !important; outline: none !important;"
                                aria-label="Decrease quantity"
                            >
                                <iconify-icon icon="mdi:minus" width="16" height="16"></iconify-icon>
                            </button>
                            <input 
                                type="number" 
                                id="quantity-display" 
                                value="1" 
                                min="1" 
                                max="<?php echo ($transformedProduct['stockQuantity'] ?? 99); ?>"
                                onchange="updateQuantityInput()"
                                class="w-12 px-2 py-2.5 text-center font-semibold bg-background"
                                style="appearance: textfield; -moz-appearance: textfield; border: none !important; outline: none !important;"
                            />
                            <button
                                onclick="updateQuantity(1)"
                                type="button"
                                class="px-4 py-2.5 hover:bg-muted transition bg-background"
                                style="border: none !important; outline: none !important;"
                                aria-label="Increase quantity"
                            >
                                <iconify-icon icon="mdi:plus" width="16" height="16"></iconify-icon>
                            </button>
                        </div>
                    </div>

                    <!-- Product Details List -->
                    <div class="mb-8">
                        <ul class="space-y-2.5">
                            <li class="flex items-start gap-2.5">
                                <iconify-icon icon="mdi:check" width="18" height="18" class="text-secondary mt-0.5 flex-shrink-0"></iconify-icon>
                                <span class="text-muted-foreground text-sm">Professional Design</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <iconify-icon icon="mdi:check" width="18" height="18" class="text-secondary mt-0.5 flex-shrink-0"></iconify-icon>
                                <span class="text-muted-foreground text-sm">Laptop Compartment (up to 15 inches)</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <iconify-icon icon="mdi:check" width="18" height="18" class="text-secondary mt-0.5 flex-shrink-0"></iconify-icon>
                                <span class="text-muted-foreground text-sm">Multiple Organization Pockets</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <iconify-icon icon="mdi:check" width="18" height="18" class="text-secondary mt-0.5 flex-shrink-0"></iconify-icon>
                                <span class="text-muted-foreground text-sm">Premium Leather Exterior</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <iconify-icon icon="mdi:check" width="18" height="18" class="text-secondary mt-0.5 flex-shrink-0"></iconify-icon>
                                <span class="text-muted-foreground text-sm">Secure Lock Mechanism</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-6">
                        <button
                            id="add-to-cart-btn"
                            onclick="handleAddToCart()"
                            class="flex-1 px-8 py-4 rounded-md font-bold uppercase transition-all duration-300 flex items-center justify-center gap-2.5 bg-secondary text-white hover:bg-secondary/90 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            style="font-size: 0.875rem; letter-spacing: 0.05em; min-height: 48px;"
                        >
                            <iconify-icon icon="mdi:cart" width="20" height="20"></iconify-icon>
                            <span>ADD TO CART</span>
                        </button>
                        <button
                            onclick="handleBuyNow()"
                            class="px-8 py-4 bg-white border-2 border-border text-foreground rounded-md font-bold uppercase hover:bg-muted hover:border-foreground hover:shadow-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="font-size: 0.875rem; letter-spacing: 0.05em; min-height: 48px;"
                        >
                            BUY NOW
                        </button>
                    </div>

                    <!-- Share Button -->
                    <a href="#" onclick="handleShare(event)" class="flex items-center gap-2 text-muted-foreground hover:text-secondary transition">
                        <iconify-icon icon="mdi:share-variant" width="18" height="18"></iconify-icon>
                        <span class="text-sm uppercase font-medium">Share</span>
                    </a>
                </div>
            </div>

            <!-- Related Products Section -->
            <?php if (!empty($relatedProducts)): ?>
                <div class="mt-16 pt-8 border-t border-border">
                    <h2 class="text-3xl font-bold mb-8 uppercase text-foreground">Related Products</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                        <?php foreach ($relatedProducts as $relatedProduct): ?>
                            <a href="<?php echo url('product.php?id=' . $relatedProduct['id']); ?>" class="group">
                                <div class="relative h-80 mb-4 overflow-hidden bg-muted rounded-lg">
                                    <img
                                        src="<?php echo getImageUrl($relatedProduct['imageUrl']); ?>"
                                        alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                    />
                                    <?php if ($relatedProduct['label']): ?>
                                        <span class="absolute top-3 left-3 px-3 py-1 text-xs font-bold uppercase rounded <?php echo getLabelColor($relatedProduct['label']); ?>">
                                            <?php echo htmlspecialchars($relatedProduct['label']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="text-base font-semibold uppercase mb-2 text-foreground hover:text-secondary transition"><?php echo htmlspecialchars($relatedProduct['name']); ?></h3>
                                <p class="text-lg font-semibold text-foreground"><?php echo htmlspecialchars($relatedProduct['price']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include INCLUDES_PATH . '/newsletter-section.php'; ?>
</main>

<script>
const PRODUCT_ID = <?php echo $transformedProduct['id']; ?>;
const PRODUCT_NAME = <?php echo json_encode($transformedProduct['name']); ?>;
const PRODUCT_PRICE = <?php echo json_encode($transformedProduct['price']); ?>;
const PRODUCT_IMAGE = <?php echo json_encode($transformedProduct['imageUrl']); ?>;
const PRODUCT_IN_STOCK = <?php echo ($transformedProduct['inStock'] ?? true) ? 'true' : 'false'; ?>;
const CART_URL = '<?php echo url('cart.php'); ?>';
const SHOP_URL = '<?php echo url('shop.php'); ?>';
const PRODUCT_URL = '<?php echo url('product.php'); ?>';
const WISHLIST_URL = '<?php echo url('wishlist.php'); ?>';

let quantity = 1;
const maxQuantity = <?php echo ($transformedProduct['stockQuantity'] ?? 99); ?>;

function updateQuantity(change) {
    quantity = Math.max(1, Math.min(maxQuantity, quantity + change));
    const quantityInput = document.getElementById('quantity-display');
    if (quantityInput) {
        quantityInput.value = quantity;
    }
}

function updateQuantityInput() {
    const quantityInput = document.getElementById('quantity-display');
    if (quantityInput) {
        quantity = Math.max(1, Math.min(maxQuantity, parseInt(quantityInput.value) || 1));
        quantityInput.value = quantity;
    }
}

function handleAddToCart() {
    if (!window.SoncisStore) return;
    const quantityInput = document.getElementById('quantity-display');
    const qty = quantityInput ? parseInt(quantityInput.value) || 1 : quantity;
    for (let i = 0; i < qty; i++) {
        window.SoncisStore.addProductToCart(PRODUCT_ID, PRODUCT_NAME, PRODUCT_PRICE, PRODUCT_IMAGE);
    }
    const btn = document.getElementById('add-to-cart-btn');
    if (btn) {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<iconify-icon icon="mdi:check" width="22" height="22"></iconify-icon> Added to Cart';
        btn.classList.add('bg-green-600', 'text-white');
        btn.classList.remove('bg-secondary');
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('bg-green-600', 'text-white');
            btn.classList.add('bg-secondary');
        }, 2000);
    }
}

function handleBuyNow() {
    handleAddToCart();
    setTimeout(() => {
        window.location.href = CART_URL;
    }, 500);
}

function handleAddToWishlist() {
    if (!window.SoncisStore) return;
    window.SoncisStore.addProductToWishlist(PRODUCT_ID, PRODUCT_NAME, PRODUCT_PRICE, PRODUCT_IMAGE, PRODUCT_IN_STOCK ? 'In Stock' : 'Out of Stock');
    const btn = document.getElementById('wishlist-btn');
    if (btn) {
        btn.classList.add('bg-secondary', 'text-white');
        const icon = btn.querySelector('iconify-icon');
        if (icon) {
            icon.setAttribute('icon', 'mdi:heart');
        }
    }
}

function handleShare(event) {
    event.preventDefault();
    const url = window.location.href;
    const title = PRODUCT_NAME;
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: 'Check out this product: ' + title,
            url: url
        }).catch(() => {
            copyToClipboard(url);
        });
    } else {
        copyToClipboard(url);
    }
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Product link copied to clipboard!');
        });
    } else {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Product link copied to clipboard!');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Check if product is in wishlist
    if (window.SoncisStore && window.SoncisStore.isInWishlist(PRODUCT_ID)) {
        const btn = document.getElementById('wishlist-btn');
        if (btn) {
            btn.classList.add('bg-secondary', 'text-white');
            const icon = btn.querySelector('iconify-icon');
            if (icon) {
                icon.setAttribute('icon', 'mdi:heart');
            }
        }
    }
    
    // Initialize quantity from input
    const quantityInput = document.getElementById('quantity-display');
    if (quantityInput) {
        quantity = parseInt(quantityInput.value) || 1;
    }
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>

