<?php
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? e($page_title) . ' - ElectroShop Rwanda' : 'ElectroShop Rwanda'; ?></title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="index.php" class="logo">Electro<span>Shop</span></a>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">&#9776;</button>

        <nav class="main-nav" id="mainNav">
            <a href="index.php">Home</a>
            <a href="products.php">Products</a>
            <a href="cart.php">Cart (<?php echo cart_count(); ?>)</a>
            <?php if (is_logged_in()): ?>
                <a href="orders.php">My Orders</a>
                <a href="logout.php">Logout (<?php echo e($_SESSION['user_name'] ?? ''); ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container main-content">
