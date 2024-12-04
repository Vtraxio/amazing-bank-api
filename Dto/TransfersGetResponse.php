<?php

namespace Dto;

readonly class TransfersGetResponse {
    /**
     * @var SingleTransfer[] $sent
     */
    public array $sent;
    /**
     * @var SingleTransfer[] $received
     */
    public array $received;

    /**
     * @param SingleTransfer[] $sent
     * @param SingleTransfer[] $received
     */
    public function __construct(array $sent, array $received) {
        $this->sent = $sent;
        $this->received = $received;
    }
}

