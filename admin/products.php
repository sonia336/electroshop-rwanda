<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Products';
$active = 'products';

$products = $pdo->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC
")->fetchAll();

require __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-toolbar">
    <h1 style="margin:0;">Products</h1>
    <a href="product-form.php" class="btn btn-primary">+ Add Product</a>
</div>

<?php if (!empty($_GET['deleted'])): ?>
    <div class="alert alert-error" style="background:#dcfce7;color:#166534;">Product deleted.</div>
<?php endif; ?>

<table class="cart-table">
    <thead>
        <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th></th></tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><img src="../<?php echo e($p['image_url']); ?>" alt="<?php echo e($p['name']); ?>" style="width:50px;height:50px;object-fit:cover;border-radius:6px;"></td>
            <td><?php echo e($p['name']); ?></td>
            <td><?php echo e($p['category_name'] ?? '—'); ?></td>
            <td><?php echo money($p['price']); ?></td>
            <td><?php echo (int) $p['stock_quantity']; ?><?php if ($p['stock_quantity'] <= 5) echo ' &#9888;'; ?></td>
            <td style="display:flex;gap:6px;">
                <a href="product-form.php?id=<?php echo (int) $p['product_id']; ?>" class="btn btn-small">Edit</a>
                <form method="post" action="product-actions.php" onsubmit="return confirm('Delete this product?');">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="product_id" value="<?php echo (int) $p['product_id']; ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
