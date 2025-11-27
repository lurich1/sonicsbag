<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

$storageDir = BASE_PATH . '/database';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

$filePath = $storageDir . '/newsletter-signups.csv';
$line = sprintf("\"%s\",\"%s\"\n", $email, date('c'));

try {
    file_put_contents($filePath, $line, FILE_APPEND | LOCK_EX);
    echo json_encode(['success' => true, 'message' => 'Subscribed successfully.']);
} catch (Exception $e) {
    http_response_code(500);
    error_log('Newsletter save error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to save subscription.']);
}

