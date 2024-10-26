<?php

namespace Core\Details;

use ArrayAccess;
use Exception;

readonly class HttpParams implements ArrayAccess {
    public function __construct(private mixed $params) {}

    public function offsetExists(mixed $offset): bool {
        return isset($this->params[$offset]);
    }

    public function offsetGet(mixed $offset): mixed {
        return $this->params[$offset] ?? null;
    }

    /**
     * @throws Exception
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        throw new Exception("Cannot modify a read-only array accessor.");
    }

    /**
     * @throws Exception
     */
    public function offsetUnset(mixed $offset): void {
        throw new Exception("Cannot unset a read-only array accessor.");
    }
}