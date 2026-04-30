<?php

/**
 * ============================================================
 * app/controllers/ProductController.php
 * ============================================================
 * Handles all public product-browsing routes.
 * ============================================================
 */

class ProductController extends Controller
{
    public function index(): void
    {
        $productModel = new Product();
        $categoryModel = new Category();

        $q          = trim((string) ($_GET['q'] ?? ''));
        $sort       = (string) ($_GET['sort'] ?? 'newest');
        $minPrice   = $_GET['min_price'] ?? '';
        $maxPrice   = $_GET['max_price'] ?? '';
        $page       = max(1, (int) ($_GET['page'] ?? 1));
        $category   = trim((string) ($_GET['category'] ?? ''));

        $filters = [
            'q'         => $q,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
        ];

        if ($category !== '') {
            $cat = $categoryModel->findBySlug($category);
            if ($cat) {
                $filters['category_id'] = (int) $cat->id;
            }
        }

        $wishlistProductIds = [];
        if (Session::isLoggedIn()) {
            $wishlistItems = (new Wishlist())->findByCustomer((int) Session::user()['id']);
            foreach ($wishlistItems as $item) {
                $wishlistProductIds[] = (int) $item->product_id;
            }
        }

        $catalogue = $productModel->catalogue($page, 24, $filters, $sort);

        $this->render('products.index', [
            'pageTitle'       => 'Shop — ' . APP_NAME,
            'metaDescription' => 'Browse our full clothing collection.',
            'products'        => $catalogue['data'],
            'totalProducts'   => $catalogue['total'],
            'categories'      => $categoryModel->findAllWithProductCount(),
            'filters'         => [
                'q'         => $q,
                'sort'      => $sort,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'category'  => $category,
            ],
            'pagination'      => [
                'page'  => $catalogue['page'],
                'pages' => $catalogue['pages'],
            ],
            'wishlistProductIds' => $wishlistProductIds,
        ]);
    }

    public function category(string $category): void
    {
        $_GET['category'] = $category;
        $this->index();
    }

    public function show(string $id): void
    {
        $productId    = (int) $id;
        $productModel = new Product();
        $sizeModel    = new ProductSize();

        $product = $productModel->findWithDetails($productId);
        if (!$product) {
            abort(404);
        }

        $sizes     = $sizeModel->findAvailable($productId);
        $colors    = (new ProductColor())->findByProduct($productId);
        $qualities = (new ProductQuality())->findByProduct($productId);

        $inWishlist = false;
        if (Session::isLoggedIn()) {
            $inWishlist = (new Wishlist())->has((int) Session::user()['id'], $productId);
        }

        // Track recently viewed (session, max 6, most recent first)
        $recentIds = Session::get('recently_viewed') ?? [];
        $recentIds = array_values(array_filter($recentIds, fn($i) => (int)$i !== $productId));
        array_unshift($recentIds, $productId);
        $recentIds = array_slice($recentIds, 0, 7);
        Session::set('recently_viewed', $recentIds);

        // Load recently viewed products for display (exclude current)
        $displayIds     = array_filter($recentIds, fn($i) => (int)$i !== $productId);
        $recentProducts = $productModel->findByIds(array_values($displayIds));

        $this->render('products.show', [
            'pageTitle'      => $product->name . ' — ' . APP_NAME,
            'product'        => $product,
            'sizes'          => $sizes,
            'colors'         => $colors,
            'qualities'      => $qualities,
            'inWishlist'     => $inWishlist,
            'recentProducts' => $recentProducts,
        ]);
    }

    public function search(): void
    {
        $this->index();
    }

    /**
     * AJAX autocomplete — returns up to 6 matching products as JSON.
     * GET /search/autocomplete?q=...
     */
    public function autocomplete(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        header('Content-Type: application/json');
        if (mb_strlen($q) < 2) {
            echo json_encode([]);
            exit;
        }
        $results = (new Product())->autocomplete($q, 6);
        echo json_encode(array_map(function ($p) {
            return [
                'id'    => (int) $p->id,
                'name'  => $p->name,
                'slug'  => $p->slug,
                'price' => formatPrice($p->price),
                'image' => $p->primary_image ? url($p->primary_image) : null,
                'url'   => url('product/' . $p->id),
            ];
        }, $results));
        exit;
    }

    /**
     * API endpoint: returns JSON array of all images for a product.
     */
    public function images(string $id): void
    {
        $productId = (int) $id;
        $imageModel = new ProductImage();

        $images = $imageModel->findByProduct($productId);

        header('Content-Type: application/json');
        echo json_encode(array_map(function ($img) {
            return [
                'id'         => (int) $img->id,
                'path'       => $img->image_path,
                'is_primary' => (bool) $img->is_primary,
            ];
        }, $images));
        exit;
    }

    /**
     * API endpoint: returns JSON array of available sizes for a product.
     */
    public function sizes(string $id): void
    {
        $productId = (int) $id;
        $sizeModel = new ProductSize();

        $sizes = $sizeModel->findAvailable($productId);

        header('Content-Type: application/json');
        echo json_encode(array_map(function ($size) {
            return [
                'id'    => (int) $size->id,
                'size'  => $size->size,
                'stock' => (int) $size->stock,
            ];
        }, $sizes));
        exit;
    }
}
