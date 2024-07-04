<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Contracts;

use Illuminate\Contracts\Database\Eloquent\Builder;

interface OrderInterface
{
    public function orderByEncrypted(string $field, string $direction = 'asc'): Builder;
}
