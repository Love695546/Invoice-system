<?php
// session_start();

// ✅ Use correct DB connection file (not this file itself!)
include '../DB_Files/db_connection.php'; // Replace with actual DB connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']); // Use float for price

    if (!empty($product_name) && $quantity > 0 && $price > 0) {

        // 🔍 Check if product already exists
        $checkQuery = "SELECT * FROM stock WHERE product_name = ?";
        $stmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($stmt, "s", $product_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            // 🔄 Update quantity and price
            $updateQuery = "UPDATE stock SET quantity = quantity + ?, price = ? WHERE product_name = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmt, "iis", $quantity, $price, $product_name);
            mysqli_stmt_execute($stmt);

            $_SESSION['message'] = "Stock and price updated successfully.";
        } else {
            // ➕ Insert new product
            $insertQuery = "INSERT INTO stock (product_name, quantity, price) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($stmt, "sid", $product_name, $quantity, $price);
            mysqli_stmt_execute($stmt);

            $_SESSION['message'] = "Product added successfully.";
        }
    } else {
        $_SESSION['message'] = "Invalid input.";
    }

    // 🔁 Redirect back to stock page
    header('Location: ../DB_Files/Stock.php'); // Update if needed
    exit();
}
?>
