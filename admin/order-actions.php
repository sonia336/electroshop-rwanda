<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-auth.php';
require_admin('login.php');

$validStatuses = ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify($_POST['csrf_token'] ?? '')) {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if ($orderId > 0 && in_array($status, $validStatuses, true)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE order_id = :id");
        $stmt->execute([':status' => $status, ':id' => $orderId]);
    }
}

header('Location: orders.php');
exit;
