<?php
require_once __DIR__ . '/../../includes/admin-auth.php';
require_admin('login.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? e($page_title) . ' - Admin' : 'Admin'; ?> - ElectroShop</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-logo">Electro<span>Shop</span> <small>Admin</small></div>
        <nav class="admin-nav">
            <a href="index.php" class="<?php echo ($active ?? '') === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
            <a href="orders.php" class="<?php echo ($active ?? '') === 'orders' ? 'active' : ''; ?>">Orders</a>
            <a href="products.php" class="<?php echo ($active ?? '') === 'products' ? 'active' : ''; ?>">Products</a>
        </nav>
        <div class="admin-user-box">
            Logged in as<br><strong><?php echo e($_SESSION['admin_name'] ?? ''); ?></strong>
            <a href="logout.php" class="btn btn-small" style="margin-top:10px;display:inline-block;">Logout</a>
            <a href="../index.php" class="admin-back-link">&larr; View store</a>
        </div>
    </aside>

    <main class="admin-content">
