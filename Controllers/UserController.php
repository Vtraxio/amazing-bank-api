<?php

namespace Controllers;

use Core\Database;
use Core\HttpException;
use Core\HttpStatusCode;
use Core\Request;
use Models\Account;
use Models\Token;

class UserController {
    public function __construct(public Database $db) {
    }

    public function showAny(Request $request): Account {
        return Account::getAccount($request['id'], $this->db) ?? throw new HttpException();
    }

    public function showMe(Request $request) {
        $token = Token::check($request->header('Authorization'), $request->ip(), $this->db) ?? throw new HttpException(HttpStatusCode::UNAUTHORIZED);
        $user = $this->db->query('SELECT * FROM "account" a JOIN "user" u ON a.user_id = u.id WHERE u.id = ?;', [
            $token
        ])->fetch();
        return [
            "accountNo" => $user['account_no'],
            "amount" => $user['amount'],
            "name" => $user['name']
        ];
    }
}