<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Login';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // Same generic error whether email or password is wrong (avoid user enumeration)
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Invalid email or password.';
        } else {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: index.php');
            exit;
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="auth-form-wrapper">
    <h1>Login</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul><?php foreach ($errors as $error) echo '<li>' . e($error) . '</li>'; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?php echo e($_POST['email'] ?? ''); ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
