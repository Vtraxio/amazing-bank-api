<?php

namespace Models;

use Core\Database;

class Token {
    static function new(string $ip, int $userId, Database $db) {
        $hash = hash('sha256', $ip . $userId . time());
        $db->query("INSERT INTO \"token\" (token, ip, userId) VALUES (?, ?, ?)", [
            $hash, $ip, $userId
        ]);

        return $hash;
    }

    static function check(string $token, string $ip, Database $db) {
        $data = $db->query("SELECT * FROM token WHERE token = ? AND ip = ?", [
            $token, $ip
        ])->fetch();

        if (!$data) {
            return false;
        }
        return $data['userid'];
    }
}