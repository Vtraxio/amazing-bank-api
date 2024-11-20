<?php

namespace Models;

use Core\Database;

/**
 * Represents a token that is used to authenticate a user
 */
class Token {
    /**
     * Create a new token
     * @param string $ip IP address
     * @param int $userId User ID
     * @param Database $db Database
     * @return string Token
     */
    static function new(string $ip, int $userId, Database $db): string {
        $hash = hash('sha256', $ip . $userId . time());
        $db->query("INSERT INTO \"token\" (token, ip, user_id) VALUES (?, ?, ?)", [
            $hash, $ip, $userId
        ]);

        return $hash;
    }

    /**
     * Check if the token is valid
     * @param string $token Token
     * @param string $ip IP address
     * @param Database $db Database
     * @return false|mixed User ID or false if the token is invalid
     */
    static function check(string $token, string $ip, Database $db): mixed {
        $data = $db->query("SELECT * FROM token WHERE token = ? AND ip = ?", [
            $token, $ip
        ])->fetch();

        if (!$data) {
            return false;
        }
        return $data['user_id'];
    }
}