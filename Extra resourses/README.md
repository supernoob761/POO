# Console Banking Application in PHP OOP – PDO

## Description

This project is a console-based banking application developed in PHP, designed for learning Object-Oriented Programming (OOP) and database manipulation with PDO.

The application allows for the structured and secure management of clients, bank accounts, and transactions, adhering to best practices in back-end development.

The project emphasizes software architecture, separation of responsibilities, data persistence, and UML modeling.

---

## Project Context

You are a back-end developer at a company specializing in digital financial solutions.

Your mission is to design and develop a console-based banking application for a financial institution.

This application allows for the reliable and maintainable management of clients, bank accounts, and transactions, while respecting professional back-end development standards.

---

## Learning Objectives

- Master object-oriented programming in PHP
- Design a clear and maintainable architecture
- Manipulate a relational database with PDO
- Implement a complete and secure CRUD operation
- Manage banking business logic
- Comply with PSR-4 and PSR-12 standards
- Understand and apply UML modeling

---

## Business Entities

- Customer
- Account (abstract class)
- Current Account
- Savings Account
- Transaction

All data is persisted in a relational database and manipulated exclusively via PDO using prepared statements.

---

## Features

### Customer Management
- Create, modify, and delete customers
- View customer list
- Verify email uniqueness

### Account Management
- Create bank accounts
- Associate an account with a customer
- Manage multiple account types via inheritance
- Delete an account only if the balance is zero

### Transaction Management
- Deposit to a bank account
- Withdrawal with business rules based on account type
- View transaction history
- Use PDO transactions (commit/rollback)

---

## User Stories (Scrum)

### Customer Management
- US01: Create a customer
- US02: View customer list
- US03: View customer details
- US04: Modify a customer
- US05: Delete a customer without a bank account

### Account Management
- US06 : Create a bank account for a client
- US07: Choose the account type
- US08: View all accounts
- US09: View a client's accounts
- US10: Delete a bank account with a zero balance

### Transaction Management
- US11: Make a deposit
As a user, I want to make a deposit into a bank account to increase its balance.

**Business rules according to account type:**
- **Checking Account**:

- A fixed fee of $1 per deposit is applied
- Amount actually credited = amount deposited − $1
- **Savings Account**:

- No fee applied
- Amount credited = amount deposited
---
#### US12: Make a withdrawal
As a user, I want to make a withdrawal according to the business rules of the account type.

**Business Rules by Account Type:**

- **Checking Account**: Withdrawals are allowed even if the balance becomes negative (overdraft limited to -$500)
- **Savings Account**: Withdrawals are allowed only if the balance is sufficient (no overdraft)

- US13: View the transaction history of an account

---

## Technical Constraints

### Object-Oriented Programming
- Classes and Objects
- Constructors
- Encapsulation (private, protected)
- Getters and Setters with Validation
- Inheritance and Polymorphism
- Abstract Classes

### Architecture and Quality
- Type Hinting
- Custom Exceptions
- CRUD via PDO with Prepared Statements
- PDO Transaction Management
- Separation of Responsibilities: (Bonus)

- Entities
- Repositories (Data Access)

- Services (Business Logic)

### Standards (Bonus)
- PSR-4 (autoloading)
- PSR-12 (code style)

---

## UML – Mandatory Modeling

### Class Diagram
The diagram must represent:
- Customer
- Account (abstract)
- Current Account
- Savings Account
- Transaction

Expected Relationships:
- A customer has multiple accounts
- An account has multiple transactions
- Inheritance between Account and its subclasses

### Use Case Diagram
Main Actor: Bank User

Use Cases:
- Manage customers
- Manage accounts
- Make a deposit
- Make a withdrawal
- View transaction history