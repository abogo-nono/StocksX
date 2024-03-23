# StockX

## How to deploy locally this project?

<p> To deploy this app you need to follow all these steps. Make sure to not skip any step to avoid errors.</p>

### Requirements

<p> Here are some requirements you need to satisfied before starting the deployment process.</p>
<ol>
    <li><code> php >= 8.2</code></li>
    <li><code> composer >= 2.3</code></li>
    <li><code> nodejs >= 18.8.0</code></li>
    <li><code> npm >= 8.18.0</code></li>
    <li><code> mailpit </code></li>
</ol>

### How to install StocksX

#### clone the repo

`git clone https://github.com/abogo-nono/StocksX.git`

#### change current directory to StocksX

`cd StocksX`

#### install php and js dependencies

`composer install` <br>
`npm install`

### Configure StocksX

#### configure the environment file

<p> Rename the file .env.example -> .env and fill with your environment config </p>
<p>

    Example:

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
</p>

### Setup database and tables

#### Migrate tables and seed database

<p> <code>php artisan migrate</code> </p>
<p> <code>php artisan db:seed</code></p>

#### configure roles and privileges

<p>
    `php artisan make:filament-user`
        -> `admin` for username
        -> `admin` for username
        -> `admin` for password
</p>
<p>
    <code>php artisan shield:install --fresh </code> <br>
    <code>php artisan shield:generate --all </code> <br>
    <code>php artisan shield:super-admin --user=1 </code>
</p>

#### Setup storage

`php artisan storage:link`

### run the app

`npm run dev`
`php artisan serve`

*In your browser type*
`127.0.0.1:8000`

### By Abogo Lincoln
