<?php

namespace Middleware;

use Core\Database;
use Core\Details\HttpContext;
use Core\HttpException;
use Core\HttpStatusCode;
use Core\Middleware;
use Core\MiddlewareFunc;
use Models\Token;
use Models\User;

class AuthMiddleware implements Middleware {
    public function __construct(public Database $db) {
    }

    public function handle(HttpContext $context, MiddlewareFunc $middlewareFunc) {
        $token = $context->request->header('Authorization') ?? throw new HttpException(HttpStatusCode::UNAUTHORIZED);
        $userId = Token::check($token, $context->request->ip(), $this->db);
        $user = $this->db->query('SELECT * FROM "user" u WHERE u.id = ?', [
            $userId
        ])->fetch();

        $userModel = new User($user['id'], $user['email']);
        $middlewareFunc->makeAvailable($userModel);
    }
}