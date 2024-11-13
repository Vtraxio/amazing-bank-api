<?php

namespace Controllers;

use Core\HttpException;
use Core\HttpStatusCode;
use Models\Transfer;
use Core\Database;
use Core\Details\HttpRequest;
use Models\User;

class MoneyController {
    public function __construct(public Database $db) {
    }

    public function transfer(HttpRequest $request, User $user): void {
        $receiver = $request->body()['target'];
        $amount = $request->body()['amount'];
        $senderAccount = $user->account();

        $this->db->con->beginTransaction();

        $this->db->query("UPDATE account SET amount = amount - ? WHERE account_no = ?", [
            $amount,
            $senderAccount->accountNo,
        ]);
        $this->db->query("UPDATE account SET amount = amount + ? WHERE account_no = ?", [
            $amount,
            $receiver,
        ]);
        Transfer::new($user->account()->accountNo, $receiver, $amount, $this->db);

        $this->db->con->commit();
    }
}