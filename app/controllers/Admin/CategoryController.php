<?php

/**
 * ============================================================
 * app/controllers/Admin/CategoryController.php
 * ============================================================
 * Full CRUD management for product categories.
 * ============================================================
 */

class CategoryController extends BaseAdminController
{
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    // ── Index ─────────────────────────────────────────────────

    public function index(): void
    {
        $this->adminView('categories.index', [
            'pageTitle'  => 'Categories',
            'categories' => $this->categoryModel->findAllWithProductCount(),
        ]);
    }

    // ── Create ────────────────────────────────────────────────

    public function create(): void
    {
        $this->adminView('categories.create', [
            'pageTitle' => 'Add Category',
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();

        [$data, $error] = $this->resolveCategoryInput();
        if ($error) {
            Session::flash('error', $error);
            $this->redirect(url('admin/categories/create'));
        }

        if ($this->categoryModel->slugExists($data['slug'])) {
            Session::flash('error', 'A category with this slug already exists. Choose a different name or slug.');
            $this->redirect(url('admin/categories/create'));
        }

        $this->categoryModel->create($data);

        Session::flash('success', 'Category created.');
        $this->redirect(url('admin/categories'));
    }

    // ── Edit ──────────────────────────────────────────────────

    public function edit(string $id): void
    {
        $category = $this->categoryModel->findById((int) $id);
        if (!$category) {
            Session::flash('error', 'Category not found.');
            $this->redirect(url('admin/categories'));
        }

        $this->adminView('categories.edit', [
            'pageTitle' => 'Edit Category',
            'category'  => $category,
        ]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();

        $category = $this->categoryModel->findById((int) $id);
        if (!$category) {
            Session::flash('error', 'Category not found.');
            $this->redirect(url('admin/categories'));
        }

        [$data, $error] = $this->resolveCategoryInput();
        if ($error) {
            Session::flash('error', $error);
            $this->redirect(url('admin/categories/edit/' . $id));
        }

        if ($this->categoryModel->slugExists($data['slug'], (int) $id)) {
            Session::flash('error', 'That slug is already used by another category.');
            $this->redirect(url('admin/categories/edit/' . $id));
        }

        $this->categoryModel->updateCategory((int) $id, $data);

        Session::flash('success', 'Category updated.');
        $this->redirect(url('admin/categories'));
    }

    // ── Delete ────────────────────────────────────────────────

    public function destroy(string $id): void
    {
        $this->verifyCsrf();

        $category = $this->categoryModel->findById((int) $id);
        if (!$category) {
            Session::flash('error', 'Category not found.');
            $this->redirect(url('admin/categories'));
        }

        // Guard: refuse deletion if products are still assigned
        $productCount = (new Product())->count('`category_id` = :cid', [':cid' => (int) $id]);
        if ($productCount > 0) {
            Session::flash('error', 'Cannot delete a category that has products. Reassign or remove those products first.');
            $this->redirect(url('admin/categories'));
        }

        $this->categoryModel->deleteById((int) $id);

        Session::flash('success', 'Category deleted.');
        $this->redirect(url('admin/categories'));
    }

    // ── Private Helpers ───────────────────────────────────────

    /**
     * Parse and validate POST category fields.
     *
     * @return array{0: array<string,mixed>, 1: string|null}
     */
    private function resolveCategoryInput(): array
    {
        $name      = trim(strip_tags($_POST['name']        ?? ''));
        $slug      = trim(strip_tags($_POST['slug']        ?? ''));
        $desc      = trim(strip_tags($_POST['description'] ?? ''));
        $sortOrder = max(0, (int) ($_POST['sort_order'] ?? 0));
        $active    = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '') {
            return [[], 'Category name is required.'];
        }

        if ($slug === '') {
            $slug = slug($name);
        } else {
            $slug = slug($slug);
        }

        return [[
            'name'        => $name,
            'slug'        => $slug,
            'description' => $desc ?: null,
            'sort_order'  => $sortOrder,
            'is_active'   => $active,
        ], null];
    }
}
