<?php

namespace Dto;

readonly class TransferGetResponse {
    public string $targetName;
    public int $targetId;
    public string $amount;
    public string $title;
    public string $date;

    /**
     * @param string $targetName
     * @param int $targetId
     * @param string $amount
     * @param string $title
     * @param string $date
     */
    public function __construct(string $targetName, int $targetId, string $amount, string $title, string $date) {
        $this->targetName = $targetName;
        $this->targetId = $targetId;
        $this->amount = $amount;
        $this->title = $title;
        $this->date = $date;
    }
}