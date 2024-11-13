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
        $senderAccountNo = $user->account()->accountNo;

        if ($amount < 0) {
            throw new HttpException(HttpStatusCode::BAD_REQUEST, ["message" => "Wypierdalaj"]);
        }

        $account = $this->db->query("SELECT * FROM account WHERE account_no = ?", [$senderAccountNo])->fetch();

        if ($account['amount'] < $amount) {
            throw new HttpException(HttpStatusCode::BAD_REQUEST, ["message" => "Nie masz tyle kasy kolego"]);
        }

        $this->db->con->beginTransaction();

        $this->db->query("UPDATE account SET amount = amount - ? WHERE account_no = ?", [
            $amount,
            $senderAccountNo,
        ]);
        $this->db->query("UPDATE account SET amount = amount + ? WHERE account_no = ?", [
            $amount,
            $receiver,
        ]);
        Transfer::new($user->account()->accountNo, $receiver, $amount, $this->db);

        $this->db->con->commit();
    }
}