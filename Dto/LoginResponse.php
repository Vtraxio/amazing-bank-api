<?php

namespace Dto;

readonly class LoginResponse {
    public string $token;

    public function __construct(string $token) {
        $this->token = $token;
    }
}