<!-- app/views/admin/orders/index.php -->

<!-- ── Status filter tabs ───────────────────────────────────── -->
<div class="filter-tabs">
    <a href="<?= url('admin/orders') ?>"
        class="filter-tab <?= $status === null ? 'filter-tab--active' : '' ?>">
        All
    </a>
    <?php foreach ($statuses as $s): ?>
        <a href="<?= url('admin/orders') ?>?status=<?= urlencode($s) ?>"
            class="filter-tab <?= $status === $s ? 'filter-tab--active' : '' ?> filter-tab--<?= e($s) ?>">
            <?= ucfirst(e($s)) ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            Orders
            <?= $status !== null ? '— <span class="badge badge--' . e($status) . '">' . e($status) . '</span>' : '' ?>
            <span class="badge-count"><?= e($total) ?></span>
        </h2>
    </div>

    <?php if (empty($orders)): ?>
        <p class="empty-state">No orders found<?= $status ? ' with status "' . e($status) . '"' : '' ?>.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?= e($o->id) ?></td>
                            <td><?= e($o->customer_name) ?></td>
                            <td>$<?= number_format((float) $o->total_price, 2) ?></td>
                            <td><span class="badge badge--<?= e($o->payment_status) ?>"><?= e($o->payment_status) ?></span></td>
                            <td><span class="badge badge--<?= e($o->status) ?>"><?= e($o->status) ?></span></td>
                            <td><?= e(date('M d, Y', strtotime($o->created_at))) ?></td>
                            <td>
                                <a href="<?= url('admin/orders/' . $o->id) ?>" class="btn btn-xs btn-outline">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ── Pagination ─────────────────────────────────────── -->
        <?php if ($pages > 1): ?>
            <nav class="pagination" aria-label="Orders pagination">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="<?= url('admin/orders') ?>?page=<?= $i ?><?= $status ? '&status=' . urlencode($status) : '' ?>"
                        class="page-link <?= $i === $page ? 'page-link--active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>

    <?php endif; ?>
</div>