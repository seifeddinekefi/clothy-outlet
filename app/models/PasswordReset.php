<?php

/**
 * app/models/PasswordReset.php
 * Password reset token store.
 */
class PasswordReset extends Model
{
    protected string $table = 'password_resets';
    protected string $primaryKey = 'id';

    private static bool $tableEnsured = false;

    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        if (self::$tableEnsured) {
            return;
        }

        $this->db->statement(
            "CREATE TABLE IF NOT EXISTS `password_resets` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `email` VARCHAR(180) NOT NULL,
                `token_hash` CHAR(64) NOT NULL,
                `expires_at` DATETIME NOT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_password_resets_token_hash` (`token_hash`),
                KEY `idx_password_resets_email` (`email`),
                KEY `idx_password_resets_expires` (`expires_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        self::$tableEnsured = true;
    }

    public function purgeForEmail(string $email): bool
    {
        return $this->delete('`email` = :email', [':email' => $email]);
    }

    public function createToken(string $email): string|false
    {
        $token = bin2hex(random_bytes(32));
        $ok = $this->insert([
            'email'       => $email,
            'token_hash'  => hash('sha256', $token),
            'expires_at'  => date('Y-m-d H:i:s', time() + 3600),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        return $ok ? $token : false;
    }

    public function findValidByToken(string $token): mixed
    {
        return $this->db->selectOne(
            "SELECT *
               FROM `password_resets`
              WHERE `token_hash` = :token_hash
                AND `expires_at` >= NOW()
              ORDER BY `id` DESC
              LIMIT 1",
            [':token_hash' => hash('sha256', $token)]
        );
    }

    public function consumeToken(string $token): bool
    {
        return $this->delete('`token_hash` = :token_hash', [
            ':token_hash' => hash('sha256', $token),
        ]);
    }
}
