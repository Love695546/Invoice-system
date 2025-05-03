<?php
session_start();
include "../DB_Files/db_connection.php";

// Get email & password from the form
$email = $_POST['email'];
$password = $_POST['password'];

// ✅ Hash the password before saving to database
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the data into the database
$sql = "INSERT INTO users (email, password) VALUES (?, ?)";

// Prepare and bind the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $hashed_password); // Use hashed password

// Execute the query
if ($stmt->execute()) {
    // Redirect or display a success message
    header("Location: login-page.php?status=success");
    exit();
} else {
    // If there is an error during insertion
    echo "Error: " . $stmt->error;
}

// Close the statement and the connection
$stmt->close();
$conn->close();
?>
