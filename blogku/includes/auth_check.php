<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: " . str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 2) . "auth/login.php");
    exit;
}
$current_user = $_SESSION['user'];
$is_admin = $current_user['role'] === 'admin';
