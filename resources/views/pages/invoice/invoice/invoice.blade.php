<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        #invoice-container {
            width: 58mm; /* Width for 58mm thermal printer */
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .invoice {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice th, .invoice td {
            padding: 2px;
            text-align: left;
            font-size: 12px;
        }

        .invoice th {
            border-bottom: 1px solid black;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            border-top: 1px dashed black;
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Invoice</h2>
<button onclick="printInvoice()">Print Invoice</button>

<div id="invoice-container">
    <table class="invoice">
        <thead>
        <tr>
            <th>#</th>
            <th>Item</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Price</th>
            <th class="text-right">Total</th>
        </tr>
        </thead>
        <tbody id="invoice-body">
        </tbody>
        <tfoot>
        <tr class="total-row">
            <td colspan="4" class="text-right">Grand Total:</td>
            <td id="grand-total" class="text-right"></td>
        </tr>
        </tfoot>
    </table>
</div>

<script>
    const invoiceData = [
        { item: "Item 1", quantity: 2, unitPrice: 10.00 },
        { item: "Item 2", quantity: 3, unitPrice: 5.00 },
        { item: "Item 3", quantity: 1, unitPrice: 20.00 },
        { item: "Item 4", quantity: 5, unitPrice: 2.50 },
        { item: "Item 5", quantity: 4, unitPrice: 7.00 },
        { item: "Item 6", quantity: 2, unitPrice: 12.00 },
        { item: "Item 7", quantity: 6, unitPrice: 3.50 },
        { item: "Item 8", quantity: 1, unitPrice: 15.00 },
        { item: "Item 9", quantity: 7, unitPrice: 1.75 },
        { item: "Item 10", quantity: 3, unitPrice: 8.00 },
    ];

    function renderInvoice() {
        const invoiceBody = document.getElementById('invoice-body');
        let grandTotal = 0;

        invoiceData.forEach((itemData, index) => {
            const total = itemData.quantity * itemData.unitPrice;
            grandTotal += total;

            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${itemData.item}</td>
                    <td class="text-right">${itemData.quantity}</td>
                    <td class="text-right">${itemData.unitPrice.toFixed(2)}</td>
                    <td class="text-right">${total.toFixed(2)}</td>
                </tr>
            `;
            invoiceBody.insertAdjacentHTML('beforeend', row);
        });

        document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
    }

    function printInvoice() {
        const printContents = document.getElementById('invoice-container').innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }

    window.onload = renderInvoice;
</script>

</body>
</html>
