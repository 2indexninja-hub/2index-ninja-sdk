<?php

namespace TwoIndexNinja\Sdk\Exception;

class ApiException extends \Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param array<string, mixed> $errors
     * @param array<string> $invalidLinks
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        public readonly array $errors = [],
        public readonly array $invalidLinks = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
