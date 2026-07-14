<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $page_title = 'Product Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<p>Sorry, that product could not be found. <a href="products.php">Back to products</a></p>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $product['name'];

require __DIR__ . '/includes/header.php';
?>

<div class="product-details">
    <img src="<?php echo e($product['image_url']); ?>" alt="<?php echo e($product['name']); ?>" onerror="this.src='assets/images/placeholder.png'">

    <div class="product-info">
        <h1><?php echo e($product['name']); ?></h1>
        <p class="category-tag"><?php echo e($product['category_name'] ?? 'Uncategorized'); ?></p>
        <p class="price-large"><?php echo money($product['price']); ?></p>
        <p><?php echo nl2br(e($product['description'])); ?></p>

        <?php if ($product['stock_quantity'] > 0): ?>
            <p class="stock-info">In stock: <?php echo (int) $product['stock_quantity']; ?></p>
            <form action="cart-actions.php" method="post" class="add-to-cart-form">
                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo (int) $product['product_id']; ?>">
                <label for="qty">Quantity:</label>
                <input type="number" id="qty" name="quantity" value="1" min="1" max="<?php echo (int) $product['stock_quantity']; ?>">
                <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>
        <?php else: ?>
            <p class="out-of-stock">Currently Out of Stock</p>
        <?php endif; ?>

        <a href="products.php" class="back-link">&larr; Back to Products</a>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
