<?php
/**
 * Quick fix script for admin password
 * Run this once via browser: http://sonicsbag.poultrycore.com/fix-admin-password.php
 * Then delete this file for security!
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Admin Password</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Fix Admin Password</h1>
    
<?php
try {
    $db = getDB();
    
    // Generate new password hash for 'admin123'
    $newPasswordHash = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Check if admin user exists
    $stmt = $db->prepare("SELECT * FROM adminusers WHERE Username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        // Update existing admin user
        $updateStmt = $db->prepare("UPDATE adminusers SET PasswordHash = ? WHERE Username = 'admin'");
        $updateStmt->execute([$newPasswordHash]);
        
        echo '<div class="success">';
        echo '<strong>✓ Password Updated Successfully!</strong><br>';
        echo 'Username: <strong>admin</strong><br>';
        echo 'Password: <strong>admin123</strong><br><br>';
        echo 'You can now login at: <a href="admin/login.php">admin/login.php</a>';
        echo '</div>';
        
        echo '<div class="info">';
        echo '<strong>⚠️ Security Notice:</strong> Please delete this file (fix-admin-password.php) after use!';
        echo '</div>';
    } else {
        // Create admin user if it doesn't exist
        $insertStmt = $db->prepare("INSERT INTO adminusers (Username, PasswordHash, Email) VALUES (?, ?, ?)");
        $insertStmt->execute(['admin', $newPasswordHash, 'admin@soncis.com']);
        
        echo '<div class="success">';
        echo '<strong>✓ Admin User Created!</strong><br>';
        echo 'Username: <strong>admin</strong><br>';
        echo 'Password: <strong>admin123</strong><br><br>';
        echo 'You can now login at: <a href="admin/login.php">admin/login.php</a>';
        echo '</div>';
    }
    
} catch (PDOException $e) {
    echo '<div class="error">';
    echo '<strong>✗ Error:</strong> ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>
</body>
</html>

