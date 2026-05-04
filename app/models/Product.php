<?php

/**
 * ============================================================
 * app/models/Product.php
 * ============================================================
 * Represents the `products` table.
 *
 * Responsibilities (DB layer only):
 *  - CRUD for products
 *  - Catalogue listing + filtering (category, featured, active)
 *  - Full-text search
 *  - Slug-based lookup
 *  - Rich JOIN queries (with images, sizes, category)
 *  - Stock management helpers
 * ============================================================
 */

class Product extends Model
{
    protected string $table      = 'products';
    protected string $primaryKey = 'id';

    // ── Finders ──────────────────────────────────────────────

    /**
     * Find a product by primary key.
     */
    public function findById(int $id): mixed
    {
        return $this->find($id);
    }

    /**
     * Find a product by ID with its primary image.
     * Useful for cart/checkout where we need the image.
     */
    public function findByIdWithImage(int $id): mixed
    {
        return $this->db->selectOne(
            "SELECT p.*,
                    c.name        AS category_name,
                    pi.image_path AS primary_image
               FROM `products`       p
               LEFT JOIN `categories`     c  ON c.id         = p.category_id
               LEFT JOIN `product_images` pi ON pi.product_id = p.id AND pi.is_primary = 1
              WHERE p.id = :id
              LIMIT 1",
            [':id' => $id]
        );
    }

    /**
     * Find a product by slug.
     */
    public function findBySlug(string $slug): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}` WHERE `slug` = :slug AND `is_active` = 1 LIMIT 1",
            [':slug' => $slug]
        );
    }

    /**
     * Return all products (admin — includes inactive).
     *
     * @return array<int, object>
     */
    public function findAll(): array
    {
        return $this->db->select(
            "SELECT p.*, c.name AS category_name,
                    pi.image_path AS primary_image
               FROM `products`         p
               JOIN `categories`       c  ON c.id         = p.category_id
               LEFT JOIN `product_images` pi ON pi.product_id = p.id AND pi.is_primary = 1
              ORDER BY p.created_at DESC"
        );
    }

    /**
     * Return all active products (storefront).
     *
     * @return array<int, object>
     */
    public function findActive(): array
    {
        return $this->db->select(
            "SELECT p.*, 
                    c.name AS category_name,
                    pi.image_path AS primary_image
               FROM `products`   p
               JOIN `categories` c ON c.id = p.category_id
               LEFT JOIN `product_images` pi ON pi.product_id = p.id AND pi.is_primary = 1
              WHERE p.is_active = 1
              ORDER BY p.created_at DESC"
        );
    }

    /**
     * Return featured active products.
     *
     * @param  int $limit
     * @return array<int, object>
     */
    public function findFeatured(int $limit = 8): array
    {
        return $this->db->select(
            "SELECT p.*,
                    c.name        AS category_name,
                    pi.image_path AS primary_image
               FROM `products`       p
               JOIN `categories`     c  ON c.id         = p.category_id
               LEFT JOIN `product_images` pi ON pi.product_id = p.id AND pi.is_primary = 1
              WHERE p.is_featured = 1
                AND p.is_active   = 1
              ORDER BY p.created_at DESC
              LIMIT :lim",
            [':lim' => $limit]
        );
    }

    /**
     * Return products in a category.
     *
     * @return array<int, object>
     */
    public function findByCategory(int $categoryId, bool $activeOnly = true): array
    {
        $filter = $activeOnly ? 'AND p.is_active = 1' : '';
        return $this->db->select(
            "SELECT p.*,
                    pi.image_path AS primary_image
               FROM `products` p
               LEFT JOIN `product_images` pi
                      ON pi.product_id = p.id AND pi.is_primary = 1
              WHERE p.category_id = :cat {$filter}
              ORDER BY p.created_at DESC",
            [':cat' => $categoryId]
        );
    }

    /**
     * Full-text search across name + description.
     *
     * @param  string $term  Search query
     * @param  int    $limit
     * @return array<int, object>
     */
    public function search(string $term, int $limit = 20): array
    {
        return $this->db->select(
            "SELECT p.*,
                    c.name        AS category_name,
                    pi.image_path AS primary_image,
                    MATCH(p.name, p.description) AGAINST (:term IN NATURAL LANGUAGE MODE) AS relevance
               FROM `products`       p
               JOIN `categories`     c  ON c.id         = p.category_id
               LEFT JOIN `product_images` pi ON pi.product_id = p.id AND pi.is_primary = 1
              WHERE p.is_active = 1
                AND MATCH(p.name, p.description) AGAINST (:term2 IN NATURAL LANGUAGE MODE)
              ORDER BY relevance DESC
              LIMIT :lim",
            [':term' => $term, ':term2' => $term, ':lim' => $limit]
        );
    }

    /**
     * Return a product with its primary image, category, all sizes.
     * Used for single product detail page.
     */
    public function findWithDetails(int $id): mixed
    {
        return $this->db->selectOne(
            "SELECT p.*,
                    c.name        AS category_name,
                    c.slug        AS category_slug,
                    pi.image_path AS primary_image
               FROM `products`       p
               JOIN `categories`     c  ON c.id         = p.category_id
               LEFT JOIN `product_images` pi ON pi.product_id = p.id AND pi.is_primary = 1
              WHERE p.id = :id AND p.is_active = 1
              LIMIT 1",
            [':id' => $id]
        );
    }

    /**
     * Paginated product catalogue with storefront filters.
     *
     * Supported filters:
     *  - category_id (int)
     *  - q           (string)
     *  - min_price   (float)
     *  - max_price   (float)
     *
     * @param  int                  $page
     * @param  int                  $perPage
     * @param  array<string, mixed> $filters
     * @param  string               $sort   'newest' | 'price_asc' | 'price_desc' | 'name'
     * @return array{data: array<int,object>, total: int, page: int, perPage: int, pages: int}
     */
    public function catalogue(int $page = 1, int $perPage = PER_PAGE, array $filters = [], string $sort = 'newest'): array
    {
        $where  = 'p.is_active = 1';
        $params = [];

        $categoryId = isset($filters['category_id']) ? (int) $filters['category_id'] : null;
        if ($categoryId !== null && $categoryId > 0) {
            $where         .= ' AND p.category_id = :cat';
            $params[':cat'] = $categoryId;
        }

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $where        .= ' AND (p.name LIKE :q OR p.description LIKE :q2)';
            $params[':q']  = '%' . $q . '%';
            $params[':q2'] = '%' . $q . '%';
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $where            .= ' AND p.price >= :min_price';
            $params[':min_price'] = (float) $filters['min_price'];
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where            .= ' AND p.price <= :max_price';
            $params[':max_price'] = (float) $filters['max_price'];
        }

        $orderMap = [
            'newest'     => 'p.created_at DESC',
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'name'       => 'p.name ASC',
        ];
        $order = $orderMap[$sort] ?? 'p.created_at DESC';

        $total = (int) ($this->db->selectOne(
            "SELECT COUNT(*) AS cnt FROM `products` p WHERE {$where}",
            $params
        )->cnt ?? 0);

        $offset = ($page - 1) * $perPage;

        $data = $this->db->select(
            "SELECT p.*,
                    c.name        AS category_name,
                    pi.image_path AS primary_image
               FROM `products` p
               JOIN `categories` c  ON c.id = p.category_id
               LEFT JOIN `product_images` pi ON pi.product_id = p.id AND pi.is_primary = 1
              WHERE {$where}
              ORDER BY {$order}
              LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data'    => $data,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'pages'   => (int) ceil($total / $perPage),
        ];
    }

    // ── Stock Management ──────────────────────────────────────

    /**
     * Decrement total product stock by quantity.
     */
    public function decrementStock(int $productId, int $qty): bool
    {
        return $this->db->statement(
            "UPDATE `{$this->table}`
                SET `stock` = GREATEST(0, `stock` - :qty)
              WHERE `id` = :id",
            [':qty' => $qty, ':id' => $productId]
        );
    }

    /**
     * Increment total product stock.
     */
    public function incrementStock(int $productId, int $qty): bool
    {
        return $this->db->statement(
            "UPDATE `{$this->table}` SET `stock` = `stock` + :qty WHERE `id` = :id",
            [':qty' => $qty, ':id' => $productId]
        );
    }

    // ── Write Operations ──────────────────────────────────────

    /**
     * Create a new product.
     *
     * @param  array<string, mixed> $data
     * @return string|false  New product id
     */
    public function create(array $data): string|false
    {
        return $this->insert([
            'name'          => $data['name'],
            'slug'          => $data['slug'],
            'description'   => $data['description']   ?? null,
            'price'         => $data['price'],
            'compare_price' => $data['compare_price']  ?? null,
            'stock'         => $data['stock']          ?? 0,
            'sku'           => $data['sku']            ?? null,
            'category_id'   => $data['category_id'],
            'is_featured'   => $data['is_featured']    ?? 0,
            'badge_type'    => $data['badge_type']     ?? 'auto',
            'badge_text'    => $data['badge_text']     ?? null,
            'is_active'     => $data['is_active']      ?? 1,
            'meta_title'    => $data['meta_title']     ?? null,
            'meta_desc'     => $data['meta_desc']      ?? null,
        ]);
    }

    /**
     * Update an existing product.
     *
     * @param  int                  $id
     * @param  array<string, mixed> $data
     * @return bool
     */
    public function updateProduct(int $id, array $data): bool
    {
        unset($data['id'], $data['created_at']);
        return $this->update($data, '`id` = :id', [':id' => $id]);
    }

    /**
     * Delete a product by id.
     * Cascades to product_images and product_sizes.
     * Restricted if referenced in order_items.
     */
    public function deleteById(int|string $id): bool
    {
        return parent::deleteById($id);
    }

    // ── Analytics ─────────────────────────────────────────────

    /**
     * Return top-selling products by total units sold.
     *
     * @param  int $limit
     * @return array<int, object>
     */
    public function topSelling(int $limit = 5): array
    {
        return $this->db->select(
            "SELECT p.id, p.name, p.slug, p.price, p.stock,
                    COALESCE(SUM(oi.quantity), 0) AS units_sold,
                    COALESCE(SUM(oi.quantity * oi.price), 0) AS revenue
               FROM `products` p
               LEFT JOIN `order_items` oi ON oi.product_id = p.id
              GROUP BY p.id
              ORDER BY units_sold DESC
              LIMIT :limit",
            [':limit' => $limit]
        );
    }

    /**
     * Return products with stock at or below the given threshold.
     *
     * @param  int $threshold
     * @return array<int, object>
     */
    public function lowStock(int $threshold = 10): array
    {
        return $this->db->select(
            "SELECT p.*, c.name AS category_name
               FROM `products` p
               LEFT JOIN `categories` c ON c.id = p.category_id
              WHERE p.stock <= :threshold AND p.is_active = 1
              ORDER BY p.stock ASC",
            [':threshold' => $threshold]
        );
    }

    /**
     * Lightweight search for autocomplete — returns id, name, slug, price, image.
     *
     * @return array<int, object>
     */
    public function autocomplete(string $term, int $limit = 6): array
    {
        return $this->db->select(
            "SELECT p.id, p.name, p.slug, p.price,
                    pi.image_path AS primary_image
               FROM `products` p
               LEFT JOIN `product_images` pi ON pi.product_id = p.id AND pi.is_primary = 1
              WHERE p.is_active = 1
                AND (p.name LIKE :q OR p.description LIKE :q2)
              ORDER BY p.name ASC
              LIMIT :lim",
            [':q' => '%' . $term . '%', ':q2' => '%' . $term . '%', ':lim' => $limit]
        );
    }

    /**
     * Fetch a set of products by their IDs, preserving the given order.
     *
     * @param  int[] $ids
     * @return array<int, object>
     */
    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $ids = array_map('intval', $ids);
        $placeholders = [];
        $params = [];
        foreach ($ids as $i => $id) {
            $key = ':id' . $i;
            $placeholders[] = $key;
            $params[$key] = $id;
        }
        $in    = implode(',', $placeholders);
        $order = 'FIELD(p.id,' . implode(',', $ids) . ')';
        return $this->db->select(
            "SELECT p.id, p.name, p.slug, p.price, p.compare_price,
                    pi.image_path AS primary_image
               FROM `products` p
               LEFT JOIN `product_images` pi ON pi.product_id = p.id AND pi.is_primary = 1
              WHERE p.id IN ({$in}) AND p.is_active = 1
              ORDER BY {$order}",
            $params
        );
    }

    // ── Existence / Validation ────────────────────────────────

    /**
     * Check slug uniqueness.
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql    = "SELECT COUNT(*) AS cnt FROM `{$this->table}` WHERE `slug` = :slug";
        $params = [':slug' => $slug];
        if ($excludeId !== null) {
            $sql   .= ' AND `id` != :xid';
            $params[':xid'] = $excludeId;
        }
        $row = $this->db->selectOne($sql, $params);
        return (int) ($row->cnt ?? 0) > 0;
    }

    /**
     * Check SKU uniqueness.
     */
    public function skuExists(string $sku, ?int $excludeId = null): bool
    {
        $sql    = "SELECT COUNT(*) AS cnt FROM `{$this->table}` WHERE `sku` = :sku";
        $params = [':sku' => $sku];
        if ($excludeId !== null) {
            $sql   .= ' AND `id` != :xid';
            $params[':xid'] = $excludeId;
        }
        $row = $this->db->selectOne($sql, $params);
        return (int) ($row->cnt ?? 0) > 0;
    }
}
