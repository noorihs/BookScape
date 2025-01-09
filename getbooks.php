<?php
include 'db.php';

if (isset($_GET['id'])) {
    $bookId = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, title, author, price, description, stock FROM books WHERE id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Book not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
