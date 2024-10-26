<?php

namespace Core\Details;

readonly class HttpRequest {
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
}