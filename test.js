import React, { useState } from 'react';
import { Users, CreditCard, ArrowDownCircle, ArrowUpCircle, List, Plus, Trash2, Eye } from 'lucide-react';

// ============ ENTITIES (Business Objects) ============

class Customer {
  constructor(id, firstName, lastName, email, phone) {
    this.id = id;
    this.firstName = firstName;
    this.lastName = lastName;
    this.email = email;
    this.phone = phone;
    this.accounts = [];
  }

  getFullName() {
    return `${this.firstName} ${this.lastName}`;
  }

  addAccount(account) {
    this.accounts.push(account);
  }

  removeAccount(accountId) {
    this.accounts = this.accounts.filter(acc => acc.id !== accountId);
  }
}

// Abstract Account class concept
class Account {
  constructor(id, accountNumber, balance, customerId) {
    if (this.constructor === Account) {
      throw new Error("Cannot instantiate abstract class Account");
    }
    this.id = id;
    this.accountNumber = accountNumber;
    this.balance = balance;
    this.customerId = customerId;
    this.transactions = [];
  }

  deposit(amount) {
    if (amount <= 0) throw new Error("Amount must be positive");
    this.balance += amount;
    return true;
  }

  withdraw(amount) {
    throw new Error("Method must be implemented by subclass");
  }

  addTransaction(transaction) {
    this.transactions.push(transaction);
  }

  getAccountType() {
    return this.constructor.name;
  }
}

class CurrentAccount extends Account {
  constructor(id, accountNumber, balance, customerId, overdraftLimit = 500) {
    super(id, accountNumber, balance, customerId);
    this.overdraftLimit = overdraftLimit;
  }

  withdraw(amount) {
    if (amount <= 0) throw new Error("Amount must be positive");
    if (this.balance - amount < -this.overdraftLimit) {
      throw new Error(`Insufficient funds. Overdraft limit: ${this.overdraftLimit}`);
    }
    this.balance -= amount;
    return true;
  }
}

class SavingsAccount extends Account {
  constructor(id, accountNumber, balance, customerId, interestRate = 2.5) {
    super(id, accountNumber, balance, customerId);
    this.interestRate = interestRate;
  }

  withdraw(amount) {
    if (amount <= 0) throw new Error("Amount must be positive");
    if (this.balance - amount < 0) {
      throw new Error("Insufficient funds. Savings account cannot go negative");
    }
    this.balance -= amount;
    return true;
  }

  applyInterest() {
    const interest = (this.balance * this.interestRate) / 100;
    this.balance += interest;
    return interest;
  }
}

class Transaction {
  constructor(id, accountId, type, amount, date) {
    this.id = id;
    this.accountId = accountId;
    this.type = type; // 'deposit' or 'withdrawal'
    this.amount = amount;
    this.date = date || new Date();
  }
}

// ============ REPOSITORY (Data Access Layer) ============

class BankRepository {
  constructor() {
    this.customers = [];
    this.accounts = [];
    this.transactions = [];
    this.nextCustomerId = 1;
    this.nextAccountId = 1;
    this.nextTransactionId = 1;
  }

  // Customer CRUD
  createCustomer(firstName, lastName, email, phone) {
    const customer = new Customer(this.nextCustomerId++, firstName, lastName, email, phone);
    this.customers.push(customer);
    return customer;
  }

  getAllCustomers() {
    return [...this.customers];
  }

  getCustomerById(id) {
    return this.customers.find(c => c.id === id);
  }

  updateCustomer(id, data) {
    const customer = this.getCustomerById(id);
    if (!customer) throw new Error("Customer not found");
    Object.assign(customer, data);
    return customer;
  }

  deleteCustomer(id) {
    const customer = this.getCustomerById(id);
    if (!customer) throw new Error("Customer not found");
    if (customer.accounts.length > 0) {
      throw new Error("Cannot delete customer with active accounts");
    }
    this.customers = this.customers.filter(c => c.id !== id);
    return true;
  }

  // Account CRUD
  createAccount(customerId, accountType, initialBalance) {
    const customer = this.getCustomerById(customerId);
    if (!customer) throw new Error("Customer not found");

    const accountNumber = `ACC${String(this.nextAccountId).padStart(6, '0')}`;
    let account;

    if (accountType === 'current') {
      account = new CurrentAccount(this.nextAccountId++, accountNumber, initialBalance, customerId);
    } else if (accountType === 'savings') {
      account = new SavingsAccount(this.nextAccountId++, accountNumber, initialBalance, customerId);
    } else {
      throw new Error("Invalid account type");
    }

    this.accounts.push(account);
    customer.addAccount(account);
    return account;
  }

  getAllAccounts() {
    return [...this.accounts];
  }

  getAccountById(id) {
    return this.accounts.find(a => a.id === id);
  }

  getCustomerAccounts(customerId) {
    return this.accounts.filter(a => a.customerId === customerId);
  }

  deleteAccount(id) {
    const account = this.getAccountById(id);
    if (!account) throw new Error("Account not found");
    if (account.balance !== 0) {
      throw new Error("Cannot delete account with non-zero balance");
    }
    
    const customer = this.getCustomerById(account.customerId);
    if (customer) customer.removeAccount(id);
    
    this.accounts = this.accounts.filter(a => a.id !== id);
    return true;
  }

  // Transaction operations
  createTransaction(accountId, type, amount) {
    const account = this.getAccountById(accountId);
    if (!account) throw new Error("Account not found");

    try {
      if (type === 'deposit') {
        account.deposit(amount);
      } else if (type === 'withdrawal') {
        account.withdraw(amount);
      } else {
        throw new Error("Invalid transaction type");
      }

      const transaction = new Transaction(
        this.nextTransactionId++,
        accountId,
        type,
        amount,
        new Date()
      );

      this.transactions.push(transaction);
      account.addTransaction(transaction);
      return transaction;
    } catch (error) {
      throw error;
    }
  }

  getAccountTransactions(accountId) {
    return this.transactions.filter(t => t.accountId === accountId);
  }
}

// ============ REACT COMPONENT ============

const BankingApp = () => {
  const [repository] = useState(() => {
    const repo = new BankRepository();
    // Add sample data
    const customer1 = repo.createCustomer("John", "Doe", "john@example.com", "123-456-7890");
    const customer2 = repo.createCustomer("Jane", "Smith", "jane@example.com", "098-765-4321");
    repo.createAccount(customer1.id, 'current', 1000);
    repo.createAccount(customer2.id, 'savings', 5000);
    return repo;
  });

  const [activeView, setActiveView] = useState('customers');
  const [message, setMessage] = useState({ text: '', type: '' });
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [selectedAccount, setSelectedAccount] = useState(null);
  const [refresh, setRefresh] = useState(0);

  const showMessage = (text, type = 'success') => {
    setMessage({ text, type });
    setTimeout(() => setMessage({ text: '', type: '' }), 3000);
  };

  const forceRefresh = () => setRefresh(r => r + 1);

  // Customer Management
  const handleCreateCustomer = () => {
    const firstName = prompt("First Name:");
    if (!firstName) return;
    const lastName = prompt("Last Name:");
    if (!lastName) return;
    const email = prompt("Email:");
    if (!email) return;
    const phone = prompt("Phone:");
    
    try {
      repository.createCustomer(firstName, lastName, email, phone);
      showMessage("Customer created successfully!");
      forceRefresh();
    } catch (error) {
      showMessage(error.message, 'error');
    }
  };

  const handleDeleteCustomer = (id) => {
    if (!confirm("Delete this customer?")) return;
    try {
      repository.deleteCustomer(id);
      showMessage("Customer deleted successfully!");
      forceRefresh();
    } catch (error) {
      showMessage(error.message, 'error');
    }
  };

  // Account Management
  const handleCreateAccount = () => {
    const customers = repository.getAllCustomers();
    if (customers.length === 0) {
      showMessage("Please create a customer first!", 'error');
      return;
    }

    const customerList = customers.map(c => `${c.id}: ${c.getFullName()}`).join('\n');
    const customerId = parseInt(prompt(`Select Customer ID:\n${customerList}`));
    if (!customerId) return;

    const accountType = prompt("Account Type (current/savings):")?.toLowerCase();
    if (!accountType || !['current', 'savings'].includes(accountType)) {
      showMessage("Invalid account type!", 'error');
      return;
    }

    const initialBalance = parseFloat(prompt("Initial Balance:"));
    if (isNaN(initialBalance) || initialBalance < 0) {
      showMessage("Invalid balance!", 'error');
      return;
    }

    try {
      repository.createAccount(customerId, accountType, initialBalance);
      showMessage("Account created successfully!");
      forceRefresh();
    } catch (error) {
      showMessage(error.message, 'error');
    }
  };

  const handleDeleteAccount = (id) => {
    if (!confirm("Delete this account?")) return;
    try {
      repository.deleteAccount(id);
      showMessage("Account deleted successfully!");
      setSelectedAccount(null);
      forceRefresh();
    } catch (error) {
      showMessage(error.message, 'error');
    }
  };

  // Transaction Management
  const handleTransaction = (type) => {
    if (!selectedAccount) {
      showMessage("Please select an account first!", 'error');
      return;
    }

    const amount = parseFloat(prompt(`Enter ${type} amount:`));
    if (isNaN(amount) || amount <= 0) {
      showMessage("Invalid amount!", 'error');
      return;
    }

    try {
      repository.createTransaction(selectedAccount.id, type, amount);
      showMessage(`${type.charAt(0).toUpperCase() + type.slice(1)} successful!`);
      forceRefresh();
    } catch (error) {
      showMessage(error.message, 'error');
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6">
      <div className="max-w-7xl mx-auto">
        <h1 className="text-4xl font-bold text-gray-800 mb-2">Banking Application</h1>
        <p className="text-gray-600 mb-8">OOP Console Banking Demo - JavaScript Edition</p>

        {message.text && (
          <div className={`mb-6 p-4 rounded-lg ${message.type === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}`}>
            {message.text}
          </div>
        )}

        <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
          <div className="flex gap-2 mb-6 flex-wrap">
            <button onClick={() => setActiveView('customers')} className={`px-4 py-2 rounded-lg flex items-center gap-2 ${activeView === 'customers' ? 'bg-blue-600 text-white' : 'bg-gray-200'}`}>
              <Users size={20} /> Customers
            </button>
            <button onClick={() => setActiveView('accounts')} className={`px-4 py-2 rounded-lg flex items-center gap-2 ${activeView === 'accounts' ? 'bg-blue-600 text-white' : 'bg-gray-200'}`}>
              <CreditCard size={20} /> Accounts
            </button>
            <button onClick={() => setActiveView('transactions')} className={`px-4 py-2 rounded-lg flex items-center gap-2 ${activeView === 'transactions' ? 'bg-blue-600 text-white' : 'bg-gray-200'}`}>
              <List size={20} /> Transactions
            </button>
          </div>

          {activeView === 'customers' && (
            <div>
              <div className="flex justify-between items-center mb-4">
                <h2 className="text-2xl font-bold text-gray-800">Customer Management</h2>
                <button onClick={handleCreateCustomer} className="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-green-700">
                  <Plus size={20} /> Create Customer
                </button>
              </div>
              <div className="grid gap-4">
                {repository.getAllCustomers().map(customer => (
                  <div key={customer.id} className="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div className="flex justify-between items-start">
                      <div>
                        <h3 className="text-xl font-semibold text-gray-800">{customer.getFullName()}</h3>
                        <p className="text-gray-600">Email: {customer.email}</p>
                        <p className="text-gray-600">Phone: {customer.phone}</p>
                        <p className="text-sm text-gray-500 mt-2">Accounts: {customer.accounts.length}</p>
                      </div>
                      <button onClick={() => handleDeleteCustomer(customer.id)} className="text-red-600 hover:text-red-800">
                        <Trash2 size={20} />
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {activeView === 'accounts' && (
            <div>
              <div className="flex justify-between items-center mb-4">
                <h2 className="text-2xl font-bold text-gray-800">Account Management</h2>
                <button onClick={handleCreateAccount} className="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-green-700">
                  <Plus size={20} /> Create Account
                </button>
              </div>
              <div className="grid gap-4">
                {repository.getAllAccounts().map(account => {
                  const customer = repository.getCustomerById(account.customerId);
                  return (
                    <div key={account.id} className={`border-2 rounded-lg p-4 cursor-pointer transition-all ${selectedAccount?.id === account.id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:shadow-md'}`} onClick={() => setSelectedAccount(account)}>
                      <div className="flex justify-between items-start">
                        <div>
                          <h3 className="text-xl font-semibold text-gray-800">{account.accountNumber}</h3>
                          <p className="text-gray-600">Type: {account.getAccountType()}</p>
                          <p className="text-gray-600">Owner: {customer?.getFullName()}</p>
                          <p className="text-2xl font-bold text-green-600 mt-2">${account.balance.toFixed(2)}</p>
                          {account instanceof CurrentAccount && (
                            <p className="text-sm text-gray-500">Overdraft: ${account.overdraftLimit}</p>
                          )}
                          {account instanceof SavingsAccount && (
                            <p className="text-sm text-gray-500">Interest: {account.interestRate}%</p>
                          )}
                        </div>
                        <button onClick={(e) => { e.stopPropagation(); handleDeleteAccount(account.id); }} className="text-red-600 hover:text-red-800">
                          <Trash2 size={20} />
                        </button>
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          )}

          {activeView === 'transactions' && (
            <div>
              <h2 className="text-2xl font-bold text-gray-800 mb-4">Transaction Management</h2>
              
              {selectedAccount ? (
                <div>
                  <div className="bg-blue-50 p-4 rounded-lg mb-4">
                    <h3 className="font-semibold text-lg">Selected Account: {selectedAccount.accountNumber}</h3>
                    <p className="text-2xl font-bold text-green-600">${selectedAccount.balance.toFixed(2)}</p>
                  </div>

                  <div className="flex gap-2 mb-6">
                    <button onClick={() => handleTransaction('deposit')} className="bg-green-600 text-white px-6 py-3 rounded-lg flex items-center gap-2 hover:bg-green-700">
                      <ArrowDownCircle size={20} /> Deposit
                    </button>
                    <button onClick={() => handleTransaction('withdrawal')} className="bg-red-600 text-white px-6 py-3 rounded-lg flex items-center gap-2 hover:bg-red-700">
                      <ArrowUpCircle size={20} /> Withdraw
                    </button>
                  </div>

                  <h3 className="text-xl font-bold text-gray-800 mb-3">Transaction History</h3>
                  <div className="space-y-2">
                    {repository.getAccountTransactions(selectedAccount.id).map(transaction => (
                      <div key={transaction.id} className={`p-3 rounded-lg ${transaction.type === 'deposit' ? 'bg-green-50 border-l-4 border-green-500' : 'bg-red-50 border-l-4 border-red-500'}`}>
                        <div className="flex justify-between items-center">
                          <div>
                            <span className="font-semibold capitalize">{transaction.type}</span>
                            <span className="text-gray-600 text-sm ml-2">{transaction.date.toLocaleString()}</span>
                          </div>
                          <span className={`font-bold ${transaction.type === 'deposit' ? 'text-green-600' : 'text-red-600'}`}>
                            {transaction.type === 'deposit' ? '+' : '-'}${transaction.amount.toFixed(2)}
                          </span>
                        </div>
                      </div>
                    ))}
                    {repository.getAccountTransactions(selectedAccount.id).length === 0 && (
                      <p className="text-gray-500 text-center py-8">No transactions yet</p>
                    )}
                  </div>
                </div>
              ) : (
                <div className="text-center py-12">
                  <Eye size={48} className="mx-auto text-gray-400 mb-4" />
                  <p className="text-gray-600">Please select an account from the Accounts tab</p>
                </div>
              )}
            </div>
          )}
        </div>

        <div className="bg-white rounded-lg shadow-lg p-6">
          <h3 className="text-xl font-bold text-gray-800 mb-3">Architecture Concepts Demonstrated</h3>
          <div className="grid md:grid-cols-3 gap-4 text-sm">
            <div className="p-3 bg-blue-50 rounded">
              <h4 className="font-semibold mb-1">OOP Principles</h4>
              <p className="text-gray-600">Classes, Inheritance (CurrentAccount/SavingsAccount extend Account), Encapsulation, Polymorphism</p>
            </div>
            <div className="p-3 bg-green-50 rounded">
              <h4 className="font-semibold mb-1">Design Patterns</h4>
              <p className="text-gray-600">Repository pattern for data access, Abstract class (Account), Business logic separation</p>
            </div>
            <div className="p-3 bg-purple-50 rounded">
              <h4 className="font-semibold mb-1">Business Rules</h4>
              <p className="text-gray-600">Overdraft limits for current accounts, Balance validation, Transaction history tracking</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default BankingApp;