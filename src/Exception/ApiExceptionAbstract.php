<?php

declare(strict_types=1);

namespace App\Exception;

abstract class ApiExceptionAbstract extends \Exception implements ApiExceptionInterface
{
    protected $statusCode;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function toArray(): array
    {
        return array(
            'error' => array(
                'status' => $this->getStatusCode(),
                'message' => $this->getMessage(),
            ),
        );
    }
}
