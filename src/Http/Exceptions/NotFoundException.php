<?php

namespace App\Http\Exceptions;

class NotFoundException extends \RuntimeException
{
    public function __construct(string $message = 'Resource not found')
    {
        parent::__construct($message, 404);
    }
}
