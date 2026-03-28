<!-- app/views/admin/settings/index.php -->

<?php
$s   = $settings ?? [];
$tab = $tab ?? 'store';

function sv(array $s, string $key, string $default = ''): string {
    return htmlspecialchars($s[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}
?>

<!-- ── Tab Navigation ───────────────────────────────────────── -->
<div class="filter-tabs" style="margin-bottom:1.5rem;">
    <a href="<?= url('admin/settings?tab=store') ?>"
       class="filter-tab <?= $tab === 'store'    ? 'filter-tab--active' : '' ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Store
    </a>
    <a href="<?= url('admin/settings?tab=account') ?>"
       class="filter-tab <?= $tab === 'account'  ? 'filter-tab--active' : '' ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
        My Account
    </a>
    <a href="<?= url('admin/settings?tab=security') ?>"
       class="filter-tab <?= $tab === 'security' ? 'filter-tab--active' : '' ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
        </svg>
        Security
    </a>
</div>


<!-- ═══════════════════════════════════════════════════════════
     TAB: Store Settings
═══════════════════════════════════════════════════════════ -->
<?php if ($tab === 'store'): ?>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Store Settings</h2>
    </div>

    <form method="POST" action="<?= url('admin/settings/store') ?>">
        <?= csrfField() ?>
        <div class="form-grid">
            <div class="form-main">

                <div class="form-group">
                    <label for="store_name">Store Name <span class="req">*</span></label>
                    <input type="text" id="store_name" name="store_name" class="form-control"
                           value="<?= sv($s, 'store_name', 'Clothy Outlet') ?>" required maxlength="100">
                    <p class="form-hint">Displayed in the browser tab and across the storefront.</p>
                </div>

                <div class="form-group">
                    <label for="store_tagline">Tagline / Subtitle</label>
                    <input type="text" id="store_tagline" name="store_tagline" class="form-control"
                           value="<?= sv($s, 'store_tagline') ?>" maxlength="120" placeholder="e.g. Fashion for Everyone">
                </div>

                <div class="form-row">
                    <div class="form-group form-group--half">
                        <label for="store_email">Contact Email</label>
                        <input type="email" id="store_email" name="store_email" class="form-control"
                               value="<?= sv($s, 'store_email') ?>" maxlength="180" placeholder="contact@example.com">
                    </div>
                    <div class="form-group form-group--half">
                        <label for="store_phone">Contact Phone</label>
                        <input type="text" id="store_phone" name="store_phone" class="form-control"
                               value="<?= sv($s, 'store_phone') ?>" maxlength="30" placeholder="+1 555-0100">
                    </div>
                </div>

                <div class="form-group">
                    <label for="store_address">Store Address</label>
                    <textarea id="store_address" name="store_address" class="form-control form-textarea"
                              rows="2" maxlength="300" placeholder="Street, City, Country"><?= sv($s, 'store_address') ?></textarea>
                </div>

            </div><!-- /.form-main -->

            <div class="form-side">

                <div class="form-group">
                    <label for="currency_symbol">Currency Symbol</label>
                    <input type="text" id="currency_symbol" name="currency_symbol" class="form-control"
                           value="<?= sv($s, 'currency_symbol', '$') ?>" maxlength="5" style="max-width:90px;">
                    <p class="form-hint">Shown next to prices (e.g. $ € £ ¥).</p>
                </div>

                <div class="form-group">
                    <label for="products_per_page">Products per Page</label>
                    <input type="number" id="products_per_page" name="products_per_page" class="form-control"
                           value="<?= sv($s, 'products_per_page', '12') ?>" min="1" max="100" style="max-width:110px;">
                    <p class="form-hint">Number of products shown per page in the storefront listing.</p>
                </div>

                <div class="form-group">
                    <label for="low_stock_threshold">Low Stock Threshold</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold" class="form-control"
                           value="<?= sv($s, 'low_stock_threshold', '10') ?>" min="0" max="1000" style="max-width:110px;">
                    <p class="form-hint">Products with stock at or below this number appear in Low Stock alerts.</p>
                </div>

            </div><!-- /.form-side -->
        </div><!-- /.form-grid -->

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Store Settings</button>
        </div>
    </form>
</div>


<!-- ═══════════════════════════════════════════════════════════
     TAB: My Account
═══════════════════════════════════════════════════════════ -->
<?php elseif ($tab === 'account'): ?>
<div class="card card--narrow">
    <div class="card-header">
        <h2 class="card-title">My Account</h2>
    </div>

    <form method="POST" action="<?= url('admin/settings/account') ?>">
        <?= csrfField() ?>
        <div class="form-main">

            <div class="form-group">
                <label for="name">Full Name <span class="req">*</span></label>
                <input type="text" id="name" name="name" class="form-control"
                       value="<?= e($adminUser['name'] ?? '') ?>" required maxlength="100">
            </div>

            <div class="form-group">
                <label for="email">Email Address <span class="req">*</span></label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= e($adminUser['email'] ?? '') ?>" required maxlength="180">
            </div>

            <div class="form-group">
                <label>Role</label>
                <p style="padding:0.58rem 0.8rem;background:#f8fafc;border:1px solid var(--adm-border);border-radius:8px;font-size:0.875rem;color:var(--adm-text-2);">
                    <?= e(ucwords(str_replace('_', ' ', $adminUser['admin_role'] ?? 'admin'))) ?>
                    <span class="form-hint" style="display:block;margin-top:0.2rem;">Role is assigned by a super admin and cannot be changed here.</span>
                </p>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Account Details</button>
        </div>
    </form>
</div>


<!-- ═══════════════════════════════════════════════════════════
     TAB: Security
═══════════════════════════════════════════════════════════ -->
<?php elseif ($tab === 'security'): ?>
<div class="card card--narrow">
    <div class="card-header">
        <h2 class="card-title">Change Password</h2>
    </div>

    <form method="POST" action="<?= url('admin/settings/password') ?>">
        <?= csrfField() ?>
        <div class="form-main">

            <div class="form-group">
                <label for="current_password">Current Password <span class="req">*</span></label>
                <input type="password" id="current_password" name="current_password"
                       class="form-control" autocomplete="current-password" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password <span class="req">*</span></label>
                <input type="password" id="new_password" name="new_password"
                       class="form-control" autocomplete="new-password" required minlength="8">
                <p class="form-hint">Minimum 8 characters.</p>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password <span class="req">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password"
                       class="form-control" autocomplete="new-password" required minlength="8">
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-danger">Change Password</button>
            <span class="form-hint">You will remain logged in after changing your password.</span>
        </div>
    </form>
</div>
<?php endif; ?>
