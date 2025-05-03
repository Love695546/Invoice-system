<?php
// Check if session is already started
// if (session_status() === PHP_SESSION_NONE) {
    // session_start();
// }

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "invoice_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}