<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-auth.php';
require_admin('login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['csrf_token'] ?? '')) {
    header('Location: products.php');
    exit;
}

$action = $_POST['action'] ?? '';
$productId = (int) ($_POST['product_id'] ?? 0);

if ($action === 'delete' && $productId > 0) {
    // Products referenced by past orders can't be hard-deleted (FK RESTRICT) --
    // that's intentional, it protects order history integrity.
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);
    } catch (PDOException $e) {
        // Likely a foreign key restriction because this product appears in an existing order.
        header('Location: products.php?deleted=0&error=has_orders');
        exit;
    }
    header('Location: products.php?deleted=1');
    exit;
}

if ($action === 'save') {
    $name = clean($_POST['name'] ?? '');
    $description = clean($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock_quantity'] ?? 0);
    $imageUrl = clean($_POST['image_url'] ?? '') ?: 'assets/images/placeholder.png';
    $categoryId = (int) ($_POST['category_id'] ?? 0);

    if ($name === '' || $price < 0 || $stock < 0 || $categoryId <= 0) {
        header('Location: product-form.php?id=' . $productId . '&error=1');
        exit;
    }

    if ($productId > 0) {
        $stmt = $pdo->prepare("
            UPDATE products
            SET name = :name, description = :description, price = :price,
                stock_quantity = :stock, image_url = :image, category_id = :cat
            WHERE product_id = :id
        ");
        $stmt->execute([
            ':name' => $name, ':description' => $description, ':price' => $price,
            ':stock' => $stock, ':image' => $imageUrl, ':cat' => $categoryId, ':id' => $productId,
        ]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO products (name, description, price, stock_quantity, image_url, category_id)
            VALUES (:name, :description, :price, :stock, :image, :cat)
        ");
        $stmt->execute([
            ':name' => $name, ':description' => $description, ':price' => $price,
            ':stock' => $stock, ':image' => $imageUrl, ':cat' => $categoryId,
        ]);
    }
}

header('Location: products.php');
exit;
