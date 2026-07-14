<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Checkout';

$cart = get_cart();
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$ids = array_map('intval', array_keys($cart));
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll();

$items = [];
$total = 0;
foreach ($products as $product) {
    $qty = $cart[$product['product_id']];
    $subtotal = $product['price'] * $qty;
    $total += $subtotal;
    $items[] = ['product' => $product, 'qty' => $qty, 'subtotal' => $subtotal];
}

// Pre-fill fields if user is logged in
$default_name = $_SESSION['user_name'] ?? '';
$default_email = $_SESSION['user_email'] ?? '';

require __DIR__ . '/includes/header.php';
?>

<h1>Checkout</h1>

<div class="checkout-layout">
    <form action="process-order.php" method="post" class="checkout-form">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">

        <h2>Customer Information</h2>

        <label for="customer_name">Full Name *</label>
        <input type="text" id="customer_name" name="customer_name" required value="<?php echo e($default_name); ?>">

        <label for="customer_email">Email Address *</label>
        <input type="email" id="customer_email" name="customer_email" required value="<?php echo e($default_email); ?>">

        <label for="customer_phone">Phone Number *</label>
        <input type="tel" id="customer_phone" name="customer_phone" required placeholder="07XXXXXXXX" pattern="[0-9+ ]{9,15}">

        <label for="shipping_address">Shipping Address *</label>
        <textarea id="shipping_address" name="shipping_address" required rows="3" placeholder="District, Sector, Street..."></textarea>

        <button type="submit" class="btn btn-primary">Place Order</button>
    </form>

    <div class="order-summary">
        <h2>Order Summary</h2>
        <ul class="summary-list">
            <?php foreach ($items as $item): ?>
            <li>
                <?php echo e($item['product']['name']); ?> &times; <?php echo (int) $item['qty']; ?>
                <span><?php echo money($item['subtotal']); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <p class="summary-total">Total: <strong><?php echo money($total); ?></strong></p>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
