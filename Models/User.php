<?php

namespace Models;

use Core\App;
use Core\Database;
use Core\HttpException;
use Core\HttpStatusCode;

class User {
    public function __construct(private int $id, public string $email) {
    }

    public function account(): Account {
        $db = App::container()->resolve(Database::class);

        $account = $db->query('SELECT * FROM "account" a JOIN "user" u on u.id = a.user_id WHERE u.id = ?', [
            $this->id
        ])->fetch();

        return new Account($account['account_no'], $account['amount'], $account['name']);
    }

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