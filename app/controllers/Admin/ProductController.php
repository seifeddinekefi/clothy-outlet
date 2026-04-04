<?php

/**
 * ============================================================
 * app/controllers/Admin/ProductController.php
 * ============================================================
 * Full CRUD management for the product catalogue.
 *
 * - Listing with category join
 * - Create / Edit with multiple image uploads
 * - Delete with filesystem cleanup
 *
 * Security:
 *  - CSRF verified on every mutating request
 *  - Uploads validated by MIME type (finfo) and size
 *  - Filenames randomised to prevent path-guessing
 * ============================================================
 */

class ProductController extends BaseAdminController
{
    private Product      $productModel;
    private Category     $categoryModel;
    private ProductImage $imageModel;
    private ProductSize  $sizeModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
        $this->imageModel    = new ProductImage();
        $this->sizeModel     = new ProductSize();
    }

    // ── Index ─────────────────────────────────────────────────

    public function index(): void
    {
        $this->adminView('products.index', [
            'pageTitle' => 'Products',
            'products'  => $this->productModel->findAll(),
        ]);
    }

    // ── Create ────────────────────────────────────────────────

    public function create(): void
    {
        $this->adminView('products.create', [
            'pageTitle'  => 'Add Product',
            'categories' => $this->categoryModel->findAll(),
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();

        [$data, $error] = $this->resolveProductInput();
        if ($error) {
            Session::flash('error', $error);
            $this->redirect(url('admin/products/create'));
        }

        if ($this->productModel->slugExists($data['slug'])) {
            $data['slug'] .= '-' . time();
        }

        $productId = $this->productModel->create($data);

        if (!$productId) {
            Session::flash('error', 'Failed to create product. Please try again.');
            $this->redirect(url('admin/products/create'));
        }

        // Handle image uploads (first image becomes primary)
        if (!empty($_FILES['images']['name'][0])) {
            $this->handleImageUploads((int) $productId, $_FILES['images'], true);
        }

        // Handle size entries
        $this->handleSizeInputs((int) $productId);

        Session::flash('success', 'Product created successfully.');
        $this->redirect(url('admin/products'));
    }

    // ── Edit ──────────────────────────────────────────────────

    public function edit(string $id): void
    {
        $product = $this->productModel->findById((int) $id);
        if (!$product) {
            Session::flash('error', 'Product not found.');
            $this->redirect(url('admin/products'));
        }

        $this->adminView('products.edit', [
            'pageTitle'  => 'Edit: ' . e($product->name),
            'product'    => $product,
            'categories' => $this->categoryModel->findAll(),
            'images'     => $this->imageModel->findByProduct((int) $id),
            'sizes'      => $this->sizeModel->findByProduct((int) $id),
        ]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();

        $product = $this->productModel->findById((int) $id);
        if (!$product) {
            Session::flash('error', 'Product not found.');
            $this->redirect(url('admin/products'));
        }

        [$data, $error] = $this->resolveProductInput();
        if ($error) {
            Session::flash('error', $error);
            $this->redirect(url('admin/products/edit/' . $id));
        }

        if ($this->productModel->slugExists($data['slug'], (int) $id)) {
            $data['slug'] .= '-' . time();
        }

        $this->productModel->updateProduct((int) $id, $data);

        // Upload new images if provided
        if (!empty($_FILES['images']['name'][0])) {
            $this->handleImageUploads((int) $id, $_FILES['images'], false);
        }

        // Change primary image if selected
        $primaryImageId = (int) ($_POST['primary_image_id'] ?? 0);
        if ($primaryImageId > 0) {
            $this->imageModel->setPrimary($primaryImageId, (int) $id);
        }

        // Delete individually checked images
        $deleteImageIds = $_POST['delete_images'] ?? [];
        foreach ((array) $deleteImageIds as $imgId) {
            $img = $this->imageModel->findById((int) $imgId);
            if ($img && (int) $img->product_id === (int) $id) {
                $this->deleteImageFile($img->image_path);
                $this->imageModel->deleteById((int) $img->id);
            }
        }

        // Handle size entries
        $this->handleSizeInputs((int) $id);

        Session::flash('success', 'Product updated successfully.');
        $this->redirect(url('admin/products'));
    }

    // ── Delete ────────────────────────────────────────────────

    public function destroy(string $id): void
    {
        $this->verifyCsrf();

        $product = $this->productModel->findById((int) $id);
        if (!$product) {
            Session::flash('error', 'Product not found.');
            $this->redirect(url('admin/products'));
        }

        // Remove image files from disk before the DB cascade deletes records
        foreach ($this->imageModel->findByProduct((int) $id) as $img) {
            $this->deleteImageFile($img->image_path);
        }

        $this->productModel->deleteById((int) $id);

        Session::flash('success', 'Product deleted.');
        $this->redirect(url('admin/products'));
    }

    // ── Private Helpers ───────────────────────────────────────

    /**
     * Parse and validate POST product fields.
     *
     * @return array{0: array<string,mixed>, 1: string|null}
     *         [data, errorMessage]
     */
    private function resolveProductInput(): array
    {
        $name    = trim(strip_tags($_POST['name']    ?? ''));
        $slug    = trim(strip_tags($_POST['slug']    ?? ''));
        $desc    = trim(strip_tags($_POST['description'] ?? '', '<p><br><em><strong><ul><li>'));
        $priceRaw   = $_POST['price']         ?? '';
        $compareRaw = $_POST['compare_price'] ?? '';
        $stockRaw   = $_POST['stock']         ?? '0';
        $sku        = trim(strip_tags($_POST['sku']    ?? '')) ?: null;
        $catId      = (int) ($_POST['category_id']  ?? 0);
        $featured   = isset($_POST['is_featured']) ? 1 : 0;
        $active     = isset($_POST['is_active'])   ? 1 : 0;

        if ($name === '') {
            return [[], 'Product name is required.'];
        }

        $price = filter_var($priceRaw, FILTER_VALIDATE_FLOAT);
        if ($price === false || $price < 0) {
            return [[], 'Price must be a valid positive number.'];
        }

        $compare = ($compareRaw !== '')
            ? filter_var($compareRaw, FILTER_VALIDATE_FLOAT)
            : null;
        if ($compare !== null && $compare === false) {
            return [[], 'Compare price must be a valid number.'];
        }

        $stock = max(0, (int) $stockRaw);

        if ($catId <= 0) {
            return [[], 'Please select a category.'];
        }

        if (!$this->categoryModel->findById($catId)) {
            return [[], 'Selected category does not exist.'];
        }

        if ($slug === '') {
            $slug = slug($name);
        } else {
            $slug = slug($slug);
        }

        return [[
            'name'          => $name,
            'slug'          => $slug,
            'description'   => $desc,
            'price'         => $price,
            'compare_price' => $compare ?: null,
            'stock'         => $stock,
            'sku'           => $sku,
            'category_id'   => $catId,
            'is_featured'   => $featured,
            'is_active'     => $active,
        ], null];
    }

    /**
     * Upload and persist product images.
     *
     * @param  int   $productId
     * @param  array $files         $_FILES['images'] structure
     * @param  bool  $firstIsPrimary  Make the first new image primary
     */
    private function handleImageUploads(int $productId, array $files, bool $firstIsPrimary): void
    {
        $uploadDir = BASE_PATH . '/public/uploads/products/' . $productId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $existingCount = count($this->imageModel->findByProduct($productId));
        $finfo         = new finfo(FILEINFO_MIME_TYPE);
        $sortOffset    = $existingCount;

        foreach ($files['tmp_name'] as $i => $tmpName) {
            if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }
            if (!is_uploaded_file($tmpName)) {
                continue;
            }
            if (($files['size'][$i] ?? 0) > UPLOAD_MAX_SIZE) {
                continue;
            }

            $mimeType = $finfo->file($tmpName);
            if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES, true)) {
                continue;
            }

            $ext          = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
            $safeExt      = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) ? $ext : 'jpg';
            $filename     = bin2hex(random_bytes(16)) . '.' . $safeExt;
            $destPath     = $uploadDir . $filename;

            if (!move_uploaded_file($tmpName, $destPath)) {
                continue;
            }

            $isPrimary    = $firstIsPrimary && $i === 0 && $existingCount === 0;
            $relativePath = 'uploads/products/' . $productId . '/' . $filename;
            $altText      = htmlspecialchars(
                pathinfo($files['name'][$i], PATHINFO_FILENAME),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $this->imageModel->create($productId, $relativePath, $altText, $isPrimary, $sortOffset + $i);
        }
    }

    /**
     * Delete an image file from disk given its DB-relative path.
     */
    private function deleteImageFile(string $relativePath): void
    {
        $fullPath = BASE_PATH . '/public/' . ltrim($relativePath, '/');
        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * Handle size inputs from the product form.
     * Expects POST data in format: sizes[XS], sizes[S], sizes[M], etc.
     * Values are stock quantities. Empty or 0 means size not available.
     */
    private function handleSizeInputs(int $productId): void
    {
        $sizesInput = $_POST['sizes'] ?? [];
        $standardSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

        foreach ($standardSizes as $size) {
            $stock = isset($sizesInput[$size]) ? (int) $sizesInput[$size] : 0;
            
            $existing = $this->sizeModel->findByProductAndSize($productId, $size);
            
            if ($stock > 0) {
                // Add or update size with stock
                $this->sizeModel->upsert($productId, $size, $stock);
            } elseif ($existing) {
                // Remove size if stock is 0 or empty
                $this->sizeModel->deleteById((int) $existing->id);
            }
        }
    }
}
