<?php

namespace Models;

use Core\Database;
use Dto\TransfersGetResponse;
use Dto\SingleTransfer;

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
     * Get all transfers on this account, split into sent and received
     * @param Database $db Database Connection
     * @return TransfersGetResponse
     */
    public function transfersResponseList(Database $db): TransfersGetResponse {
        $transfers = $db->query("SELECT * FROM get_transfers_on_account(?)", [
            $this->id
        ])->fetchAll();

        // array_value is used because php is retarded and array_filter does not start keys from 0
        $sentTransfers = array_values(array_filter($transfers, fn($transfer) => $transfer["sent"] === true));
        $receivedTransfers = array_values(array_filter($transfers, fn($transfer) => $transfer["sent"] === false));

        $sentTransfers = array_map(function ($tr) {
            return new SingleTransfer($tr["account_name"], $tr["account_no"], $tr["amount"], $tr["title"], $tr["created_at"]);
        }, $sentTransfers);

        $receivedTransfers = array_map(function ($tr) {
            return new SingleTransfer($tr["account_name"], $tr["account_no"], $tr["amount"], $tr["title"], $tr["created_at"]);
        }, $receivedTransfers);

        return new TransfersGetResponse($sentTransfers, $receivedTransfers);
    }
}