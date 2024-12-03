<?php

namespace Models;

use Core\Database;
use Dto\TransferGetResponse;

/**
 * Represents a user account that can hold a bunch of money a {@link User} can own
 * multiple accounts
 */
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

    /**
     * @return TransferGetResponse[]
     */
    public function transfersResponseList(Database $db): array {
        $transfers = $db->query("SELECT * FROM get_transfers_on_account(?)", [
            $this->id
        ])->fetchAll();

        $responseList = [];

        foreach ($transfers as $transfer) {
            $responseList[] = new TransferGetResponse($transfer["account_name"], $transfer["account_no"], $transfer["amount"], $transfer["title"], $transfer["created_at"]);
        }

        return $responseList;
    }
}