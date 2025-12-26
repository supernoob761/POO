<?php
require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../src/Repository/CustomerRepository.php';
require_once __DIR__ . '/../src/Repository/AccountRepository.php';
require_once __DIR__ . '/../src/Repository/transactionreposotory.php';
require_once __DIR__ . '/../src/Entity/Accounts.php';
require_once __DIR__ . '/../src/Entity/checking.php';
require_once __DIR__ . '/../src/Entity/saving.php';
require_once __DIR__ . '/../src/Entity/Client.php';
require_once __DIR__ . '/../src/Entity/transaction.php';

$customerRepo = new CustomerRepository();
$accountRepo = new AccountRepository();
$transactionRepo = new TransactionRepository();

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_customer':
                    if ($customerRepo->AddCus($_POST['name'], $_POST['email'])) {
                        $message = "Customer added successfully!";
                    }
                    break;
                
                case 'edit_customer':
                    $customerId = (int)$_POST['customer_id'];
                    if ($customerRepo->Update($_POST['name'], $_POST['email'], $customerId)) {
                        $message = "Customer updated successfully!";
                    }
                    break;
                
                case 'delete_customer':
                    $customerId = (int)$_POST['customer_id'];
                    if ($customerRepo->Delete($customerId)) {
                        $message = "Customer deleted successfully!";
                    } else {
                        $error = "Failed to delete customer. They may have existing accounts.";
                    }
                    break;
                
                case 'create_account':
                    $customerId = (int)$_POST['customer_id'];
                    $balance = floatval($_POST['balance']);
                    $type = $_POST['type'];
                    
                    if (empty($customerId)) {
                        $error = "Please select a customer!";
                    } else {
                        if ($accountRepo->createAccount($type, $balance, $customerId)) {
                            $message = "Account created successfully!";
                        }
                    }
                    break;
                
                case 'delete_account':
                    $accountId = (int)$_POST['account_id'];
                    $accountData = $accountRepo->findById($accountId);
                    
                    if ($accountData && $accountData['balance'] != 0) {
                        $error = "Cannot delete account! Balance must be exactly $0.00. Current balance: $" . number_format($accountData['balance'], 2);
                    } else if ($accountRepo->delete($accountId)) {
                        $message = "Account deleted successfully!";
                    } else {
                        $error = "Failed to delete account. It may not exist or there was a database error.";
                    }
                    break;
                
                case 'deposit':
                    $accountId = (int)$_POST['account_id'];
                    $amount = floatval($_POST['amount']);
                    
                    if ($amount <= 0) {
                        $error = "Amount must be greater than 0!";
                        break;
                    }
                    
                    $data = $accountRepo->findById($accountId);
                    
                    if ($data) {
                        if ($data['type'] === 'checking') {
                            $account = new checkingsAccount($data['id'], $data['balance'], $data['customer_id']);
                        } else {
                            $account = new savingsAccount($data['id'], $data['balance'], $data['customer_id']);
                        }
                        
                        $account->deposit($amount);
                        $accountRepo->updateBalance($account->getId(), $account->getBalance());
                        $transactionRepo->create('deposit', $amount, $accountId);
                        $message = "Deposit successful!";
                    }
                    break;
                
                case 'withdraw':
                    $accountId = (int)$_POST['account_id'];
                    $amount = floatval($_POST['amount']);
                    
                    if ($amount <= 0) {
                        $error = "Amount must be greater than 0!";
                        break;
                    }
                    
                    $data = $accountRepo->findById($accountId);
                    
                    if ($data) {
                        if ($data['type'] === 'checking') {
                            $account = new checkingsAccount($data['id'], $data['balance'], $data['customer_id']);
                        } else {
                            $account = new savingsAccount($data['id'], $data['balance'], $data['customer_id']);
                        }
                        
                        // Debug info
                        $currentBalance = $account->getBalance();
                        $afterWithdraw = $currentBalance - $amount;
                        
                        if ($account->withdraw($amount)) {
                            $accountRepo->updateBalance($account->getId(), $account->getBalance());
                            $transactionRepo->create('withdraw', $amount, $accountId);
                            $message = "Withdrawal successful! Previous balance: $" . number_format($currentBalance, 2) . " | New balance: $" . number_format($account->getBalance(), 2);
                        } else {
                            if ($data['type'] === 'checking') {
                                $error = "Withdrawal not allowed! Current balance: $" . number_format($currentBalance, 2) . " | Attempting to withdraw: $" . number_format($amount, 2) . " | Would result in: $" . number_format($afterWithdraw, 2) . " | Checking account limit: -$5,000.00";
                            } else {
                                $error = "Withdrawal not allowed! Current balance: $" . number_format($currentBalance, 2) . " | Attempting to withdraw: $" . number_format($amount, 2) . " | Savings accounts cannot have negative balance!";
                            }
                        }
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch customers - need to get id field manually if showAll() doesn't include it
$customers = $customerRepo->showAll();

// Debug: Check if we have IDs
if (!empty($customers) && !isset($customers[0]['id'])) {
    // If showAll() doesn't return IDs, we need to fetch them differently
    // This is a workaround - you should update CustomerRepository::showAll() to include id
    $pdo = Db::getInstance()->connection();
    $stmt = $pdo->query("SELECT id, name, email FROM customers");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$accounts = $accountRepo->findAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banking System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        h1 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .card h2 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.5em;
            border-bottom: 3px solid #2a5298;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
        }
        
        input, select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.5);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tr:hover {
            background: #f5f5f5;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-checking {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .badge-savings {
            background: #fce7f3;
            color: #be185d;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            margin-right: 5px;
        }
        
        .btn-edit {
            background: #059669;
            color: white;
        }
        
        .btn-edit:hover {
            background: #047857;
        }
        
        .btn-delete {
            background: #dc2626;
            color: white;
        }
        
        .btn-delete:hover {
            background: #b91c1c;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 20px;
        }
        
        .close:hover {
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏦 Banking System</h1>
        
        <?php if ($message): ?>
            <div class="message success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="grid">
            <!-- Add Customer -->
            <div class="card">
                <h2>Add Customer</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_customer">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <button type="submit">Add Customer</button>
                </form>
            </div>
            
            <!-- Create Account -->
            <div class="card">
                <h2>Create Account</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="create_account">
                    <div class="form-group">
                        <label>Customer:</label>
                        <select name="customer_id" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id'] ?? '' ?>">
                                    <?= htmlspecialchars($customer['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Account Type:</label>
                        <select name="type" required>
                            <option value="checking">Checking</option>
                            <option value="savings">Savings</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Initial Balance:</label>
                        <input type="number" name="balance" step="0.01" value="0" required>
                    </div>
                    <button type="submit">Create Account</button>
                </form>
            </div>
            
            <!-- Deposit -->
            <div class="card">
                <h2>Deposit</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="deposit">
                    <div class="form-group">
                        <label>Account:</label>
                        <select name="account_id" required>
                            <option value="">Select Account</option>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?= $account['id'] ?>">
                                    ID: <?= $account['id'] ?> - <?= ucfirst($account['type']) ?> 
                                    (Balance: $<?= number_format($account['balance'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount:</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required>
                    </div>
                    <button type="submit">Deposit</button>
                </form>
            </div>
            
            <!-- Withdraw -->
            <div class="card">
                <h2>Withdraw</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="withdraw">
                    <div class="form-group">
                        <label>Account:</label>
                        <select name="account_id" required>
                            <option value="">Select Account</option>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?= $account['id'] ?>">
                                    ID: <?= $account['id'] ?> - <?= ucfirst($account['type']) ?> 
                                    (Balance: $<?= number_format($account['balance'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount:</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required>
                    </div>
                    <button type="submit">Withdraw</button>
                </form>
            </div>
        </div>
        
        <!-- Customers Table -->
        <div class="table-container" style="margin-bottom: 20px;">
            <h2>Customers</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?= $customer['id'] ?? 'N/A' ?></td>
                            <td><?= htmlspecialchars($customer['name']) ?></td>
                            <td><?= htmlspecialchars($customer['email']) ?></td>
                            <td>
                                <button class="btn-small btn-edit" onclick="editCustomer(<?= $customer['id'] ?? 0 ?>, '<?= htmlspecialchars($customer['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($customer['email'], ENT_QUOTES) ?>')">Edit</button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                    <input type="hidden" name="action" value="delete_customer">
                                    <input type="hidden" name="customer_id" value="<?= $customer['id'] ?? 0 ?>">
                                    <button type="submit" class="btn-small btn-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Accounts Table -->
        <div class="table-container">
            <h2>Accounts</h2>
            <table>
                <thead>
                    <tr>
                        <th>Account ID</th>
                        <th>Type</th>
                        <th>Balance</th>
                        <th>Customer ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                        <tr>
                            <td><?= $account['id'] ?></td>
                            <td>
                                <span class="badge badge-<?= $account['type'] ?>">
                                    <?= ucfirst($account['type']) ?>
                                </span>
                            </td>
                            <td>$<?= number_format($account['balance'], 2) ?></td>
                            <td><?= $account['customer_id'] ?></td>
                            <td>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('⚠️ WARNING: Account can only be deleted if balance is $0.00!\n\nCurrent balance: $<?= number_format($account['balance'], 2) ?>\n\nAre you sure you want to try to delete this account?');">
                                    <input type="hidden" name="action" value="delete_account">
                                    <input type="hidden" name="account_id" value="<?= $account['id'] ?>">
                                    <button type="submit" class="btn-small btn-delete" <?= $account['balance'] != 0 ? 'style="opacity: 0.5;"' : '' ?>>Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Edit Customer Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 style="color: #1e3c72; margin-bottom: 20px;">Edit Customer</h2>
            <form method="POST">
                <input type="hidden" name="action" value="edit_customer">
                <input type="hidden" name="customer_id" id="edit_customer_id">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                <button type="submit">Update Customer</button>
            </form>
        </div>
    </div>
    
    <script>
        function editCustomer(id, name, email) {
            document.getElementById('edit_customer_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>