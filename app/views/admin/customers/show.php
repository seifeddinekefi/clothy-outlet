<!-- app/views/admin/customers/show.php -->

<div class="order-detail-grid">

    <!-- ── Profile ────────────────────────────────────────── -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?= e($customer->name) ?></h2>
            <a href="<?= url('admin/customers/edit/' . $customer->id) ?>"
                class="btn btn-sm btn-secondary">Edit</a>
        </div>
        <dl class="detail-list">
            <dt>Email</dt>
            <dd><?= $customer->email ? e($customer->email) : '<span class="text-muted">—</span>' ?></dd>

            <dt>Phone</dt>
            <dd><?= $customer->phone ? e($customer->phone) : '<span class="text-muted">—</span>' ?></dd>

            <?php if ($customer->address || $customer->city): ?>
                <dt>Address</dt>
                <dd>
                    <?= $customer->address ? e($customer->address) : '' ?>
                    <?= $customer->address && $customer->city ? ', ' : '' ?>
                    <?= $customer->city ? e($customer->city) : '' ?>
                </dd>
            <?php endif; ?>

            <?php if ($customer->notes): ?>
                <dt>Notes</dt>
                <dd><?= e($customer->notes) ?></dd>
            <?php endif; ?>

            <dt>Joined</dt>
            <dd><?= e(date('F j, Y', strtotime($customer->created_at))) ?></dd>
        </dl>
    </div>

    <!-- ── Quick stats ────────────────────────────────────── -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Order Summary</h2>
        </div>
        <?php
        $orderCount  = count($orders);
        $totalSpent  = array_sum(array_map(fn($o) => (float) $o->total_price, $orders));
        $paidOrders  = count(array_filter($orders, fn($o) => $o->payment_status === 'paid'));
        ?>
        <div class="stats-grid stats-grid--sm">
            <div class="stat-card">
                <span class="stat-value"><?= $orderCount ?></span>
                <span class="stat-label">Total Orders</span>
            </div>
            <div class="stat-card">
                <span class="stat-value"><?= formatPrice($totalSpent) ?></span>
                <span class="stat-label">Total Spent</span>
            </div>
            <div class="stat-card">
                <span class="stat-value"><?= $paidOrders ?></span>
                <span class="stat-label">Paid Orders</span>
            </div>
        </div>

        <!-- Delete (only if no orders) -->
        <?php if ($orderCount === 0): ?>
            <div style="padding: 1rem 1.5rem;">
                <form method="POST" action="<?= url('admin/customers/delete/' . $customer->id) ?>"
                    onsubmit="return confirm('Delete this customer? This cannot be undone.')">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-sm btn-danger">Delete Customer</button>
                </form>
            </div>
        <?php else: ?>
            <p class="form-hint" style="padding: 0 1.5rem 1rem;">
                This customer cannot be deleted while they have orders.
            </p>
        <?php endif; ?>
    </div>

</div><!-- /.order-detail-grid -->

<!-- ── Order History ────────────────────────────────────────── -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Order History</h2>
        <a href="<?= url('admin/customers') ?>" class="btn btn-sm btn-outline">&larr; All Customers</a>
    </div>

    <?php if (empty($orders)): ?>
        <p class="empty-state">This customer has no orders yet.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
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
                            <td><?= formatPrice($o->total_price) ?></td>
                            <td>
                                <span class="badge badge--<?= e($o->payment_status) ?>">
                                    <?= e($o->payment_status) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge--<?= e($o->status) ?>">
                                    <?= e($o->status) ?>
                                </span>
                            </td>
                            <td><?= e(date('M d, Y', strtotime($o->created_at))) ?></td>
                            <td>
                                <a href="<?= url('admin/orders/' . $o->id) ?>"
                                    class="btn btn-xs btn-outline">View Order</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>