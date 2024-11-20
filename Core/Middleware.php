<?php

namespace Core;

use Core\Details\HttpContext;

/**
 * Represents a middleware that can be used to intercept requests
 */
abstract class Middleware {
    /**
     * List of values that are available for reflection
     * @var array
     */
    public array $available = [];

    /**
     * Execute the middleware
     * @param HttpContext $context Http request context
     * @return void
     */
    abstract public function handle(HttpContext $context): void;

    /**
     * Make a value available for reflection
     * @param mixed $value Value
     * @return void
     */
    function makeAvailable(mixed $value): void {
        $this->available[] = $value;
    }
}
