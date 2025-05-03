
async function generatePDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  const billedTo = document.getElementById("billed-to").value;
  const invoiceNumber = document.getElementById("Invoice-number").value;
  const invoiceDate = document.getElementById("Invoice-date").value;

  doc.setFontSize(18);
  doc.text("Invoice System Pvt Ltd.", 105, 15, null, null, "center");

  doc.setFontSize(12);
  doc.text(`Billed To: ${billedTo}`, 10, 30);
  doc.text(`Invoice Number: ${invoiceNumber}`, 10, 40);
  doc.text(`Invoice Date: ${invoiceDate}`, 10, 50);

  // Item Table
  doc.autoTable({
    startY: 60,
    head: [['Item Name', 'Qty', 'Price', 'Total']],
    body: getTableData()
  });

  // GST Table
  doc.autoTable({
    startY: doc.lastAutoTable.finalY + 10,
    head: [['Tax Type', 'Rate (%)', 'Amount']],
    body: getGSTData()
  });

  const finalY = doc.lastAutoTable.finalY || 70;

  const grandTotal = document.getElementById("grandTotal").value;
  const totalInWords = document.getElementById("totalInWords").value;

  doc.setFontSize(12);
  doc.text(`Grand Total:  ${grandTotal}`, 10, finalY + 10);
  doc.text(`In Words: ${totalInWords}`, 10, finalY + 20);

  doc.setFontSize(14);
  doc.text("Thank you for your Visit!", 105, finalY + 40, null, null, "center");

  // Add QR Code
  await addQRCodeToPDF(doc, finalY + 50);

  // Save PDF
  doc.save(`Invoice_${invoiceNumber}.pdf`);
}

async function addQRCodeToPDF(doc, yPosition) {
  return new Promise((resolve) => {
    const billedTo = document.getElementById("billed-to").value;
    const invoiceNumber = document.getElementById("Invoice-number").value;
    const invoiceDate = document.getElementById("Invoice-date").value;
    const grandTotal = document.getElementById("grandTotal").value;
    const totalInWords = document.getElementById("totalInWords").value;

    const cgstRate = document.getElementById("cgstRate").value || "0";
    const cgstAmount = document.getElementById("cgstAmount").value || "0.00";
    const sgstRate = document.getElementById("sgstRate").value || "0";
    const sgstAmount = document.getElementById("sgstAmount").value || "0.00";

    const itemRows = document.querySelectorAll("#tableBody tr");
    const items = [];

    itemRows.forEach((row) => {
      const item_name = row.querySelector(".item-name")?.value || '';
      const qty = row.querySelector(".qty")?.value || '';
      const price = row.querySelector(".price")?.value || '';
      const total = row.querySelector(".total")?.value || '';

      items.push({
        item_name,
        qty,
        price,
        total
      });
    });

    // Prepare QR Code data
    const qrData = {
      invoiceNumber: invoiceNumber,
      billedTo: billedTo,
      invoiceDate: invoiceDate,
      items: items,
      gst: {
        cgstRate: cgstRate,
        cgstAmount: cgstAmount,
        sgstRate: sgstRate,
        sgstAmount: sgstAmount
      },
      grandTotal: grandTotal,
      totalInWords: totalInWords
    };

    const qrContent = JSON.stringify(qrData, null, 2);

    // Create a temporary container for QR generation
    const qrContainer = document.createElement("div");
    qrContainer.style.display = "none";
    document.body.appendChild(qrContainer);

    const qrCode = new QRCode(qrContainer, {
      text: qrContent,
      width: 300,
      height: 300
    });

    setTimeout(() => {
      const qrImg = qrContainer.querySelector("img");

      if (qrImg) {
        const imgData = qrImg.src;

        // Add QR image to PDF (x=150, adjust as needed)
        doc.addImage(imgData, "PNG", 150, yPosition, 40, 40);
      }

      document.body.removeChild(qrContainer);
      resolve();
    }, 1000);
  });
}

function getTableData() {
  const rows = document.querySelectorAll("#tableBody tr");
  const data = [];

  rows.forEach((row) => {
    const item = row.querySelector(".item-name")?.value || '';
    const quantity = row.querySelector(".qty")?.value || '';
    const unitPrice = row.querySelector(".price")?.value || '';
    const total = row.querySelector(".total")?.value || '';

    data.push([item, quantity, unitPrice, total]);
  });

  return data;
}

function getGSTData() {
  const cgstRate = document.getElementById("cgstRate").value || "0";
  const cgstAmount = document.getElementById("cgstAmount").value || "0.00";
  const sgstRate = document.getElementById("sgstRate").value || "0";
  const sgstAmount = document.getElementById("sgstAmount").value || "0.00";

  return [
    ['CGST', `${cgstRate}%`, ` ${cgstAmount}`],
    ['SGST', `${sgstRate}%`, ` ${sgstAmount}`]
  ];
}
