<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Dashboard';
$active = 'dashboard';

$totalOrders   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue  = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status != 'Cancelled'")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$lowStock      = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= 5")->fetchColumn();

$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 8")->fetchAll();

require __DIR__ . '/includes/admin-header.php';
?>

<h1>Dashboard</h1>
<p>Overview of your store's activity.</p>

<div class="admin-stats">
    <div class="stat-card"><div class="stat-value"><?php echo (int) $totalOrders; ?></div><div class="stat-label">Total Orders</div></div>
    <div class="stat-card"><div class="stat-value"><?php echo money($totalRevenue); ?></div><div class="stat-label">Total Revenue</div></div>
    <div class="stat-card"><div class="stat-value"><?php echo (int) $pendingOrders; ?></div><div class="stat-label">Pending Orders</div></div>
    <div class="stat-card"><div class="stat-value"><?php echo (int) $totalProducts; ?></div><div class="stat-label">Products</div></div>
    <div class="stat-card"><div class="stat-value"><?php echo (int) $lowStock; ?></div><div class="stat-label">Low Stock (&le;5)</div></div>
</div>

<h2>Recent Orders</h2>
<?php if (empty($recentOrders)): ?>
    <p>No orders yet.</p>
<?php else: ?>
    <table class="cart-table">
        <thead>
            <tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $order): ?>
            <tr>
                <td>#<?php echo (int) $order['order_id']; ?></td>
                <td><?php echo e($order['customer_name']); ?></td>
                <td><?php echo money($order['total_amount']); ?></td>
                <td><span class="status-badge"><?php echo e($order['status']); ?></span></td>
                <td><?php echo e(date('d M Y', strtotime($order['created_at']))); ?></td>
                <td><a href="order-view.php?id=<?php echo (int) $order['order_id']; ?>" class="btn btn-small">View</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p style="margin-top:14px;"><a href="orders.php">View all orders &rarr;</a></p>
<?php endif; ?>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
