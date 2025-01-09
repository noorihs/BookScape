<?php
include 'db.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Invalid book ID.']);
    exit;
}

$bookId = intval($_GET['id']);
$query = $conn->prepare("SELECT * FROM books WHERE id = ?");
$query->bind_param("i", $bookId);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $book = $result->fetch_assoc();
    echo json_encode($book);
} else {
    echo json_encode(['error' => 'Book not found.']);
}
?>
