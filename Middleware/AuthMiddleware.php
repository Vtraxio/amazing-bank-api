<?php

namespace Middleware;

use Core\Database;
use Core\Details\HttpContext;
use Core\HttpException;
use Core\HttpStatusCode;
use Core\Middleware;
use Models\Token;
use Models\User;

/**
 * Middleware to check if the user is authenticated
 * makes a {@link User} object available for reflection
 * throws a http exception if the user is not authenticated
 */
class AuthMiddleware extends Middleware {
    public function __construct(public Database $db) {
    }

    public function handle(HttpContext $context): void {
        $token = $context->request->header('Authorization') ?? throw new HttpException(HttpStatusCode::UNAUTHORIZED);
        $userId = Token::check($token, $context->request->ip(), $this->db);
        $user = $this->db->query('SELECT * FROM "user" u WHERE u.id = ?', [
            $userId
        ])->fetch();

        $userModel = new User($user['id'], $user['email']);
        $this->makeAvailable($userModel);
    }
}