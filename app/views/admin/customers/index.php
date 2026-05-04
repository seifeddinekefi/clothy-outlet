<!-- app/views/admin/customers/index.php -->

<div class="page-header">
    <div>
        <h1 class="page-header-title">Customers</h1>
        <p class="page-header-sub"><?= e($total) ?> registered customer<?= $total !== 1 ? 's' : '' ?></p>
    </div>
</div>

<!-- ── Search bar ────────────────────────────────────────────── -->
<form method="GET" action="<?= url('admin/customers') ?>" class="search-bar">
    <input type="text" name="search" class="form-control search-input"
        value="<?= e($search) ?>" placeholder="Search by name, email, phone or city…">
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
    <?php if ($search !== ''): ?>
        <a href="<?= url('admin/customers') ?>" class="btn btn-outline btn-sm">Clear</a>
    <?php endif; ?>
</form>

<div class="card">
    <?php if (empty($customers)): ?>
        <p class="empty-state">No customers found<?= $search !== '' ? ' matching "' . e($search) . '"' : '' ?>.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table table-cards">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Spent</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                        <tr data-href="<?= url('admin/customers/' . $c->id) ?>">
                            <!-- Customer name — primary cell -->
                            <td class="tc-primary">
                                <div class="td-name"><?= e($c->name) ?></div>
                                <?php if ($c->city): ?>
                                    <div class="td-muted" style="font-size:0.73rem;"><?= e($c->city) ?></div>
                                <?php endif; ?>
                            </td>

                            <td data-label="Email" class="tc-hide">
                                <?= $c->email ? e($c->email) : '<span class="td-muted">—</span>' ?>
                            </td>

                            <td data-label="Phone" class="tc-hide">
                                <?= $c->phone ? e($c->phone) : '<span class="td-muted">—</span>' ?>
                            </td>

                            <td data-label="Orders"><?= e($c->order_count) ?></td>

                            <td data-label="Spent"><?= formatPrice($c->total_spent) ?></td>

                            <td data-label="Joined" class="td-muted tc-hide">
                                <?= e(date('M d, Y', strtotime($c->created_at))) ?>
                            </td>

                            <td class="tc-actions">
                                <div class="table-actions">
                                    <a href="<?= url('admin/customers/' . $c->id) ?>"
                                       class="btn btn-xs btn-outline"
                                       onclick="event.stopPropagation()">View</a>
                                    <a href="<?= url('admin/customers/edit/' . $c->id) ?>"
                                       class="btn btn-xs btn-secondary"
                                       onclick="event.stopPropagation()">Edit</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ── Pagination ─────────────────────────────────────── -->
        <?php if ($pages > 1): ?>
            <nav class="pagination" aria-label="Customers pagination">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="<?= url('admin/customers') ?>?page=<?= $i ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?>"
                        class="page-link <?= $i === $page ? 'page-link--active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>

    <?php endif; ?>
</div>
