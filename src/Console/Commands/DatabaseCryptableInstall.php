<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Console\Commands;

use AdityaDarma\LaravelDatabaseCryptable\LaravelDatabaseCryptableServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DatabaseCryptableInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database-cryptable:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will publish config file for database cryptable';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        //config
        if (File::exists(config_path('cryptable.php'))) {
            $confirm = $this->confirm("cryptable.php config file already exist. Do you want to overwrite?");
            if ($confirm) {
                $this->publishConfig();
                $this->info("config overwrite finished");
            }
            else {
                $this->info("skipped config publish");
            }
        }
        else {
            $this->publishConfig();
            $this->info("config published");
        }
    }

    private function publishConfig(): void
    {
        $this->call('vendor:publish', [
            '--provider' => LaravelDatabaseCryptableServiceProvider::class,
            '--tag'      => 'config',
            '--force'    => true
        ]);
    }
}
