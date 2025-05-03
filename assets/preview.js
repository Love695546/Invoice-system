function previewInvoice() {
  // Basic Info
  document.getElementById('previewBilledTo').innerText = document.getElementById('billed-to').value;
  document.getElementById('previewInvoiceNumber').innerText = document.getElementById('Invoice-number').value;
  document.getElementById('previewInvoiceDate').innerText = document.getElementById('Invoice-date').value;

  // Access GST Rate and Amount from Classes (not IDs)
  const cgstRateElement = document.querySelector('.cgst-rate');
  const cgstAmountElement = document.querySelector('.cgst-amount');

  const sgstRateElement = document.querySelector('.sgst-rate');
  const sgstAmountElement = document.querySelector('.sgst-amount');

  // Get the current values from input fields (actual form)
  const cgstRateValue = document.querySelector('#cgstRate').value || 0;
  const cgstAmountValue = document.querySelector('#cgstAmount').value || 0;

  const sgstRateValue = document.querySelector('#sgstRate').value || 0;
  const sgstAmountValue = document.querySelector('#sgstAmount').value || 0;

  // Update Preview Section GST Fields
  cgstRateElement.innerText = cgstRateValue;
  cgstAmountElement.innerText = parseFloat(cgstAmountValue).toFixed(2);

  sgstRateElement.innerText = sgstRateValue;
  sgstAmountElement.innerText = parseFloat(sgstAmountValue).toFixed(2);

  // Table Data Copy (Item rows)
  const tableBody = document.getElementById('tableBody');
  const previewBody = document.getElementById('previewTableBody');
  previewBody.innerHTML = '';

  [...tableBody.children].forEach(row => {
    const item = row.querySelector('.item-name').value;
    const qty = row.querySelector('.qty').value;
    const price = row.querySelector('.price').value;
    const total = row.querySelector('.total').value;

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${item}</td>
      <td>${qty}</td>
      <td>${price}</td>
      <td>${total}</td>
    `;
    previewBody.appendChild(tr);
  });

  // Totals
  const grandTotal = document.getElementById('grandTotal').value;
  const totalInWords = document.getElementById('totalInWords').value;

  document.getElementById('previewGrandTotal').value = grandTotal;
  document.getElementById('previewTotalInWords').value = totalInWords;

  // Show Preview Section (remove hidden class)
  document.getElementById('invoicePreview').classList.remove('hidden');
  generateQRCode();
}

//generate qr
function generateQRCode() {
  const billedTo = document.getElementById('billed-to').value;
  const invoiceNumber = document.getElementById('Invoice-number').value;
  const invoiceDate = document.getElementById('Invoice-date').value;
  const grandTotal = document.getElementById('grandTotal').value;
  const totalInWords = document.getElementById('totalInWords').value;

  const cgstRate = document.querySelector('#cgstRate').value || 0;
  const cgstAmount = document.querySelector('#cgstAmount').value || 0;
  const sgstRate = document.querySelector('#sgstRate').value || 0;
  const sgstAmount = document.querySelector('#sgstAmount').value || 0;

  const tableBody = document.getElementById('tableBody');
  let items = [];

  [...tableBody.children].forEach(row => {
    let itemName = row.querySelector('.item-name').value;
    let qty = row.querySelector('.qty').value;
    let price = row.querySelector('.price').value;
    let total = row.querySelector('.total').value;
    items.push({ itemName, qty, price, total });
  });

  const invoiceData = {
    "Billed To": billedTo,
    "Invoice Number": invoiceNumber,
    "Invoice Date": invoiceDate,
    "CGST Rate": cgstRate,
    "CGST Amount": cgstAmount,
    "SGST Rate": sgstRate,
    "SGST Amount": sgstAmount,
    "Grand Total": grandTotal,
    "Total In Words": totalInWords,
    "Items": items
  };

  const qrData = JSON.stringify(invoiceData);

  // Generate QR
  document.getElementById("qrcode").innerHTML = "";
  new QRCode(document.getElementById("qrcode"), {
    text: qrData,
    width: 200,
    height: 200
  });
}

