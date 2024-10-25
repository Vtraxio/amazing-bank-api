<?php

namespace Core;

use ArrayAccess;
use Exception;

readonly class Request implements ArrayAccess {
    public function __construct(private mixed $params) {}

    public function qs(): array {
        $final = [];
        parse_str($_SERVER['QUERY_STRING'] ?? "", $final);
        return $final;
    }

    public function params() {
        return $this->params;
    }

    public function param($param) {
        return $this->params[$param] ?? null;
    }

    public function body() {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function headers(): array {
        // https://stackoverflow.com/questions/541430/how-do-i-read-any-request-header-in-php
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (!str_starts_with($key, 'HTTP_')) {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }

    public function header($header) {
        return $this->headers()[$header] ?? null;
    }

    public function ip() {
        return $_SERVER['REMOTE_ADDR'];
    }

    // Array Access
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