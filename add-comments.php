<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    
    error_log(print_r($data, true));

    if (isset($_SESSION['user_id'], $data['book_id'], $data['rating'], $data['comment'])) {
        $userId = intval($_SESSION['user_id']);
        $bookId = intval($data['book_id']);
        $rating = intval($data['rating']);
        $comment = htmlspecialchars($data['comment']);

        $stmt = $conn->prepare("INSERT INTO comments (book_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $bookId, $userId, $rating, $comment);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Comment added successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add comment.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input or session not set.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
