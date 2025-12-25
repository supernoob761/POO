<?php

abstract class Accounts
{
    private int $id;
    private float $balance;
    private int $customerId;

    public function __construct(int $id, float $balance, int $customerId)
    {
        $this->id = $id;
        $this->balance = $balance;
        $this->customerId = $customerId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function displayInfo(): void
    {
        echo "Account ID: {$this->id} | ";
        echo "Balance: {$this->balance} | ";
        echo "Customer ID: {$this->customerId}<br>";
    }
}
