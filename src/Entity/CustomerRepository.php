<?php

class CustomerRepository
{
    private PDO $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function getAll(): array
    {
        $stmt = $this->database->query(
            "SELECT id, name, email FROM customers"
        );

        $customers = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $customers[] = new Customers(
                (int)$row['id'],
                (string)$row['name'],
                (string)$row['email']
            );
        }

        return $customers;
    }
}
?>