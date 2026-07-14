<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$productId = (int) ($_GET['id'] ?? 0);
$product = [
    'name' => '', 'description' => '', 'price' => '', 'stock_quantity' => '',
    'image_url' => 'assets/images/placeholder.png', 'category_id' => '',
];

if ($productId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :id");
    $stmt->execute([':id' => $productId]);
    $found = $stmt->fetch();
    if ($found) $product = $found;
}

$page_title = $productId > 0 ? 'Edit Product' : 'Add Product';
$active = 'products';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

require __DIR__ . '/includes/admin-header.php';
?>

<p><a href="products.php">&larr; Back to products</a></p>
<h1><?php echo $productId > 0 ? 'Edit Product' : 'Add Product'; ?></h1>

<form method="post" action="product-actions.php" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="product_id" value="<?php echo (int) $productId; ?>">

    <label for="name">Product Name</label>
    <input type="text" id="name" name="name" required value="<?php echo e($product['name']); ?>">

    <label for="category_id">Category</label>
    <select id="category_id" name="category_id" required>
        <option value="">-- Select --</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?php echo (int) $c['category_id']; ?>" <?php echo (string)$product['category_id'] === (string)$c['category_id'] ? 'selected' : ''; ?>>
                <?php echo e($c['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="description">Description</label>
    <textarea id="description" name="description"><?php echo e($product['description']); ?></textarea>

    <label for="price">Price (RWF)</label>
    <input type="number" id="price" name="price" min="0" step="1" required value="<?php echo e($product['price']); ?>">

    <label for="stock_quantity">Stock Quantity</label>
    <input type="number" id="stock_quantity" name="stock_quantity" min="0" step="1" required value="<?php echo e($product['stock_quantity']); ?>">

    <label for="image_url">Image path</label>
    <input type="text" id="image_url" name="image_url" value="<?php echo e($product['image_url']); ?>" placeholder="assets/images/yourfile.jpg">
    <small style="color:#6b7280;">Upload the image file into <code>assets/images/</code> first, then type its filename here.</small>

    <div class="actions">
        <button type="submit" class="btn btn-primary">Save Product</button>
        <a href="products.php" class="btn btn-small">Cancel</a>
    </div>
</form>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
