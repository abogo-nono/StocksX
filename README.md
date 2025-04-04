# 📦 StockX – Stocks Management System  

**StockX** is a powerful and intuitive inventory management solution built with **Laravel** and **FilamentPHP**, designed to streamline your stock, orders, suppliers, and user roles—all in one place. Whether you're managing a warehouse, a small business, or a large-scale operation, StockX provides all the tools you need to keep your inventory under control.

## 🚀 Key Features

### 📦 Inventory Management
- **Product Categories** – Create, update, soft-delete, and manage product categories.
- **Suppliers** – Maintain supplier records with full CRUD capabilities.
- **Products** – Track products with key details like quantity, supplier, pricing, and category.

### 📑 Order Management
- **Order Handling** – Create and manage orders with automatic stock validation and dynamic inventory updates.
- **Stock Validation** – Ensure product availability during order processing.
- **Low Stock Alerts** – Email notifications when stock dips below a set threshold.

### 👥 User & Role Management
- **User Admin** – Create, edit, and manage users with role assignments.
- **Role-Based Access Control (RBAC)** – Fine-tuned permission handling with **Filament Shield**.

### 🔔 Notifications
- **Low Stock Emails** – Automatic alerts to notify admins when stock is low.

### 📊 Dashboard & Analytics
- **Interactive Charts** – Visualize sales and inventory trends.
- **Quick Stats** – Glance at total users, products, orders, and alerts.

### 🧭 Global Search
- **Smart Search** – Look up products, orders, and suppliers with rich result details.
- **Quick Navigation** – Jump straight to the item’s page from search results.

### 🔍 Filters & Tabs
- **Order Filters** – Filter by custom timeframes like today, this week, or this year.
- **Supplier Tabs** – Organize suppliers by product categories.

### 🔐 Authentication & Security
- **Secure Login** – Full auth system with email verification.
- **Permissions System** – Restrict access by user roles for enhanced security.

## 🖼️ Screenshots

Here’s a quick peek at what StockX looks like in action:

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

- **Email Notification (Low Stock)**  
  ![Email](./screenshots/Screenshot%20from%202025-04-04%2005-00-18.png)


## ⚙️ Deployment Guide

### 📋 Prerequisites

Make sure you have the following installed:

- **PHP** ≥ 8.2  
- **Composer** ≥ 2.3  
- **Node.js** ≥ 18.8  
- **NPM** ≥ 8.18  
- **Mailpit** – For testing email notifications  

### 📥 Installation Steps

#### 1. Clone the Repository
```bash
git clone https://github.com/abogo-nono/StocksX.git
```

#### 2. Navigate into the Project
```bash
cd StocksX
```

#### 3. Install Dependencies
```bash
composer install
npm install
```

### 🔧 Configuration

#### 1. Set Up `.env`
Rename `.env.example` to `.env` and adjust environment variables:
```dotenv
APP_NAME=StockX
APP_ENV=local
APP_URL=http://127.0.0.1:8000
DB_DATABASE=stocksx
DB_USERNAME=root
DB_PASSWORD=
MAIL_HOST=localhost
MAIL_PORT=1025
```

### 🗃️ Database Setup

#### 1. Run Migrations and Seeders
```bash
php artisan migrate
php artisan db:seed
```

#### 2. Configure Roles & Permissions
```bash
php artisan make:filament-user
php artisan shield:install --fresh
php artisan shield:generate --all
php artisan shield:super-admin --user=1
```

### 🖇️ Storage Linking
```bash
php artisan storage:link
```

### 🚀 Run the Application

#### Start Laravel Server:
```bash
php artisan serve
```

#### Start Frontend (Vite Dev Server):
```bash
npm run dev
```

Access it at [http://127.0.0.1:8000](http://127.0.0.1:8000)

## 🤝 Feedback & Contributions

We’d love to hear your thoughts!  
- 🐛 Found a bug? [Open an issue](https://github.com/abogo-nono/StocksX/issues)  
- 🌟 Got a feature idea? Share it or [submit a pull request](https://github.com/abogo-nono/StocksX/pulls)

## 📄 License

This project is open-source and available under the **[MIT License](LICENSE)**.
