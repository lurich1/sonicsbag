<?php
require_once __DIR__ . '/auth-check.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db-helper.php';

$pageTitle = 'Manage Orders';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $orderId = intval($_POST['orderId']);
    $status = $_POST['status'] ?? '';
    $paymentStatus = $_POST['paymentStatus'] ?? '';
    
    try {
        $db = getDB();
        if (!empty($status) && !empty($paymentStatus)) {
            $stmt = $db->prepare("UPDATE Orders SET Status = ?, PaymentStatus = ?, UpdatedAt = datetime('now') WHERE Id = ?");
            $stmt->execute([$status, $paymentStatus, $orderId]);
        } elseif (!empty($status)) {
            $stmt = $db->prepare("UPDATE Orders SET Status = ?, UpdatedAt = datetime('now') WHERE Id = ?");
            $stmt->execute([$status, $orderId]);
        } elseif (!empty($paymentStatus)) {
            $stmt = $db->prepare("UPDATE Orders SET PaymentStatus = ?, UpdatedAt = datetime('now') WHERE Id = ?");
            $stmt->execute([$paymentStatus, $orderId]);
        }
        $message = 'Order status updated successfully!';
    } catch (PDOException $e) {
        $error = 'Failed to update order status: ' . $e->getMessage();
        error_log("Order update error: " . $e->getMessage());
    }
}

// Get status filter
$statusFilter = $_GET['status'] ?? '';

// Load orders
try {
    $db = getDB();
    if (!empty($statusFilter)) {
        $stmt = $db->prepare("SELECT * FROM Orders WHERE Status = ? ORDER BY CreatedAt DESC");
        $stmt->execute([$statusFilter]);
    } else {
        $stmt = $db->query("SELECT * FROM Orders ORDER BY CreatedAt DESC");
    }
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $metaStmt = $db->prepare("SELECT MetaKey, MetaValue FROM OrderMeta WHERE OrderId = ?");

    // Load order items and meta for each order
    foreach ($orders as &$order) {
        $stmt = $db->prepare("SELECT * FROM OrderItems WHERE OrderId = ?");
        $stmt->execute([$order['Id']]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $metaStmt->execute([$order['Id']]);
        $metaRows = $metaStmt->fetchAll(PDO::FETCH_ASSOC);
        $order['meta'] = [];
        foreach ($metaRows as $metaRow) {
            $order['meta'][$metaRow['MetaKey']] = $metaRow['MetaValue'];
        }
    }
} catch (PDOException $e) {
    error_log("Error loading orders: " . $e->getMessage());
    $orders = [];
}

$statusOptions = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
        <?php if (isset($message)): ?>
            <div class="mb-4 p-3 bg-green-100 border border-green-400 rounded text-green-700">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-400 rounded text-red-700">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <h1 class="text-3xl font-bold text-foreground mb-6">Manage Orders</h1>

        <!-- Filters -->
        <div class="mb-6 flex gap-2">
            <form method="GET" class="flex gap-2">
                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="px-4 py-2 border border-border rounded bg-background text-foreground"
                >
                    <option value="">All Orders</option>
                    <option value="Pending" <?php echo $statusFilter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Processing" <?php echo $statusFilter === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="Shipped" <?php echo $statusFilter === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="Delivered" <?php echo $statusFilter === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="Cancelled" <?php echo $statusFilter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </form>
        </div>

        <!-- Orders List -->
        <div class="space-y-4">
            <?php foreach ($orders as $order): ?>
                <div class="bg-card border border-border rounded-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-foreground">Order #<?php echo htmlspecialchars($order['OrderNumber']); ?></h3>
                            <p class="text-sm text-muted-foreground">
                                <?php echo date('F j, Y g:i A', strtotime($order['CreatedAt'])); ?>
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <?php
                            $statusLower = strtolower($order['Status']);
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'processing' => 'bg-blue-100 text-blue-800',
                                'shipped' => 'bg-purple-100 text-purple-800',
                                'delivered' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                            $statusColor = $statusColors[$statusLower] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $statusColor; ?>">
                                <?php echo strtoupper($order['Status']); ?>
                            </span>
                            <?php
                            $paymentStatusLower = strtolower($order['PaymentStatus']);
                            $paymentColor = $statusColors[$paymentStatusLower] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $paymentColor; ?>">
                                Payment: <?php echo strtoupper($order['PaymentStatus']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm font-medium text-foreground">Customer</p>
                            <p class="text-muted-foreground"><?php echo htmlspecialchars($order['CustomerName']); ?></p>
                            <p class="text-muted-foreground text-sm"><?php echo htmlspecialchars($order['CustomerEmail']); ?></p>
                            <?php if ($order['CustomerPhone']): ?>
                                <p class="text-muted-foreground text-sm"><?php echo htmlspecialchars($order['CustomerPhone']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-foreground">Shipping Address</p>
                            <p class="text-muted-foreground"><?php echo nl2br(htmlspecialchars($order['ShippingAddress'] ?: 'N/A')); ?></p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-medium text-foreground mb-2">Items</p>
                        <div class="space-y-2">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="flex justify-between text-sm">
                                    <span class="text-foreground"><?php echo htmlspecialchars($item['ProductName']); ?> x <?php echo $item['Quantity']; ?></span>
                                    <span class="text-muted-foreground"><?php echo htmlspecialchars($item['Subtotal']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-border">
                        <p class="text-lg font-bold text-foreground">Total: <?php echo htmlspecialchars($order['Total']); ?></p>
                        <div class="flex gap-2">
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="orderId" value="<?php echo $order['Id']; ?>">
                                <input type="hidden" name="paymentStatus" value="<?php echo htmlspecialchars($order['PaymentStatus']); ?>">
                                <select
                                    name="status"
                                    onchange="this.form.submit()"
                                    class="px-3 py-1 border border-border rounded bg-background text-foreground text-sm"
                                >
                                    <option value="Pending" <?php echo $order['Status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Processing" <?php echo $order['Status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="Shipped" <?php echo $order['Status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Delivered" <?php echo $order['Status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="Cancelled" <?php echo $order['Status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="orderId" value="<?php echo $order['Id']; ?>">
                                <input type="hidden" name="status" value="<?php echo htmlspecialchars($order['Status']); ?>">
                                <select
                                    name="paymentStatus"
                                    onchange="this.form.submit()"
                                    class="px-3 py-1 border border-border rounded bg-background text-foreground text-sm"
                                >
                                    <option value="Pending" <?php echo $order['PaymentStatus'] === 'Pending' ? 'selected' : ''; ?>>Payment Pending</option>
                                    <option value="Paid" <?php echo $order['PaymentStatus'] === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                    <option value="Failed" <?php echo $order['PaymentStatus'] === 'Failed' ? 'selected' : ''; ?>>Failed</option>
                                    <option value="Refunded" <?php echo $order['PaymentStatus'] === 'Refunded' ? 'selected' : ''; ?>>Refunded</option>
                                </select>
                            </form>
                        </div>
                    </div>

                        <?php if (!empty($order['meta'])): ?>
                            <div class="bg-muted/30 rounded-lg p-3 text-sm text-muted-foreground space-y-1">
                                <?php if (!empty($order['meta']['mobile_money_number'])): ?>
                                    <p><strong>Mobile Money:</strong> <?php echo htmlspecialchars($order['meta']['mobile_money_number']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($order['meta']['payment_details'])): ?>
                                    <p><strong>Payment Details:</strong> <?php echo htmlspecialchars($order['meta']['payment_details']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($order['meta']['company'])): ?>
                                    <p><strong>Company:</strong> <?php echo htmlspecialchars($order['meta']['company']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($order['meta']['notes'])): ?>
                                    <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($order['meta']['notes'])); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($orders)): ?>
                <div class="text-center py-12 text-muted-foreground">
                    <iconify-icon icon="mdi:package" width="64" height="64" class="mx-auto mb-4 opacity-50"></iconify-icon>
                    <p>No orders found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>

