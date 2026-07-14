<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Your Cart';

$cart = get_cart();
$items = [];
$total = 0;

if (!empty($cart)) {
    $ids = array_map('intval', array_keys($cart));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    foreach ($products as $product) {
        $qty = $cart[$product['product_id']];
        $subtotal = $product['price'] * $qty;
        $total += $subtotal;
        $items[] = [
            'product' => $product,
            'qty' => $qty,
            'subtotal' => $subtotal,
        ];
    }
}

require __DIR__ . '/includes/header.php';
?>

<h1>Your Shopping Cart</h1>

<?php if (empty($items)): ?>
    <p>Your cart is empty. <a href="products.php">Browse products</a></p>
<?php else: ?>

<table class="cart-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <div class="cart-product">
                    <img src="<?php echo e($item['product']['image_url']); ?>" alt="" onerror="this.src='assets/images/placeholder.png'">
                    <?php echo e($item['product']['name']); ?>
                </div>
            </td>
            <td><?php echo money($item['product']['price']); ?></td>
            <td>
                <form action="cart-actions.php" method="post" class="qty-form">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="product_id" value="<?php echo (int) $item['product']['product_id']; ?>">
                    <input type="number" name="quantity" value="<?php echo (int) $item['qty']; ?>" min="1" max="<?php echo (int) $item['product']['stock_quantity']; ?>">
                    <button type="submit" class="btn btn-small">Update</button>
                </form>
            </td>
            <td><?php echo money($item['subtotal']); ?></td>
            <td>
                <form action="cart-actions.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="product_id" value="<?php echo (int) $item['product']['product_id']; ?>">
                    <button type="submit" class="btn btn-danger">Remove</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="cart-summary">
    <h2>Total: <?php echo money($total); ?></h2>
    <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
    <form action="cart-actions.php" method="post" style="display:inline;">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
        <input type="hidden" name="action" value="clear">
        <button type="submit" class="btn btn-danger">Clear Cart</button>
    </form>
</div>

<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
