<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nettoyage des données
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 

  
    $checkUser = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkUser);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        $existingUser = $result->fetch_assoc();
        if ($existingUser['username'] === $username) {
            die("Error: The username already exists.");
        }
        if ($existingUser['email'] === $email) {
            die("Error: The email already exists.");
        }
    } else {
      
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
          
            $_SESSION['user_id'] = $conn->insert_id; 
            $_SESSION['username'] = $username; 

          
            header("Location: Books.php");
            exit();
        } else {
            die("Error: Registration failed. Please try again later.");
        }
    }
}
?>
