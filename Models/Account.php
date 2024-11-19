<?php

namespace Models;

use Core\Database;

class Account {
    public int $id;
    public int $accountNo;
    public int $amount;
    public string $name;

    public function __construct(int $accountNo, int $amount, string $name) {
        $this->accountNo = $accountNo;
        $this->amount = $amount;
        $this->name = $name;
    }

    /**
     * Get full account instance from a given account number
     * @param int $accountNo Account number
     * @param Database $db Database
     * @return Account|false Account instance or false if the account does not exist
     */
    public static function getAccount(int $accountNo, Database $db): Account|false {
        $data = $db->query("SELECT * FROM account WHERE account_no = ?", [
            $accountNo
        ])->fetch();

        if (!$data)
            return false;

        $account = new Account($data["account_no"], $data["amount"], $data["name"]);
        $account->id = $data["id"];
        return $account;
    }
}