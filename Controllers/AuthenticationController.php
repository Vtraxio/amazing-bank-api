<?php

namespace Controllers;

use Core\Database;
use Core\Details\HttpRequest;
use Models\Token;
use Models\User;

class AuthenticationController {
    public function __construct(public Database $db) {
    }

    public function login(HttpRequest $request): array {
        $user = User::login($request->body()['login'], $request->body()['password'], $this->db);
        return [
            'token' => Token::new($request->ip(), $user, $this->db)
        ];
    }
}