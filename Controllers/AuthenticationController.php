<?php

namespace Controllers;

use Core\Database;
use Core\Details\HttpRequest;
use Core\HttpException;
use Models\Token;
use Models\User;

class AuthenticationController {
    public function __construct(public Database $db) {
    }

    /**
     * Login a user
     * @param HttpRequest $request Request
     * @return array Token
     * @throws HttpException
     */
    public function login(HttpRequest $request): array {
        $user = User::login($request->body()['login'], $request->body()['password'], $this->db);
        return [
            'token' => Token::new($request->ip(), $user, $this->db)
        ];
    }
}