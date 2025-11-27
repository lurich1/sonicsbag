<?php
require_once INCLUDES_PATH . '/api-helper.php';

$activeFilter = $_GET['filter'] ?? 'bestsellers';
$products = fetchProducts($activeFilter);
$transformedProducts = $products; // Already transformed from database
?>

<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h2 class="text-5xl font-bold mb-2">Featured Collection</h2>
            <p class="text-gray-600">Handmade leather bags for any occasion</p>
        </div>

        <div class="flex justify-center gap-8 mb-12 flex-wrap">
            <a href="?filter=bestsellers" class="uppercase text-sm font-semibold transition <?php echo $activeFilter === 'bestsellers' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-primary'; ?>">
                Best Sellers
            </a>
            <a href="?filter=newarrivals" class="uppercase text-sm font-semibold transition <?php echo $activeFilter === 'newarrivals' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-primary'; ?>">
                New Arrivals
            </a>
            <a href="?filter=bestreviewed" class="uppercase text-sm font-semibold transition <?php echo $activeFilter === 'bestreviewed' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-primary'; ?>">
                Best Reviewed
            </a>
        </div>

        <!-- Product Grid -->
        <?php if (empty($transformedProducts)): ?>
            <div class="text-center py-12">
                <p class="text-muted-foreground">No products found. Add products from the admin panel.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($transformedProducts as $product): ?>
                    <div class="group">
                        <a href="<?php echo url('product.php?id=' . $product['id']); ?>" class="block">
                            <div class="relative h-64 mb-4 overflow-hidden bg-gray-100 rounded">
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
                                        class="p-2 rounded-full shadow bg-white hover:bg-gray-100 transition"
                                        aria-label="Add to wishlist"
                                    >
                                        <iconify-icon icon="mdi:heart-outline" width="18" height="18"></iconify-icon>
                                    </button>
                                    <button
                                        onclick="event.preventDefault(); window.SoncisStore && SoncisStore.addProductToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>', '<?php echo htmlspecialchars(addslashes($product['price'])); ?>', '<?php echo htmlspecialchars(addslashes($product['imageUrl'])); ?>');"
                                        class="p-2 rounded-full shadow bg-white hover:bg-gray-100 transition"
                                        aria-label="Add to cart"
                                    >
                                        <iconify-icon icon="mdi:cart-outline" width="18" height="18"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                            <h3 class="text-sm font-semibold uppercase mb-2 line-clamp-2 text-foreground">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </h3>
                            <p class="text-foreground font-medium"><?php echo htmlspecialchars($product['price']); ?></p>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 mt-4">
                                <button
                                    type="button"
                                    onclick="window.SoncisStore && SoncisStore.addProductToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>', '<?php echo htmlspecialchars(addslashes($product['price'])); ?>', '<?php echo htmlspecialchars(addslashes($product['imageUrl'])); ?>')"
                                    class="flex-1 px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm font-semibold hover:opacity-90 transition"
                                >
                                    Add to Cart
                                </button>
                                <button
                                    type="button"
                                    onclick="window.SoncisStore && SoncisStore.addProductToWishlist(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>', '<?php echo htmlspecialchars(addslashes($product['price'])); ?>', '<?php echo htmlspecialchars(addslashes($product['imageUrl'])); ?>')"
                                    class="flex-1 px-4 py-2 border border-border rounded-md text-sm font-semibold hover:border-primary transition"
                                >
                                    Wishlist
                                </button>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

