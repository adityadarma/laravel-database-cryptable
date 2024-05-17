<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Exception;

use Exception;

class UnsupportedDriverException extends Exception
{
    protected $message = 'Driver encryption cant support';
}
