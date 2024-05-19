<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Contracts;

interface Cryptable
{
    public function getKey(): string;

    public function encrypt(mixed $value): string;

    public function decrypt(string $value): mixed;

    public function isEncrypted(mixed $value): bool;

    public function uniqueEncryptableValidation(mixed $data, string $table, string $field, mixed $value = null): bool;
}
