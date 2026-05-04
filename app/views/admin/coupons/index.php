<!-- app/views/admin/coupons/index.php -->

<div class="page-header">
    <div>
        <h1 class="page-header-title">Coupons</h1>
        <p class="page-header-sub"><?= count($coupons) ?> coupon<?= count($coupons) !== 1 ? 's' : '' ?> configured</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= url('admin/coupons/create') ?>" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Coupon
        </a>
    </div>
</div>

<div class="card">
    <?php if (empty($coupons)): ?>
        <p class="empty-state">No coupons yet. <a href="<?= url('admin/coupons/create') ?>">Create one.</a></p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table table-cards">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Used</th>
                        <th>Min Order</th>
                        <th>Validity</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coupons as $coupon): ?>
                        <?php
                        $discountDisplay = $coupon->discount_type === 'percent'
                            ? e($coupon->discount_value) . '%'
                            : formatPrice($coupon->discount_value);

                        $minOrder = $coupon->min_order_amount
                            ? formatPrice($coupon->min_order_amount)
                            : '—';

                        $startsAt  = $coupon->starts_at  ? date('M j, Y', strtotime($coupon->starts_at))  : 'Any time';
                        $expiresAt = $coupon->expires_at ? date('M j, Y', strtotime($coupon->expires_at)) : 'Never';

                        $isExpired = $coupon->expires_at && strtotime($coupon->expires_at) < time();
                        ?>
                        <tr class="<?= $isExpired ? 'row-expired' : '' ?>"
                            data-href="<?= url('admin/coupons/edit/' . $coupon->id) ?>">

                            <!-- Code — primary cell -->
                            <td class="tc-primary">
                                <code style="font-size:0.9em;font-weight:700;letter-spacing:0.05em;"><?= e($coupon->code) ?></code>
                                <div class="td-muted" style="font-size:0.72rem;margin-top:0.15rem;"><?= $coupon->discount_type ?></div>
                            </td>

                            <!-- Discount -->
                            <td data-label="Discount"><strong><?= $discountDisplay ?></strong></td>

                            <!-- Usage count -->
                            <td data-label="Used">
                                <span class="badge <?= (int)$coupon->usage_count > 0 ? 'badge--info' : 'badge--muted' ?>">
                                    <?= (int)$coupon->usage_count ?>×
                                </span>
                            </td>

                            <!-- Min Order -->
                            <td data-label="Min Order" class="tc-hide"><?= $minOrder ?></td>

                            <!-- Validity -->
                            <td data-label="Expires" class="tc-hide" style="font-size:0.82rem;">
                                <?= $expiresAt ?>
                            </td>

                            <!-- Status -->
                            <td data-label="Status">
                                <?php if ($isExpired): ?>
                                    <span class="badge badge--warning">Expired</span>
                                <?php elseif ($coupon->is_active): ?>
                                    <span class="badge badge--success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge--muted">Disabled</span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td class="tc-actions">
                                <div class="table-actions">
                                    <a href="<?= url('admin/coupons/edit/' . $coupon->id) ?>"
                                       class="btn btn-xs btn-outline"
                                       onclick="event.stopPropagation()">Edit</a>
                                    <form method="POST" action="<?= url('admin/coupons/delete/' . $coupon->id) ?>"
                                          class="inline-form"
                                          onclick="event.stopPropagation()"
                                          onsubmit="return confirm('Delete coupon &quot;<?= e($coupon->code) ?>&quot;?')">
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
