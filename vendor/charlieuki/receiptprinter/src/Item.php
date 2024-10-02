<?php

namespace charlieuki\ReceiptPrinter;

class Item
{
    private $name;
    private $qty;
    private $price;
    private $currency = 'Rp';

    function __construct($name, $qty, $price)
    {
        $this->name = $name;
        $this->qty = $qty;
        $this->price = $price;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getQty()
    {
        return $this->qty;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function __toString()
    {
        $right_cols = 17; // Adjust based on expected subtotal width
        $left_cols = 10;  // This will adjust how the price and quantity are displayed

        $item_price = $this->currency . number_format($this->price, 0, ',', '.');
        $item_subtotal = $this->currency . number_format($this->price * $this->qty, 0, ',', '.');

        // Left align item name
        $print_name = str_pad($this->name, 22);

        // Create a string for price x quantity, right-aligned
        $price_qty = str_pad($item_price . ' x ' . $this->qty, $left_cols, ' ', STR_PAD_LEFT);

        // Right-align the subtotal
        $print_subtotal = str_pad($item_subtotal, $right_cols, ' ', STR_PAD_LEFT);

        // Return formatted string with justified output
        return "$print_name\n$price_qty$print_subtotal\n";
    }
}
