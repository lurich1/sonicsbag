<?php
require_once 'config.php';

$pageTitle = 'Wishlist';
$pageDescription = 'View your saved items.';

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background pt-16 sm:pt-20">
    <section class="py-12">
        <div class="container mx-auto px-4">
            <!-- Breadcrumbs -->
            <div class="mb-6 text-sm text-muted-foreground">
                <a href="<?php echo url(); ?>" class="hover:text-primary">Home</a>
                <span class="mx-1">/</span>
                <span class="hover:text-primary">Pages</span>
                <span class="mx-1">/</span>
                <span class="text-foreground">Wishlist</span>
            </div>

            <h1 class="text-4xl md:text-5xl font-bold mb-12 uppercase">Wishlist</h1>

            <!-- Wishlist Table -->
            <div id="wishlist-container">
                <div class="text-center py-16">
                    <iconify-icon icon="mdi:heart" width="64" height="64" class="mx-auto mb-4 text-muted-foreground"></iconify-icon>
                    <p class="text-xl text-muted-foreground mb-4">Your wishlist is empty</p>
                    <a href="<?php echo url('shop.php'); ?>" class="inline-block px-6 py-3 bg-primary text-primary-foreground rounded hover:opacity-90 transition">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
const SHOP_URL = '<?php echo url('shop.php'); ?>';
const PRODUCT_URL = '<?php echo url('product.php'); ?>';
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

function loadWishlist() {
    const wishlist = JSON.parse(localStorage.getItem('soncis-wishlist') || '[]');
    const container = document.getElementById('wishlist-container');
    
    if (wishlist.length === 0) {
        container.innerHTML = `
            <div class="text-center py-16">
                <iconify-icon icon="mdi:heart" width="64" height="64" class="mx-auto mb-4 text-muted-foreground"></iconify-icon>
                <p class="text-xl text-muted-foreground mb-4">Your wishlist is empty</p>
                <a href="${SHOP_URL}" class="inline-block px-6 py-3 bg-primary text-primary-foreground rounded hover:opacity-90 transition">
                    Continue Shopping
                </a>
            </div>
        `;
        return;
    }
    
    // Use cards on mobile, table on larger screens
    html = '<div class="block md:hidden space-y-4">';
    wishlist.forEach((item) => {
        const imageUrl = getImageUrl(item.image || item.imageUrl || '');
        html += `
            <div class="border border-border rounded-lg p-4 bg-background">
                <div class="flex gap-4 mb-4">
                    <div class="relative w-24 h-24 rounded overflow-hidden bg-muted flex-shrink-0">
                        <img src="${imageUrl}" alt="${item.name.replace(/"/g, '&quot;')}" class="w-full h-full object-cover" onerror="this.src='${BASE_URL}assets/images/placeholder.jpg'" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold uppercase text-sm mb-2 text-foreground line-clamp-2">${item.name}</h3>
                        <p class="font-medium text-foreground mb-1">${item.price}</p>
                        <p class="text-sm text-green-600 mb-3">${item.stock || 'In Stock'}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        onclick="handleAddToCart(${item.id}, '${item.name.replace(/'/g, "\\'")}', '${item.price.replace(/'/g, "\\'")}', '${imageUrl.replace(/'/g, "\\'")}')"
                        class="flex-1 px-3 py-2 border border-border rounded hover:border-primary hover:text-primary transition text-xs font-semibold uppercase flex items-center justify-center gap-2"
                    >
                        <iconify-icon icon="mdi:cart" width="14" height="14"></iconify-icon>
                        Add to Cart
                    </button>
                    <button
                        onclick="removeFromWishlist(${item.id})"
                        class="p-2 hover:text-destructive transition"
                        aria-label="Remove from wishlist"
                    >
                        <iconify-icon icon="mdi:delete" width="18" height="18"></iconify-icon>
                    </button>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    // Table for desktop
    html += '<div class="hidden md:block overflow-x-auto"><table class="w-full border-collapse">';
    html += '<thead><tr class="border-b border-border">';
    html += '<th class="text-left py-4 px-4 uppercase text-sm font-semibold">Product</th>';
    html += '<th class="text-left py-4 px-4 uppercase text-sm font-semibold">Unit Price</th>';
    html += '<th class="text-left py-4 px-4 uppercase text-sm font-semibold">Stock Status</th>';
    html += '<th class="text-center py-4 px-4 uppercase text-sm font-semibold">Actions</th>';
    html += '</tr></thead><tbody>';
    
    wishlist.forEach((item) => {
        const imageUrl = getImageUrl(item.image || item.imageUrl || '');
        html += `
            <tr class="border-b border-border hover:bg-muted/50 transition">
                <td class="py-6 px-4">
                    <div class="flex items-center gap-4">
                        <div class="relative w-20 h-20 rounded overflow-hidden bg-muted flex-shrink-0">
                            <img src="${imageUrl}" alt="${item.name.replace(/"/g, '&quot;')}" class="w-full h-full object-cover" onerror="this.src='${BASE_URL}assets/images/placeholder.jpg'" />
                        </div>
                        <span class="font-semibold uppercase">${item.name}</span>
                    </div>
                </td>
                <td class="py-6 px-4 font-medium">${item.price}</td>
                <td class="py-6 px-4">
                    <span class="text-green-600">${item.stock || 'In Stock'}</span>
                </td>
                <td class="py-6 px-4">
                    <div class="flex items-center justify-center gap-3">
                        <button
                            onclick="handleAddToCart(${item.id}, '${item.name.replace(/'/g, "\\'")}', '${item.price.replace(/'/g, "\\'")}', '${imageUrl.replace(/'/g, "\\'")}')"
                            class="px-4 py-2 border border-border rounded hover:border-primary hover:text-primary transition text-sm font-semibold uppercase flex items-center gap-2"
                        >
                            <iconify-icon icon="mdi:cart" width="16" height="16"></iconify-icon>
                            Add to Cart
                        </button>
                        <button
                            onclick="removeFromWishlist(${item.id})"
                            class="p-2 hover:text-destructive transition"
                            aria-label="Remove from wishlist"
                        >
                            <iconify-icon icon="mdi:delete" width="20" height="20"></iconify-icon>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function removeFromWishlist(id) {
    if (!window.SoncisStore) return;
    window.SoncisStore.removeProductFromWishlist(id);
    loadWishlist();
}

function handleAddToCart(id, name, price, image) {
    if (!window.SoncisStore) return;
    window.SoncisStore.addProductToCart(id, name, price, image);
}

// Load wishlist on page load
document.addEventListener('DOMContentLoaded', loadWishlist);
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>

