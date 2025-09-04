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
        $this->call([
            AdminSeeder::class,
            PortfolioCategorySeeder::class,
            PortfolioSeeder::class,
            CompanyInfoSeeder::class,
            NewsSeeder::class,
            CareersSeeder::class,
            TeamSeeder::class,
        ]);
    }
}
