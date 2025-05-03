<?php
include '../invoice-demo/DB_Files/db_connection.php'; // Database connection

// Fetch all stock items
$stockItemsQuery = "SELECT * FROM stock";
$stockItems = mysqli_query($conn, $stockItemsQuery);
?>

<!-- Invoice Table -->
<div class="table-responsive">
    <table class="table table-bordered text-center" id="invoiceTable">
        <thead class="table-light">
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <tr>
                <td>
                    <select name="product_name[]" class="form-control item-name" onchange="updatePrice(this)">
                        <option value="">Select Item</option>
                        <?php while ($item = mysqli_fetch_assoc($stockItems)) { ?>
                            <option value="<?php echo $item['product_name']; ?>" 
                                    data-price="<?php echo $item['price']; ?>">
                                <?php echo $item['product_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
                <td><input type="number" class="form-control qty" placeholder="Qty" name="quantity[]" oninput="updateTotal(this)"></td>
                <td><input type="text" class="form-control price" placeholder="Price" name="price[]" readonly></td>
                <td><input type="text" class="form-control total" name="total[]" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button></td>
            </tr>
        </tbody>
    </table>
    <button type="button" onclick="addRow()" class="btn btn-success btn-sm">+ Add Row</button>
</div>
<script>
    function updatePrice(selectElement) {
    let row = selectElement.closest("tr");
    let priceField = row.querySelector(".price");

    // Get price from selected option's data-price attribute
    priceField.value = selectElement.selectedOptions[0].getAttribute("data-price") || "";
    
    updateTotal(row.querySelector(".qty"));
}

function updateTotal(qtyField) {
    let row = qtyField.closest("tr");
    let qty = parseFloat(qtyField.value) || 0;
    let price = parseFloat(row.querySelector(".price").value) || 0;
    
    row.querySelector(".total").value = (qty * price).toFixed(2);
}


function updateTotal(qtyField) {
    let row = qtyField.closest("tr");
    let qty = parseFloat(qtyField.value) || 0;
    let price = parseFloat(row.querySelector(".price").value) || 0;
    
    row.querySelector(".total").value = (qty * price).toFixed(2);
}
</script>
