<?php
require_once __DIR__ . '/includes/functions.php';

// Only accept POST requests for cart mutations
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

// CSRF protection
if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    die('Invalid request. Please go back and try again.');
}

$action = $_POST['action'] ?? '';
$product_id = (int) ($_POST['product_id'] ?? 0);

switch ($action) {
    case 'add':
        $qty = (int) ($_POST['quantity'] ?? 1);
        add_to_cart($product_id, $qty);
        break;

    case 'update':
        $qty = (int) ($_POST['quantity'] ?? 1);
        update_cart_item($product_id, $qty);
        break;

    case 'remove':
        remove_from_cart($product_id);
        break;

    case 'clear':
        clear_cart();
        break;
}

header('Location: cart.php');
exit;
