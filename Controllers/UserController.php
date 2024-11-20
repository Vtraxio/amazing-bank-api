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

/**
 * Manages user data
 */
class UserController {
    public function __construct(public Database $db) {
    }

    /**
     * Show data from a specified user
     * @param HttpParams $params HTTP parameters
     * @return Account The account of the user
     * @throws HttpException If the user does not exist
     */
    public function showAny(HttpParams $params): Account {
        return Account::getAccount($params['id'], $this->db) ?? throw new HttpException();
    }

    /**
     * Show data from the current user
     * @param User $user Current logged in user
     * @return Account The account of the user
     */
    public function showMe(User $user): Account {
        return $user->account();
    }
}