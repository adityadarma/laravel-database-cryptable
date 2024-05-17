<?php

namespace AdityaDarma\LaravelDatabaseCryptable;

use Exception;
use Illuminate\Support\Str;

class Crypter
{
    protected string $key;
    protected string $cipher;
    protected string $iv;

    public function __construct($key, $cipher = 'aes-128-ecb')
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
}
