<?php
require 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'] ?? [];

if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit;
}

try {
    $conn->begin_transaction();

    $total_price = 0;
    foreach ($cart as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    $stmt = $conn->prepare('INSERT INTO orders (user_id, total_price, order_date) VALUES (?, ?, NOW())');
    $user_id = 12;
    $stmt->bind_param('id', $user_id, $total_price);
    $stmt->execute();

    $order_id = $conn->insert_id;

    $conn->commit();

    echo json_encode(['success' => true, 'id' => $order_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error placing order.', 'error' => $e->getMessage()]);
    exit;
}
?>
