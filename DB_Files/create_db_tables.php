<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$database = "invoice_db"; // ✅ Your database name

// Step 1: Create connection to MySQL server (without selecting database yet)
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Create Database if it doesn't exist
$sql_create_db = "CREATE DATABASE IF NOT EXISTS `$database`";
if ($conn->query($sql_create_db) === TRUE) {
    echo "✅ Database '$database' created successfully or already exists.<br>";
} else {
    die("❌ Error creating database: " . $conn->error);
}

// Step 3: Select the newly created database
$conn->select_db($database);

// Step 4: Define all table creation queries
$table_queries = [

    // Users Table
    "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

    // Invoices Table
    "CREATE TABLE IF NOT EXISTS `invoices` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `billed_to` VARCHAR(255) NOT NULL,
        `invoice_number` VARCHAR(100) NOT NULL,
        `invoice_date` DATE NOT NULL,
        `cgst_rate` DECIMAL(5,2) DEFAULT 0.00,
        `cgst_amount` DECIMAL(10,2) DEFAULT 0.00,
        `sgst_rate` DECIMAL(5,2) DEFAULT 0.00,
        `sgst_amount` DECIMAL(10,2) DEFAULT 0.00,
        `grand_total` DECIMAL(12,2) NOT NULL,
        `total_in_words` VARCHAR(500) DEFAULT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

    // Invoice Items Table
    "CREATE TABLE IF NOT EXISTS `invoice_items` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `invoice_id` INT(11) NOT NULL,
        `item_name` VARCHAR(255) NOT NULL,
        `qty` INT(11) NOT NULL,
        `price` DECIMAL(10,2) NOT NULL,
        `total` DECIMAL(12,2) NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

    // Stock Table
    "CREATE TABLE IF NOT EXISTS `stock` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `product_name` VARCHAR(255) NOT NULL,
        `quantity` INT(255) NOT NULL DEFAULT 0,
        `price` DOUBLE(20,2) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
];

// Step 5: Execute each table creation query
foreach ($table_queries as $index => $query) {
    if ($conn->query($query) === TRUE) {
        echo "✅ Table " . ($index + 1) . " created successfully.<br>";
    } else {
        echo "❌ Error creating table " . ($index + 1) . ": " . $conn->error . "<br>";
    }
}

// Step 6: Close the database connection
$conn->close();
?>
