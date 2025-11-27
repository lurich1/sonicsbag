<?php
require_once 'config.php';

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
    echo json_encode(['success' => false, 'message' => 'Paystack secret key is not configured on the server.']);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || empty($data['email']) || empty($data['amount'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid email and amount are required to initialize Paystack checkout.']);
    exit;
}

$email = $data['email'];
$amount = floatval($data['amount']);
$phone = $data['phone'] ?? '';
$name = $data['name'] ?? '';

if ($amount <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than zero.']);
    exit;
}

// Get callback URL
$baseUrl = defined('SITE_URL') ? SITE_URL : 'http://localhost';
$callbackUrl = rtrim($baseUrl, '/') . url('paystack-callback.php');

// Initialize Paystack transaction
$ch = curl_init('https://api.paystack.co/transaction/initialize');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $paystackSecretKey,
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => $email,
    'amount' => round($amount * 100), // Paystack expects amount in the smallest currency unit (pesewas)
    'currency' => 'GHS',
    'channels' => ['mobile_money'],
    'callback_url' => $callbackUrl,
    'metadata' => [
        'customer_name' => $name,
        'customer_phone' => $phone,
        'integration' => 'soncis-store',
    ],
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    error_log("Paystack initialization failed: HTTP $httpCode - $response");
    http_response_code(500);
    $errorData = json_decode($response, true);
    echo json_encode([
        'success' => false,
        'message' => $errorData['message'] ?? 'Failed to initialize Paystack transaction.'
    ]);
    exit;
}

$result = json_decode($response, true);

if (!$result || !$result['status'] || !isset($result['data']['authorization_url'])) {
    error_log("Paystack initialization invalid response: " . $response);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $result['message'] ?? 'Failed to initialize Paystack transaction.'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'authorizationUrl' => $result['data']['authorization_url'],
    'reference' => $result['data']['reference'],
    'accessCode' => $result['data']['access_code'] ?? '',
]);
?>

