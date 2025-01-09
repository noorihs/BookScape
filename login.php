<?php
include 'db.php';
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérez les données JSON
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['username']) || !isset($data['password']) || empty(trim($data['username'])) || empty(trim($data['password']))) {
        echo json_encode(['success' => false, 'message' => 'Please enter both username and password.']);
        exit();
    }

    $username = trim($data['username']);
    $password = trim($data['password']);

   
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

       
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];

            echo json_encode([
                'success' => true,
                'is_admin' => $user['is_admin'] == 1,
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password. Please try again.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found. Please sign up.']);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}
?>
