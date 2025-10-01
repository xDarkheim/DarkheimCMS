<?php

/**
 * Test contact messages
 * @author Dmytro Hovenko
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestContactMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-contact-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
    }
}
