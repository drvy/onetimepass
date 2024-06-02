<?php

namespace App\Errors;

use Exception;
use Throwable;

class AppException extends Exception
{
    private $shortCode = '';


    public function __construct(string $short, string $message, int $code = 0, Throwable $previous = null)
    {
        $this->shortCode = $short;
        parent::__construct($message, $code, $previous);
    }


    /**
     * Return the short code for this exception
     *
     * @return string
     */
    public function getShortCode(): string
    {
        return $this->shortCode;
    }
}
