<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Orders';
$active = 'orders';

$statusFilter = $_GET['status'] ?? '';
$validStatuses = ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'];

if ($statusFilter && in_array($statusFilter, $validStatuses, true)) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE status = :status ORDER BY created_at DESC");
    $stmt->execute([':status' => $statusFilter]);
} else {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
}
$orders = $stmt->fetchAll();

require __DIR__ . '/includes/admin-header.php';
?>

<h1>Orders</h1>

<div class="admin-toolbar">
    <div>
        <a href="orders.php" class="btn btn-small<?php echo $statusFilter === '' ? ' btn-primary' : ''; ?>">All</a>
        <?php foreach ($validStatuses as $s): ?>
            <a href="orders.php?status=<?php echo urlencode($s); ?>" class="btn btn-small<?php echo $statusFilter === $s ? ' btn-primary' : ''; ?>"><?php echo e($s); ?></a>
        <?php endforeach; ?>
    </div>
</div>

<?php if (empty($orders)): ?>
    <p>No orders found.</p>
<?php else: ?>
    <table class="cart-table">
        <thead>
            <tr><th>#</th><th>Customer</th><th>Email</th><th>Phone</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo (int) $order['order_id']; ?></td>
                <td><?php echo e($order['customer_name']); ?></td>
                <td><?php echo e($order['customer_email']); ?></td>
                <td><?php echo e($order['customer_phone']); ?></td>
                <td><?php echo money($order['total_amount']); ?></td>
                <td>
                    <form method="post" action="order-actions.php" style="display:flex;gap:6px;align-items:center;">
                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                        <input type="hidden" name="order_id" value="<?php echo (int) $order['order_id']; ?>">
                        <select name="status" class="status-select" onchange="this.form.submit()">
                            <?php foreach ($validStatuses as $s): ?>
                                <option value="<?php echo e($s); ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>><?php echo e($s); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <noscript><button type="submit" class="btn btn-small">Update</button></noscript>
                    </form>
                </td>
                <td><?php echo e(date('d M Y, H:i', strtotime($order['created_at']))); ?></td>
                <td><a href="order-view.php?id=<?php echo (int) $order['order_id']; ?>" class="btn btn-small">View</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
