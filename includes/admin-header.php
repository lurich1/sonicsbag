<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Admin Panel - ' . SITE_NAME; ?>">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | Admin - ' . SITE_NAME : 'Admin - ' . SITE_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo asset('assets/images/photo_2025-11-02_05-35-41-removebg-preview.png'); ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo asset('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('assets/css/normalize.css'); ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Iconify -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    
    <!-- Store JS (for shared cart/wishlist helpers if needed) -->
    <script src="<?php echo asset('assets/js/store.js'); ?>" defer></script>
</head>
<body>

