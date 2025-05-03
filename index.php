<?php
// Check if user is not logged in
// session_start();
// if (!isset($_SESSION['user_email'])) {
//     header("Location: /phpcode/project management/INVOICE-DEMO/Login/login.php");
//     exit();
// }

// Show success/error message if set
// $message = "";
// if (isset($_SESSION['message'])) {
//     $message = $_SESSION['message'];
//     unset($_SESSION['message']);
// }

// Database connection (assuming connection is required here)
// $conn = mysqli_connect("localhost", "root", "", "invoice_db"); // change database name
// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error());
// }
include "../invoice-demo/session/session.php"
// ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Simple Invoice</title>

 <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- jsPDF and AutoTable Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- script.js -->
    <script src="assets/preview.js"></script>
    <script src="assets/generate-invoice.js"></script>
</head>
<body>

<!-- Start Header -->
 <?php include "../invoice-demo/include/header.php"; ?>
    <!-- End Header -->

    <!-- Invoice Form -->
    <form id="invoiceForm" action="../invoice-demo/DB_Files/submit_invoice.php" method="post" class="bg-gray-100 py-10"
        onkeydown="return event.key != 'Enter';">
        <div class="max-w-4xl mx-auto bg-white p-8 rounded shadow-lg space-y-6">

            <!-- Header -->
            <h1 class="text-3xl font-bold text-blue-600 m-0 text-center">Invoice Management System</h1>

            <!-- Stock management button 
            <div class="flex justify-end mb-4">
                <a href="http://localhost/phpcode/project%20management/invoice-demo/Stock.php"
                    class="inline-block text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md shadow-md transition duration-300">
                    Stock Management
                </a>
            </div> -->


            <!-- Billing Info -->
            <div class="grid grid-cols-1 gap-4">
                <input type="text" class="form-control" id="billed-to" name="billed_to" placeholder="Billed-To">
                <input type="text" class="form-control" id="Invoice-number" name="invoice_number"
                    placeholder="Invoice-Number" readonly>
                <input type="date" class="form-control" id="Invoice-date" name="invoice_date" placeholder="Date">
            </div>

            <!-- Invoice Table -->
            <?php include "../invoice-demo/front-end_files/row_list.php"; ?>
            <!-- End Invoice Table -->

            <!-- GST Section -->
            <div class="my-4 p-4 border rounded bg-gray-100">
                <h3 class="text-lg font-bold mb-2">GST Details</h3>

                <div class="flex justify-between items-center space-x-4 mb-3">
                    <div class="w-1/3">
                        <label class="font-bold">CGST (%) :</label>
                        <input type="number" id="cgstRate" name="cgst_rate" class="form-control"
                            placeholder="Enter CGST %" value="0" oninput="updateGrandTotal()" />
                    </div>
                    <div class="w-1/3">
                        <label class="font-bold">CGST Amount:</label>
                        <input type="text" id="cgstAmount" name="cgst_amount" class="form-control" readonly />
                    </div>
                </div>

                <div class="flex justify-between items-center space-x-4">
                    <div class="w-1/3">
                        <label class="font-bold">SGST (%) :</label>
                        <input type="number" id="sgstRate" name="sgst_rate" class="form-control"
                            placeholder="Enter SGST %" value="0" oninput="updateGrandTotal()" />
                    </div>
                    <div class="w-1/3">
                        <label class="font-bold">SGST Amount:</label>
                        <input type="text" id="sgstAmount" name="sgst_amount" class="form-control" readonly />
                    </div>
                </div>
            </div>

            <!-- Grand Total -->
            <div class="flex justify-end items-center space-x-2">
                <label class="font-bold">Grand Total:</label>
                <input type="text" id="grandTotal" name="grand_total" class="form-control w-1/3" readonly />
            </div>

            <!-- Total in Words -->
            <div class="flex justify-end items-center space-x-2">
                <label class="font-bold">In Words:</label>
                <textarea id="totalInWords" name="total_in_words" class="form-control w-1/3" rows="2"
                    readonly></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between mt-6">
                <button type="button" onclick="previewInvoice()" class="btn btn-info btn-sm">Preview Invoice</button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="generatePDF()">Save and Generate
                    PDF</button>
            </div>
        </div>
    </form>

    <!-- Preview Section -->
    <div id="invoicePreview" class="hidden mt-10 border p-6 rounded bg-gray-50">
        <h2 class="text-2xl font-bold text-green-600 mb-4 text-center">Invoice Preview</h2>

        <div class="mb-4">
            <p><strong>Billed To:</strong> <span id="previewBilledTo"></span></p>
            <p><strong>Invoice Number:</strong> <span id="previewInvoiceNumber"></span></p>
            <p><strong>Date:</strong> <span id="previewInvoiceDate"></span></p>
        </div>

        <!-- Items Table -->
        <table class="table table-bordered text-center w-full">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="previewTableBody"></tbody>
        </table>

        <!-- GST Section -->
        <div class="gst-section mt-4 p-4 border border-gray-300 rounded bg-white shadow">
            <h3 class="text-lg font-bold mb-2">GST Details</h3>
            <table class="gst-table table table-bordered text-center w-full">
                <thead class="table-light">
                    <tr>
                        <th>Tax</th>
                        <th>Rate (%)</th>
                        <th>Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CGST</td>
                        <td><span id="previewCgstRate">0</span>%</td>
                        <td>₹ <span id="previewCgstAmount">0</span></td>
                    </tr>
                    <tr>
                        <td>SGST</td>
                        <td><span id="previewSgstRate">0</span>%</td>
                        <td>₹ <span id="previewSgstAmount">0</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end items-center space-x-2 mt-4">
            <label class="font-bold">Grand Total:</label>
            <input type="text" id="previewGrandTotal" class="form-control w-1/3" readonly />
        </div>

        <div class="flex justify-end items-center space-x-2 mt-2">
            <label class="font-bold">In Words:</label>
            <textarea id="previewTotalInWords" class="form-control w-1/3" rows="2" readonly></textarea>
        </div>

        <!-- QR Code -->
        <div id="qrcode" class="flex justify-left items-left space-x-2 mt-2"></div>
    </div>

    <!-- Scripts -->
    <script src="assets/pdf.js"></script>
    <script src="assets/preview.js"></script>
    <script src="assets/generate-invoice.js"></script>

    <script>

// Row = document.createElement('tr');

function addRow() {
    const tableBody = document.getElementById('tableBody');
    const newRow = document.createElement('tr');

    // Fetch product data directly from PHP
    const stockItems = <?php
        $stockItems = mysqli_query($conn, "SELECT * FROM stock");
        $items = [];
        while ($item = mysqli_fetch_assoc($stockItems)) {
            $items[] = ['name' => $item['product_name'], 'price' => $item['price']];
        }
        echo json_encode($items);
    ?>;

    const optionsHtml = stockItems.map(item => 
        `<option value="${item.name}" data-price="${item.price}">${item.name}</option>`
    ).join('');

    newRow.innerHTML = `
        <td>
            <select name="product_name[]" class="form-control item-name" onchange="updatePrice(this)">
                <option value="">Select Item</option>
                ${optionsHtml}
            </select>
        </td>
        <td><input type="number" class="form-control qty" placeholder="Qty" name="quantity[]" oninput="updateTotal(this)"></td>
        <td><input type="text" class="form-control price" placeholder="Price" name="price[]" readonly></td>
        <td><input type="text" class="form-control total" name="total[]" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button></td>
    `;

    tableBody.appendChild(newRow);
}



    function removeRow(button) {
        button.closest('tr').remove();
        updateGrandTotal();
    }

    function updateRow(element) {
        const row = element.closest('tr');
        const qty = row.querySelector('.qty').value || 0;
        const price = row.querySelector('.price').value || 0;
        const totalField = row.querySelector('.total');
        const total = parseFloat(qty) * parseFloat(price);
        totalField.value = total.toFixed(2);
        updateGrandTotal();
    }

    function updateGrandTotal() {
        const totalFields = document.querySelectorAll('.total');
        let subTotal = 0;
        totalFields.forEach(field => {
            subTotal += parseFloat(field.value) || 0;
        });

        const cgstRate = parseFloat(document.getElementById('cgstRate').value) || 0;
        const sgstRate = parseFloat(document.getElementById('sgstRate').value) || 0;

        const cgstAmount = subTotal * (cgstRate / 100);
        const sgstAmount = subTotal * (sgstRate / 100);

        document.getElementById('cgstAmount').value = cgstAmount.toFixed(2);
        document.getElementById('sgstAmount').value = sgstAmount.toFixed(2);

        const grandTotal = subTotal + cgstAmount + sgstAmount;

        document.getElementById('grandTotal').value = grandTotal.toFixed(2);
        document.getElementById('totalInWords').value = numberToWords(Math.floor(grandTotal));
    }

    function numberToWords(num) {
        if (num === 0) return 'Zero Only';
        const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen',
            'Nineteen'
        ];
        const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        let result = '';

        function twoDigits(n) {
            if (n < 10) return ones[n];
            if (n < 20) return teens[n - 10];
            return tens[Math.floor(n / 10)] + (n % 10 ? ' ' + ones[n % 10] : '');
        }

        function threeDigits(n) {
            if (n === 0) return '';
            let str = '';
            if (n >= 100) {
                str += ones[Math.floor(n / 100)] + ' Hundred ';
                n = n % 100;
            }
            if (n > 0) {
                str += twoDigits(n) + ' ';
            }
            return str.trim();
        }

        let crore = Math.floor(num / 10000000);
        num = num % 10000000;

        let lakh = Math.floor(num / 100000);
        num = num % 100000;

        let thousand = Math.floor(num / 1000);
        num = num % 1000;

        let hundredAndBelow = num;

        if (crore > 0) result += threeDigits(crore) + ' Crore ';
        if (lakh > 0) result += threeDigits(lakh) + ' Lakh ';
        if (thousand > 0) result += threeDigits(thousand) + ' Thousand ';
        if (hundredAndBelow > 0) result += threeDigits(hundredAndBelow);

        return result.trim() + ' Only';
    }

    // check Stock function
    function checkStock(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const availableStock = selectedOption.getAttribute('data-stock');
        // alert(`Available stock: ${availableStock}`);
    }
    </script>
    <script src="assets/pdf.js"></script>



    <!-- Preview Section -->
    <div id="invoicePreview" class="hidden mt-10 border p-6 rounded bg-gray-50">
        <h2 class="text-2xl font-bold text-green-600 mb-4 text-center">Invoice Preview</h2>

        <div class="mb-4">
            <p><strong>Billed To:</strong> <span id="previewBilledTo"></span></p>
            <p><strong>Invoice Number:</strong> <span id="previewInvoiceNumber"></span></p>
            <p><strong>Date:</strong> <span id="previewInvoiceDate"></span></p>
        </div>

        <!-- Items Table -->
        <table class="table table-bordered text-center w-full">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="previewTableBody">
                <!-- Rows added here from JS -->
            </tbody>
        </table>

        <!-- GST Section (Static Structure with Classes) -->
        <div class="gst-section mt-4 p-4 border border-gray-300 rounded bg-white shadow">
            <h3 class="text-lg font-bold mb-2">GST Details</h3>

            <table class="gst-table table table-bordered text-center w-full">
                <thead class="table-light">
                    <tr>
                        <th class="gst-header">Tax</th>
                        <th class="gst-header">Rate (%)</th>
                        <th class="gst-header">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="gst-row">
                        <td class="tax-type">CGST</td>
                        <td class="tax-rate"><span id="previewCgstRate" class="cgst-rate">0</span> %</td>
                        <td class="tax-amount">₹ <span id="previewCgstAmount" class="cgst-amount">0</span></td>
                    </tr>
                    <tr class="gst-row">
                        <td class="tax-type">SGST</td>
                        <td class="tax-rate"><span id="previewSgstRate" class="sgst-rate">0</span> %</td>
                        <td class="tax-amount">₹ <span id="previewSgstAmount" class="sgst-amount">0</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end items-center space-x-2 mt-4">
            <label class="font-bold">Grand Total:</label>
            <input type="text" id="previewGrandTotal" class="form-control w-1/3" readonly />
        </div>

        <div class="flex justify-end items-center space-x-2 mt-2">
            <label class="font-bold">In Words:</label>
            <textarea id="previewTotalInWords" class="form-control w-1/3" rows="2" readonly></textarea>
        </div>
        <!-- Qr Generate  -->
        <div id="qrcode" class="flex justify-left items-left space-x-2 mt-2"></div>

    </div>

    </script>
</body>

</html>