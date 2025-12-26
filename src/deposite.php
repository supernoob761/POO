<?php
require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/Entity/Accounts.php';
require_once __DIR__ . '/Entity/Checking.php';
require_once __DIR__ . '/Entity/Saving.php';

require_once __DIR__ . '/Repository/AccountRepository.php';
require_once __DIR__ . '/Repository/TransactionRepository.php';

$repo = new AccountRepository();
$data = $repo->findById($accountId);

if (!$data) {
    echo "Account not found\n";
    exit;
}

if ($data['type'] === 'checking') {
    $account = new CheckingAccount(
        $data['id'],
        $data['balance'],
        $data['customer_id']
    );
} else {
    $account = new SavingsAccount(
        $data['id'],
        $data['balance'],
        $data['customer_id']
    );
}

$account->deposit($amount);

$repo->updateBalance(
    $account->getId(),
    $account->getBalance()
);

echo "Deposit successful\n";
