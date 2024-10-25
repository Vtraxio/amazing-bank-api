<?php

namespace Controllers;

use Core\Database;
use Core\Request;
use Models\Token;
use Models\User;

class AuthenticationController {
    public function __construct(public Database $db) {
    }

    public function login(Request $request): array {
        $user = User::login($request->body()['login'], $request->body()['password'], $this->db);
        return [
            'token' => Token::new($request->ip(), $user, $this->db)
        ];
    }
}