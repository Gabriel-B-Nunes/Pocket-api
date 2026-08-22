<?php
Class HashService {
    public static function hashPasswordWithPepper(string $password): string {
        $pepperedPassword = $password . getenv("HASH_PEPPER");

        return password_hash($pepperedPassword, PASSWORD_DEFAULT);
    }

    public static function verifyPassword(string $password, string $hash) {
        $pepperedPassword = $password . getenv("HASH_PEPPER");

        return password_verify($pepperedPassword, $hash);
    }
}