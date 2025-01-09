<?php
session_start();

echo json_encode([
    'loggedIn' => isset($_SESSION['user_id']),
    'username' => isset($_SESSION['username']) ? $_SESSION['username'] : null
]);
?>
