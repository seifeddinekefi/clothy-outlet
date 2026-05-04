<!-- app/views/admin/products/index.php -->

<div class="page-header">
    <div>
        <h1 class="page-header-title">Products</h1>
        <p class="page-header-sub"><?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?> in catalogue</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= url('admin/products/create') ?>" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Product
        </a>
    </div>
</div>

<div class="card">
    <?php if (empty($products)): ?>
        <p class="empty-state">No products yet. <a href="<?= url('admin/products/create') ?>">Add the first one.</a></p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table table-cards">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Badge</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <?php
                            $_badgeMeta = productBadgeMeta($p);
                            $stock = (int) $p->stock;
                            $barClass = $stock === 0 ? 'critical' : ($stock <= 3 ? 'critical' : ($stock <= 10 ? 'low' : 'ok'));
                            $barWidth = min(100, round(($stock / 20) * 100));
                        ?>
                        <tr data-href="<?= url('admin/products/edit/' . $p->id) ?>">

                            <!-- Product: thumbnail + name + category -->
                            <td class="tc-primary prod-name-cell">
                                <?php if (!empty($p->primary_image)): ?>
                                    <img src="<?= e($p->primary_image) ?>" alt="" class="table-thumb">
                                <?php else: ?>
                                    <div class="table-no-thumb">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    </div>
                                <?php endif; ?>
                                <div class="prod-name-text">
                                    <div class="td-name"><?= e($p->name) ?></div>
                                    <div class="td-muted" style="font-size:0.73rem;"><?= e($p->category_name) ?></div>
                                </div>
                            </td>

                            <!-- Price -->
                            <td data-label="Price"><?= formatPrice($p->price) ?></td>

                            <!-- Stock -->
                            <td data-label="Stock">
                                <div class="td-stock">
                                    <span class="td-stock-num<?= $stock === 0 ? ' badge badge--danger' : ($stock <= 3 ? ' badge badge--warning' : '') ?>"><?= $stock ?></span>
                                    <div class="stock-bar">
                                        <div class="stock-bar-fill stock-bar-fill--<?= $barClass ?>" style="width:<?= $barWidth ?>%"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Badge -->
                            <td data-label="Badge" class="tc-hide">
                                <?php if (!empty($_badgeMeta['show'])): ?>
                                    <span class="badge <?= e($_badgeMeta['class']) ?>"><?= e($_badgeMeta['label']) ?></span>
                                <?php else: ?>
                                    <span class="td-muted">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- Status: featured star + live/draft -->
                            <td data-label="Status">
                                <div style="display:flex;gap:0.3rem;flex-wrap:wrap;align-items:center;">
                                    <?php if ($p->is_featured): ?>
                                        <span class="badge badge--warning" title="Featured">&#9733; Featured</span>
                                    <?php endif; ?>
                                    <?php if ($p->is_active): ?>
                                        <span class="badge badge--success">Live</span>
                                    <?php else: ?>
                                        <span class="badge badge--muted">Draft</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="tc-actions">
                                <div class="table-actions">
                                    <a href="<?= url('admin/products/edit/' . $p->id) ?>"
                                       class="btn btn-xs btn-outline"
                                       onclick="event.stopPropagation()">Edit</a>
                                    <form method="POST" action="<?= url('admin/products/delete/' . $p->id) ?>"
                                          class="inline-form"
                                          onclick="event.stopPropagation()"
                                          onsubmit="return confirm('Delete &quot;<?= e(addslashes($p->name)) ?>&quot;? This cannot be undone.')">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-xs btn-danger">Del</button>
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
