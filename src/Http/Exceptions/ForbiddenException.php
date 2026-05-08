<?php

namespace App\Http\Exceptions;

use Yiisoft\ErrorHandler\Exception\UserException;

class ForbiddenException extends UserException
{
    public function __construct(string $message = 'Access denied')
    {
        parent::__construct($message, 403);
    }
}
