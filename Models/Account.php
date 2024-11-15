<?php

namespace Models;

use Core\Database;

class Account {
    public int $accountNo;
    public int $amount;
    public string $name;

    public function __construct(int $accountNo, int $amount, string $name) {
        $this->accountNo = $accountNo;
        $this->amount = $amount;
        $this->name = $name;
    }

    public static function getAccount(int $accountNo, Database $db): Account|false {
        $data = $db->query("SELECT * FROM account WHERE account_no = ?", [
            $accountNo
        ])->fetch();

        if (!$data)
            return false;
        return new Account($data["account_no"], $data["amount"], $data["name"]);
    }
}