<?php
include 'db.php'; 
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}


$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['book_id'])) {
    echo json_encode(['success' => false, 'message' => 'Book ID is missing.']);
    exit();
}

$bookId = intval($data['book_id']);
$userId = $_SESSION['user_id'];


$query = $conn->prepare("SELECT stock FROM books WHERE id = ?");
$query->bind_param("i", $bookId);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Book not found.']);
    exit();
}

$book = $result->fetch_assoc();
if ($book['stock'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Book is out of stock.']);
    exit();
}


$conn->begin_transaction();
try {
    
    $query = $conn->prepare("INSERT INTO orders (user_id, total_price, order_date) VALUES (?, 4500, NOW())");
    $query->bind_param("i", $userId);
    $query->execute();

   
    $query = $conn->prepare("UPDATE books SET stock = stock - 1 WHERE id = ?");
    $query->bind_param("i", $bookId);
    $query->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Book added to cart successfully.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to add book to cart.']);
}
?>
