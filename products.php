<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Products';

$search = clean($_GET['search'] ?? '');
$category = isset($_GET['category']) ? (int) $_GET['category'] : 0;

// Build query dynamically but SAFELY using prepared statements
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (name LIKE :search OR description LIKE :search2)";
    $params[':search'] = '%' . $search . '%';
    $params[':search2'] = '%' . $search . '%';
}

if ($category > 0) {
    $sql .= " AND category_id = :category";
    $params[':category'] = $category;
}

$sql .= " ORDER BY name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<h1>Our Products</h1>

<form method="get" class="filter-bar">
    <input type="text" name="search" placeholder="Search products..." value="<?php echo e($search); ?>">

    <select name="category">
        <option value="0">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?php echo (int) $cat['category_id']; ?>" <?php echo $category === (int) $cat['category_id'] ? 'selected' : ''; ?>>
                <?php echo e($cat['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-small">Filter</button>
</form>

<?php if (empty($products)): ?>
    <p>No products found matching your criteria.</p>
<?php else: ?>
<div class="product-grid">
    <?php foreach ($products as $product): ?>
    <div class="product-card">
        <a href="product-details.php?id=<?php echo (int) $product['product_id']; ?>">
            <img src="<?php echo e($product['image_url']); ?>" alt="<?php echo e($product['name']); ?>" onerror="this.src='assets/images/placeholder.png'">
            <h3><?php echo e($product['name']); ?></h3>
            <p class="price"><?php echo money($product['price']); ?></p>
        </a>
        <?php if ($product['stock_quantity'] > 0): ?>
        <form action="cart-actions.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?php echo (int) $product['product_id']; ?>">
            <button type="submit" class="btn btn-small">Add to Cart</button>
        </form>
        <?php else: ?>
            <p class="out-of-stock">Out of Stock</p>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
