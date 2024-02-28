# Deploy stockX

## Requirements
`php>=8.2`, `mysql`, `composer`, `nodejs>= 18`

## How to install
*clone the repo*
`git clone https://github.com/abogo-nono/StocksX.git`

*change current dir*
`cd StocksX`

*install dependencies*
`composer install`
`npm install`

### *Rename the .env.exemple -> .env and fill with your environment config*

*Migrate tables*
`php artisan migrate`
`php artisan db:seed`

*configure roles and privileges*
`php artisan make:filement-user`
    -> `admin` for username
    -> `admin` for password
`shield:install --fresh`
`shield:generate --all`
`shield:super-admin --user=1`

### run the app
`php artisan storage:link`
`npm run dev`
`php artisan serve`

*In your browser type*
`127.0.0.1:8000`

### By Abogo Lincoln
