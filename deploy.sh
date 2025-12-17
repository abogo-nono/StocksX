#!/bin/bash

set -e

echo "|=======================================================|"
echo "|                                                       |"
echo "|                 StocksX Deployment script v1.0.1      |"
echo "|                                                       |"
echo "|                                      By ABOGO Lincoln |"
echo "|=======================================================|"

# checking for requirement
echo "||------------------------------------------------------------------"
echo "||====== Requirement dependencies"
echo "||------------------------------------------------------------------"
echo "Checking PHP extensions..."
required_extensions=("pdo_mysql" "mbstring" "tokenizer" "xml" "ctype" "json" "bcmath" "openssl" "curl" "fileinfo" "gd" "intl")
for extension in "${required_extensions[@]}"; do
    if php -m | grep -q "$extension"; then
        echo "$extension is installed."
    else
        echo "$extension is not installed. Installing..."
        sudo apt-get install -y php-"$extension"
    fi
done
echo
echo "[Your php version]"
php --version
echo "[Your mysql version]"
mysql --version
echo "[Your node version]"
node --version
echo "[Your npm version]"
npm --version
echo "[Your composer version]"
composer --version
echo

# cloning the repo
echo "||------------------------------------------------------------------"
echo "||====== Downloading of the stocksx base"
echo "||------------------------------------------------------------------"
echo
git clone https://github.com/abogo-nono/StocksX.git
echo
echo "||------------------------------------------------------------------"
echo "||====== Download finished!"
echo "||------------------------------------------------------------------"

# changing the current dir
echo "||------------------------------------------------------------------"
echo "||====== Changing current directory to StocksX"
echo "||------------------------------------------------------------------"
cd StocksX

# installing php and js dependencies
echo "||------------------------------------------------------------------"
echo "||====== Installing dependencies"
echo "||------------------------------------------------------------------"
composer install
npm install

# Configure the environment
echo "||------------------------------------------------------------------"
echo "||====== Configuring environment variables"
echo "||------------------------------------------------------------------"
cp .env.example .env

# generate app key
echo "||------------------------------------------------------------------"
echo "||====== generate app key"
echo "||------------------------------------------------------------------"
php artisan key:generate

# migrate, seed database and generate roles
echo "||------------------------------------------------------------------"
echo "||====== Migrate, Seed database and generate roles and permissions"
echo "||------------------------------------------------------------------"
php artisan migrate --seed

# create the super admin user
php artisan make:filament-user --name=Admin --email=admin@example.com --password=12345678
php artisan shield:super-admin --user=1

echo "||------------------------------------------------------------------"
echo "||====== Here are your credentials"
echo "||------------------------------------------------------------------"
echo "Username: admin@example.com"
echo "Password: 12345678"
echo
echo "By default this user will be the superadmin on the app"

# linking the storage folder
echo "||------------------------------------------------------------------"
echo "||====== linking the storage folder"
echo "||------------------------------------------------------------------"
php artisan storage:link

# generating policies for all resources and first user role
echo "||------------------------------------------------------------------"
echo "||====== Generate roles"
echo "||------------------------------------------------------------------"
php artisan shield:install --fresh

echo "||------------------------------------------------------------------"
echo "||====== Serving the app"
echo "||------------------------------------------------------------------"
php artisan serve

# By Abogo Lincoln
