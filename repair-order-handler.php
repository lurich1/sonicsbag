<?php
require_once 'config.php';
require_once INCLUDES_PATH . '/db-helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

function sanitize_input($value) {
    return htmlspecialchars(trim((string) $value));
}

$customerName   = sanitize_input($_POST['customer_name'] ?? '');
$customerEmail  = sanitize_input($_POST['customer_email'] ?? '');
$customerPhone  = sanitize_input($_POST['customer_phone'] ?? '');
$street         = sanitize_input($_POST['address_street'] ?? '');
$city           = sanitize_input($_POST['address_city'] ?? '');
$region         = sanitize_input($_POST['address_region'] ?? '');
$postal         = sanitize_input($_POST['address_postal'] ?? '');
$country        = sanitize_input($_POST['address_country'] ?? '');
$bagType        = sanitize_input($_POST['bag_type'] ?? '');
$bagTypeOther   = sanitize_input($_POST['bag_type_other'] ?? '');
$bagColor       = sanitize_input($_POST['bag_color'] ?? '');
$bagMaterial    = sanitize_input($_POST['bag_material'] ?? '');
$bagSize        = sanitize_input($_POST['bag_size'] ?? '');
$bagSizeCustom  = sanitize_input($_POST['bag_size_custom'] ?? '');
$issues         = sanitize_input($_POST['issues_description'] ?? '');
$urgency        = sanitize_input($_POST['repair_urgency'] ?? '');
$returnMethod   = sanitize_input($_POST['return_method'] ?? '');
$paymentOption  = sanitize_input($_POST['payment_option'] ?? '');
$additional     = sanitize_input($_POST['additional_notes'] ?? '');

$repairTypes    = isset($_POST['repair_types']) && is_array($_POST['repair_types'])
    ? array_map('sanitize_input', $_POST['repair_types'])
    : [];

$required = [
    'customer_name' => $customerName,
    'customer_email' => $customerEmail,
    'customer_phone' => $customerPhone,
    'address_street' => $street,
    'address_city' => $city,
    'address_region' => $region,
    'address_postal' => $postal,
    'address_country' => $country,
    'bag_color' => $bagColor,
    'bag_material' => $bagMaterial,
    'issues_description' => $issues,
    'repair_urgency' => $urgency,
    'return_method' => $returnMethod,
    'payment_option' => $paymentOption,
];

foreach ($required as $field => $value) {
    if (empty($value)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }
}

if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Provide a valid email address.']);
    exit;
}

$finalBagType = $bagType === 'Other' && $bagTypeOther ? $bagTypeOther : ($bagType ?: $bagTypeOther);

$uploadedFiles = [];
$maxFiles = 5;
$allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
$maxSize = 5 * 1024 * 1024; // 5MB

if (!empty($_FILES['reference_images']['name'][0])) {
    $uploadDir = __DIR__ . '/assets/images/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileCount = min(count($_FILES['reference_images']['name']), $maxFiles);
    for ($i = 0; $i < $fileCount; $i++) {
        $error = $_FILES['reference_images']['error'][$i];
        if ($error !== UPLOAD_ERR_OK) {
            continue;
        }

        $tmpName = $_FILES['reference_images']['tmp_name'][$i];
        $fileSize = $_FILES['reference_images']['size'][$i];
        $fileType = mime_content_type($tmpName);

        if ($fileSize > $maxSize || !in_array($fileType, $allowedMime, true)) {
            continue;
        }

        $originalName = basename($_FILES['reference_images']['name'][$i]);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $newName = 'repair-' . time() . '-' . uniqid() . '.' . $extension;
        $destination = $uploadDir . $newName;

        if (move_uploaded_file($tmpName, $destination)) {
            $uploadedFiles[] = 'assets/images/uploads/' . $newName;
        }
    }
}

$payload = [
    'customer_name' => $customerName,
    'customer_email' => $customerEmail,
    'customer_phone' => $customerPhone,
    'address' => [
        'street' => $street,
        'city' => $city,
        'region' => $region,
        'postal' => $postal,
        'country' => $country,
    ],
    'bag' => [
        'type' => $finalBagType ?: 'Not specified',
        'color' => $bagColor,
        'material' => $bagMaterial,
        'size' => $bagSize ?: 'Not specified',
        'size_custom' => $bagSizeCustom,
    ],
    'repair_types' => $repairTypes,
    'issues' => $issues,
    'urgency' => $urgency,
    'return_method' => $returnMethod,
    'payment_option' => $paymentOption,
    'notes' => $additional,
    'uploaded_files' => $uploadedFiles,
];

$result = saveRepairOrder($payload);

if ($result['success']) {
    echo json_encode(['success' => true, 'message' => 'Thanks! Our repair team will review your request and reach out shortly.']);
} else {
    http_response_code(500);
    $message = $result['error'] ?? 'Unable to submit request at this time.';
    echo json_encode(['success' => false, 'message' => $message]);
}
?>


