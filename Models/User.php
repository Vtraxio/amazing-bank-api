<?php
namespace Models;

use Core\Database;
use Core\HttpException;
use Core\HttpStatusCode;

class User {
    /**
     * @throws HttpException
     */
    static function login(string $login, string $password, Database $db): mixed {
        $data = $db->query('SELECT id, password_hash from "user" WHERE email = ?', [
            $login
        ])->fetch();

        if (!$data)
            throw new HttpException(HttpStatusCode::UNAUTHORIZED);

        if (password_verify($password, $data['password_hash'])) {
            return $data['id'];
        }
        throw new HttpException(HttpStatusCode::UNAUTHORIZED);
    }
}