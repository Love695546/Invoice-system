<?php
include("../session/session.php");
?>

<?php
include '../DB_Files/db_connection.php'; // Replace with actual DB connection file

$year = $_POST['year'] ?? '';
$month = $_POST['month'] ?? '';

$invoiceCondition = "1=1";
$itemCondition = "1=1";

if ($year !== '') {
    $invoiceCondition .= " AND YEAR(invoice_date) = '$year'";
    $itemCondition .= " AND YEAR(inv.invoice_date) = '$year'";
}
if ($month !== '') {
    $invoiceCondition .= " AND MONTH(invoice_date) = '$month'";
    $itemCondition .= " AND MONTH(inv.invoice_date) = '$month'";
}

// Invoice Summary
$invoiceQuery = "SELECT invoice_number, invoice_date, grand_total 
                 FROM invoices 
                 WHERE $invoiceCondition";
$invoiceResult = mysqli_query($conn, $invoiceQuery);

// Product-wise Sales Table
$productQuery = "SELECT ii.item_name, SUM(ii.qty) as total_qty, SUM(ii.total) as total_sales 
                 FROM invoice_items ii
                 JOIN invoices inv ON ii.invoice_id = inv.id
                 WHERE $itemCondition
                 GROUP BY ii.item_name
                 ORDER BY total_sales DESC";
$productResult = mysqli_query($conn, $productQuery);

// Product Sales for Chart
$chartQuery = "SELECT ii.item_name, YEAR(inv.invoice_date) AS year, SUM(ii.total) AS total_sales
               FROM invoice_items ii
               JOIN invoices inv ON ii.invoice_id = inv.id
               GROUP BY ii.item_name, YEAR(inv.invoice_date)
               ORDER BY ii.item_name, year";
$chartResult = mysqli_query($conn, $chartQuery);

$chartData = [];
$years = [];

while ($row = mysqli_fetch_assoc($chartResult)) {
    $item = $row['item_name'];
    $yearVal = $row['year'];
    $amount = (float)$row['total_sales'];

    if (!isset($chartData[$item])) {
        $chartData[$item] = [];
    }
    $chartData[$item][$yearVal] = $amount;

    if (!in_array($yearVal, $years)) {
        $years[] = $yearVal;
    }
}
sort($years);

// Export to Excel
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=product_sales_report.xls");

    echo "<table border='1'>";
    echo "<tr><th>Item Name</th><th>Total Quantity</th><th>Total Sales (₹)</th></tr>";

    $exportQuery = "SELECT ii.item_name, SUM(ii.qty) as total_qty, SUM(ii.total) as total_sales 
                    FROM invoice_items ii
                    JOIN invoices inv ON ii.invoice_id = inv.id
                    WHERE $itemCondition
                    GROUP BY ii.item_name
                    ORDER BY total_sales DESC";
    $exportResult = mysqli_query($conn, $exportQuery);
    
    while ($row = mysqli_fetch_assoc($exportResult)) {
        echo "<tr>
                <td>{$row['item_name']}</td>
                <td>{$row['total_qty']}</td>
                <td>{$row['total_sales']}</td>
              </tr>";
    }
    echo "</table>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-center">📊 Sales Report</h1>

    <!-- Filter Form -->
    <form method="POST" class="bg-white p-6 rounded-lg shadow-md mb-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block mb-1 font-semibold">Select Year:</label>
            <select name="year" class="w-full border p-2 rounded">
                <option value="">All</option>
                <option value="2024" <?= ($year == "2024") ? "selected" : "" ?>>2024</option>
                <option value="2025" <?= ($year == "2025") ? "selected" : "" ?>>2025</option>
            </select>
        </div>
        <div>
            <label class="block mb-1 font-semibold">Select Month:</label>
            <select name="month" class="w-full border p-2 rounded">
                <option value="">All</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $monthValue = str_pad($m, 2, '0', STR_PAD_LEFT);
                    $monthName = date('F', mktime(0, 0, 0, $m, 10));
                    echo "<option value='$monthValue' " . (($month == $monthValue) ? "selected" : "") . ">$monthName</option>";
                }
                ?>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded w-full">
                Show Report
            </button>
        </div>
    </form>

    <!-- Excel Export Button -->
    <a href="?export=excel&year=<?= $year ?>&month=<?= $month ?>" 
       class="inline-block mb-4 text-white bg-green-600 hover:bg-green-700 px-4 py-2 rounded">
       📥 Export to Excel
    </a>

    <!-- Invoice Summary -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold mb-4">🧾 Invoice Summary</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border border-gray-200">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left">Invoice No</th>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-right">Grand Total (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    while ($row = mysqli_fetch_assoc($invoiceResult)) {
                        echo "<tr class='border-t'>
                                <td class='px-4 py-2'>{$row['invoice_number']}</td>
                                <td class='px-4 py-2'>{$row['invoice_date']}</td>
                                <td class='px-4 py-2 text-right'>₹" . number_format($row['grand_total'], 2) . "</td>
                              </tr>";
                        $grandTotal += $row['grand_total'];
                    }
                    ?>
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr>
                        <td colspan="2" class="px-4 py-2 font-bold text-right">Total Sales:</td>
                        <td class="px-4 py-2 font-bold text-right text-green-700">₹<?= number_format($grandTotal, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Product-wise Sales -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">📦 Product-wise Sales</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border border-gray-200">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left">Item Name</th>
                        <th class="px-4 py-2 text-right">Quantity Sold</th>
                        <th class="px-4 py-2 text-right">Total Sales (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($item = mysqli_fetch_assoc($productResult)) {
                        echo "<tr class='border-t'>
                                <td class='px-4 py-2'>{$item['item_name']}</td>
                                <td class='px-4 py-2 text-right'>{$item['total_qty']}</td>
                                <td class='px-4 py-2 text-right'>₹" . number_format($item['total_sales'], 2) . "</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white p-6 mt-10 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">📈 Product Sales by Year</h2>
        <canvas id="salesChart" height="100"></canvas>
    </div>
</div>

<script>
const years = <?= json_encode($years) ?>;
const datasets = [];

<?php foreach ($chartData as $product => $sales): ?>
datasets.push({
    label: "<?= $product ?>",
    data: years.map(year => <?= json_encode($sales) ?>[year] ?? 0),
    backgroundColor: 'rgba(<?= rand(0,255) ?>, <?= rand(0,255) ?>, <?= rand(0,255) ?>, 0.7)'
});
<?php endforeach; ?>

const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: years,
        datasets: datasets
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Product-wise Sales per Year'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₹' + value;
                    }
                }
            }
        }
    }
});
</script>
</body>
</html>
