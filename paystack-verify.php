<?php
require_once 'config.php';
require_once INCLUDES_PATH . '/db-helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get Paystack secret key from config
$paystackSecretKey = defined('PAYSTACK_SECRET_KEY') ? PAYSTACK_SECRET_KEY : '';

if (empty($paystackSecretKey)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Paystack secret key is not configured.']);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || empty($data['reference'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Payment reference is required.']);
    exit;
}

$reference = $data['reference'];

// Verify transaction with Paystack
$ch = curl_init('https://api.paystack.co/transaction/verify/' . urlencode($reference));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $paystackSecretKey,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    error_log("Paystack verification failed: HTTP $httpCode - $response");
    http_response_code(500);
    $errorData = json_decode($response, true);
    echo json_encode([
        'success' => false,
        'verified' => false,
        'message' => $errorData['message'] ?? 'Failed to verify Paystack transaction.'
    ]);
    exit;
}

$result = json_decode($response, true);

if (!$result || !$result['status']) {
    echo json_encode([
        'success' => false,
        'verified' => false,
        'message' => $result['message'] ?? 'Transaction verification failed.',
        'payment' => $result['data'] ?? null
    ]);
    exit;
}

$transaction = $result['data'];

// Check if transaction was successful (matching React version logic)
if (strtolower($transaction['status']) !== 'success') {
    echo json_encode([
        'success' => true,
        'verified' => false,
        'message' => 'Payment was not successful. Status: ' . $transaction['status'],
        'payment' => $transaction
    ]);
    exit;
}

// Payment is successful - create the order
// The order data should be passed from the frontend or stored in session
$orderData = null;
$orderNumber = null;
$orderCreated = false;

// Try to get order data from POST body (sent from frontend)
if (!empty($data['orderData'])) {
    $orderData = $data['orderData'];
} else {
    // If not in POST, you could check session or database for pending orders
    // For now, we'll return success but note that order needs to be created
    echo json_encode([
        'success' => true,
        'verified' => true,
        'orderNumber' => null,
        'orderCreated' => false,
        'message' => 'Payment verified successfully. Please complete your order.'
    ]);
    exit;
}

// Create the order with payment details
if ($orderData) {
    $orderData['paymentStatus'] = 'Paid - Mobile Money';
    $orderData['paymentTransactionId'] = $reference;
    $orderData['paymentMethod'] = 'mobile-money';
    
    $result = createOrder($orderData);
    
    if ($result['success']) {
        $orderNumber = $result['orderNumber'];
        $orderCreated = true;
    }
}

// Return response matching React version structure
echo json_encode([
    'success' => true,
    'verified' => true,
    'payment' => $transaction, // Include payment data like React version
    'orderNumber' => $orderNumber,
    'orderCreated' => $orderCreated,
    'message' => $orderCreated ? 'Payment verified and order created successfully.' : 'Payment verified but order creation failed.'
]);
?>

