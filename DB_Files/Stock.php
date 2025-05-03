<?php
include("../session/session.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <!-- Bootstrap Library -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">

    <!-- Start Header -->
    <?php include "../include/header.php"; ?>
    <!-- End Header -->

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <!-- Bootstrap Card for Stock Management -->
                <div class="card shadow-lg">
                    <div class="card-header bg-success text-white text-center">
                        <h3 class="mb-0">Stock Management</h3>
                    </div>

                    <div class="card-body">

                        <!-- Form to Add Product in Stock -->
                        <form action="../DB_Files/add_stock.php" method="POST" class="row g-3 mb-4">
                            <div class="col-md-5">
                                <input type="text" name="product_name" class="form-control" placeholder="Product Name"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="quantity" class="form-control" placeholder="Quantity"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="price" class="form-control" placeholder="price" required>
                            </div>
                            <div class="col-md-3 d-grid">
                                <button type="submit" class="btn btn-success">Add / Update Stock</button>
                            </div>
                        </form>

                        <!-- Table showing current stock -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity Available</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include "../DB_Files/db_connection.php"; // replace with your DB connection file
                                    $query = "SELECT * FROM stock";
                                    $result = mysqli_query($conn, $query);

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3'>No stock available</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>


</body>

</html>