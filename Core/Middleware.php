<?php

namespace Core;

use Core\Details\HttpContext;

abstract class Middleware {
    public array $available = [];

    abstract public function handle(HttpContext $context);

    function makeAvailable(mixed $value): void {
        $this->available[] = $value;
    }
}
