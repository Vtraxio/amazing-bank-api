<?php

namespace Core;

use Throwable;

/**
 * Represents an HTTP exception that can be thrown from anywhere,
 * it is caught by the index file and transformed into a response.
 */
class HttpException extends \Exception {
    public function __construct(public readonly HttpStatusCode $statusCode = HttpStatusCode::NOT_FOUND, public readonly mixed $json = null) {
        parent::__construct($this->statusCode->description(), $this->statusCode->value);
    }
}