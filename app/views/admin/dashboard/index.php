<?php
// ── Build chart data ────────────────────────────────────────
$revLabels = [];
$revValues = [];
foreach ($revenueByDay as $row) {
    $revLabels[] = date('M d', strtotime($row->date));
    $revValues[] = (float) $row->revenue;
}

$statusLabels = [];
$statusValues = [];
foreach ($ordersByStatus as $row) {
    $statusLabels[] = ucfirst($row->status);
    $statusValues[] = (int) $row->count;
}
?>

<!-- ── Stat Cards ────────────────────────────────────────────── -->
<div class="stats-grid">

    <!-- Products -->
    <div class="stat-card stat-card--products">
        <div class="stat-head">
            <div class="stat-icon stat-icon--products">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
                </svg>
            </div>
            <span class="stat-trend stat-trend--muted">Catalog</span>
        </div>
        <div>
            <div class="stat-value"><?= number_format(e($totalProducts)) ?></div>
            <div class="stat-label">Total Products</div>
        </div>
        <div class="stat-sub">
            <?= count($lowStockProducts) ?> item<?= count($lowStockProducts) !== 1 ? 's' : '' ?> low on stock
        </div>
    </div>

    <!-- Orders -->
    <div class="stat-card stat-card--orders">
        <div class="stat-head">
            <div class="stat-icon stat-icon--orders">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <?php if ($pendingOrdersCount > 0): ?>
                <span class="stat-trend stat-trend--warn">&#9650; <?= e($pendingOrdersCount) ?> pending</span>
            <?php else: ?>
                <span class="stat-trend stat-trend--up">All clear</span>
            <?php endif; ?>
        </div>
        <div>
            <div class="stat-value"><?= number_format(e($totalOrders)) ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-sub">
            <?= e($pendingOrdersCount) ?> awaiting action
        </div>
    </div>

    <!-- Customers -->
    <div class="stat-card stat-card--customers">
        <div class="stat-head">
            <div class="stat-icon stat-icon--customers">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="stat-trend stat-trend--muted">Members</span>
        </div>
        <div>
            <div class="stat-value"><?= number_format(e($totalCustomers)) ?></div>
            <div class="stat-label">Customers</div>
        </div>
        <div class="stat-sub">Registered accounts</div>
    </div>

    <!-- Revenue -->
    <div class="stat-card stat-card--revenue">
        <div class="stat-head">
            <div class="stat-icon stat-icon--revenue">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="stat-trend stat-trend--up">Revenue</span>
        </div>
        <div>
            <div class="stat-value"><?= formatPrice($totalRevenue, 0) ?></div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-sub">This month: <?= formatPrice($revenueThisMonth) ?></div>
    </div>
</div>

<!-- ── Charts Row ─────────────────────────────────────────────── -->
<div class="charts-grid">
    <!-- Revenue chart -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Revenue — Last 30 Days</h2>
            <a href="<?= url('admin/orders') ?>" class="btn btn-sm btn-outline">All Orders</a>
        </div>
        <div class="chart-container" style="height:240px;">
            <canvas id="revenueChart"
                    data-labels="<?= htmlspecialchars(json_encode($revLabels), ENT_QUOTES, 'UTF-8') ?>"
                    data-values="<?= htmlspecialchars(json_encode($revValues), ENT_QUOTES, 'UTF-8') ?>">
            </canvas>
        </div>
    </div>

    <!-- Status donut -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Orders by Status</h2>
        </div>
        <div class="chart-container" style="height:240px;">
            <canvas id="statusChart"
                    data-labels="<?= htmlspecialchars(json_encode($statusLabels), ENT_QUOTES, 'UTF-8') ?>"
                    data-values="<?= htmlspecialchars(json_encode($statusValues), ENT_QUOTES, 'UTF-8') ?>">
            </canvas>
        </div>
    </div>
</div>

<!-- ── Low Stock & Top Selling ──────────────────────────────── -->
<div class="two-col-grid">

    <!-- Low Stock Alerts -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                Low Stock Alerts
                <?php if (!empty($lowStockProducts)): ?>
                    <span class="badge-count"><?= count($lowStockProducts) ?></span>
                <?php endif; ?>
            </h2>
            <a href="<?= url('admin/products') ?>" class="btn btn-sm btn-outline">Manage</a>
        </div>

        <?php if (empty($lowStockProducts)): ?>
            <p class="empty-state">All products are well-stocked.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockProducts as $p): ?>
                            <?php
                                $stock = (int) $p->stock;
                                $barClass = $stock === 0 ? 'critical' : ($stock <= 3 ? 'critical' : ($stock <= 7 ? 'low' : 'ok'));
                                $barWidth = min(100, round(($stock / 10) * 100));
                            ?>
                            <tr data-href="<?= url('admin/products/edit/' . $p->id) ?>">
                                <td class="td-name"><?= e($p->name) ?></td>
                                <td class="td-muted"><?= e($p->category_name ?? '—') ?></td>
                                <td>
                                    <div class="td-stock">
                                        <span class="td-stock-num<?= $stock === 0 ? ' badge badge--danger' : '' ?>"><?= $stock ?></span>
                                        <div class="stock-bar">
                                            <div class="stock-bar-fill stock-bar-fill--<?= $barClass ?>"
                                                 style="width:<?= $barWidth ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Top Selling -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Top Selling Products</h2>
            <a href="<?= url('admin/products') ?>" class="btn btn-sm btn-outline">All Products</a>
        </div>

        <?php if (empty($topSellingProducts)): ?>
            <p class="empty-state">No sales data yet.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Units</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topSellingProducts as $i => $p): ?>
                            <tr data-href="<?= url('admin/products/edit/' . $p->id) ?>">
                                <td class="td-muted"><?= $i + 1 ?></td>
                                <td class="td-name"><?= e($p->name) ?></td>
                                <td><?= number_format($p->units_sold) ?></td>
                                <td><?= formatPrice($p->revenue) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ── Recent Orders ─────────────────────────────────────────── -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Recent Orders</h2>
        <a href="<?= url('admin/orders') ?>" class="btn btn-sm btn-outline">View All</a>
    </div>

    <?php if (empty($recentOrders)): ?>
        <p class="empty-state">No orders yet. Go share your store!</p>
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
                    <?php foreach ($recentOrders as $order): ?>
                        <tr data-href="<?= url('admin/orders/' . $order->id) ?>">
                            <td class="td-muted">#<?= e($order->id) ?></td>
                            <td class="td-name"><?= e($order->customer_name) ?></td>
                            <td><?= formatPrice($order->total_price) ?></td>
                            <td><span class="badge badge--<?= e($order->payment_status) ?>"><?= e($order->payment_status) ?></span></td>
                            <td><span class="badge badge--<?= e($order->status) ?>"><?= e($order->status) ?></span></td>
                            <td class="td-muted"><?= e(date('M d, Y', strtotime($order->created_at))) ?></td>
                            <td>
                                <a href="<?= url('admin/orders/' . $order->id) ?>"
                                   class="btn btn-xs btn-outline" onclick="event.stopPropagation()">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php $view->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<?php $view->endSection(); ?>
