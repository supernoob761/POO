<?php

class TransactionRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::getInstance()->connection();
    }


    public function create(string $type, float $amount, int $accountId): bool
    {
        $sql = "INSERT INTO transactions (type, amount, account_id)
                VALUES (:type, :amount, :account_id)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'type'       => $type,
            'amount'     => $amount,
            'account_id' => $accountId
        ]);
    }

    public function findByAccount(int $accountId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM transactions WHERE account_id = :account_id"
        );

        $stmt->execute(['account_id' => $accountId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
