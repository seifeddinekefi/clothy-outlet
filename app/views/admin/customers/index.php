<!-- app/views/admin/customers/index.php -->

<!-- ── Search bar ────────────────────────────────────────────── -->
<form method="GET" action="<?= url('admin/customers') ?>" class="search-bar">
    <input type="text" name="search" class="form-control search-input"
        value="<?= e($search) ?>" placeholder="Search by name, email, phone or city…">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if ($search !== ''): ?>
        <a href="<?= url('admin/customers') ?>" class="btn btn-outline">Clear</a>
    <?php endif; ?>
</form>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            Customers
            <span class="badge-count"><?= e($total) ?></span>
            <?php if ($search !== ''): ?>
                <span class="badge badge--info"><?= e($search) ?></span>
            <?php endif; ?>
        </h2>
    </div>

    <?php if (empty($customers)): ?>
        <p class="empty-state">No customers found<?= $search !== '' ? ' matching "' . e($search) . '"' : '' ?>.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td><?= e($c->id) ?></td>
                            <td><?= e($c->name) ?></td>
                            <td><?= $c->email ? e($c->email) : '<span class="text-muted">—</span>' ?></td>
                            <td><?= $c->phone ? e($c->phone) : '<span class="text-muted">—</span>' ?></td>
                            <td><?= $c->city  ? e($c->city)  : '<span class="text-muted">—</span>' ?></td>
                            <td><?= e($c->order_count) ?></td>
                            <td>$<?= number_format((float) $c->total_spent, 2) ?></td>
                            <td><?= e(date('M d, Y', strtotime($c->created_at))) ?></td>
                            <td class="table-actions">
                                <a href="<?= url('admin/customers/' . $c->id) ?>"
                                    class="btn btn-xs btn-outline">View</a>
                                <a href="<?= url('admin/customers/edit/' . $c->id) ?>"
                                    class="btn btn-xs btn-secondary">Edit</a>
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
                    <a href="<?= url('admin/customers') ?>?page=<?= $i ?>"
                        class="page-link <?= $i === $page ? 'page-link--active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>

    <?php endif; ?>
</div>