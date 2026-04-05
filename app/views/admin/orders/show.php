<!-- app/views/admin/orders/show.php -->

<div class="order-detail-grid">

    <!-- ── Order Summary ──────────────────────────────────── -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Order #<?= e($order->id) ?></h2>
            <span class="badge badge--<?= e($order->status) ?> badge--lg"><?= e($order->status) ?></span>
        </div>

        <dl class="detail-list">
            <dt>Order Date</dt>
            <dd><?= e(date('F j, Y — H:i', strtotime($order->created_at))) ?></dd>

            <dt>Payment Method</dt>
            <dd><?= e(str_replace('_', ' ', $order->payment_method)) ?></dd>

            <dt>Payment Status</dt>
            <dd><span class="badge badge--<?= e($order->payment_status) ?>"><?= e($order->payment_status) ?></span></dd>

            <?php if ($order->notes): ?>
                <dt>Notes</dt>
                <dd><?= e($order->notes) ?></dd>
            <?php endif; ?>
        </dl>

        <!-- ── Totals ──────────────────────────────────────── -->
        <table class="totals-table">
            <tr>
                <td>Subtotal</td>
                <td><?= formatPrice($order->subtotal) ?></td>
            </tr>
            <?php if ((float) $order->discount > 0): ?>
                <tr>
                    <td>Discount</td>
                    <td>−<?= formatPrice($order->discount) ?></td>
                </tr>
            <?php endif; ?>
            <?php if ((float) $order->shipping_fee > 0): ?>
                <tr>
                    <td>Shipping</td>
                    <td><?= formatPrice($order->shipping_fee) ?></td>
                </tr>
            <?php endif; ?>
            <tr class="totals-total">
                <td><strong>Total</strong></td>
                <td><strong><?= formatPrice($order->total_price) ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- ── Customer Info ──────────────────────────────────── -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Customer</h2>
        </div>
        <dl class="detail-list">
            <dt>Name</dt>
            <dd><?= e($order->customer_name)    ?></dd>
            <dt>Email</dt>
            <dd><?= e($order->customer_email)   ?></dd>
            <?php if ($order->customer_phone): ?>
                <dt>Phone</dt>
                <dd><?= e($order->customer_phone)  ?></dd>
            <?php endif; ?>
            <?php if ($order->customer_address): ?>
                <dt>Address</dt>
                <dd><?= e($order->customer_address) ?><?= $order->customer_city ? ', ' . e($order->customer_city) : '' ?></dd>
            <?php endif; ?>
        </dl>
    </div>

</div><!-- /.order-detail-grid -->

<!-- ── Order Items ──────────────────────────────────────────── -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Items</h2>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php if ($item->product_image): ?>
                                <img src="<?= url($item->product_image) ?>"
                                    alt="<?= e($item->product_name) ?>"
                                    class="item-thumb">
                            <?php else: ?>
                                <span class="item-no-image">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($item->product_name) ?></td>
                        <td><?= e($item->size ?? '—') ?></td>
                        <td><?= formatPrice($item->price) ?></td>
                        <td><?= e($item->quantity) ?></td>
                        <td><?= formatPrice((float) $item->price * (int) $item->quantity) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ── Update Order & Payment Status ───────────────────────── -->
<div class="two-col-grid" style="margin-bottom:1rem;">

    <!-- Order Status -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Order Status</h2>
            <span class="badge badge--<?= e($order->status) ?>"><?= ucfirst(e($order->status)) ?></span>
        </div>
        <form method="POST" action="<?= url('admin/orders/' . $order->id . '/status') ?>"
            style="padding:1.1rem 1.4rem;" data-confirm="Update order status to the selected value?">
            <?= csrfField() ?>
            <div class="form-group" style="margin-bottom:0.9rem;">
                <label for="status">New Status</label>
                <select id="status" name="status" class="form-control">
                    <?php foreach ($statuses as $s): ?>
                        <option value="<?= e($s) ?>" <?= $order->status === $s ? 'selected' : '' ?>>
                            <?= ucfirst(e($s)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
        </form>
    </div>

    <!-- Payment Status -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Payment Status</h2>
            <span class="badge badge--<?= e($order->payment_status) ?>"><?= ucfirst(e($order->payment_status)) ?></span>
        </div>
        <form method="POST" action="<?= url('admin/orders/' . $order->id . '/payment') ?>"
            style="padding:1.1rem 1.4rem;" data-confirm="Update payment status to the selected value?">
            <?= csrfField() ?>
            <div class="form-group" style="margin-bottom:0.9rem;">
                <label for="payment_status">New Payment Status</label>
                <select id="payment_status" name="payment_status" class="form-control">
                    <?php foreach ($paymentStatuses as $ps): ?>
                        <option value="<?= e($ps) ?>" <?= $order->payment_status === $ps ? 'selected' : '' ?>>
                            <?= ucfirst(e($ps)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success btn-sm">Update Payment</button>
        </form>
    </div>

</div>

<div class="form-actions">
    <a href="<?= url('admin/orders') ?>" class="btn btn-outline">&larr; Back to Orders</a>
</div>