#!/bin/bash

echo "Downloading of the stockx base"
git clone https://github.com/abogo-nono/stockx.git
echo "Download finished!"

cd stockx

echo "Installation of dependences"
composer install
npm install



echo "Seeding database"
php artisan db:seed

echo "Serving app on port 8000"
php artisan serve


