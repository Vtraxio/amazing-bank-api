<?php

namespace Dto;

readonly class TransferRequest {
    public int $target;
    public int $amount;
    public string $title;

    public function __construct(array $json) {
        $this->target = $json['target'];
        $this->amount = $json['amount'];
        $this->title = $json['title'];
    }
}