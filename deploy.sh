#!/bin/bash

echo "|=======================================================|"
echo "|                                                       |"
echo "|                 StocksX Deployement script v1.0.0     |"
echo "|                                                       |"
echo "|                                      By Abogo Lincoln |"
echo "|=======================================================|"

# checking for requirement
echo "||------------------------------------------------------------------"
echo "||====== Requirement dependencies"
echo "||------------------------------------------------------------------"
echo
echo "[Your php version]"
`php --version`
echo "[Your mysql version]"
`mysql --version`
echo "[Your node version]"
`node --version`
echo "[Your npm version]"
`npm --version`
echo "[Your composer version]"
`composer --version`
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
mv .env.example .env

# generate app key
echo "||------------------------------------------------------------------"
echo "||====== generate app key"
echo "||------------------------------------------------------------------"
php artisan key:generate

# migrate, seed database and generate roles
echo "||------------------------------------------------------------------"
echo "||====== Migrate, Seed database and generatre roles and permission"
echo "||------------------------------------------------------------------"
php artisan migrate --seed

# create a the super admain user
php artisan make:filament-user --name=Admin --email=admin@example.com --password=12345
php artisan shield:super-admin --user=1

echo "||------------------------------------------------------------------"
echo "||====== Here are your credentials"
echo "||------------------------------------------------------------------"
echo "Username: admin@example.com"
echo "Password: 12345"
echo
echo "By default this user will be the superadmin on the app"

# linking the storage folder
echo "||------------------------------------------------------------------"
echo "||====== linking the storage folder"
echo "||------------------------------------------------------------------"
php artisan storage:link

# generating politicise for all resources and first user role
echo "||------------------------------------------------------------------"
echo "||====== Generate roles"
echo "||------------------------------------------------------------------"
php artisan shield:install --fresh

echo "||------------------------------------------------------------------"
echo "||====== Serving the app"
echo "||------------------------------------------------------------------"
echo "Username: admin@example.com"
echo "Password: 12345"
echo
echo "By default this user will be the superadmin on the app"
php artisan serve

# By Abogo Lincoln
