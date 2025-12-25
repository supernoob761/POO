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
- US12: Make a withdrawal according to business rules
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
- Type hinting
- Custom exceptions
- CRUD via PDO with prepared statements
- PDO transaction management
- Separation of responsibilities: (Bonus)

- Entities
- Repositories (data access)
- Services (logic) (Business)

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