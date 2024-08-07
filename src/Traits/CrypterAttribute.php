<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Traits;

use AdityaDarma\LaravelDatabaseCryptable\Builders\CryptableEloquentBuilderMariaDB;
use AdityaDarma\LaravelDatabaseCryptable\Builders\CryptableEloquentBuilderMySql;
use AdityaDarma\LaravelDatabaseCryptable\Builders\CryptableEloquentBuilderPostgreSQL;
use AdityaDarma\LaravelDatabaseCryptable\Exception\UnsupportedDriverException;
use AdityaDarma\LaravelDatabaseCryptable\Facades\Crypt;
use Exception;
use RuntimeException;

trait CrypterAttribute
{
    /**
     * Extend eloquent builder
     *
     * @param $query
     * @return void
     */
    public function newEloquentBuilder($query)
    {
        switch ($this->getDatabaseDriver()){
            case 'mysql':
                return new CryptableEloquentBuilderMySql($query);
            case 'mariadb':
                return new CryptableEloquentBuilderMariaDB($query);
            case 'pgsql':
                return new CryptableEloquentBuilderPostgreSQL($query);
            default:
                throw new RuntimeException("Unknown driver encryption");
       }
    }

    /**
     * Get driver database
     *
     * @return string
     */
    public function getDatabaseDriver(): string
    {
        $connectionName = $this->getConnectionName();

        if (is_null($connectionName)) {
            $connectionName = config('database.default');
        }

        $connectionConfig = config("database.connections.$connectionName");

        return $connectionConfig['driver'];
    }

    /**
     * Driver support
     *
     * @return bool
     */
    public function isSupportDriver(): bool
    {
        return in_array($this->getDatabaseDriver(), config('cryptable.driver'));
    }

    /**
     * Check attribute is encryptable
     *
     * @param $key
     * @return bool
     */
    public function isEncryptable(string $key): bool
    {
        return in_array($key, $this->encryptable);
    }

    /**
     * Get list attribute encryptable
     *
     * @return array
     */
    public function getEncryptableAttributes(): array
    {
        return $this->encryptable;
    }

    /**
     * Get attribute
     *
     * @param $key
     * @return mixed
     */
    public function getAttribute($key): mixed
    {
        $value = parent::getAttribute($key);
        if ($this->isEncryptable($key) && (!is_null($value) && $value != ''))
        {
            if (! $this->isSupportDriver()) {
                throw new UnsupportedDriverException();
            }

            try {
                $value = Crypt::decrypt($value);
            } catch (Exception $e) {
                echo $e. PHP_EOL;
            }
        }
        return $value;
    }

    /**
     * Set attribute
     *
     * @param $key
     * @param $value
     * @return string
     */
    public function setAttribute($key, $value): string
    {
        if ($this->isEncryptable($key) && (!is_null($value) && $value != ''))
        {
            if (! $this->isSupportDriver()) {
                throw new UnsupportedDriverException();
            }

            try {
                $value = Crypt::encrypt($value);
            } catch (Exception $e) {}
        }
        return parent::setAttribute($key, $value);
    }

    /**
     * Set data encryptable
     *
     * @return mixed
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        if ($attributes) {
            foreach ($attributes as $key => $value)
            {
                if ($this->isEncryptable($key) && (!is_null($value)) && $value != '')
                {
                    if (! $this->isSupportDriver()) {
                        throw new UnsupportedDriverException();
                    }

                    $attributes[$key] = $value;
                    try {
                        $attributes[$key] = Crypt::decrypt($value);
                    } catch (Exception $e) {}
                }
            }
        }
        return $attributes;
    }
}
