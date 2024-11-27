<?php

namespace Controllers;

use Core\Database;
use Core\Details\HttpRequest;
use Core\HttpException;
use Core\Json;
use Dto\LoginRequest;
use Models\Token;
use Models\User;

/**
 * Manages user authentication and maybe registration
 */
class AuthenticationController {
    public function __construct(public Database $db) {
    }

    /**
     * Login a user
     * @param HttpRequest $req
     * @param LoginRequest $data
     * @return array Token
     * @throws HttpException
     */
    public function login(HttpRequest $req, #[Json] LoginRequest $data): array {
        $user = User::login($data->login, $data->password, $this->db);
        return [
            'token' => Token::new($req->ip(), $user, $this->db)
        ];
    }
}