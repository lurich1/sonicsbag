<?php
/**
 * Database bootstrap script.
 * Run once (CLI: php database/setup.php) to create the production schema.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: text/plain');

try {
    $db = getDB();
    if (DB_TYPE === 'sqlite') {
        $db->exec('PRAGMA foreign_keys = ON');
    }

    $schemaStatements = [
        'Products' => "
            CREATE TABLE IF NOT EXISTS Products (
                Id INTEGER PRIMARY KEY AUTOINCREMENT,
                Name TEXT NOT NULL,
                Price REAL NOT NULL,
                Description TEXT,
                ImageUrl TEXT,
                Category TEXT,
                InStock INTEGER NOT NULL DEFAULT 1,
                StockQuantity INTEGER NOT NULL DEFAULT 0,
                Tags TEXT,
                CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
                UpdatedAt DATETIME
            )
        ",
        'Orders' => "
            CREATE TABLE IF NOT EXISTS Orders (
                Id INTEGER PRIMARY KEY AUTOINCREMENT,
                OrderNumber TEXT NOT NULL UNIQUE,
                CustomerName TEXT NOT NULL,
                CustomerEmail TEXT NOT NULL,
                CustomerPhone TEXT,
                ShippingAddress TEXT,
                BillingAddress TEXT,
                Total TEXT NOT NULL,
                Status TEXT NOT NULL DEFAULT 'Pending',
                PaymentMethod TEXT NOT NULL DEFAULT 'Cash',
                PaymentStatus TEXT NOT NULL DEFAULT 'Pending',
                CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
                UpdatedAt DATETIME
            )
        ",
        'OrderItems' => "
            CREATE TABLE IF NOT EXISTS OrderItems (
                Id INTEGER PRIMARY KEY AUTOINCREMENT,
                OrderId INTEGER NOT NULL,
                ProductId INTEGER,
                ProductName TEXT NOT NULL,
                Price TEXT NOT NULL,
                Quantity INTEGER NOT NULL DEFAULT 1,
                Subtotal TEXT NOT NULL,
                FOREIGN KEY (OrderId) REFERENCES Orders(Id) ON DELETE CASCADE
            )
        ",
        'SiteContents' => "
            CREATE TABLE IF NOT EXISTS SiteContents (
                Id INTEGER PRIMARY KEY AUTOINCREMENT,
                Key TEXT NOT NULL UNIQUE,
                Value TEXT,
                UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ",
        'AdminUsers' => "
            CREATE TABLE IF NOT EXISTS AdminUsers (
                Id INTEGER PRIMARY KEY AUTOINCREMENT,
                Username TEXT NOT NULL UNIQUE,
                PasswordHash TEXT NOT NULL,
                Email TEXT,
                CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
                LastLoginAt DATETIME
            )
        "
    ];

    foreach ($schemaStatements as $name => $sql) {
        $db->exec($sql);
        echo "[OK] {$name} table ready." . PHP_EOL;
    }

    // Seed default admin user
    $adminCount = (int) $db->query("SELECT COUNT(*) FROM AdminUsers")->fetchColumn();
    if ($adminCount === 0) {
        $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO AdminUsers (Username, PasswordHash, Email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $passwordHash, 'admin@soncis.com']);
        echo "[OK] Default admin created (admin / admin123)." . PHP_EOL;
    } else {
        echo "[OK] Admin user(s) already exist." . PHP_EOL;
    }

    // Optional seed content placeholder
    $contentCount = (int) $db->query("SELECT COUNT(*) FROM SiteContents")->fetchColumn();
    if ($contentCount === 0) {
        $stmt = $db->prepare("INSERT INTO SiteContents (Key, Value) VALUES (?, ?)");
        $stmt->execute(['homepage_hero', json_encode(['title' => 'Crafted with purpose', 'subtitle' => 'Built to last'])]);
        echo "[OK] Site content placeholder created." . PHP_EOL;
    }

    echo PHP_EOL . "Database setup complete." . PHP_EOL;
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database setup failed: " . $e->getMessage();
    error_log('Database setup failed: ' . $e->getMessage());
    exit(1);
}

