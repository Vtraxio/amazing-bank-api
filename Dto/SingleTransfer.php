<?php

namespace Dto;

readonly class SingleTransfer {
    public string $targetName;
    public int $targetId;
    public int $amount;
    public string $title;
    public string $date;

    /**
     * @param string $targetName
     * @param int $targetId
     * @param int $amount
     * @param string $title
     * @param string $date
     */
    public function __construct(string $targetName, int $targetId, int $amount, string $title, string $date) {
        $this->targetName = $targetName;
        $this->targetId = $targetId;
        $this->amount = $amount;
        $this->title = $title;
        $this->date = $date;
    }
}