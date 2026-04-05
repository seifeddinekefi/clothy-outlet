<?php

/**
 * ============================================================
 * app/models/Customer.php
 * ============================================================
 * Represents the `customers` table.
 *
 * Responsibilities (DB layer only):
 *  - CRUD for customer profiles
 *  - Lookup by email / phone
 *  - Order history summary per customer
 * ============================================================
 */

class Customer extends Model
{
    protected string $table      = 'customers';
    protected string $primaryKey = 'id';

    // ── Finders ──────────────────────────────────────────────

    /**
     * Find a customer by primary key.
     */
    public function findById(int $id): mixed
    {
        return $this->find($id);
    }

    /**
     * Return all customers ordered by most recent.
     *
     * @return array<int, object>
     */
    public function findAll(): array
    {
        return $this->db->select(
            "SELECT * FROM `{$this->table}` ORDER BY `created_at` DESC"
        );
    }

    /**
     * Find a customer by email.
     */
    public function findByEmail(string $email): mixed
    {
        // Prefer registered accounts over guests when looking up by email
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}` WHERE `email` = :email ORDER BY `is_guest` ASC LIMIT 1",
            [':email' => $email]
        );
    }

    /**
     * Find a customer by phone number.
     */
    public function findByPhone(string $phone): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}` WHERE `phone` = :phone LIMIT 1",
            [':phone' => $phone]
        );
    }

    /**
     * Return customers with their order count and total spend.
     *
     * @return array<int, object>
     */
    public function findAllWithStats(): array
    {
        return $this->db->select(
            "SELECT c.*,
                    COUNT(o.id)       AS order_count,
                    COALESCE(SUM(o.total_price), 0) AS total_spent
               FROM `customers` c
               LEFT JOIN `orders` o ON o.customer_id = c.id
              GROUP BY c.id
              ORDER BY c.created_at DESC"
        );
    }

    /**
     * Paginated customer list with stats.
     *
     * @param  int $page
     * @param  int $perPage
     * @return array{data: array<int,object>, total: int, page: int, perPage: int, pages: int}
     */
    public function paginateWithStats(int $page = 1, int $perPage = 20): array
    {
        $total = $this->count();
        $offset = ($page - 1) * $perPage;

        $data = $this->db->select(
            "SELECT c.*,
                    COUNT(o.id)       AS order_count,
                    COALESCE(SUM(o.total_price), 0) AS total_spent
               FROM `customers` c
               LEFT JOIN `orders` o ON o.customer_id = c.id
              GROUP BY c.id
              ORDER BY c.created_at DESC
              LIMIT {$perPage} OFFSET {$offset}"
        );

        return [
            'data'    => $data,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'pages'   => (int) ceil($total / $perPage),
        ];
    }

    // ── Write Operations ──────────────────────────────────────

    /**
     * Create a new customer profile.
     *
     * @param  array{name: string, email: string, password?: string|null, phone?: string|null, address?: string|null, city?: string|null, notes?: string|null, is_guest?: int, guest_token?: string|null} $data
     * @return string|false  New customer id
     */
    public function create(array $data): string|false
    {
        return $this->insert([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => $data['password']    ?? null,
            'phone'       => $data['phone']       ?? null,
            'address'     => $data['address']     ?? null,
            'city'        => $data['city']        ?? null,
            'notes'       => $data['notes']       ?? null,
            'is_guest'    => $data['is_guest']    ?? 0,
            'guest_token' => $data['guest_token'] ?? null,
        ]);
    }

    /**
     * Create a guest customer for checkout.
     *
     * @param  array{name: string, email: string, phone: string, address: string, city: string, notes?: string|null, guest_token: string} $data
     * @return string|false  New customer id
     */
    public function createGuest(array $data): string|false
    {
        return $this->create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'phone'       => $data['phone'],
            'address'     => $data['address'],
            'city'        => $data['city'],
            'notes'       => $data['notes'] ?? null,
            'is_guest'    => 1,
            'guest_token' => $data['guest_token'],
            'password'    => null,
        ]);
    }

    /**
     * Find a guest customer by their guest token.
     */
    public function findByGuestToken(string $token): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}` WHERE `guest_token` = :token AND `is_guest` = 1 LIMIT 1",
            [':token' => $token]
        );
    }

    /**
     * Find a registered (non-guest) customer by email.
     */
    public function findRegisteredByEmail(string $email): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}` WHERE `email` = :email AND `is_guest` = 0 LIMIT 1",
            [':email' => $email]
        );
    }

    /**
     * Check if an email has a registered (non-guest) account.
     */
    public function hasRegisteredAccount(string $email): bool
    {
        $row = $this->db->selectOne(
            "SELECT COUNT(*) AS cnt FROM `{$this->table}` WHERE `email` = :email AND `is_guest` = 0",
            [':email' => $email]
        );
        return (int) ($row->cnt ?? 0) > 0;
    }

    /**
     * Convert a guest customer to a registered user.
     *
     * @param  int    $customerId  The guest customer ID
     * @param  string $password    Plain text password (will be hashed)
     * @return bool
     */
    public function convertGuestToUser(int $customerId, string $password): bool
    {
        return $this->update(
            [
                'password'    => password_hash($password, PASSWORD_BCRYPT),
                'is_guest'    => 0,
                'guest_token' => null,
            ],
            '`id` = :id AND `is_guest` = 1',
            [':id' => $customerId]
        );
    }

    /**
     * Update a customer's profile.
     *
     * @param  int                  $id
     * @param  array<string, mixed> $data
     * @return bool
     */
    public function updateCustomer(int $id, array $data): bool
    {
        unset($data['id'], $data['created_at']);
        return $this->update($data, '`id` = :id', [':id' => $id]);
    }

    /**
     * Update a customer's password by email.
     */
    public function updatePasswordByEmail(string $email, string $passwordHash): bool
    {
        return $this->update(
            ['password' => $passwordHash],
            '`email` = :email',
            [':email' => $email]
        );
    }

    /**
     * Delete a customer by id.
     * Will fail (FK RESTRICT) if the customer has existing orders.
     */
    public function deleteById(int|string $id): bool
    {
        return parent::deleteById($id);
    }

    /**
     * Search customers by name, email, phone or city — with order stats.
     *
     * @return array<int, object>
     */
    public function searchWithStats(string $term): array
    {
        $like = '%' . $term . '%';
        return $this->db->select(
            "SELECT c.*,
                    COUNT(o.id)       AS order_count,
                    COALESCE(SUM(o.total_price), 0) AS total_spent
               FROM `customers` c
               LEFT JOIN `orders` o ON o.customer_id = c.id
              WHERE c.name  LIKE :n
                 OR c.email LIKE :e
                 OR c.phone LIKE :p
                 OR c.city  LIKE :ci
              GROUP BY c.id
              ORDER BY c.created_at DESC",
            [':n' => $like, ':e' => $like, ':p' => $like, ':ci' => $like]
        );
    }

    // ── Existence / Validation ────────────────────────────────

    /**
     * Check if an email is already registered (exclude id for edit).
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql    = "SELECT COUNT(*) AS cnt FROM `{$this->table}` WHERE `email` = :email";
        $params = [':email' => $email];
        if ($excludeId !== null) {
            $sql   .= ' AND `id` != :xid';
            $params[':xid'] = $excludeId;
        }
        $row = $this->db->selectOne($sql, $params);
        return (int) ($row->cnt ?? 0) > 0;
    }
}
