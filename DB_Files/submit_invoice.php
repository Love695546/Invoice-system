<?php
session_start();

// ✅ Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "invoice_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// ✅ Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ⏳ Optional delay (remove if unnecessary)
    sleep(4);

    // ✅ Get main invoice data
    $billed_to = $_POST['billed_to'];
    $invoice_number = $_POST['invoice_number'];
    $invoice_date = $_POST['invoice_date'];
    $cgst_rate = $_POST['cgst_rate'];
    $cgst_amount = $_POST['cgst_amount'];
    $sgst_rate = $_POST['sgst_rate'];
    $sgst_amount = $_POST['sgst_amount'];
    $grand_total = $_POST['grand_total'];
    $total_in_words = $_POST['total_in_words'];

    // ✅ Insert into invoices table
    $stmt = $conn->prepare("INSERT INTO invoices (billed_to, invoice_number, invoice_date, cgst_rate, cgst_amount, sgst_rate, sgst_amount, grand_total, total_in_words) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiddids", $billed_to, $invoice_number, $invoice_date, $cgst_rate, $cgst_amount, $sgst_rate, $sgst_amount, $grand_total, $total_in_words);

    if ($stmt->execute()) {

        $invoice_id = $stmt->insert_id;

        // ✅ Items arrays (Access correctly from $_POST)
        $item_names = $_POST['product_name'];  // Correct access to array
        $qtys = $_POST['quantity'];
        $prices = $_POST['price'];
        $totals = $_POST['total'];

        if (is_array($item_names) && count($item_names) > 0) {

            // ✅ Prepare statement for inserting items (bind once before loop)
            $stmt_item = $conn->prepare("INSERT INTO invoice_items (invoice_id, item_name, qty, price, total) VALUES (?, ?, ?, ?, ?)");

            // ✅ Loop through items and execute the prepared statement for each item
            for ($i = 0; $i < count($item_names); $i++) {

                $item_name = $item_names[$i];
                $qty = $qtys[$i];
                $price = $prices[$i];
                $total = $totals[$i];

                // ✅ 1. Check stock availability
                $stock_check_query = "SELECT quantity FROM stock WHERE product_name = ?";
                $stock_stmt = $conn->prepare($stock_check_query);
                $stock_stmt->bind_param("s", $item_name);
                $stock_stmt->execute();
                $stock_result = $stock_stmt->get_result();

                if ($stock_result && $stock_result->num_rows > 0) {
                    $stock_row = $stock_result->fetch_assoc();
                    $current_stock = $stock_row['quantity'];
                
                    if ($current_stock >= $qty) {
                
                        // ✅ 2. Reduce stock
                        $update_stock_query = "UPDATE stock SET quantity = quantity - ? WHERE product_name = ?";
                        $update_stmt = $conn->prepare($update_stock_query);
                        $update_stmt->bind_param("is", $qty, $item_name);
                        $update_stmt->execute();
                        $update_stmt->close();
                
                        // ✅ 3. Check if remaining stock is less than 3
                        $remaining_stock = $current_stock - $qty;
                
                        if ($remaining_stock < 3) {
                            echo "<script>alert('Warning: $item_name ka stock sirf $remaining_stock bacha hai!');</script>";
                            // Optional redirect if needed
                            // echo "<script>window.location.href='../index.php';</script>";
                            // exit();
                        }
                
                    } else {
                        // ❌ Not enough stock
                        echo "<script>alert('Stock kam hai for $item_name! Available: $current_stock, Required: $qty'); window.location.href='../index.php';</script>";
                        exit();
                    }
                
                } else {
                    // ❌ Item not found in stock
                    echo "<script>alert('Item $item_name ka stock nahi mila!'); window.location.href='../DB_Files/Stock.php';</script>";
                    exit();
                }

                // ✅ 4. Insert invoice item
                $stmt_item->bind_param("isidd", $invoice_id, $item_name, $qty, $price, $total);
                $stmt_item->execute();

                $stock_stmt->close(); // Close stock_stmt inside the loop
            }

            $stmt_item->close(); // Close item insert stmt after loop ends

            // ✅ SUCCESS
            header("Location: ../index.php");
            exit();

        } else {
            // ❌ No items in invoice
            echo "<script>alert('Invoice saved but no items added!'); window.location.href='../index.php';</script>";
            exit();
        }

    } else {
        // ❌ Error inserting invoice
        echo "<script>alert('Invoice creation failed!'); window.location.href='../index.php';</script>";
        exit();
    }

    $stmt->close(); // Close invoice insert stmt
}

$conn->close(); // Close DB connection
?>
