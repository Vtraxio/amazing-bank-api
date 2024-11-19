<?php

namespace Core\Details;

readonly class HttpRequest {
    public function __construct(private mixed $params) {}

    /**
     * Get the request query string
     * @return array The query string as an associative array
     */
    public function qs(): array {
        $final = [];
        parse_str($_SERVER['QUERY_STRING'] ?? "", $final);
        return $final;
    }

    /**
     * Get the request parameters
     * @return mixed The request parameters
     */
    public function params(): mixed {
        return $this->params;
    }

    /**
     * Get a specific request parameter
     * @param string $param The parameter to get
     * @return mixed The parameter value
     */
    public function param(string $param): mixed {
        return $this->params[$param] ?? null;
    }

    /**
     * Get the request body from json
     * @return mixed The request body
     */
    public function body(): mixed {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Get the request headers
     * @return array The request headers
     */
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

    /**
     * Get a specific request header
     * @param string $header The header to get
     * @return string|null The header value
     */
    public function header(string $header): ?string {
        return $this->headers()[$header] ?? null;
    }

    /**
     * Get the user ip
     * @return string The user ip
     */
    public function ip(): string {
        return $_SERVER['REMOTE_ADDR'];
    }
}