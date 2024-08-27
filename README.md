# Wallet Systems

## Overview

The Wallet Systems project is a Laravel-based application designed for comprehensive financial management. It includes features for user authentication, wallet management, and transaction management. The system uses Laravel Passport for API authentication and follows the repository pattern to manage data access.

## Features

### User Authentication

- **Registration**: Allows users to create an account by providing necessary details like name, email, and password. Passwords are securely hashed.
- **Login**: Users can log in with their email and password to obtain an access token for authenticated API requests.
- **Logout**: Authenticated users can log out, which revokes their access tokens.
- **Profile Update**: Users can update their profile information, such as name and email.
- **Password Change**: Users can change their password, requiring the current password for verification.

### Wallet Management

- **Deposit Funds**: Users can deposit funds into their wallet by specifying the amount.
- **Transfer Funds**: Users can transfer funds from their wallet to another user's wallet, with validations to ensure sufficient balance and correct recipient details.
- **Check Balance**: Users can check the current balance of their wallet.

### Transaction Management

- **Transaction History**: Users can view their transaction history, which includes all deposits, transfers, and other wallet-related activities.

## Postman Collection

To interact with the APIs and test the endpoints, you can use the provided Postman collection. The collection can be accessed [here](https://documenter.getpostman.com/view/11963978/2sAXjGcZ9y#81f49c92-7819-401e-9165-c78e414d5f8f).

## Tools

- **Laravel Passport**: Used for API authentication and managing access tokens.
- **Repository Pattern**: Implements the repository pattern for managing data access and separation of concerns.
- **Unit Testing**: The project includes unit tests to ensure the functionality of various components and endpoints.

## Setup

Follow these steps to set up and run the project locally:

1. **Clone the Repository**

   ```bash
   git clone https://github.com/mohamedsayedfci/wallet-systems.git
1. **Postman Collection**

   ```
   https://documenter.getpostman.com/view/11963978/2sAXjGcZ9y#81f49c92-7819-401e-9165-c78e414d5f8f
