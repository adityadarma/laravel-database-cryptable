<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getKey()
 * @method static string encrypt(mixed $value)
 * @method static mixed decrypt(string $value)
 * @method static bool isEncrypted(mixed $value)
 *
 * @see \AdityaDarma\LaravelDatabaseCryptable\Crypter
 */
class Crypt extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'cryptable';
    }
}
