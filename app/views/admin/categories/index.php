<!-- app/views/admin/categories/index.php -->

<div class="page-header">
    <div>
        <h1 class="page-header-title">Categories</h1>
        <p class="page-header-sub"><?= count($categories) ?> category<?= count($categories) !== 1 ? 'ies' : '' ?> configured</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= url('admin/categories/create') ?>" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Category
        </a>
    </div>
</div>

<div class="card">
    <?php if (empty($categories)): ?>
        <p class="empty-state">No categories yet. <a href="<?= url('admin/categories/create') ?>">Add one.</a></p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table table-cards">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Products</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr data-href="<?= url('admin/categories/edit/' . $cat->id) ?>">
                            <!-- Name — primary cell -->
                            <td class="tc-primary">
                                <div class="td-name"><?= e($cat->name) ?></div>
                                <div class="td-muted" style="font-size:0.72rem;"><code><?= e($cat->slug) ?></code></div>
                            </td>

                            <td data-label="Slug" class="tc-hide"><code><?= e($cat->slug) ?></code></td>

                            <td data-label="Products"><?= e($cat->product_count ?? 0) ?></td>

                            <td data-label="Sort" class="td-muted tc-hide"><?= e($cat->sort_order) ?></td>

                            <td data-label="Status">
                                <?= $cat->is_active
                                    ? '<span class="badge badge--success">Active</span>'
                                    : '<span class="badge badge--muted">Inactive</span>' ?>
                            </td>

                            <td class="tc-actions">
                                <div class="table-actions">
                                    <a href="<?= url('admin/categories/edit/' . $cat->id) ?>"
                                       class="btn btn-xs btn-outline"
                                       onclick="event.stopPropagation()">Edit</a>
                                    <form method="POST" action="<?= url('admin/categories/delete/' . $cat->id) ?>"
                                          class="inline-form"
                                          onclick="event.stopPropagation()"
                                          onsubmit="return confirm('Delete category &quot;<?= e(addslashes($cat->name)) ?>&quot;?')">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-xs btn-danger"
                                            <?= ($cat->product_count ?? 0) > 0 ? 'disabled title="Has products assigned"' : '' ?>>
                                            Del
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
