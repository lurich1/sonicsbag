<?php
require_once 'config.php';
require_once INCLUDES_PATH . '/db-helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$bag_type = $_POST['bag_type'] ?? '';
$description = $_POST['description'] ?? '';
$budget = $_POST['budget'] ?? '';
$deadline = $_POST['deadline'] ?? '';

// Validation
if (empty($name) || empty($email) || empty($phone) || empty($quantity) || empty($bag_type) || empty($description)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

$result = saveCustomOrder([
    'name' => htmlspecialchars($name),
    'email' => htmlspecialchars($email),
    'phone' => htmlspecialchars($phone),
    'quantity' => intval($quantity),
    'bag_type' => htmlspecialchars($bag_type),
    'description' => htmlspecialchars($description),
    'budget' => htmlspecialchars($budget),
    'deadline' => htmlspecialchars($deadline),
]);

if ($result['success']) {
    echo json_encode(['success' => true, 'message' => 'Thank you for your custom order request! We will review it and contact you soon.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}
?>

