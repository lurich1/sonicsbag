<?php
// Database Connection and Helper Functions

// Database Configuration
define('DB_TYPE', 'mysql'); // 'sqlite', 'mysql', or 'sqlserver'
define('DB_PATH', __DIR__ . '/../database/soncis.db'); // SQLite path (unused for MySQL)
define('DB_HOST', '127.0.0.1'); // MySQL host
define('DB_NAME', 'soncisdb'); // MySQL database name
define('DB_USER', 'root'); // MySQL username (default XAMPP)
define('DB_PASS', ''); // MySQL password (blank by default)

class Database {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                if (DB_TYPE === 'sqlite') {
                    self::$connection = new PDO('sqlite:' . DB_PATH);
                    self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } elseif (DB_TYPE === 'mysql') {
                    self::$connection = new PDO(
                        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                        DB_USER,
                        DB_PASS
                    );
                    self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } elseif (DB_TYPE === 'sqlserver') {
                    self::$connection = new PDO(
                        "sqlsrv:Server=" . DB_HOST . ";Database=" . DB_NAME,
                        DB_USER,
                        DB_PASS
                    );
                    self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                die("Database connection failed. Please check your configuration.");
            }
        }
        return self::$connection;
    }
}

// Helper function to get database connection
function getDB() {
    return Database::getConnection();
}
?>

