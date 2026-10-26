# Sales & Inventory Management System

A web-based application developed using Laravel to manage users, customers, products, suppliers, purchases, invoices, payments, and business reports.

## Key Features

- User Management
- Customer Management
- Product Management
- Supplier Management
- Purchase Management
- Invoice Management
- Payment Management
- Business Reports and Stock Reports

## User Roles

- **Admin** – Manages users and accesses all system features.
- **Sales User** – Manages customers, invoices, payments, and sales reports.
- **Inventory User** – Manages products, categories, suppliers, purchases, and stock reports.

## Technologies Used

- Laravel 13
- PHP 8.5
- MySQL
- Blade
- HTML and CSS
- JavaScript

## Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd assignment2
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Configure Environment

Copy `.env.example` to `.env` and configure your database connection.

```bash
php artisan key:generate
```

### 4. Run Database Migrations

Create the database in MySQL, then run:

```bash
php artisan migrate
```

### 5. Start the Application

```bash
php artisan serve
```

If frontend assets need to be compiled, run this in another terminal:

```bash
npm run dev
```

Open the application at:

http://127.0.0.1:8000

## Login & Security

- Secure user authentication.
- Role-based authorization for Admin, Sales User, and Inventory User.
- Login rate limiting to temporarily block repeated failed login attempts.
- Passwords are securely hashed by Laravel.
