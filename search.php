<?php
include 'db.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}


$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['query'])) {
    echo json_encode(['success' => false, 'message' => 'Search query is missing.']);
    exit();
}

$query = '%' . $data['query'] . '%';


$stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? LIMIT 1");
$stmt->bind_param("ss", $query, $query);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $book = $result->fetch_assoc();
    echo json_encode(['success' => true, 'book' => $book]);
} else {
    echo json_encode(['success' => false, 'message' => 'Book not found.']);
}
$stmt->close();
?>
