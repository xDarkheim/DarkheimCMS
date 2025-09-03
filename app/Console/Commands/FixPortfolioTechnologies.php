<?php

namespace App\Console\Commands;

use App\Models\Portfolio;
use Illuminate\Console\Command;

class FixPortfolioTechnologies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portfolio:fix-technologies {--dry-run : Show what will be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix double JSON encoded technologies in portfolio records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🔧 Checking portfolio technologies for double JSON encoding...');

        $portfolios = Portfolio::all();
        $fixedCount = 0;
        $errorCount = 0;

        foreach ($portfolios as $portfolio) {
            $rawTechnologies = $portfolio->getAttributes()['technologies'];

            // Проверяем, является ли technologies строкой (что указывает на двойное кодирование)
            if (is_string($rawTechnologies) && $this->isJsonString($rawTechnologies)) {
                try {
                    // Декодируем JSON строку обратно в массив
                    $decodedTechnologies = json_decode($rawTechnologies, true);

                    if (is_array($decodedTechnologies)) {
                        $this->line("📄 Portfolio ID {$portfolio->id}: '{$portfolio->title}'");
                        $this->line("   🔴 Current (corrupted): {$rawTechnologies}");
                        $this->line("   🟢 Will be fixed to: " . json_encode($decodedTechnologies));

                        if (!$isDryRun) {
                            // Обновляем запись напрямую в базе данных, чтобы избежать автоматического кастинга
                            \DB::table('portfolios')
                                ->where('id', $portfolio->id)
                                ->update(['technologies' => json_encode($decodedTechnologies)]);

                            $this->info("   ✅ Fixed!");
                        }

                        $fixedCount++;
                    }
                } catch (\Exception $e) {
                    $this->error("   ❌ Error fixing portfolio ID {$portfolio->id}: " . $e->getMessage());
                    $errorCount++;
                }
            }
        }

        $this->newLine();

        if ($isDryRun) {
            $this->info("📊 DRY RUN RESULTS:");
            $this->info("   Found {$fixedCount} portfolios that need fixing");
            if ($errorCount > 0) {
                $this->warn("   Found {$errorCount} portfolios with errors");
            }
            $this->info("   Run without --dry-run to apply fixes");
        } else {
            $this->info("✅ COMPLETED:");
            $this->info("   Fixed {$fixedCount} portfolios");
            if ($errorCount > 0) {
                $this->warn("   Failed to fix {$errorCount} portfolios");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Check if a string is valid JSON
     */
    private function isJsonString($string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
