<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Order Confirmation';

$order_id = $_SESSION['last_order_id'] ?? null;

if (!$order_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :id");
$stmt->execute([':id' => $order_id]);
$order = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :id");
$stmt->execute([':id' => $order_id]);
$items = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="confirmation-box">
    <h1>&#10003; Thank you, <?php echo e($order['customer_name']); ?>!</h1>
    <p>Your order <strong>#<?php echo (int) $order['order_id']; ?></strong> has been placed successfully and is currently <strong><?php echo e($order['status']); ?></strong>.</p>

    <h2>Order Details</h2>
    <ul class="summary-list">
        <?php foreach ($items as $item): ?>
        <li>
            <?php echo e($item['product_name']); ?> &times; <?php echo (int) $item['quantity']; ?>
            <span><?php echo money($item['subtotal']); ?></span>
        </li>
        <?php endforeach; ?>
    </ul>
    <p class="summary-total">Total Paid: <strong><?php echo money($order['total_amount']); ?></strong></p>

    <h2>Shipping To</h2>
    <p><?php echo e($order['shipping_address']); ?><br>
       <?php echo e($order['customer_phone']); ?> &middot; <?php echo e($order['customer_email']); ?></p>

    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
