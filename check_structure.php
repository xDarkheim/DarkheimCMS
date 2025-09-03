<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require_once __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking portfolios table structure:\n";

try {
    $columns = Schema::getColumnListing('portfolios');
    echo "Columns: " . implode(', ', $columns) . "\n";

    // Также проверим, есть ли записи в таблице категорий
    $categories = DB::table('portfolio_categories')->get();
    echo "\nCategories in database:\n";
    foreach ($categories as $category) {
        echo "- {$category->name} ({$category->slug})\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
