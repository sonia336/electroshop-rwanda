<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Register';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $full_name = clean($_POST['full_name'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (strlen($full_name) < 2) $errors[] = 'Please enter your full name.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
        if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
        if ($password !== $confirm_password) $errors[] = 'Passwords do not match.';

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $errors[] = 'An account with this email already exists.';
            }
        }

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (:name, :email, :hash)");
            $stmt->execute([':name' => $full_name, ':email' => $email, ':hash' => $hash]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_email'] = $email;

            header('Location: index.php');
            exit;
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="auth-form-wrapper">
    <h1>Create an Account</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul><?php foreach ($errors as $error) echo '<li>' . e($error) . '</li>'; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">

        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" required value="<?php echo e($_POST['full_name'] ?? ''); ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?php echo e($_POST['email'] ?? ''); ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="6">

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
