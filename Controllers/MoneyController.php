<?php

namespace Controllers;

use Core\HttpException;
use Core\HttpStatusCode;
use Core\Json;
use Dto\TransfersGetResponse;
use Dto\TransferRequest;
use Models\Account;
use Models\Transfer;
use Core\Database;
use Core\Details\HttpRequest;
use Models\User;

/**
 * Manages the flow of money between accounts
 */
class MoneyController {
    public function __construct(public Database $db) {
    }

    /**
     * Transfer money from one account to another
     * @param TransferRequest $transfer
     * @param User $user Current logged in user
     * @return void
     * @throws HttpException If the amount is negative or the sender does not have enough money
     */
    public function transfer(#[Json] TransferRequest $transfer, User $user): void {
        $amount = $transfer->amount;
        $receiverAccount = Account::getAccount($transfer->target, $this->db);
        $senderAccount = $user->account();

        if (!$receiverAccount) {
            throw new HttpException(HttpStatusCode::BAD_REQUEST, ["message" => "Nie ma takiego konta"]);
        }

        if ($amount <= 0) {
            throw new HttpException(HttpStatusCode::BAD_REQUEST, ["message" => "Nie oszukuj nas tu"]);
        }

        if ($senderAccount->amount < $amount) {
            throw new HttpException(HttpStatusCode::BAD_REQUEST, ["message" => "Nie masz tyle kasy kolego"]);
        }

        $this->db->con->beginTransaction();

        $this->db->query("UPDATE account SET amount = amount - ? WHERE id = ?", [
            $amount,
            $senderAccount->id,
        ]);
        $this->db->query("UPDATE account SET amount = amount + ? WHERE id = ?", [
            $amount,
            $receiverAccount->id,
        ]);
        Transfer::new($senderAccount->id, $receiverAccount->id, $amount, $transfer->title, $this->db);

        $this->db->con->commit();
    }

    public function getTransfers(User $user): TransfersGetResponse {
        $account = $user->account();
        return $account->transfersResponseList($this->db);
    }
}