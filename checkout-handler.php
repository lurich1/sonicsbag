<?php
require_once 'config.php';
require_once INCLUDES_PATH . '/db-helper.php';

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors, but log them
ini_set('log_errors', 1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$json = file_get_contents('php://input');
$orderData = json_decode($json, true);

if (!$orderData) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit;
}

// Validation
if (empty($orderData['customerName']) || empty($orderData['customerEmail']) || 
    empty($orderData['customerPhone']) || empty($orderData['shippingAddress']) ||
    empty($orderData['items']) || count($orderData['items']) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($orderData['customerEmail'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

$paymentMethod = strtolower($orderData['paymentMethod'] ?? 'cash');
$paymentStatusMap = [
    'cash' => 'Pending - Cash on Delivery',
    'mobile-money' => 'Awaiting Mobile Money Payment',
    'bank-transfer' => 'Awaiting Bank Transfer',
    'paypal' => 'Awaiting PayPal Payment',
];
$paymentStatus = $paymentStatusMap[$paymentMethod] ?? 'Pending';

if ($paymentMethod === 'mobile-money') {
    $mobileMoneyNumber = preg_replace('/\D+/', '', $orderData['mobileMoneyNumber'] ?? '');
    if (strlen($mobileMoneyNumber) < 9) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Valid mobile money number required for mobile money payments.']);
        exit;
    }
    $orderData['mobileMoneyNumber'] = $mobileMoneyNumber;
}

$meta = [];
if (!empty($orderData['company'])) {
    $meta['company'] = $orderData['company'];
}
if (!empty($orderData['notes'])) {
    $meta['notes'] = $orderData['notes'];
}
if (!empty($orderData['mobileMoneyNumber'])) {
    $meta['mobile_money_number'] = $orderData['mobileMoneyNumber'];
}
if (!empty($orderData['paymentDetails'])) {
    $meta['payment_details'] = $orderData['paymentDetails'];
}

$orderData['meta'] = array_merge($meta, is_array($orderData['meta'] ?? null) ? $orderData['meta'] : []);
$orderData['paymentStatus'] = $paymentStatus;

try {
    $result = createOrder($orderData);

    if ($result['success']) {
        echo json_encode([
            'success' => true, 
            'message' => 'Order placed successfully',
            'orderNumber' => $result['orderNumber'],
            'orderId' => $result['orderId']
        ]);
    } else {
        http_response_code(500);
        $errorMessage = 'An error occurred while placing your order. Please try again.';
        if (!empty($result['error'])) {
            // Log the detailed error for debugging
            error_log("Order creation failed: " . $result['error']);
            error_log("Order data: " . json_encode($orderData));
            
            // For security, don't expose database errors directly to users
            // But we can provide a more helpful message
            $error = $result['error'];
            if (stripos($error, 'table') !== false && stripos($error, "doesn't exist") !== false) {
                $errorMessage = 'Database table not found. Please ensure the database is properly set up.';
            } elseif (stripos($error, 'UNIQUE constraint') !== false || stripos($error, 'Duplicate entry') !== false) {
                $errorMessage = 'An order with this information already exists. Please contact support if this is an error.';
            } elseif (stripos($error, 'FOREIGN KEY') !== false) {
                $errorMessage = 'Invalid product information. Please refresh the page and try again.';
            } elseif (stripos($error, 'Column') !== false && stripos($error, "doesn't exist") !== false) {
                $errorMessage = 'Database schema mismatch. Please check your database structure.';
            } elseif (stripos($error, 'SQLSTATE') !== false) {
                $errorMessage = 'Database error occurred. Please check your database connection and try again.';
            }
        }
        // In development, include error details
        $response = ['success' => false, 'message' => $errorMessage];
        // Always show detailed errors for debugging (helps identify the issue)
        if (!empty($result['error'])) {
            $response['debug'] = $result['error'];
            $response['error_code'] = $result['error'];
        }
        echo json_encode($response);
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log("Checkout handler exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again.']);
}
?>

