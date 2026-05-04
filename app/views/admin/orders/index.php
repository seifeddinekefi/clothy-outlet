<!-- app/views/admin/orders/index.php -->

<div class="page-header">
    <div>
        <h1 class="page-header-title">Orders</h1>
        <p class="page-header-sub"><?= e($total) ?> order<?= $total !== 1 ? 's' : '' ?><?= $status ? ' — filtered by ' . e($status) : '' ?></p>
    </div>
</div>

<!-- ── Search ───────────────────────────────────────────────── -->
<form method="GET" action="<?= url('admin/orders') ?>" class="search-bar">
    <?php if ($status): ?>
        <input type="hidden" name="status" value="<?= e($status) ?>">
    <?php endif; ?>
    <input type="search" name="search" class="form-control search-input"
           placeholder="Search by customer name or order #…"
           value="<?= e($search) ?>">
    <button type="submit" class="btn btn-outline btn-sm">Search</button>
    <?php if ($search !== ''): ?>
        <a href="<?= url('admin/orders') ?><?= $status ? '?status=' . urlencode($status) : '' ?>" class="btn btn-outline btn-sm">Clear</a>
    <?php endif; ?>
</form>

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
    <?php if (empty($orders)): ?>
        <p class="empty-state">No orders found<?= $status ? ' with status "' . e($status) . '"' : '' ?>.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table table-cards">
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
                        <tr data-href="<?= url('admin/orders/' . $o->id) ?>">
                            <!-- Order # — hidden on mobile (shown as sub-text inside customer cell) -->
                            <td class="td-muted tc-hide" style="font-weight:600;">#<?= e($o->id) ?></td>

                            <!-- Customer name — primary identifier on mobile -->
                            <td class="tc-primary">
                                <div class="td-name"><?= e($o->customer_name) ?></div>
                                <div class="td-muted" style="font-size:0.73rem;font-weight:400;">Order #<?= e($o->id) ?></div>
                            </td>

                            <!-- Total -->
                            <td data-label="Total"><?= formatPrice($o->total_price) ?></td>

                            <!-- Payment status -->
                            <td data-label="Payment" class="tc-hide">
                                <span class="badge badge--<?= e($o->payment_status) ?>"><?= e($o->payment_status) ?></span>
                            </td>

                            <!-- Order status -->
                            <td data-label="Status">
                                <span class="badge badge--<?= e($o->status) ?>"><?= e($o->status) ?></span>
                            </td>

                            <!-- Date -->
                            <td class="td-muted tc-hide"><?= e(date('M d, Y', strtotime($o->created_at))) ?></td>

                            <!-- Actions -->
                            <td class="tc-actions">
                                <a href="<?= url('admin/orders/' . $o->id) ?>"
                                   class="btn btn-xs btn-outline"
                                   onclick="event.stopPropagation()">View</a>
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
                    <?php
                        $qs = http_build_query(array_filter([
                            'page'   => $i,
                            'status' => $status,
                            'search' => $search,
                        ]));
                    ?>
                    <a href="<?= url('admin/orders') ?>?<?= $qs ?>"
                        class="page-link <?= $i === $page ? 'page-link--active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>

    <?php endif; ?>
</div>
