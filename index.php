<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Home';

// Fetch a few featured products (latest 4)
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
$featured = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="hero-text">
        <h1>Power Up Your Life with ElectroShop</h1>
        <p>Quality smartphones, laptops, audio gear and accessories — delivered across Rwanda.</p>
        <a href="products.php" class="btn btn-primary">Shop Now</a>
    </div>
</section>

<section class="categories">
    <h2>Shop by Category</h2>
    <div class="category-grid">
        <a href="products.php?category=1" class="category-card">
            <img src="assets/images/phone1.jpg" alt="Smartphones">
            <span>Smartphones</span>
        </a>
        <a href="products.php?category=2" class="category-card">
            <img src="assets/images/laptop1.jpg" alt="Laptops">
            <span>Laptops</span>
        </a>
        <a href="products.php?category=3" class="category-card">
            <img src="assets/images/audio1.jpg" alt="Audio">
            <span>Audio</span>
        </a>
        <a href="products.php?category=4" class="category-card">
            <img src="assets/images/acc1.jpg" alt="Accessories">
            <span>Accessories</span>
        </a>
    </div>
</section>

<section class="featured-products">
    <h2>Featured Products</h2>
    <div class="product-grid">
        <?php foreach ($featured as $product): ?>
        <div class="product-card">
            <a href="product-details.php?id=<?php echo (int) $product['product_id']; ?>">
                <img src="<?php echo e($product['image_url']); ?>" alt="<?php echo e($product['name']); ?>" onerror="this.src='assets/images/placeholder.png'">
                <h3><?php echo e($product['name']); ?></h3>
                <p class="price"><?php echo money($product['price']); ?></p>
            </a>
            <form action="cart-actions.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo (int) $product['product_id']; ?>">
                <button type="submit" class="btn btn-small">Add to Cart</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
