<?php
require_once __DIR__ . '/db.php';

// Product Functions
function getProducts($filter = null, $category = null) {
    try {
        $db = getDB();
        $sql = "SELECT * FROM products WHERE InStock = 1";
        $params = [];
        
        if ($filter) {
            // Filter by tag (tags are stored as comma-separated string or JSON)
            $sql .= " AND (Tags LIKE ? OR Tags LIKE ? OR Tags LIKE ?)";
            $filterParam = "%{$filter}%";
            $params[] = $filterParam;
            $params[] = "%\"{$filter}\"%";
            $params[] = "%'{$filter}'%";
        }
        
        if ($category) {
            $sql .= " AND Category = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY CreatedAt DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map('transformProductFromDB', $products);
    } catch (PDOException $e) {
        error_log("Error fetching products: " . $e->getMessage());
        return [];
    }
}

function getProductById($id) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM products WHERE Id = ?");
        $stmt->execute([intval($id)]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            return transformProductFromDB($product);
        }
        return null;
    } catch (PDOException $e) {
        error_log("Error fetching product: " . $e->getMessage());
        return null;
    }
}

function transformProductFromDB($product) {
    // Parse tags (can be JSON array or comma-separated string)
    $tags = [];
    if (!empty($product['Tags'])) {
        $tagsJson = json_decode($product['Tags'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($tagsJson)) {
            $tags = $tagsJson;
        } else {
            // Try comma-separated
            $tags = array_map('trim', explode(',', $product['Tags']));
        }
    }
    
    // Format price
    $price = $product['Price'];
    if (is_numeric($price)) {
        $price = '₵' . number_format(floatval($price), 2);
    }
    
    return [
        'id' => intval($product['Id']),
        'name' => $product['Name'] ?? '',
        'price' => $price,
        'description' => $product['Description'] ?? '',
        'imageUrl' => $product['ImageUrl'] ?? '',
        'category' => $product['Category'] ?? '',
        'inStock' => (bool)($product['InStock'] ?? true),
        'stockQuantity' => intval($product['StockQuantity'] ?? 0),
        'tags' => $tags,
        'label' => getProductLabel($tags),
    ];
}

function getProductLabel($tags) {
    if (!is_array($tags)) {
        return null;
    }
    
    foreach ($tags as $tag) {
        $tagLower = strtolower($tag);
        if (strpos($tagLower, 'bestseller') !== false) {
            return 'BESTSELLER';
        }
        if (strpos($tagLower, 'newarrival') !== false || strpos($tagLower, 'new arrival') !== false) {
            return 'NEW ARRIVAL';
        }
        if (strpos($tagLower, 'limited') !== false) {
            return 'LIMITED STOCK';
        }
    }
    
    return null;
}

// Order Functions
function ensureOrderMetaTable($db) {
    static $ensured = false;
    if ($ensured) {
        return;
    }

    $dbType = defined('DB_TYPE') ? DB_TYPE : 'sqlite';
    
    if ($dbType === 'mysql') {
        // MySQL syntax - use lowercase table name to match existing schema
        $db->exec("
            CREATE TABLE IF NOT EXISTS ordermeta (
                Id INT AUTO_INCREMENT PRIMARY KEY,
                OrderId INT NOT NULL,
                MetaKey VARCHAR(255) NOT NULL,
                MetaValue TEXT,
                FOREIGN KEY (OrderId) REFERENCES orders(Id) ON DELETE CASCADE,
                INDEX IX_OrderMeta_OrderId (OrderId)
            )
        ");
    } else {
        // SQLite syntax
        $db->exec("
            CREATE TABLE IF NOT EXISTS OrderMeta (
                Id INTEGER PRIMARY KEY AUTOINCREMENT,
                OrderId INTEGER NOT NULL,
                MetaKey TEXT NOT NULL,
                MetaValue TEXT,
                FOREIGN KEY (OrderId) REFERENCES Orders(Id) ON DELETE CASCADE
            )
        ");
    }
    $ensured = true;
}

function saveOrderMetaEntries($db, $orderId, $meta = []) {
    if (empty($meta) || !is_array($meta)) {
        return;
    }

    $metaStmt = $db->prepare("INSERT INTO ordermeta (OrderId, MetaKey, MetaValue) VALUES (?, ?, ?)");
    foreach ($meta as $key => $value) {
        if ($value === null || $value === '') {
            continue;
        }
        $metaStmt->execute([$orderId, (string) $key, (string) $value]);
    }
}

function createOrder($orderData) {
    try {
        $db = getDB();
        ensureOrderMetaTable($db);
        $db->beginTransaction();
        
        // Generate order number
        $orderNumber = 'SONCIS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        
        // Get current timestamp (works for all database types)
        $createdAt = date('Y-m-d H:i:s');
        
        // Convert total from formatted string to numeric value for MySQL
        $dbType = defined('DB_TYPE') ? DB_TYPE : 'sqlite';
        $totalValue = $orderData['total'];
        if ($dbType === 'mysql' || $dbType === 'sqlserver') {
            // Remove currency symbols and commas, convert to float
            $totalValue = floatval(str_replace(['₵', '$', ',', ' '], '', $orderData['total']));
        }
        
        // Insert order (using lowercase table name to match database schema)
        $stmt = $db->prepare("
            INSERT INTO orders (OrderNumber, CustomerName, CustomerEmail, CustomerPhone, 
                              ShippingAddress, BillingAddress, Total, Status, 
                              PaymentMethod, PaymentStatus, CreatedAt)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $paymentMethod = $orderData['paymentMethod'] ?? 'Cash';
        $paymentStatus = $orderData['paymentStatus'] ?? 'Pending';
        $orderStatus = 'Pending'; // Default order status

        $stmt->execute([
            $orderNumber,
            $orderData['customerName'],
            $orderData['customerEmail'],
            $orderData['customerPhone'] ?? '',
            $orderData['shippingAddress'] ?? '',
            $orderData['billingAddress'] ?? '',
            $totalValue,
            $orderStatus,
            $paymentMethod,
            $paymentStatus,
            $createdAt,
        ]);
        
        $orderId = $db->lastInsertId();
        
        // Insert order items (using lowercase table name to match database schema)
        if (!empty($orderData['items'])) {
            $itemStmt = $db->prepare("
                INSERT INTO orderitems (OrderId, ProductId, ProductName, Price, Quantity, Subtotal)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($orderData['items'] as $item) {
                // Extract numeric price value
                $itemPrice = floatval(str_replace(['₵', '$', ',', ' '], '', $item['price']));
                $quantity = intval($item['quantity'] ?? 1);
                $subtotal = $itemPrice * $quantity;
                
                // For MySQL, use numeric values; for SQLite, use formatted strings
                $priceValue = ($dbType === 'mysql' || $dbType === 'sqlserver') ? $itemPrice : $item['price'];
                $subtotalValue = ($dbType === 'mysql' || $dbType === 'sqlserver') ? $subtotal : ('₵' . number_format($subtotal, 2));
                
                $itemStmt->execute([
                    $orderId,
                    $item['id'],
                    $item['name'],
                    $priceValue,
                    $quantity,
                    $subtotalValue
                ]);
            }
        }
        
        if (!empty($orderData['meta'])) {
            saveOrderMetaEntries($db, $orderId, $orderData['meta']);
        }
        
        $db->commit();
        return ['success' => true, 'orderNumber' => $orderNumber, 'orderId' => $orderId];
    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        $errorMsg = $e->getMessage();
        $errorCode = $e->getCode();
        error_log("Error creating order [Code: $errorCode]: $errorMsg");
        error_log("SQL Error Info: " . print_r($e->errorInfo ?? [], true));
        return ['success' => false, 'error' => $errorMsg, 'code' => $errorCode];
    } catch (Exception $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        error_log("General error creating order: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Contact Form Functions
function saveContactMessage($data) {
    try {
        // For now, we'll just log it or send email
        // You can create a ContactMessages table if needed
        $message = "New Contact Form Submission\n\n";
        $message .= "Name: " . $data['name'] . "\n";
        $message .= "Email: " . $data['email'] . "\n";
        $message .= "Subject: " . $data['subject'] . "\n";
        $message .= "Message: " . $data['message'] . "\n";
        
        // Log to file
        error_log($message);
        
        // Optionally send email
        $to = 'contact@soncis.com';
        $subject = 'Contact Form: ' . $data['subject'];
        $headers = "From: " . $data['email'] . "\r\n";
        $headers .= "Reply-To: " . $data['email'] . "\r\n";
        
        @mail($to, $subject, $data['message'], $headers);
        
        return ['success' => true];
    } catch (Exception $e) {
        error_log("Error saving contact message: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Custom Order Functions
function saveCustomOrder($data) {
    try {
        // Similar to contact, log or save to database
        $message = "New Custom Order Request\n\n";
        $message .= "Name: " . $data['name'] . "\n";
        $message .= "Email: " . $data['email'] . "\n";
        $message .= "Phone: " . $data['phone'] . "\n";
        $message .= "Quantity: " . $data['quantity'] . "\n";
        $message .= "Bag Type: " . $data['bag_type'] . "\n";
        $message .= "Description: " . $data['description'] . "\n";
        $message .= "Budget: " . ($data['budget'] ?? 'Not specified') . "\n";
        $message .= "Deadline: " . ($data['deadline'] ?? 'Not specified') . "\n";
        
        error_log($message);
        
        // Optionally send email
        $to = 'contact@soncis.com';
        $subject = 'Custom Order Request: ' . $data['bag_type'];
        $headers = "From: " . $data['email'] . "\r\n";
        $headers .= "Reply-To: " . $data['email'] . "\r\n";
        
        @mail($to, $subject, $message, $headers);
        
        return ['success' => true];
    } catch (Exception $e) {
        error_log("Error saving custom order: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function saveRepairOrder($data) {
    try {
        $address = $data['address'] ?? [];
        $bag = $data['bag'] ?? [];

        $message = "New Bag Repair Request\n\n";
        $message .= "Customer: " . ($data['customer_name'] ?? '') . "\n";
        $message .= "Email: " . ($data['customer_email'] ?? '') . "\n";
        $message .= "Phone: " . ($data['customer_phone'] ?? '') . "\n\n";
        $message .= "Address: " . ($address['street'] ?? '') . ", " . ($address['city'] ?? '') . ", " . ($address['region'] ?? '') . ", " . ($address['postal'] ?? '') . ", " . ($address['country'] ?? '') . "\n\n";
        $message .= "Bag Type: " . ($bag['type'] ?? '') . "\n";
        $message .= "Color: " . ($bag['color'] ?? '') . "\n";
        $message .= "Material: " . ($bag['material'] ?? '') . "\n";
        $message .= "Size: " . ($bag['size'] ?? '') . "\n";
        if (!empty($bag['size_custom'])) {
            $message .= "Custom Size: " . $bag['size_custom'] . "\n";
        }
        $message .= "\nRepairs Needed: " . (empty($data['repair_types']) ? 'Not specified' : implode(', ', $data['repair_types'])) . "\n";
        $message .= "Urgency: " . ($data['urgency'] ?? '') . "\n";
        $message .= "Return Method: " . ($data['return_method'] ?? '') . "\n";
        $message .= "Payment Option: " . ($data['payment_option'] ?? '') . "\n\n";
        $message .= "Issue Description:\n" . ($data['issues'] ?? '') . "\n\n";
        if (!empty($data['notes'])) {
            $message .= "Additional Notes:\n" . $data['notes'] . "\n\n";
        }
        if (!empty($data['uploaded_files'])) {
            $message .= "Uploaded Images:\n" . implode("\n", $data['uploaded_files']) . "\n\n";
        }

        error_log($message);

        $to = 'repairs@soncis.com';
        $subject = 'Bag Repair Request - ' . ($bag['type'] ?? 'SONCIS Bag');
        $headers = "From: " . ($data['customer_email'] ?? 'no-reply@soncis.com') . "\r\n";
        $headers .= "Reply-To: " . ($data['customer_email'] ?? 'no-reply@soncis.com') . "\r\n";

        @mail($to, $subject, $message, $headers);

        return ['success' => true];
    } catch (Exception $e) {
        error_log("Error saving repair order: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
?>

