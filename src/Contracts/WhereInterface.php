<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Contracts;

use Illuminate\Contracts\Database\Eloquent\Builder;

interface WhereInterface
{
    public function whereEncrypted(string $param1, mixed $param2, mixed $param3 = null): Builder;

    public function orWhereEncrypted(string $param1, mixed $param2, mixed $param3 = null): Builder;
}
