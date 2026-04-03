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

        $sizes = $sizeModel->findAvailable($productId);

        $inWishlist = false;
        if (Session::isLoggedIn()) {
            $inWishlist = (new Wishlist())->has((int) Session::user()['id'], $productId);
        }

        $this->render('products.show', [
            'pageTitle' => $product->name . ' — ' . APP_NAME,
            'product'   => $product,
            'sizes'     => $sizes,
            'inWishlist' => $inWishlist,
        ]);
    }

    public function search(): void
    {
        $this->index();
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
}
