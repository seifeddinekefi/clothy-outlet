<!-- app/views/admin/products/index.php -->

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Products <span class="badge-count"><?= count($products) ?></span></h2>
        <a href="<?= url('admin/products/create') ?>" class="btn btn-primary btn-sm">+ Add Product</a>
    </div>

    <?php if (empty($products)): ?>
        <p class="empty-state">No products found. <a href="<?= url('admin/products/create') ?>">Add the first one.</a></p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Featured</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= e($p->id) ?></td>
                            <td class="td-name"><?= e($p->name) ?></td>
                            <td><?= e($p->category_name) ?></td>
                            <td>$<?= number_format((float) $p->price, 2) ?></td>
                            <td><?= e($p->stock) ?></td>
                            <td><?= $p->is_featured ? '<span class="badge badge--success">Yes</span>' : '<span class="badge badge--muted">No</span>' ?></td>
                            <td><?= $p->is_active  ? '<span class="badge badge--success">Yes</span>' : '<span class="badge badge--danger">No</span>'  ?></td>
                            <td class="td-actions">
                                <a href="<?= url('admin/products/edit/' . $p->id) ?>" class="btn btn-xs btn-outline">Edit</a>
                                <form method="POST" action="<?= url('admin/products/delete/' . $p->id) ?>"
                                    class="inline-form"
                                    onsubmit="return confirm('Delete product &quot;<?= e(addslashes($p->name)) ?>&quot;? This cannot be undone.')">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>