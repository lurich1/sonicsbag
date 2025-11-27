<?php
// Configuration file for SONCIS PHP Frontend

// Site Configuration
define('SITE_NAME', 'SONCIS');
// Auto-detect site URL based on environment
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('SITE_URL', $protocol . '://' . $host);
define('SITE_DESCRIPTION', 'If it needs a bag, it needs SONCIS. Crafted with Purpose. Built to Last.');

// Paystack Configuration
// Get your keys from https://dashboard.paystack.com/#/settings/developer
 // Your Paystack Public Key (starts with pk_)
define('PAYSTACK_SECRET_KEY', 'sk_test_54e9330a22e15665d72ae46260c1917b73fcd9e0');
define('PAYSTACK_PUBLIC_KEY', 'pk_test_9b2120a8562ce8ca19f89f696b6ca70baca6c03f');
// Get base path (works for both root and subdirectory installations)
function getBasePath() {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $scriptDir = dirname($scriptName);
    
    // Normalize the path
    $scriptDir = str_replace('\\', '/', $scriptDir);
    $scriptDir = rtrim($scriptDir, '/');
    
    // If script is in root (dirname returns '/' or '.')
    if ($scriptDir === '/' || $scriptDir === '.' || empty($scriptDir)) {
        return '';
    }
    
    // Check if we're in a known subdirectory that should go back to root
    // For admin pages, assets are at root level, so we need to go up
    $parts = explode('/', trim($scriptDir, '/'));
    
    // If the last part is 'admin', we're in admin subdirectory
    // Assets are at root, so return parent directory or empty
    if (end($parts) === 'admin') {
        array_pop($parts);
        if (empty($parts)) {
            return '';
        }
        return '/' . implode('/', $parts);
    }
    
    // Otherwise, this directory is the base
    return $scriptDir;
}

// Initialize BASE_PATH
$GLOBALS['BASE_PATH'] = getBasePath();

// Paths
define('BASE_PATH', __DIR__);
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('PAGES_PATH', BASE_PATH . '/pages');
define('ASSETS_PATH', BASE_PATH . '/assets');

// Helper Functions
function getImageUrl($imagePath) {
    if (empty($imagePath)) {
        return asset('assets/images/placeholder.jpg');
    }
    if (strpos($imagePath, 'http') === 0) {
        return $imagePath;
    }
    // If path already starts with /assets, use it as is
    if (strpos($imagePath, '/assets/') === 0) {
        return asset(ltrim($imagePath, '/'));
    }
    return asset('assets/images/' . basename($imagePath));
}

function formatPrice($price) {
    return $price; // Price is already formatted from API
}

function getCurrentPage() {
    $page = $_GET['page'] ?? 'home';
    return htmlspecialchars($page);
}

// Helper function to get asset URL
function asset($path) {
    // Remove leading slash if present
    $path = ltrim($path, '/');
    $basePath = isset($GLOBALS['BASE_PATH']) ? $GLOBALS['BASE_PATH'] : getBasePath();
    return $basePath . '/' . $path;
}

// Helper function to get URL (for internal links)
function url($path = '') {
    $basePath = isset($GLOBALS['BASE_PATH']) ? $GLOBALS['BASE_PATH'] : getBasePath();
    // Remove leading slash if present
    $path = ltrim($path, '/');
    if (empty($path)) {
        return $basePath . '/';
    }
    return $basePath . '/' . $path;
}
?>

