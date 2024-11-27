<?php

namespace Dto;

readonly class LoginRequest {
    public string $login;
    public string $password;

    public function __construct(array $json) {
        $this->login = $json['login'];
        $this->password = $json['password'];
    }
}