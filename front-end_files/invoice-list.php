<?php
include '../DB_Files/db_connection.php'; // Database connection file

include("../session/session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- <script src="pdf.js"></script> -->
    <style>
    body {
        background-color: #f8f9fa;
    }

    .invoice-container {
        max-width: 90%;
        margin: auto;
    }

    .card-custom {
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table th {
        background-color: #007bff !important;
        color: white;
    }

    .btn-custom {
        padding: 5px 12px;
        font-size: 14px;
    }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

</head>

<body>
    <!-- Start Header -->
 <?php include "../include/header.php"; ?>
    <!-- End Header -->
    <div class="container mt-5 invoice-container">
        <div class="card card-custom p-4">
            <h2 class="text-center mb-3 text-primary">Invoice List</h2>
            <!-- Search Invoice Form -->
            <form action="../front-end_files/search_invoice.php" method="POST" class="d-flex p-3">
                <input type="text" name="search_invoice" class="form-control me-2" placeholder="Enter Invoice Number"
                    required>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>

            <?php
            $search_query = "";
            if (isset($_POST['search_invoice'])) {
                $search_query = $_POST['search_invoice'];
                $sql = "SELECT * FROM invoices WHERE invoice_number LIKE ? OR billed_to LIKE ? ORDER BY invoice_date DESC";
                $stmt = $conn->prepare($sql);
                $search_param = "%$search_query%";
                $stmt->bind_param("ss", $search_param, $search_param);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $sql = "SELECT * FROM invoices ORDER BY invoice_date DESC";
                $result = $conn->query($sql);
            }
            ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Billed To</th>
                            <th>Invoice Date</th>
                            <th>Grand Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['invoice_number']; ?></td>
                            <td><?php echo $row['billed_to']; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['invoice_date'])); ?></td>
                            <td>₹ <?php echo number_format($row['grand_total'], 2); ?></td>
                            <td>
                                <a href="../front-end_files/view-invoice-item.php?invoice_number=<?php echo $row['invoice_number']; ?>"
                                    class="btn btn-info btn-sm btn-custom">View</a>
                                <!-- <button type="button" class="btn btn-success btn-sm btn-custom"
                                    onclick="generatePDF()">Download</button> -->

                            </td>
                        </tr>
                        <?php }
                        } else { ?>
                        <tr>
                            <td colspan="5" class="text-center text-danger">No invoices found.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="assets/pdf.js"></script>
</body>

</html>
<?php
$conn->close();
?>