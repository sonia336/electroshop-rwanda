<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Order Detail';
$active = 'orders';

$orderId = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :id");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch();

if (!$order) {
    require __DIR__ . '/includes/admin-header.php';
    echo '<h1>Order not found</h1><p><a href="orders.php">&larr; Back to orders</a></p>';
    require __DIR__ . '/includes/admin-footer.php';
    exit;
}

$itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :id");
$itemsStmt->execute([':id' => $orderId]);
$items = $itemsStmt->fetchAll();

require __DIR__ . '/includes/admin-header.php';
?>

<p><a href="orders.php">&larr; Back to orders</a></p>
<h1>Order #<?php echo (int) $order['order_id']; ?></h1>
<p><span class="status-badge"><?php echo e($order['status']); ?></span> &nbsp; Placed on <?php echo e(date('d M Y, H:i', strtotime($order['created_at']))); ?></p>

<h2>Customer</h2>
<p>
    <strong><?php echo e($order['customer_name']); ?></strong><br>
    <?php echo e($order['customer_email']); ?><br>
    <?php echo e($order['customer_phone']); ?><br>
    <?php echo e($order['shipping_address']); ?>
</p>

<h2>Items</h2>
<table class="cart-table">
    <thead>
        <tr><th>Product</th><th>Unit Price</th><th>Qty</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?php echo e($item['product_name']); ?></td>
            <td><?php echo money($item['unit_price']); ?></td>
            <td><?php echo (int) $item['quantity']; ?></td>
            <td><?php echo money($item['subtotal']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr><td colspan="3" style="text-align:right;"><strong>Total</strong></td><td><strong><?php echo money($order['total_amount']); ?></strong></td></tr>
    </tfoot>
</table>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
