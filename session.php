<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: account.html?message=please_login");
    exit();
}
?>
