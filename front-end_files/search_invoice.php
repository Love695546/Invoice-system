<?php
// Start a new session or resume existing one (optional but useful for login/session management)
session_start();

// =================== DATABASE CONNECTION ===================
// Create connection to MySQL database
$conn = new mysqli("localhost", "root", "", "invoice_db");

// Check if connection failed, and stop execution if it did
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// =================== FUNCTION TO SANITIZE INPUT ===================
/**
 * Function: safe()
 * Purpose: Securely escape and sanitize user input to prevent SQL injection and XSS attacks.
 * Params:
 *   - $conn : Database connection (needed for real_escape_string)
 *   - $data : User input data to sanitize
 * Returns:
 *   - Sanitized and escaped string
 */
function safe($conn, $data) {
    return htmlspecialchars($conn->real_escape_string($data));
}

// =================== FUNCTION TO RENDER INVOICE HTML ===================
/**
 * Function: renderInvoice()
 * Purpose: Display the invoice details and its items in a structured HTML layout.
 * Params:
 *   - $invoice : Array containing single invoice record
 *   - $items_result : Result set containing all invoice items linked to this invoice
 */
function renderInvoice($invoice, $items_result) {
    $items_html = ''; // Initialize variable to hold all item rows (HTML)

    // Loop through each item if items exist and add a table row for each item
    if ($items_result && $items_result->num_rows > 0) {
        while ($item = $items_result->fetch_assoc()) {
            // Add table row for the current item (template string with item details)
            $items_html .= "<tr>
                <td>{$item['item_name']}</td>
                <td>{$item['qty']}</td>
                <td>₹ {$item['price']}</td>
                <td>₹ {$item['total']}</td>
            </tr>";
        }
    } else {
        // If no items found, show a placeholder row
        $items_html = "<tr><td colspan='4'>No items found!</td></tr>";
    }

    // =================== INVOICE HTML STRUCTURE (HEREDOC) ===================
    // Heredoc syntax to make the HTML structure readable and easy to maintain
    $html = <<<HTML
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Invoice Details</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
            .invoice-box { max-width: 800px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
            .invoice-header { text-align: center; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            table, th, td { border: 1px solid #ddd; }
            th, td { padding: 10px; text-align: center; }
            .totals { text-align: right; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='invoice-box'>
            <div class='invoice-header'>
                <h2>Invoice</h2> <!-- Invoice title -->
            </div>

            <!-- Invoice general details -->
            <div>
                <p><strong>Invoice Number:</strong> {$invoice['invoice_number']}</p>
                <p><strong>Billed To:</strong> {$invoice['billed_to']}</p>
                <p><strong>Invoice Date:</strong> {$invoice['invoice_date']}</p>
            </div>

            <!-- Items table -->
            <h3>Items</h3>
            <table>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price (₹)</th>
                    <th>Total (₹)</th>
                </tr>
                $items_html <!-- Dynamically generated item rows -->
            </table>

            <!-- GST & Tax details -->
            <div class='gst-section'>
                <h3>GST Details</h3>
                <p><strong>CGST:</strong> {$invoice['cgst_rate']}% - ₹ {$invoice['cgst_amount']}</p>
                <p><strong>SGST:</strong> {$invoice['sgst_rate']}% - ₹ {$invoice['sgst_amount']}</p>
            </div>

            <!-- Final totals -->
            <div class='totals'>
                <p><strong>Grand Total:</strong> ₹ {$invoice['grand_total']}</p>
                <p><strong>Total in Words:</strong> {$invoice['total_in_words']}</p>
            </div>
        </div>
    </body>
    </html>
    HTML;

    // Output the generated HTML page
    echo $html;
}

// =================== HANDLE SEARCH FORM SUBMISSION ===================
if (isset($_POST['search_invoice']) && !empty($_POST['search_invoice'])) {
    // Sanitize user input (the invoice number entered)
    $invoice_number = safe($conn, $_POST['search_invoice']);

    // Query to fetch invoice details from invoices table
    $invoice_sql = "SELECT * FROM `invoices` WHERE `invoice_number` = '$invoice_number'";
    $invoice_result = $conn->query($invoice_sql);

    // Check if invoice is found
    if ($invoice_result && $invoice_result->num_rows > 0) {
        // Fetch single invoice record as associative array
        $invoice = $invoice_result->fetch_assoc();

        // Get invoice ID to search for items
        $invoice_id = $invoice['id'];

        // Query to fetch all items linked to the invoice from invoice_items table
        $items_sql = "SELECT * FROM `invoice_items` WHERE `invoice_id` = '$invoice_id'";
        $items_result = $conn->query($items_sql);

        // Render the invoice and items as a structured HTML page
        renderInvoice($invoice, $items_result);

    } else {
        // No invoice found matching the invoice number entered
        echo "<h3>No invoice found for: <em>$invoice_number</em></h3>";
    }

} else {
    // If no invoice number entered in the form
    echo "<h3>Please enter an invoice number.</h3>";
}

// =================== CLOSE DATABASE CONNECTION ===================
$conn->close();
?>