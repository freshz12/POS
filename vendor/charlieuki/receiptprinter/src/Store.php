<?php

namespace charlieuki\ReceiptPrinter;

class Store
{
    private $mid = '';
    private $name = '';
    private $address = '';
    private $phone = '';
    private $email = '';
    private $website = '';
    private $operator = '';

    function __construct($mid, $name, $address, $phone, $email, $website, $operator) {
        $this->mid = $mid;
        $this->name = $name;
        $this->address = $address;
        $this->phone = $phone;
        $this->email = $email;
        $this->website = $website;
        $this->operator = $operator;
    }

    public function getMID() {
        return $this->mid;
    }

    public function getOperator() {
        return $this->operator;
    }

    public function getName() {
        return $this->name;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getWebsite() {
        return $this->website;
    }
}