# StockX - Stocks Management System

---

## How It Works

The Stocks Management System provides a user-friendly interface for managing all aspects of your inventory. Users can register or log in to access the system's functionalities. Once logged in, users can navigate through the dashboard to perform various tasks such as managing product categories, suppliers, products, orders, roles, and users.

Users can create, update, list, soft-delete, and delete entities as per their requirements. They can also assign roles to users to control their access permissions within the system. Additionally, users will receive email notifications when products need to be restocked, ensuring timely replenishment of inventory.

## Features

StockX, offering the following features:

- **Product Category Management**: Allows users to create, update, list, soft-delete, and delete product categories.
- **Product Supplier Management**: Provides functionalities to manage product suppliers including creating, updating, listing, soft-deleting, and deleting.
- **Product Management**: Enables users to manage products by creating, updating, listing, soft-deleting, and deleting them.
- **Order Management**: Facilitates order management by allowing users to create, update, list, soft-delete, and delete orders.
- **Roles Management**: Offers role management functionalities such as creating, updating, listing, soft-deleting, and deleting roles.
- **User Management**: Provides user management features including creating, updating, listing, soft-deleting, and deleting users. Users can also be assigned roles.
- **Full Functional Auth System**: Implements a robust authentication system ensuring secure access to the system's functionalities.
- **Email Notification**: Sends email notifications when a product needs to be restocked, ensuring timely replenishment of inventory.

## Gallery

Here are some screenshots showcasing the StockX application:

- <img src="./screenshots/Screenshot 2024-04-12 at 14-03-26 Dashboard - StocksX.png" alt="Dashboard - StocksX">
- <img src="./screenshots/Screenshot 2024-04-12 at 14-04-41 Products - StocksX.png" alt="Products - StocksX">
- <img src="./screenshots/Screenshot 2024-04-12 at 14-05-06 Orders - StocksX.png" alt="Orders - StocksX">
- <img src="./screenshots/Screenshot 2024-04-12 at 14-05-36 Create Order - StocksX.png" alt="Create Order - StocksX">
- <img src="./screenshots/Screenshot 2024-04-12 at 14-06-02 Roles - StocksX.png" alt="Roles - StocksX">
- <img src="./screenshots/Screenshot 2024-04-12 at 14-06-12 Create Role - StocksX.png" alt="Create Role - StocksX">
- <img src="./screenshots/Screenshot 2024-04-12 at 14-06-31 Edit cashier - StocksX.png" alt="Edit cashier - StocksX">

## How to Deploy StockX Locally?

To deploy this app, follow these steps. Ensure you follow each step to avoid errors.

### Requirements

Before starting the deployment process, ensure the following requirements are met:

- `php` version >= 8.2
- `composer` version >= 2.3
- `nodejs` version >= 18.8.0
- `npm` version >= 8.18.0
- `mailpit`

### Installation

#### Clone the Repository

```bash
git clone https://github.com/oi/StocksX.git
```

#### Change Directory

```bash
cd StocksX
```

#### Install Dependencies

```bash
composer install
npm install
```

### Configuration

#### Configure Environment

Rename the `.env.example` file to `.env` and fill in your environment configurations.

Example:

```dotenv
APP_NAME=StockX
APP_ENV=local
APP_KEY=base64:R6fRMhFwFTaTxPlKDUi+nUYVLLLO8bX+g7AWARu91l8=
APP_DEBUG=true
APP_URL=127.0.0.1:8000
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

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

### Setup Database and Tables

#### Migrate Tables and Seed Database

```bash
php artisan migrate
php artisan db:seed
```

#### Configure Roles and Privileges

```bash
php artisan make:filament-user
php artisan shield:install --fresh
php artisan shield:generate --all
php artisan shield:super-admin --user=1
```

### Setup Storage

```bash
php artisan storage:link
```

### Run the App

```bash
npm run dev
php artisan serve
```

Access the app in your browser at `127.0.0.1:8000`.

## How Can I Improve It?

Your feedback is valuable! If you have any suggestions, feature requests, or encounter any issues during deployment, please let us know. Your input helps us improve the deployment guide and the project itself.

---
