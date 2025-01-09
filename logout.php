<?php
session_start();

if (isset($_SESSION['user_id'])) {
    session_destroy(); 
    header("Location: com.html?message=logged_out"); 
} else {
    header("Location: com.html?message=already_logged_out"); 
}
exit();
?>
