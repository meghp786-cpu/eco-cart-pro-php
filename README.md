# Eco-Cart — PHP Shopping Portal

Eco-Cart is a PHP and MySQL e-commerce project for browsing products and managing shopping activity through a customer-facing storefront and an administration area. The project includes product categories, cart/order flows, reviews, and order tracking data.

## Tech stack

- PHP
- MySQL / MariaDB
- HTML, CSS and JavaScript
- Bootstrap and jQuery-based UI components

## Highlights

- Product listing and category navigation
- Shopping cart flow
- User and administrator areas
- Product/category management
- Order and order-status tracking
- Product-review data structure
- SQL database export for local setup

## Project structure

The application is contained in the `shopping/` directory. The SQL export is available at `SQL file/shopping.sql`.

## Run locally

### Prerequisites

- PHP 8.x
- MySQL or MariaDB
- Apache or another PHP-capable local web server (for example, XAMPP)

### 1. Clone the repository

```bash
git clone https://github.com/meghp786-cpu/eco-cart-pro-php.git
cd eco-cart-pro-php
```

### 2. Create the database

Create a database named `shopping` in MySQL, then import:

```text
SQL file/shopping.sql
```

You can import it through phpMyAdmin or the MySQL command line.

### 3. Configure database access

Update `shopping/includes/config.php` to use your local database host, username, password, and database name. Do not use development credentials on a public server.

### 4. Start the application

Place the project in your local web server's document root and open the `shopping/` directory through your local server. For example, with XAMPP the project can be placed under `htdocs/`.

## Security and deployment notes

This repository should be treated as a learning/demo project until it has been reviewed for production use. Before deploying publicly:

- Replace hard-coded database settings with environment-based configuration.
- Review authentication and password storage; never keep default or sample administrator credentials.
- Use prepared statements and validate all user input.
- Disable error display in production and log errors securely.
- Configure HTTPS, session security, authorization checks, and CSRF protection.
- Review the SQL export for sample data before sharing a production database.

## Author

**Megh P.** — [GitHub profile](https://github.com/meghp786-cpu)
