<?php
// Check if user is not logged in
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: /phpcode/project management/INVOICE-DEMO/Login/login.php");
    exit();
}

// Show success/error message if set
$message = "";
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Database connection (assuming connection is required here)
$conn = mysqli_connect("localhost", "root", "", "invoice_db"); // change database name
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>