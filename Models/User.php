<?php

namespace Models;

use Core\App;
use Core\Database;
use Core\HttpException;
use Core\HttpStatusCode;
use Exception;

class User {
    public function __construct(private int $id, public string $email) {
    }

    /**
     * Get the account of the user
     * @return Account Account
     * @throws Exception System error
     */
    public function account(): Account {
        $db = App::container()->resolve(Database::class);

        $account = $db->query('SELECT * FROM "account" a JOIN "user" u on u.id = a.user_id WHERE u.id = ?', [
            $this->id
        ])->fetch();

        $acc = new Account($account['account_no'], $account['amount'], $account['name']);
        $acc->id = $account['id'];
        return $acc;
    }

    /**
     * Login the user, return instance
     * @param string $login Email
     * @param string $password Password
     * @param Database $db Database
     * @return mixed User ID
     * @throws HttpException If the user does not exist or the password is incorrect
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