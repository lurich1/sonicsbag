<?php
require_once 'config.php';
require_once INCLUDES_PATH . '/api-helper.php';

$pageTitle = 'Shop';
$pageDescription = 'Browse our collection of premium leather goods and quality bags.';

$sortBy = $_GET['sort'] ?? 'default';
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? null;
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 12;

// Fetch all products from database
$allProducts = fetchProducts($filter);
$transformedProducts = $allProducts; // Already transformed from database

// Filter by search
if ($search) {
    $transformedProducts = array_filter($transformedProducts, function($product) use ($search) {
        return stripos($product['name'], $search) !== false;
    });
}

// Sort products
switch ($sortBy) {
    case 'price-low':
        usort($transformedProducts, function($a, $b) {
            $priceA = floatval(str_replace(['₵', ','], '', $a['price']));
            $priceB = floatval(str_replace(['₵', ','], '', $b['price']));
            return $priceA - $priceB;
        });
        break;
    case 'price-high':
        usort($transformedProducts, function($a, $b) {
            $priceA = floatval(str_replace(['₵', ','], '', $a['price']));
            $priceB = floatval(str_replace(['₵', ','], '', $b['price']));
            return $priceB - $priceA;
        });
        break;
    case 'newest':
        $transformedProducts = array_reverse($transformedProducts);
        break;
}

// Pagination
$totalPages = ceil(count($transformedProducts) / $itemsPerPage);
$startIndex = ($currentPage - 1) * $itemsPerPage;
$displayedProducts = array_slice($transformedProducts, $startIndex, $itemsPerPage);

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background pt-16 sm:pt-20">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "Shop - Soncis",
        "description": "Browse our collection of premium leather goods",
        "url": "<?php echo SITE_URL; ?>/shop.php"
    }
    </script>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <!-- Header with results count and sorting -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <p class="text-muted-foreground text-sm">
                    Showing <?php echo $startIndex + 1; ?>-<?php echo min($startIndex + $itemsPerPage, count($transformedProducts)); ?> of <?php echo count($transformedProducts); ?> results
                </p>
                
                <div class="relative">
                    <select 
                        onchange="window.location.href='?sort=' + this.value + '<?php echo $search ? '&search=' . urlencode($search) : ''; ?>'"
                        class="px-4 py-2 border border-border rounded-md hover:border-primary transition text-sm"
                    >
                        <option value="default" <?php echo $sortBy === 'default' ? 'selected' : ''; ?>>Default Sorting</option>
                        <option value="price-low" <?php echo $sortBy === 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price-high" <?php echo $sortBy === 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="newest" <?php echo $sortBy === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    </select>
                </div>
            </div>

            <!-- Product Grid -->
            <?php if (empty($displayedProducts)): ?>
                <div class="text-center py-12">
                    <p class="text-muted-foreground">No products found. Add products from the admin panel.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php foreach ($displayedProducts as $product): ?>
                        <div class="group">
                            <a href="<?php echo url('product.php?id=' . $product['id']); ?>" class="block">
                                <div class="relative h-64 mb-4 overflow-hidden bg-muted rounded">
                                    <img
                                        src="<?php echo getImageUrl($product['imageUrl']); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                                    />
                                    <?php if ($product['label']): ?>
                                        <span class="absolute top-2 left-2 px-2 py-1 text-xs font-semibold uppercase rounded <?php echo getLabelColor($product['label']); ?>">
                                            <?php echo htmlspecialchars($product['label']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <div class="absolute top-2 right-2 flex gap-2 opacity-0 group-hover:opacity-100 transition">
                                        <button
                                            onclick="event.preventDefault(); window.SoncisStore && SoncisStore.addProductToWishlist(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>', '<?php echo htmlspecialchars(addslashes($product['price'])); ?>', '<?php echo htmlspecialchars(addslashes($product['imageUrl'])); ?>');"
                                            class="p-2 rounded-full shadow bg-background hover:bg-muted transition"
                                            aria-label="Add to wishlist"
                                        >
                                            <iconify-icon icon="mdi:heart-outline" width="18" height="18"></iconify-icon>
                                        </button>
                                        <button
                                            onclick="event.preventDefault(); window.SoncisStore && SoncisStore.addProductToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>', '<?php echo htmlspecialchars(addslashes($product['price'])); ?>', '<?php echo htmlspecialchars(addslashes($product['imageUrl'])); ?>');"
                                            class="p-2 rounded-full shadow bg-background hover:bg-muted transition"
                                            aria-label="Add to cart"
                                        >
                                            <iconify-icon icon="mdi:cart-outline" width="18" height="18"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="text-sm font-semibold uppercase mb-2 line-clamp-2 text-foreground"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="text-foreground font-medium"><?php echo htmlspecialchars($product['price']); ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="flex justify-center gap-2 mt-12">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?php echo $currentPage - 1; ?>&sort=<?php echo $sortBy; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="px-4 py-2 border border-border rounded-md hover:border-primary transition">
                            Previous
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&sort=<?php echo $sortBy; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="px-4 py-2 border rounded-md transition <?php echo $currentPage === $i ? 'bg-primary text-primary-foreground border-primary' : 'border-border hover:border-primary'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?php echo $currentPage + 1; ?>&sort=<?php echo $sortBy; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="px-4 py-2 border border-border rounded-md hover:border-primary transition">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>

