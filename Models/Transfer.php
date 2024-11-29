<?php

namespace Models;

use Core\Database;

/**
 * Represents a single transfer of money from one account to another
 */
class Transfer {
    /**
     * Create a new transfer
     * @param int $source Source account ID
     * @param int $target Target account ID
     * @param int $amount Amount
     * @param string $title Title of the transfer
     * @param Database $db Database
     * @return void
     */
    static function new(int $source, int $target, int $amount, string $title, Database $db): void {
        $db->query("INSERT INTO transfer(source_id, target_id, amount, title) VALUES (?, ?, ?, ?)", [
            $source,
            $target,
            $amount,
            $title
        ]);
    }
}