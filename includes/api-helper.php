<?php
// Product Helper Functions (using direct database access)
require_once __DIR__ . '/db-helper.php';

function fetchProducts($filter = null) {
    return getProducts($filter);
}

function transformProduct($product) {
    // Product is already transformed from database
    return $product;
}

function getLabelColor($label) {
    switch (strtoupper($label)) {
        case 'BESTSELLER':
        case 'BESTSELLERS':
            return 'bg-primary text-white';
        case 'NEW ARRIVAL':
        case 'NEW ARRIVALS':
            return 'bg-secondary text-white';
        case 'TRAVEL ESSENTIAL':
        case 'TRAVEL ESSENTIALS':
            return 'bg-accent text-accent-foreground';
        case 'LIMITED STOCK':
            return 'bg-destructive text-white';
        default:
            return 'bg-muted text-foreground';
    }
}
?>

