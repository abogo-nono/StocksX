<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Current products table columns:\n";
$columns = Schema::getColumnListing('products');
foreach ($columns as $column) {
    echo "- $column\n";
}

echo "\nSample product data:\n";
$product = DB::table('products')->first();
if ($product) {
    foreach ($product as $key => $value) {
        echo "$key: $value\n";
    }
} else {
    echo "No products found\n";
}
