<!-- app/views/admin/coupons/edit.php -->

<div class="card card--narrow">
    <div class="card-header">
        <h2 class="card-title">Edit Coupon: <?= e($coupon->code) ?></h2>
        <a href="<?= url('admin/coupons') ?>" class="btn btn-sm btn-outline">&larr; Back</a>
    </div>

    <form method="POST" action="<?= url('admin/coupons/edit/' . $coupon->id) ?>" novalidate>
        <?= csrfField() ?>

        <div class="form-group">
            <label for="code">Coupon Code <span class="req">*</span></label>
            <input id="code" type="text" name="code" class="form-control"
                value="<?= e($_POST['code'] ?? $coupon->code) ?>" required maxlength="50"
                placeholder="e.g. WELCOME10" style="text-transform: uppercase;">
            <small class="form-hint">Code will be auto-converted to uppercase.</small>
        </div>

        <div class="form-row">
            <div class="form-group form-group--half">
                <label for="discount_type">Discount Type <span class="req">*</span></label>
                <select id="discount_type" name="discount_type" class="form-control">
                    <?php $currentType = $_POST['discount_type'] ?? $coupon->discount_type; ?>
                    <option value="percent" <?= $currentType === 'percent' ? 'selected' : '' ?>>Percentage (%)</option>
                    <option value="fixed" <?= $currentType === 'fixed' ? 'selected' : '' ?>>Fixed Amount ($)</option>
                </select>
            </div>
            <div class="form-group form-group--half">
                <label for="discount_value">Discount Value <span class="req">*</span></label>
                <input id="discount_value" type="number" name="discount_value" class="form-control"
                    value="<?= e($_POST['discount_value'] ?? $coupon->discount_value) ?>" required min="0.01" step="0.01"
                    placeholder="e.g. 10">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group--half">
                <label for="min_order_amount">Minimum Order Amount</label>
                <input id="min_order_amount" type="number" name="min_order_amount" class="form-control"
                    value="<?= e($_POST['min_order_amount'] ?? $coupon->min_order_amount ?? '') ?>" min="0" step="0.01"
                    placeholder="e.g. 50.00">
                <small class="form-hint">Leave empty for no minimum.</small>
            </div>
            <div class="form-group form-group--half">
                <label for="max_discount_amount">Maximum Discount Cap</label>
                <input id="max_discount_amount" type="number" name="max_discount_amount" class="form-control"
                    value="<?= e($_POST['max_discount_amount'] ?? $coupon->max_discount_amount ?? '') ?>" min="0" step="0.01"
                    placeholder="e.g. 30.00">
                <small class="form-hint">Caps percentage discounts.</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group--half">
                <label for="starts_at">Valid From</label>
                <?php
                $startsAt = $_POST['starts_at'] ?? ($coupon->starts_at ? date('Y-m-d\TH:i', strtotime($coupon->starts_at)) : '');
                ?>
                <input id="starts_at" type="datetime-local" name="starts_at" class="form-control"
                    value="<?= e($startsAt) ?>">
                <small class="form-hint">Leave empty for immediate start.</small>
            </div>
            <div class="form-group form-group--half">
                <label for="expires_at">Expires At</label>
                <?php
                $expiresAt = $_POST['expires_at'] ?? ($coupon->expires_at ? date('Y-m-d\TH:i', strtotime($coupon->expires_at)) : '');
                ?>
                <input id="expires_at" type="datetime-local" name="expires_at" class="form-control"
                    value="<?= e($expiresAt) ?>">
                <small class="form-hint">Leave empty for no expiration.</small>
            </div>
        </div>

        <div class="form-group form-group--center-v">
            <label class="checkbox-label">
                <?php $isActive = isset($_POST['is_active']) ? $_POST['is_active'] : $coupon->is_active; ?>
                <input type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?>>
                Active
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= url('admin/coupons') ?>" class="btn btn-outline">Cancel</a>
        </div>

    </form>
</div>

<style>
    .form-hint {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.8em;
        color: var(--text-muted, #888);
    }
</style>