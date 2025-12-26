<?php

class Transaction
{
    private int $id;
    private string $type;
    private float $amount;
    private int $accountId;

    public function __construct(
        int $id,
        string $type,
        float $amount,
        int $accountId,
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->amount = $amount;
        $this->accountId = $accountId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

}
