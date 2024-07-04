<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Adapters;

use AdityaDarma\LaravelDatabaseCryptable\Contracts\CryptableInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrypterPostgreSQL implements CryptableInterface
{
    protected string $key;

    public function __construct($key)
    {
        if (Str::startsWith($key, $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        $this->key = substr(hash('sha256', $key), 0, 16);
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Encrypt data
     *
     * @param mixed $value
     * @return string
     */
    public function encrypt(mixed $value): string
    {
        return DB::connection()->select(
            "select encode(pgp_sym_encrypt(?, ?)::bytea, 'base64') AS text",
            [$value, $this->key]
        )[0]->text;
    }

    /**
     * Decrypt data
     *
     * @param string $value
     * @return mixed
     */
    public function decrypt(string $value): mixed
    {
        return DB::connection()->select(
            "select convert_from(pgp_sym_decrypt(decode(?,'base64')::bytea , ?)::bytea, 'UTF-8') AS text",
            [$value, $this->key]
        )[0]->text;
    }

    /**
     * Check data is ecrypted
     *
     * @param mixed $value
     * @return bool
     */
    public function isEncrypted(mixed $value): bool
    {
        try {
            return $this->decrypt($value) !== false ? true : false;
        } catch(Exception $e){
            return false;
        }
    }

    /**
     * Validate unique data encryption
     *
     * @param mixed $data
     * @param string $table
     * @param string $field
     * @param mixed $value
     * @return bool
     */
    public function uniqueEncryptableValidation(mixed $data, string $table, string $field, mixed $value): bool
    {
        $data = DB::table($table)
                ->whereRaw("convert_from(pgp_sym_decrypt(decode({$field},'base64')::bytea , '{$this->key}')::bytea, 'UTF-8') = '{$data}' ")
                ->when(isset($value), function(Builder $query) use ($value) {
                    $query->where('id','!=', $value);
                })
                ->first();

        return $data ? false : true;
    }
}
