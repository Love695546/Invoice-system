<?php
session_start();
include "../DB_Files/db_connection.php"; // Database connection file

// Get email and password from form
$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Basic validation (optional, already done on client-side)
if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Please fill in both fields.";
    header("Location: login.php");
    exit();
}

// Query the database to get the user data based on the provided email
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if the email exists
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verify the password (hashed)
    if (password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_id'] = $user['id'];
        
        // Redirect to dashboard or home page
        header("Location: ../index.php");
        exit();
    } else {
        // Password mismatch
        $_SESSION['error'] = "Incorrect password.";
        header("Location: login.php");
        exit();
    }
} else {
    // Email not found
    $_SESSION['error'] = "No account found with this email.";
    header("Location: login.php");
    exit();
}

$stmt->close();
$conn->close();
?>
