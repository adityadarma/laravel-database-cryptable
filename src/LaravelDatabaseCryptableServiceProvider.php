<?php

namespace AdityaDarma\LaravelDatabaseCryptable;

use AdityaDarma\LaravelDatabaseCryptable\Console\Commands\DecryptAttribute;
use AdityaDarma\LaravelDatabaseCryptable\Console\Commands\EncryptAttribute;
use AdityaDarma\LaravelDatabaseCryptable\Crypter;
use AdityaDarma\LaravelDatabaseCryptable\Facades\Crypt;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class LaravelDatabaseCryptableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('cryptable', function($app) {
            $config = $app->make('config')->get('app');

            return new Crypter($config['key']);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                EncryptAttribute::class,
                DecryptAttribute::class
            ]);
        }

        Validator::extend('unique_encrypted', function ($attribute, $value, $parameters, $validator) {
            $key = Crypt::getKey();
            $data = DB::table($parameters[0])
                ->whereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$parameters[1]}`), '{$key}') USING utf8mb4) = '{$value}' ")
                ->when(isset($parameters[2]), function(Builder $query) use ($parameters) {
                    $query->where('id','!=',$parameters[2]);
                })
                ->first();

            return $data ? false : true;
        });
    }
}
