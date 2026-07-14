<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$page_title = 'My Orders';

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute([':uid' => current_user_id()]);
$orders = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<h1>My Orders</h1>

<?php if (empty($orders)): ?>
    <p>You haven't placed any orders yet. <a href="products.php">Start shopping</a></p>
<?php else: ?>
<table class="cart-table">
    <thead>
        <tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td>#<?php echo (int) $order['order_id']; ?></td>
            <td><?php echo e(date('d M Y', strtotime($order['created_at']))); ?></td>
            <td><?php echo money($order['total_amount']); ?></td>
            <td><span class="status-badge"><?php echo e($order['status']); ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
