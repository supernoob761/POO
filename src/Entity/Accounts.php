<?php

abstract class Accounts
{
    protected int $id;
    protected float $balance;
    protected int $customerId;

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

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    abstract public function deposit(float $amount): void;
    abstract public function withdraw(float $amount): bool;
}
