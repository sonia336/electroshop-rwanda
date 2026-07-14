<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$errors = [];

if (is_admin()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND role = 'admin'");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            $errors[] = 'Invalid admin email or password.';
        } else {
            $_SESSION['admin_id'] = $admin['user_id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_email'] = $admin['email'];
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - ElectroShop</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-login-body">

<div class="admin-login-wrapper">
    <h1>ElectroShop <span>Admin</span></h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul><?php foreach ($errors as $error) echo '<li>' . e($error) . '</li>'; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">

        <label for="email">Admin Email</label>
        <input type="email" id="email" name="email" required value="<?php echo e($_POST['email'] ?? ''); ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <p><a href="../index.php">&larr; Back to store</a></p>
</div>

</body>
</html>
