<?php
/**
 * ONE-TIME SETUP SCRIPT.
 * Run this once in your browser to:
 *   1. Add a `role` column to the users table (if it doesn't exist yet)
 *   2. Create (or upgrade) the admin account
 *
 * After it runs successfully, DELETE this file (or at least rename it) --
 * leaving a script that can (re)create an admin account publicly
 * accessible is a security risk.
 */

require_once __DIR__ . '/../includes/db.php';

$adminEmail = 'electroshop@gmail.com';
$adminPassword = '123456789'; // change this after first login for real use
$adminName = 'Administrator';

$messages = [];

// 1. Add the role column if it doesn't already exist
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'customer' AFTER password_hash");
    $messages[] = "Added 'role' column to users table.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        $messages[] = "'role' column already exists — skipped.";
    } else {
        die("Unexpected DB error: " . htmlspecialchars($e->getMessage()));
    }
}

// 2. Create or upgrade the admin account
$hash = password_hash($adminPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
$stmt->execute([':email' => $adminEmail]);
$existing = $stmt->fetch();

if ($existing) {
    $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash, role = 'admin', full_name = :name WHERE email = :email");
    $stmt->execute([':hash' => $hash, ':name' => $adminName, ':email' => $adminEmail]);
    $messages[] = "Existing user with this email upgraded to admin and password reset.";
} else {
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (:name, :email, :hash, 'admin')");
    $stmt->execute([':name' => $adminName, ':email' => $adminEmail, ':hash' => $hash]);
    $messages[] = "New admin account created.";
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Admin Setup</title>
<style>body{font-family:sans-serif;max-width:600px;margin:60px auto;line-height:1.6;color:#1f2937}
.ok{background:#dcfce7;color:#166534;padding:12px;border-radius:8px;margin-bottom:10px}
.warn{background:#fef3c7;color:#92400e;padding:14px;border-radius:8px;margin-top:20px}
a{color:#2563eb}</style>
</head>
<body>
<h1>Admin Setup Complete</h1>
<?php foreach ($messages as $m): ?>
    <div class="ok"><?php echo htmlspecialchars($m); ?></div>
<?php endforeach; ?>

<p>You can now log in at: <a href="login.php">admin/login.php</a></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($adminEmail); ?><br>
<strong>Password:</strong> the one you set in this script</p>

<div class="warn">
    <strong>Important:</strong> Delete this file (<code>admin/setup-admin.php</code>) now that setup is done.
    Leaving it on your server means anyone who finds the URL could reset your admin password.
</div>
</body>
</html>
