<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    die('Invalid request. Please go back and try again.');
}

$cart = get_cart();
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

// ---------- Server-side validation ----------
$errors = [];

$customer_name = clean($_POST['customer_name'] ?? '');
$customer_email = filter_var(trim($_POST['customer_email'] ?? ''), FILTER_SANITIZE_EMAIL);
$customer_phone = clean($_POST['customer_phone'] ?? '');
$shipping_address = clean($_POST['shipping_address'] ?? '');

if ($customer_name === '' || strlen($customer_name) < 2) {
    $errors[] = 'Please provide a valid full name.';
}
if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please provide a valid email address.';
}
if (!preg_match('/^[0-9+ ]{9,15}$/', $customer_phone)) {
    $errors[] = 'Please provide a valid phone number.';
}
if ($shipping_address === '' || strlen($shipping_address) < 5) {
    $errors[] = 'Please provide a complete shipping address.';
}

if (!empty($errors)) {
    // In a fuller implementation we'd redisplay checkout.php with these errors.
    // Kept simple here for clarity.
    $page_title = 'Checkout Error';
    require __DIR__ . '/includes/header.php';
    echo '<h1>There was a problem with your order</h1><ul>';
    foreach ($errors as $error) {
        echo '<li>' . e($error) . '</li>';
    }
    echo '</ul><p><a href="checkout.php">Go back to checkout</a></p>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

// ---------- Fetch current product data (never trust the cart's price) ----------
$ids = array_map('intval', array_keys($cart));
$placeholders = implode(',', array_fill(0, count($ids), '?'));

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    $productsById = [];
    foreach ($products as $p) {
        $productsById[$p['product_id']] = $p;
    }

    $total = 0;
    $orderItems = [];

    foreach ($cart as $product_id => $qty) {
        if (!isset($productsById[$product_id])) {
            continue; // product no longer exists
        }
        $product = $productsById[$product_id];

        if ($product['stock_quantity'] < $qty) {
            throw new Exception("Not enough stock for {$product['name']}.");
        }

        $subtotal = $product['price'] * $qty;
        $total += $subtotal;
        $orderItems[] = [
            'product_id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'qty' => $qty,
            'subtotal' => $subtotal,
        ];
    }

    if (empty($orderItems)) {
        throw new Exception('Your cart items are no longer available.');
    }

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_email, customer_phone, shipping_address, total_amount, status)
                            VALUES (:user_id, :name, :email, :phone, :address, :total, 'Pending')");
    $stmt->execute([
        ':user_id' => current_user_id(),
        ':name' => $customer_name,
        ':email' => $customer_email,
        ':phone' => $customer_phone,
        ':address' => $shipping_address,
        ':total' => $total,
    ]);
    $order_id = $pdo->lastInsertId();

    // Insert order items + decrement stock
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, subtotal)
                                VALUES (:order_id, :product_id, :product_name, :unit_price, :quantity, :subtotal)");
    $stockStmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - :qty WHERE product_id = :id");

    foreach ($orderItems as $item) {
        $itemStmt->execute([
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':product_name' => $item['name'],
            ':unit_price' => $item['price'],
            ':quantity' => $item['qty'],
            ':subtotal' => $item['subtotal'],
        ]);
        $stockStmt->execute([':qty' => $item['qty'], ':id' => $item['product_id']]);
    }

    $pdo->commit();

    clear_cart();
    $_SESSION['last_order_id'] = $order_id;

    header('Location: order-confirmation.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $page_title = 'Order Failed';
    require __DIR__ . '/includes/header.php';
    echo '<h1>We could not process your order</h1><p>' . e($e->getMessage()) . '</p>';
    echo '<p><a href="cart.php">Back to cart</a></p>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
