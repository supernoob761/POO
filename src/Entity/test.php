<?php

require_once 'database.php';
// require_once 'Client.php';
// require_once 'Accounts.php';
// require_once 'AccountRepository.php';
require_once 'CustomerRepository.php';


$repo = new CustomerRepository();

$repo->Update("amina", "Grov@email.com",7);

$users = $repo->showAll();
echo "<pre>";
print_r($users);
echo "</pre>";

// $repo->delete(8);


