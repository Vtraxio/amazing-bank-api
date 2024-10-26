<?php

namespace Controllers;

use Core\Database;
use Core\Details\HttpParams;
use Core\Details\HttpRequest;
use Core\HttpException;
use Core\HttpStatusCode;
use Models\Account;
use Models\Token;
use Models\User;

class UserController {
    public function __construct(public Database $db) {
    }

    public function showAny(HttpParams $params): Account {
        return Account::getAccount($params['id'], $this->db) ?? throw new HttpException();
    }

    public function showMe(HttpRequest $request, User $user) {
        return $user->account();
    }
}