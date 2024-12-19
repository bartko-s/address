<?php

declare(strict_types=1);

namespace App\Exception;

class BadRequestException extends ApiExceptionAbstract
{
    protected $statusCode = 400;
}
