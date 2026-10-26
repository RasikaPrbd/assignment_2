# Sales & Inventory Management System

A web-based application developed using Laravel to manage users, customers, products, suppliers, purchases, invoices, payments, and business reports.

## Key Features

- User Management
- Customer Management
- Product and Category Management
- Supplier Management
- Purchase Management
- Invoice Management
- Payment Management
- Stock Reports
- Business Reports with Date Filters
- Role-Based Access Control
- Login Security with Rate Limiting

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
- Node.js and NPM

## Installation and Setup

### 1. Clone the Repository

```bash
git clone https://github.com/RasikaPrbd/assignment_2.git
cd assignment_2
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Configure the Environment

Copy `.env.example` to `.env` and update the database settings.

Create a MySQL database named `sales_inventory`, or use another database name and update `.env` accordingly.

Generate the application key:

```bash
php artisan key:generate
```

### 4. Run Database Migrations and Seeders

Run the database migrations:

```bash
php artisan migrate
```

If the project has seeders configured to create the required roles or sample data, run:

```bash
php artisan db:seed
```

### 5. Build Frontend Assets

```bash
npm run build
```

### 6. Start the Application

```bash
php artisan serve
```

Open the application in your browser:

http://127.0.0.1:8000

## Security

- Secure user authentication.
- Password hashing using Laravel.
- Role-based authorization.
- Validation of user input.
- Login rate limiting to reduce repeated failed login attempts.
- Inactive user accounts are prevented from logging in.
