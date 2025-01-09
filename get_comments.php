<?php
include 'db.php';

if (isset($_GET['book_id']) && is_numeric($_GET['book_id'])) {
    $bookId = intval($_GET['book_id']);

    $stmt = $conn->prepare("SELECT c.rating, c.comment, c.created_at, u.username 
                            FROM comments c 
                            JOIN users u ON c.user_id = u.id 
                            WHERE c.book_id = ? 
                            ORDER BY c.created_at DESC");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='comment'>
                    <strong>{$row['username']}</strong>
                    <p>Rating: " . str_repeat("★", $row['rating']) . "</p>
                    <p>{$row['comment']}</p>
                    <small>{$row['created_at']}</small>
                  </div>";
        }
    } else {
        echo "<p>No comments yet. Be the first to leave one!</p>";
    }

    $stmt->close();
}

$conn->close();
?>
