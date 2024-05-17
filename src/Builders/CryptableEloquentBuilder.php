<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Builders;

use AdityaDarma\LaravelDatabaseCryptable\Facades\Crypt;
use Illuminate\Database\Eloquent\Builder;

class CryptableEloquentBuilder extends Builder
{
    /**
     * @param $param1
     * @param $param2
     * @param $param3
     * @return Builder
     */
    public function whereEncrypted($param1, $param2, $param3 = null)
    {
        $field     = $param1;
        $operation = isset($param3) ? $param2 : '=';
        $value     = isset($param3) ? $param3 : $param2;

        $key = Crypt::getKey();

        return self::whereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$field}`), '{$key}') USING utf8mb4) {$operation} ? ", [$value]);
    }

    /**
     * @param $param1
     * @param $param2
     * @param $param3
     * @return Builder
     */
    public function orWhereEncrypted($param1, $param2, $param3 = null)
    {
        $field     = $param1;
        $operation = isset($param3) ? $param2 : '=';
        $value     = isset($param3) ? $param3 : $param2;

        $key = Crypt::getKey();

        return self::orWhereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$field}`), '{$key}') USING utf8mb4) {$operation} ? ", [$value]);
    }

    /**
     * @param $field
     * @param $direction
     * @return Builder
     */
    public function orderByEncrypted($field, $direction = 'asc') {
        $key = Crypt::getKey();

        return self::orderByRaw("CONVERT(AES_DECRYPT(FROM_bASE64(`{$field}`), '{$key}') USING utf8mb4) {$direction}");
    }
}
