<?php

namespace AdityaDarma\LaravelDatabaseCryptable;

use AdityaDarma\LaravelDatabaseCryptable\Adapters\CrypterMariaDB;
use AdityaDarma\LaravelDatabaseCryptable\Adapters\CrypterMySql;
use AdityaDarma\LaravelDatabaseCryptable\Adapters\CrypterPostgreSQL;
use AdityaDarma\LaravelDatabaseCryptable\Console\Commands\DatabaseCryptableInstall;
use AdityaDarma\LaravelDatabaseCryptable\Console\Commands\DecryptAttribute;
use AdityaDarma\LaravelDatabaseCryptable\Console\Commands\EncryptAttribute;
use AdityaDarma\LaravelDatabaseCryptable\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class LaravelDatabaseCryptableServiceProvider extends ServiceProvider
{
    public const CONFIG_PATH = __DIR__ . '/../config/cryptable.php';

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('cryptable', function($app) {
            $config = $app->make('config')->get('cryptable');

            switch ($config['default']){
                case 'mysql':
                    return new CrypterMySql($config['key']);
                case 'mariadb':
                    return new CrypterMariaDB($config['key']);
                case 'pgsql':
                    return new CrypterPostgreSQL($config['key']);
                default:
                    throw new RuntimeException("Unknown driver encryption");
           }
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('cryptable.php')
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                DatabaseCryptableInstall::class,
                EncryptAttribute::class,
                DecryptAttribute::class,
            ]);
        }

        Validator::extend('unique_encrypted', function ($attribute, $value, $parameters, $validator) {
            return Crypt::uniqueEncryptableValidation($value, $parameters[0], $parameters[1], $parameters[2] ?? null);
        });
    }
}
