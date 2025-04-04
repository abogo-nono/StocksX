# StockX - Stocks Management System

## Overview

StockX is a comprehensive inventory management system designed to streamline the process of managing products, suppliers, orders, and users. It provides a user-friendly interface with robust features to ensure efficient inventory tracking and management. The system also includes role-based access control, dynamic filtering, global search, and email notifications for low stock alerts, making it a powerful tool for businesses of all sizes.

## Features

StockX offers the following features:

### Inventory Management
- **Product Categories**: Create, update, list, soft-delete, and delete product categories.
- **Product Suppliers**: Manage suppliers with functionalities to create, update, list, soft-delete, and delete supplier records.
- **Products**: Add, update, list, soft-delete, and delete products with detailed information such as price, quantity, supplier, and category.

### Order Management
- **Order Creation and Tracking**: Create, update, list, soft-delete, and delete orders.
- **Stock Validation**: Automatically validate stock availability during order creation and editing.
- **Dynamic Stock Adjustment**: Automatically adjust product stock based on order changes.
- **Low Stock Alerts**: Notify the admin when product stock falls below a predefined threshold.

### User and Role Management
- **User Management**: Create, update, list, soft-delete, and delete users. Assign roles to control access permissions.
- **Role-Based Access Control**: Manage roles and permissions using the Filament Shield plugin.

### Notifications
- **Email Alerts**: Automatically send email notifications to the admin for low stock products.

### Dashboard and Analytics
- **Dynamic Charts**: Visualize order trends and inventory statistics with interactive charts.
- **Quick Stats**: View key metrics such as total users, products, orders, and low stock items.

### Filters and Tabs
- **Order Filters**: Filter orders by time periods such as today, yesterday, this week, this month, last month, this year, and last year.
- **Supplier Tabs**: View suppliers categorized by product categories for better organization.

### Global Search
- **Searchable Attributes**: Search across products, orders, and suppliers using attributes like name, price, quantity, and category.
- **Search Result Details**: Display additional details like category, price, and stock status in search results.
- **Quick Navigation**: Link search results to their respective pages for easy access.

### Authentication and Security
- **Full Authentication System**: Secure login and registration with email verification.
- **Role-Based Access Control**: Restrict access to specific features based on user roles.

## Screenshots

Here are some screenshots showcasing the StockX application:

- **Dashboard**  
  ![Dashboard](./screenshots/127.0.0.1_8000_stocks-manager%20(9).png)
  ![Dashboard](./screenshots/127.0.0.1_8000_stocks-manager%20(15).png)

- **Products**  
  ![Products](./screenshots/127.0.0.1_8000_stocks-manager%20(8).png)
  ![Products](./screenshots/127.0.0.1_8000_stocks-manager%20(16).png)

- **Orders**  
  ![Orders](./screenshots/127.0.0.1_8000_stocks-manager%20(7).png)
  ![Orders](./screenshots/127.0.0.1_8000_stocks-manager_orders.png)

- **Create Order**  
  ![Create Order](./screenshots/127.0.0.1_8000_stocks-manager%20(10).png)

- **Roles**  
  ![Roles](./screenshots/127.0.0.1_8000_stocks-manager%20(12).png)

- **Edit Role**  
  ![Edit Role](./screenshots/127.0.0.1_8000_stocks-manager%20(13).png)

- **Low stock email notification**
  ![Low stock email notifications](./screenshots/Screenshot%20from%202025-04-04%2005-00-18.png)

## Deployment Guide

Follow these steps to deploy StockX locally.

### Prerequisites

Ensure the following tools are installed on your system:

- **PHP**: Version >= 8.2
- **Composer**: Version >= 2.3
- **Node.js**: Version >= 18.8.0
- **NPM**: Version >= 8.18.0
- **Mailpit**: For email testing

### Installation Steps

#### 1. Clone the Repository

```bash
git clone https://github.com/abogo-nono/StocksX.git
```

#### 2. Navigate to the Project Directory

```bash
cd StocksX
```

#### 3. Install Dependencies

```bash
composer install
npm install
```

### Configuration

#### 1. Configure Environment Variables

Rename the `.env.example` file to `.env` and update the configurations:

```dotenv
APP_NAME=StockX
APP_ENV=local
APP_KEY=base64:R6fRMhFwFTaTxPlKDUi+nUYVLLLO8bX+g7AWARu91l8=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stocksx
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Database Setup

#### 1. Migrate and Seed the Database

```bash
php artisan migrate
php artisan db:seed
```

#### 2. Configure Roles and Permissions

```bash
php artisan make:filament-user
php artisan shield:install --fresh
php artisan shield:generate --all
php artisan shield:super-admin --user=1
```

### Storage Setup

Create a symbolic link for storage:

```bash
php artisan storage:link
```

### Run the Application

#### 1. Start the Development Server

```bash
php artisan serve
```

#### 2. Start the Frontend Development Server

```bash
npm run dev
```

Access the application at [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Feedback and Contributions

We welcome your feedback and contributions! If you encounter any issues or have suggestions for improvement, feel free to open an issue or submit a pull request.

## License

This project is licensed under the [MIT License](LICENSE).
