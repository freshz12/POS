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

        .invoice th, .invoice td {
            padding: 2px;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            border-top: 1px dashed black;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Invoice</h2>

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
        <tbody>
        <tr>
            <td>1</td>
            <td>Health Scalp & Grooming</td>
            <td class="text-right">1</td>
            <td class="text-right">210.000</td>
            <td class="text-right">210.000</td>
        </tr>
        </tbody>
        <tfoot>
        <tr class="total-row">
            <td colspan="4" class="text-right">Grand Total:</td>
            <td class="text-right">210.000</td>
        </tr>
        </tfoot>
    </table>
</div>

<form action="transactions/store" method="POST">
    <input type="hidden" name="_token" value="wqTbyQJ8FEW9O0wLaAYT4SX54EsSiGXg6eGQNAy6">
    <input type="hidden" name="total_amount" value="210.000">
    <input type="hidden" name="cart_items" value='[{"id":36,"name":"Health Scalp & Grooming","qty":"1","price":"210.000"}]'>
    <input type="hidden" name="customer_id" value="14">
    <input type="hidden" name="capster_id" value="6">
    <input type="hidden" name="amount" value="2.000.000">
    <button type="submit">Submit</button>
</form>

</body>
</html>
