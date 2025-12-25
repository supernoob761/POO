<?php

require_once 'database.php';
require_once 'Client.php';
require_once 'Accounts.php';
require_once 'AccountRepository.php';
require_once 'CustomerRepository.php';


try {
    $db = new Db();
    $pdo = $db->Connection();

    $repo1 = new CustomerRepository($pdo);
    $customers = $repo1->getAll();

    foreach ($customers as $customer) {
        $customer->displayInfo();
    }

    $repo = new AccountRepository($pdo);
    $accounts = $repo->getAll();

    foreach ($accounts as $account) {
        $account->displayInfo();

}
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
