<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Запускаем сидеры для создания тестовых данных
        $this->call([
            AdminSeeder::class,
            PortfolioCategorySeeder::class, // Создаем категории перед проектами
            PortfolioSeeder::class,
            NewsSeeder::class,
        ]);
    }
}
