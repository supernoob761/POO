CREATE TABLE customers (
  id INT IDENTITY(1,1) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE accounts (
  id INT IDENTITY(1,1) PRIMARY KEY,
  balance DECIMAL(10,2) NOT NULL DEFAULT 0,
  type VARCHAR(20) CHECK (type IN ('Checking','Savings')),
  customer_id INT NOT NULL,
  FOREIGN KEY (customer_id) REFERENCES customers(id)
);

CREATE TABLE transactions (
  id INT IDENTITY(1,1) PRIMARY KEY,
  type VARCHAR(20) CHECK (type IN ('deposit','withdrawal')),
  amount DECIMAL(10,2),
  created_at DATETIME DEFAULT GETDATE(),
  account_id INT NOT NULL,
  FOREIGN KEY (account_id) REFERENCES accounts(id)
);
