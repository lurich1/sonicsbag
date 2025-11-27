<?php
require_once __DIR__ . '/auth-check.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db-helper.php';

$pageTitle = 'Admin Dashboard';

// Get statistics
try {
    $db = getDB();
    
    // Total products
    $stmt = $db->query("SELECT COUNT(*) as count FROM Products");
    $productsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total orders
    $stmt = $db->query("SELECT COUNT(*) as count FROM Orders");
    $ordersCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending orders
    $stmt = $db->query("SELECT COUNT(*) as count FROM Orders WHERE Status = 'Pending'");
    $pendingOrdersCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total revenue (from paid orders)
    $stmt = $db->query("SELECT SUM(CAST(REPLACE(REPLACE(Total, '₵', ''), ',', '') AS REAL)) as total FROM Orders WHERE PaymentStatus = 'Paid'");
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (PDOException $e) {
    error_log("Error loading stats: " . $e->getMessage());
    $productsCount = 0;
    $ordersCount = 0;
    $pendingOrdersCount = 0;
    $revenue = 0;
}

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="min-h-screen bg-background">
    <!-- Header -->
    <header class="bg-card border-b border-border shadow-sm">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-foreground">Admin Dashboard</h1>
            <div class="flex items-center gap-4">
                <span class="text-muted-foreground">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                <a href="<?php echo url('admin/logout.php'); ?>" class="flex items-center gap-2 px-4 py-2 bg-destructive text-destructive-foreground rounded hover:opacity-90 transition">
                    <iconify-icon icon="mdi:logout" width="18" height="18"></iconify-icon>
                    Logout
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-card border border-border rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <iconify-icon icon="mdi:package" width="32" height="32" class="text-primary"></iconify-icon>
                    <span class="text-3xl font-bold text-foreground"><?php echo $productsCount; ?></span>
                </div>
                <h3 class="text-muted-foreground text-sm uppercase">Total Products</h3>
            </div>

            <div class="bg-card border border-border rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <iconify-icon icon="mdi:cart" width="32" height="32" class="text-accent"></iconify-icon>
                    <span class="text-3xl font-bold text-foreground"><?php echo $ordersCount; ?></span>
                </div>
                <h3 class="text-muted-foreground text-sm uppercase">Total Orders</h3>
            </div>

            <div class="bg-card border border-border rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <iconify-icon icon="mdi:account-group" width="32" height="32" class="text-primary"></iconify-icon>
                    <span class="text-3xl font-bold text-foreground"><?php echo $pendingOrdersCount; ?></span>
                </div>
                <h3 class="text-muted-foreground text-sm uppercase">Pending Orders</h3>
            </div>

            <div class="bg-card border border-border rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <iconify-icon icon="mdi:file-document" width="32" height="32" class="text-accent"></iconify-icon>
                    <span class="text-3xl font-bold text-foreground">₵<?php echo number_format($revenue, 2); ?></span>
                </div>
                <h3 class="text-muted-foreground text-sm uppercase">Total Revenue</h3>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="<?php echo url('admin/products.php'); ?>" class="bg-card border border-border rounded-lg p-6 hover:border-primary transition block">
                <iconify-icon icon="mdi:package" width="48" height="48" class="text-primary mb-4"></iconify-icon>
                <h2 class="text-xl font-bold text-foreground mb-2">Manage Products</h2>
                <p class="text-muted-foreground">Add, edit, or delete products</p>
            </a>

            <a href="<?php echo url('admin/orders.php'); ?>" class="bg-card border border-border rounded-lg p-6 hover:border-primary transition block">
                <iconify-icon icon="mdi:cart" width="48" height="48" class="text-primary mb-4"></iconify-icon>
                <h2 class="text-xl font-bold text-foreground mb-2">Manage Orders</h2>
                <p class="text-muted-foreground">View and update order status</p>
            </a>

            <a href="<?php echo url('admin/content.php'); ?>" class="bg-card border border-border rounded-lg p-6 hover:border-primary transition block">
                <iconify-icon icon="mdi:file-document-edit" width="48" height="48" class="text-primary mb-4"></iconify-icon>
                <h2 class="text-xl font-bold text-foreground mb-2">Manage Content</h2>
                <p class="text-muted-foreground">Update website content</p>
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>

