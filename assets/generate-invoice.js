// let invoiceCounter = 1; // Start with 1 for the invoice counter

const Invoice = 'INV';


function generateInvoiceNumber() {
    const now = new Date();  // Define `now` inside the function
    const year = now.getFullYear().toString().slice(-2);  // Last 2 digits of the year
    const month = String(now.getMonth() + 1).padStart(2, '0');  // Month in 2-digit format

    // Format the counter with leading zeros (always 4 digits)
    const randomNumber = Math.floor(Math.random() * 10000); // Generates a number between 0 and 9999
    // const formattedNumber = String(randomNumber).padStart(4, '0'); // Ensures it's always 4 digits
    
    // console.log(formattedNumber); // Example output: "0385", "4721", "0007"
    
   
    
        // Increment the counter (for example, on each page load)
        // invoiceCounter++;  // Increment the counter for the next use
    
    // Return the formatted invoice number
    return `${Invoice}-${randomNumber}-${month}-${year}`;
}

// Event listener to set the invoice number on page load
document.addEventListener('DOMContentLoaded', () => {
    const invoiceNumberField = document.getElementById('Invoice-number');
    if (invoiceNumberField) {
        invoiceNumberField.value = generateInvoiceNumber();
    }
});
