<!-- app/views/admin/categories/index.php -->

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Categories <span class="badge-count"><?= count($categories) ?></span></h2>
        <a href="<?= url('admin/categories/create') ?>" class="btn btn-primary btn-sm">+ Add Category</a>
    </div>

    <?php if (empty($categories)): ?>
        <p class="empty-state">No categories yet. <a href="<?= url('admin/categories/create') ?>">Add one.</a></p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Products</th>
                        <th>Sort</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= e($cat->id) ?></td>
                            <td class="td-name"><?= e($cat->name) ?></td>
                            <td><code><?= e($cat->slug) ?></code></td>
                            <td><?= e($cat->product_count ?? 0) ?></td>
                            <td><?= e($cat->sort_order) ?></td>
                            <td><?= $cat->is_active ? '<span class="badge badge--success">Yes</span>' : '<span class="badge badge--danger">No</span>' ?></td>
                            <td class="td-actions">
                                <a href="<?= url('admin/categories/edit/' . $cat->id) ?>" class="btn btn-xs btn-outline">Edit</a>
                                <form method="POST" action="<?= url('admin/categories/delete/' . $cat->id) ?>"
                                    class="inline-form"
                                    onsubmit="return confirm('Delete category &quot;<?= e(addslashes($cat->name)) ?>&quot;?')">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn btn-xs btn-danger"
                                        <?= ($cat->product_count ?? 0) > 0 ? 'disabled title="Has products assigned"' : '' ?>>
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>