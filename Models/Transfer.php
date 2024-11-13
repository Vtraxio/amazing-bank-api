<?php

namespace Models;

use Core\Database;

class Transfer {
    static function new(int $source, int $target, int $amount, Database $db): void {
        $db->query("INSERT INTO transfer(source_id, target_id, amount) VALUES (?, ?, ?)", [
            $source,
            $target,
            $amount
        ]);
    }
}