<?php
class AccountRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::getInstance()->connection();
    }

    public function createAccount(string $type, float $balance, int $customerId): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO accounts (type, balance, customer_id)
             VALUES (:type, :balance, :customer_id)"
        );

        return $stmt->execute([
            'type' => $type,
            'balance' => $balance,
            'customer_id' => $customerId
        ]);
    }


    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM accounts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll(): array
    {
        return $this->pdo
            ->query("SELECT * FROM accounts")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByCustomer(int $customerId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM accounts WHERE customer_id = :cid"
        );
        $stmt->execute(['cid' => $customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateBalance(int $id, float $balance): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE accounts SET balance = :balance WHERE id = :id"
        );
        return $stmt->execute([
            'balance' => $balance,
            'id' => $id
        ]);
    }


    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM accounts WHERE id = :id AND balance = 0"
        );
        return $stmt->execute(['id' => $id]);
    }
}
