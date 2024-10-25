<?php

namespace Core;

use Throwable;

class HttpException extends \Exception {
    public function __construct(public readonly HttpStatusCode $statusCode = HttpStatusCode::NOT_FOUND, public readonly mixed $json = null) {
        parent::__construct($this->statusCode->description(), $this->statusCode->value);
    }
}