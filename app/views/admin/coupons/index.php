<!-- app/views/admin/coupons/index.php -->

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Coupons <span class="badge-count"><?= count($coupons) ?></span></h2>
        <a href="<?= url('admin/coupons/create') ?>" class="btn btn-primary btn-sm">+ Add Coupon</a>
    </div>

    <?php if (empty($coupons)): ?>
        <p class="empty-state">No coupons yet. <a href="<?= url('admin/coupons/create') ?>">Create one.</a></p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Min Order</th>
                        <th>Max Discount</th>
                        <th>Validity</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coupons as $coupon): ?>
                        <?php
                        $discountDisplay = $coupon->discount_type === 'percent'
                            ? e($coupon->discount_value) . '%'
                            : '$' . number_format($coupon->discount_value, 2);

                        $minOrder = $coupon->min_order_amount
                            ? '$' . number_format($coupon->min_order_amount, 2)
                            : '—';

                        $maxDiscount = $coupon->max_discount_amount
                            ? '$' . number_format($coupon->max_discount_amount, 2)
                            : '—';

                        $startsAt = $coupon->starts_at ? date('M j, Y', strtotime($coupon->starts_at)) : 'Any time';
                        $expiresAt = $coupon->expires_at ? date('M j, Y', strtotime($coupon->expires_at)) : 'Never';
                        $validity = $startsAt . ' — ' . $expiresAt;

                        $isExpired = $coupon->expires_at && strtotime($coupon->expires_at) < time();
                        ?>
                        <tr class="<?= $isExpired ? 'row-muted' : '' ?>">
                            <td><code class="coupon-code"><?= e($coupon->code) ?></code></td>
                            <td><strong><?= $discountDisplay ?></strong> <small>(<?= $coupon->discount_type ?>)</small></td>
                            <td><?= $minOrder ?></td>
                            <td><?= $maxDiscount ?></td>
                            <td class="td-small"><?= $validity ?></td>
                            <td>
                                <?php if ($isExpired): ?>
                                    <span class="badge badge--warning">Expired</span>
                                <?php elseif ($coupon->is_active): ?>
                                    <span class="badge badge--success">Yes</span>
                                <?php else: ?>
                                    <span class="badge badge--danger">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="td-actions">
                                <a href="<?= url('admin/coupons/edit/' . $coupon->id) ?>" class="btn btn-xs btn-outline">Edit</a>
                                <form method="POST" action="<?= url('admin/coupons/delete/' . $coupon->id) ?>"
                                    class="inline-form"
                                    onsubmit="return confirm('Delete coupon &quot;<?= e($coupon->code) ?>&quot;?')">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
    .coupon-code {
        font-size: 0.9em;
        background: var(--bg-tertiary, #f5f5f5);
        padding: 0.25em 0.5em;
        border-radius: 4px;
        font-weight: 600;
    }

    .row-muted {
        opacity: 0.6;
    }

    .td-small {
        font-size: 0.85em;
        color: var(--text-muted, #666);
    }

    .badge--warning {
        background: #f59e0b;
        color: white;
    }
</style>