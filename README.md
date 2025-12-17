<div align="center">

# 📦 StockX – Modern Inventory Management System

**A powerful, intuitive inventory management solution built with Laravel & FilamentPHP**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![FilamentPHP](https://img.shields.io/badge/FilamentPHP-3.x-F59E0B?style=for-the-badge&logo=php&logoColor=white)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

*Streamline your stock, orders, suppliers, and user roles—all in one place*

[🚀 Quick Start](#-quick-start) • [✨ Features](#-feature-highlights) • [📸 Screenshots](#-screenshots) • [📚 Documentation](#-documentation)

</div>

---

## ✨ Overview

StockX is a comprehensive inventory management platform designed for businesses of all sizes. Whether you're managing a warehouse, running a small business, or operating a large-scale enterprise, StockX provides all the essential tools to keep your inventory organized, tracked, and optimized.

### 🎯 Perfect For
- 🏪 **Retail Stores** - Boutiques, local shops, chain stores
- 🏭 **Warehouses** - Distribution centers, storage facilities
- 🛒 **E-commerce** - Online stores, dropshipping businesses
- 🏢 **Enterprises** - Large-scale operations with complex inventory needs

---

## 🚀 Feature Highlights

<table>
<tr>
<td width="50%">

### 📦 **Inventory Management**
- ✅ **Product Categories** - Organize with smart categorization
- ✅ **Supplier Management** - Full CRUD with contact details
- ✅ **Product Tracking** - Real-time stock levels & pricing
- ✅ **Bulk Operations** - Mass import/export capabilities

### 📊 **Analytics & Insights**
- ✅ **Interactive Charts** - Sales trends & performance metrics
- ✅ **Dashboard Widgets** - Key metrics at a glance
- ✅ **Stock Reports** - Low stock alerts & inventory reports
- ✅ **Order Analytics** - Track patterns & revenue trends

</td>
<td width="50%">

### 📑 **Order Management**
- ✅ **Smart Processing** - Auto stock validation & updates
- ✅ **Order Lifecycle** - From creation to delivery
- ✅ **Dynamic Inventory** - Real-time stock adjustments
- ✅ **Delivery Tracking** - Monitor shipment status

### 🔐 **Security & Access**
- ✅ **Role-Based Permissions** - Granular access control
- ✅ **User Management** - Team member administration
- ✅ **Secure Authentication** - Email verification ready
- ✅ **Audit Trails** - Track all system changes

</td>
</tr>
</table>

### 🔥 **Advanced Features**
- 🔍 **Global Search** - Find anything instantly with smart search
- 📧 **Smart Notifications** - Automated low stock email alerts
- 🏷️ **Dynamic Filtering** - Filter by date ranges, categories, status
- 📱 **Responsive Design** - Works seamlessly on all devices
- ⚡ **Real-time Updates** - Live data synchronization
- 🎨 **Modern UI/UX** - Clean, intuitive interface powered by FilamentPHP

---

## 📸 Screenshots

<div align="center">

### 📊 Dashboard Overview
![Dashboard](./screenshots/127.0.0.1_8000_stocks-manager%20(9).png)

### 📦 Product Management
<table>
<tr>
<td align="center">
<img src="./screenshots/127.0.0.1_8000_stocks-manager%20(8).png" alt="Product List" width="400"/>
<br><em>Product Listing</em>
</td>
<td align="center">
<img src="./screenshots/127.0.0.1_8000_stocks-manager%20(16).png" alt="Product Details" width="400"/>
<br><em>Product Details</em>
</td>
</tr>
</table>

### 📋 Order Management
<table>
<tr>
<td align="center">
<img src="./screenshots/127.0.0.1_8000_stocks-manager%20(7).png" alt="Orders List" width="400"/>
<br><em>Orders Overview</em>
</td>
<td align="center">
<img src="./screenshots/127.0.0.1_8000_stocks-manager%20(10).png" alt="Create Order" width="400"/>
<br><em>Order Creation</em>
</td>
</tr>
</table>

### 👥 User & Role Management
<table>
<tr>
<td align="center">
<img src="./screenshots/127.0.0.1_8000_stocks-manager%20(12).png" alt="Roles" width="400"/>
<br><em>Role Management</em>
</td>
<td align="center">
<img src="./screenshots/127.0.0.1_8000_stocks-manager%20(13).png" alt="Edit Role" width="400"/>
<br><em>Permission Settings</em>
</td>
</tr>
</table>

### 📧 Email Notifications
![Low Stock Alert](./screenshots/Screenshot%20from%202025-04-04%2005-00-18.png)
*Automated low stock alert emails*

</div>

---

## 🚀 Quick Start

### Option 1: Docker Deployment (Recommended)

The fastest way to get StockX up and running. Only requires Docker on your system.

```bash
# Clone the repository
git clone https://github.com/abogo-nono/StocksX.git
cd StocksX

# Copy environment configuration
cp .env.example .env

# Build and start containers
docker-compose up --build -d

# Setup database and permissions
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan make:filament-user
docker-compose exec app php artisan shield:install --fresh
docker-compose exec app php artisan shield:generate --all
docker-compose exec app php artisan shield:super-admin --user=1

# 🎉 Visit http://localhost:9000
```

### Option 2: Manual Installation

<details>
<summary>Click to expand manual installation steps</summary>

#### Prerequisites
- **PHP** ≥ 8.2
- **Composer** ≥ 2.3
- **Node.js** ≥ 18.8
- **MySQL** ≥ 8.0
- **Mailpit** (for email testing)

#### Installation Steps

```bash
# Clone and navigate
git clone https://github.com/abogo-nono/StocksX.git
cd StocksX

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
# Edit .env with your database and mail settings

# Build assets
npm run build

# Database setup
php artisan migrate --seed
php artisan storage:link

# Create admin user and setup permissions
php artisan make:filament-user
php artisan shield:install --fresh
php artisan shield:generate --all
php artisan shield:super-admin --user=1

# Start the server
php artisan serve
```

</details>

---

## ⚙️ Configuration

### Environment Variables

Key configuration options in your `.env` file:

```env
# Application
APP_NAME=StockX
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stocksx
DB_USERNAME=root
DB_PASSWORD=

# Mail (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@stockx.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Default Credentials

After running the seeders, you can login with:
- **Email**: admin@example.com
- **Password**: password

> ⚠️ **Important**: Change the default password immediately after first login!

---

## 📋 System Requirements

### Docker Deployment
- **Docker** ≥ 20.10
- **Docker Compose** ≥ 1.29
- **4GB RAM** (recommended)
- **10GB Disk Space**

### Manual Installation
- **PHP** ≥ 8.2 with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Composer** ≥ 2.3
- **Node.js** ≥ 18.8 & **NPM** ≥ 8.18
- **MySQL** ≥ 8.0 or **PostgreSQL** ≥ 13
- **Redis** (optional, for caching)

---

## 🛠️ Development

### Running Tests

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage
```

### Code Quality

```bash
# PHP CS Fixer
./vendor/bin/php-cs-fixer fix

# PHPStan
./vendor/bin/phpstan analyse
```

### Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📚 Documentation

- [API Documentation](docs/api.md)
- [User Guide](docs/user-guide.md)
- [Admin Guide](docs/admin-guide.md)
- [Deployment Guide](docs/deployment.md)

---

## 🤝 Support & Community

- 🐛 **Found a bug?** [Open an issue](https://github.com/abogo-nono/StocksX/issues)
- 💡 **Feature request?** [Start a discussion](https://github.com/abogo-nono/StocksX/discussions)
- 📧 **Need help?** Contact us at support@stockx.com

---

## 📄 License

This project is open-source and available under the **[MIT License](LICENSE)**.

---

<div align="center">

**Made with ❤️ by the StockX Team**

[⭐ Star this repo](https://github.com/abogo-nono/StocksX/stargazers) if you find it helpful!

</div>
