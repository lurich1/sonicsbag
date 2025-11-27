<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/db-helper.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ' . url('admin/dashboard.php'));
    exit;
}

$error = '';
$success = '';

try {
    $db = getDB();
    // Always ensure table exists (MySQL syntax)
    $db->exec("CREATE TABLE IF NOT EXISTS AdminUsers (
        Id INT AUTO_INCREMENT PRIMARY KEY,
        Username VARCHAR(100) UNIQUE NOT NULL,
        PasswordHash VARCHAR(255) NOT NULL,
        Email VARCHAR(255),
        CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        LastLoginAt DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Ensure at least one admin user exists
    $adminCount = (int) $db->query("SELECT COUNT(*) FROM AdminUsers")->fetchColumn();
    if ($adminCount === 0) {
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO AdminUsers (Username, PasswordHash, Email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $defaultPassword, 'admin@soncis.com']);
        $success = 'Default admin account created! Username: admin, Password: admin123';
    }
} catch (PDOException $e) {
    $error = 'Unable to connect to database. Please check your configuration. Error: ' . htmlspecialchars($e->getMessage());
    error_log("Admin init error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $db->prepare("SELECT * FROM AdminUsers WHERE Username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['PasswordHash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_id'] = $admin['Id'];
                
                $updateStmt = $db->prepare("UPDATE AdminUsers SET LastLoginAt = NOW() WHERE Id = ?");
                $updateStmt->execute([$admin['Id']]);
                
                header('Location: ' . url('admin/dashboard.php'));
                exit;
            } else {
                $error = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            $error = 'Login failed. Please try again.';
            error_log("Admin login error: " . $e->getMessage());
        }
    } else {
        $error = 'Please enter both username and password';
    }
}

$pageTitle = 'Admin Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' | ' . SITE_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo asset('assets/images/photo_2025-11-02_05-35-41-removebg-preview.png'); ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo asset('assets/css/style.css'); ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Iconify -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    
    <style>
        :root {
            --login-bg: #f5f7fb;
            --login-card-bg: #ffffff;
            --login-muted: #6b7280;
            --login-border: #e5e7eb;
            --login-shadow: 0 20px 80px rgba(15, 23, 42, 0.12);
        }
        body {
            background-color: var(--login-bg);
        }
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-split-card {
            width: 100%;
            max-width: 1100px;
            background: var(--login-card-bg);
            border-radius: 28px;
            box-shadow: var(--login-shadow);
            overflow: hidden;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        }
        .login-form-pane {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1.5rem;
        }
        .login-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .login-brand img {
            height: 44px;
            width: auto;
        }
        .login-subtitle {
            color: var(--login-muted);
            font-size: 0.95rem;
        }
        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .input-label {
            font-weight: 600;
            color: #111827;
        }
        .input-field {
            border: 1px solid var(--login-border);
            border-radius: 999px;
            padding: 0.85rem 1.25rem;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #f9fafb;
        }
        .input-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(141, 182, 0, 0.2);
            outline: none;
            background: #fff;
        }
        .form-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--login-muted);
        }
        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .primary-btn {
            width: 100%;
            border: none;
            border-radius: 999px;
            padding: 0.95rem;
            font-weight: 600;
            background: var(--primary);
            color: var(--primary-foreground);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 1rem;
            transition: transform 0.15s, box-shadow 0.15s;
            cursor: pointer;
        }
        .primary-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 20px rgba(141, 182, 0, 0.25);
        }
        .primary-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .helper-text {
            text-align: center;
            font-size: 0.85rem;
            color: var(--login-muted);
        }
        .login-image-pane {
            position: relative;
            min-height: 420px;
            background: #000;
        }
        .login-image-pane img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .login-image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(25, 31, 48, 0.2) 10%, rgba(25, 31, 48, 0.85) 90%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 2.5rem;
            color: #fff;
        }
        .image-caption {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .image-description {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 1.5rem;
        }
        .image-dots {
            display: flex;
            gap: 0.6rem;
        }
        .image-dots span {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.4);
        }
        .image-dots span.active {
            background: #fff;
        }
        .message-bar {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .message-success {
            background: rgba(141, 182, 0, 0.12);
            border: 1px solid rgba(141, 182, 0, 0.3);
            color: #3a5300;
        }
        .message-error {
            background: rgba(217, 43, 43, 0.12);
            border: 1px solid rgba(217, 43, 43, 0.3);
            color: #7a1c1c;
        }
        @media (max-width: 768px) {
            .login-form-pane {
                padding: 2rem;
            }
            .login-image-pane {
                min-height: 280px;
            }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-split-card">
        <div class="login-form-pane">
            <div class="login-brand">
                <img src="<?php echo asset('assets/images/logo.svg'); ?>" alt="SONCIS logo">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-muted-foreground">SONCIS Admin</p>
                    <h1 class="text-3xl font-bold text-foreground">Welcome Back</h1>
                </div>
            </div>
            <p class="login-subtitle">Sign in to manage products, orders, and content all in one secure place.</p>

            <?php if ($success): ?>
                <div class="message-bar message-success">
                    <iconify-icon icon="mdi:check-circle" width="20" height="20"></iconify-icon>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="message-bar message-error">
                    <iconify-icon icon="mdi:alert-circle" width="20" height="20"></iconify-icon>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="input-group">
                    <label for="username" class="input-label">Username</label>
                    <input
                        id="username"
                        type="text"
                        name="username"
                        placeholder="Enter your username"
                        required
                        autocomplete="username"
                        class="input-field"
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    />
                </div>

                <div class="input-group">
                    <label for="password" class="input-label">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                        class="input-field"
                    />
                </div>

                <div class="form-meta">
                    <label class="remember-checkbox">
                        <input type="checkbox" name="remember" />
                        Remember me
                    </label>
                    <a href="#" class="text-primary text-sm hover:underline">Forgot password?</a>
                </div>

                <button type="submit" class="primary-btn">
                    <iconify-icon icon="mdi:lock" width="20" height="20"></iconify-icon>
                    Sign In
                </button>
            </form>

            <div class="helper-text">
                Default Credentials — Username: <code>admin</code>, Password: <code>admin123</code>
            </div>
            <div class="helper-text text-xs">
                Need help? Email <a href="mailto:contact@soncis.com" class="text-primary font-medium">contact@soncis.com</a>
            </div>
        </div>

        <div class="login-image-pane">
            <img src="<?php echo asset('assets/images/banner-large-image1.jpg'); ?>" alt="Admin workspace">
            <div class="login-image-overlay">
                <p class="image-caption">Empower Every Operation</p>
                <p class="image-description">
                    Track orders, update catalogues, and keep SONCIS customers delighted using one intuitive dashboard.
                </p>
                <div class="image-dots">
                    <span class="active"></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userInput = document.getElementById('username');
    if (userInput) userInput.focus();

    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', function() {
        const submitBtn = loginForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<iconify-icon icon="mdi:loading" width="20" height="20" class="animate-spin"></iconify-icon> Signing in...';
    });
});
</script>
</body>
</html>

