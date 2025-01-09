<?php
require 'db.php'; 

header('Content-Type: application/json');


$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'] ?? null;
$cart = $data['cart'] ?? [];


if (!$order_id || empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data.']);
    exit;
}


foreach ($cart as $index => $item) {
    if (!isset($item['book_id'], $item['quantity'], $item['price'])) {
        echo json_encode(['success' => false, 'message' => "Invalid cart data at index $index: missing book_id, quantity, or price."]);
        exit;
    }

    if (empty($item['book_id']) || empty($item['quantity']) || empty($item['price'])) {
        echo json_encode(['success' => false, 'message' => "Invalid cart data at index $index: book_id, quantity, or price is empty."]);
        exit;
    }

 
    $book_id = $conn->real_escape_string($item['book_id']);
    $quantity = $conn->real_escape_string($item['quantity']);

    $stock_check_query = "SELECT stock, title FROM books WHERE id = '$book_id'";
    $result = $conn->query($stock_check_query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['stock'] < $quantity) {
            echo json_encode(['success' => false, 'message' => " Sorry not enough stock for the book '{$row['title']}'. It will be Available soon!"]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Book '{$row['title']}' not found in inventory."]);
        exit;
    }
}


try {
    $conn->begin_transaction();
    foreach ($cart as $item) {
        $book_id = $conn->real_escape_string($item['book_id']);
        $quantity = $conn->real_escape_string($item['quantity']);
        $price = $conn->real_escape_string($item['price']);

        
        $query = "INSERT INTO purchased_books (order_id, book_id, quantity, price, purchase_date) 
                  VALUES ('$order_id', '$book_id', '$quantity', '$price', NOW())";
        if (!$conn->query($query)) {
            throw new Exception("Database error: " . $conn->error);
        }

       
        $update_stock_query = "UPDATE books SET stock = stock - '$quantity' WHERE id = '$book_id'";
        if (!$conn->query($update_stock_query)) {
            throw new Exception("Failed to update stock for book ID $book_id: " . $conn->error);
        }
    }
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Purchased books updated successfully.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
}
?>
