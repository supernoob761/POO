<?php

class AccountRepository
{
    private PDO $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function getAll(): array
    {
        $stmt = $this->database->query(
            "SELECT id, balance, customer_id FROM accounts"
        );

        $accounts = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $accounts[] = new Accounts(
                (int)$row['id'],
                (float)$row['balance'],
                (int)$row['customer_id']
            );
        }

        return $accounts;
    }
}
