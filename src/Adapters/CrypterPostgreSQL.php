<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Adapters;

use AdityaDarma\LaravelDatabaseCryptable\Contracts\Cryptable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrypterPostgreSQL implements Cryptable
{
    protected string $key;
    protected string $cipher;
    protected string $iv;

    public function __construct($key, $cipher = 'aes-256-cbc')
    {
        if (Str::startsWith($key, $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        $this->key = substr(hash('sha256', $key), 0, openssl_cipher_iv_length(strtolower($cipher)));
        $this->cipher = $cipher;
        $this->iv = "";
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
        return openssl_encrypt(
            $value,
            strtolower($this->cipher),
            $this->key,
            0,
            $this->iv
        );
    }

    /**
     * Decrypt data
     *
     * @param string $value
     * @return mixed
     */
    public function decrypt(string $value): mixed
    {
        return openssl_decrypt(
            $value,
            strtolower($this->cipher),
            $this->key,
            0,
            $this->iv
        );
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
    public function uniqueEncryptableValidation(mixed $data, string $table, string $field, mixed $value = null): bool
    {
        $data = DB::table($table)
                ->whereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$field}`), '{$this->key}') USING utf8mb4) = '{$data}' ")
                ->when(isset($value), function(Builder $query) use ($value) {
                    $query->where('id','!=', $value);
                })
                ->first();

        return $data ? false : true;
    }
}
