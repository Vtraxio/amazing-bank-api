<?php

namespace Core;

class MiddlewareFunc {
    public array $available = [];

    public function makeAvailable(mixed $value): void {
        $this->available[] = $value;
    }
}