<?php

/**
 * app/models/Subscriber.php
 * Newsletter subscriber list.
 */

class Subscriber extends Model
{
    protected string $table      = 'subscribers';
    protected string $primaryKey = 'id';

    public function findAll(): array
    {
        return $this->db->select(
            "SELECT * FROM `subscribers` ORDER BY `created_at` DESC"
        );
    }

    public function emailExists(string $email): bool
    {
        $row = $this->db->selectOne(
            "SELECT COUNT(*) AS cnt FROM `subscribers` WHERE `email` = :email",
            [':email' => $email]
        );
        return (int) ($row->cnt ?? 0) > 0;
    }

    public function subscribe(string $email): string|false
    {
        return $this->insert(['email' => $email]);
    }

    public function countActive(): int
    {
        $row = $this->db->selectOne(
            "SELECT COUNT(*) AS cnt FROM `subscribers` WHERE `is_active` = 1"
        );
        return (int) ($row->cnt ?? 0);
    }
}
