<?php

namespace Dto;

readonly class TransferRequest {
    public int $target;
    public int $amount;

    public function __construct(array $json) {
        $this->target = $json['target'];
        $this->amount = $json['amount'];
    }
}