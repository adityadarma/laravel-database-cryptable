<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Builders;

use AdityaDarma\LaravelDatabaseCryptable\Contracts\OrderInterface;
use AdityaDarma\LaravelDatabaseCryptable\Contracts\WhereInterface;
use AdityaDarma\LaravelDatabaseCryptable\Facades\Crypt;
use Illuminate\Database\Eloquent\Builder;

class CryptableEloquentBuilderPostgreSQL extends Builder implements WhereInterface, OrderInterface
{
    /**
     * @param string $param1
     * @param mixed $param2
     * @param mixed $param3
     * @return Builder
     */
    public function whereEncrypted(string $param1, mixed $param2, mixed $param3 = null): Builder
    {
        $field     = $param1;
        $operation = isset($param3) ? $param2 : '=';
        $value     = isset($param3) ? $param3 : $param2;

        $key = Crypt::getKey();

        return self::whereRaw("convert_from(pgp_sym_decrypt(decode({$field},'base64')::bytea , '{$key}')::bytea, 'UTF-8') {$operation} ? ", [$value]);
    }

    /**
     * @param string $param1
     * @param mixed $param2
     * @param mixed $param3
     * @return Builder
     */
    public function orWhereEncrypted(string $param1, mixed $param2, mixed $param3 = null): Builder
    {
        $field     = $param1;
        $operation = isset($param3) ? $param2 : '=';
        $value     = isset($param3) ? $param3 : $param2;

        $key = Crypt::getKey();

        return self::orWhereRaw("convert_from(pgp_sym_decrypt(decode({$field},'base64')::bytea , '{$key}')::bytea, 'UTF-8') {$operation} ? ", [$value]);
    }

    /**
     * @param string $field
     * @param string $direction
     * @return Builder
     */
    public function orderByEncrypted(string $field, string $direction = 'asc'): Builder
    {
        $key = Crypt::getKey();

        return self::orderByRaw("lower(convert_from(pgp_sym_decrypt(decode({$field},'base64')::bytea , '{$key}')::bytea, 'UTF-8')) {$direction}");
    }
}
