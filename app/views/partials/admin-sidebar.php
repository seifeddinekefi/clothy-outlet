<!-- ============================================================
     app/views/partials/admin-sidebar.php
     Admin panel sidebar navigation.
     ============================================================ -->
<?php
// Determine current URI segment to highlight active nav item
$_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$_dir = dirname($_SERVER['SCRIPT_NAME']);
if ($_dir !== '/' && str_starts_with($_uri, $_dir)) {
    $_uri = substr($_uri, strlen($_dir));
}
$_uri = '/' . trim($_uri, '/');

function _sidebar_active(string $prefix, string $uri): string
{
    return str_starts_with($uri, $prefix) ? 'active' : '';
}
?>
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-brand">
        <a href="<?= url('admin') ?>">
            <span class="brand-name"><?= e(APP_NAME) ?></span>
            <span class="brand-tag">Admin</span>
        </a>
        <button class="sidebar-close" id="sidebar-close" aria-label="Close sidebar">&times;</button>
    </div>

    <nav class="sidebar-nav" role="navigation" aria-label="Admin navigation">
        <ul>
            <!-- Main -->
            <li class="<?= _sidebar_active('/admin/dashboard', $_uri) ?: (_sidebar_active('/admin', $_uri) && !str_starts_with($_uri, '/admin/products') && !str_starts_with($_uri, '/admin/categories') && !str_starts_with($_uri, '/admin/orders') && !str_starts_with($_uri, '/admin/customers') && !str_starts_with($_uri, '/admin/coupons') && !str_starts_with($_uri, '/admin/settings') && !str_starts_with($_uri, '/admin/subscribers') ? 'active' : '') ?>">
                <a href="<?= url('admin') ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="9" />
                        <rect x="14" y="3" width="7" height="5" />
                        <rect x="14" y="12" width="7" height="9" />
                        <rect x="3" y="16" width="7" height="5" />
                    </svg>
                    Dashboard
                </a>
            </li>

            <!-- Catalog -->
            <li class="nav-group-label" role="presentation">Catalog</li>

            <li class="<?= _sidebar_active('/admin/products', $_uri) ?>">
                <a href="<?= url('admin/products') ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <path d="M16 10a4 4 0 01-8 0" />
                    </svg>
                    Products
                </a>
            </li>
            <li class="<?= _sidebar_active('/admin/categories', $_uri) ?>">
                <a href="<?= url('admin/categories') ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    Categories
                </a>
            </li>

            <!-- Commerce -->
            <li class="nav-group-label" role="presentation">Commerce</li>

            <li class="<?= _sidebar_active('/admin/orders', $_uri) ?>">
                <a href="<?= url('admin/orders') ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 17H7A5 5 0 017 7h10a5 5 0 010 10h-1" />
                        <path d="M9 12l2 2 4-4" />
                    </svg>
                    Orders
                    <?php if (!empty($pendingOrdersCount) && $pendingOrdersCount > 0): ?>
                        <span class="nav-badge"><?= (int) $pendingOrdersCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="<?= _sidebar_active('/admin/customers', $_uri) ?>">
                <a href="<?= url('admin/customers') ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87" />
                        <path d="M16 3.13a4 4 0 010 7.75" />
                    </svg>
                    Customers
                </a>
            </li>
            <li class="<?= _sidebar_active('/admin/coupons', $_uri) ?>">
                <a href="<?= url('admin/coupons') ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 12v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6" />
                        <path d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2" />
                        <line x1="12" y1="4" x2="12" y2="20" />
                        <path d="M8 12h.01" />
                        <path d="M16 12h.01" />
                    </svg>
                    Coupons
                </a>
            </li>

            <!-- Admin -->
            <li class="nav-group-label" role="presentation">Admin</li>

            <li class="<?= _sidebar_active('/admin/subscribers', $_uri) ?>">
                <a href="<?= url('admin/subscribers') ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    Subscribers
                </a>
            </li>
            <li class="<?= _sidebar_active('/admin/settings', $_uri) ?>">
                <a href="<?= url('admin/settings') ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                    </svg>
                    Settings
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <?php if (!empty($adminUser)): ?>
            <div class="sidebar-user">
                <span class="sidebar-user-avatar"><?= strtoupper(substr($adminUser['name'] ?? 'A', 0, 1)) ?></span>
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name"><?= e($adminUser['name'] ?? $adminUser['email'] ?? 'Admin') ?></span>
                    <?php
                    $__sideRole = str_replace('_', ' ', $adminUser['admin_role'] ?? '');
                    if ($__sideRole):
                    ?>
                        <span class="sidebar-user-role"><?= e(ucwords($__sideRole)) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <a href="<?= url('admin/logout') ?>" class="sidebar-logout">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                <polyline points="16 17 21 12 16 7" />
                <line x1="21" y1="12" x2="9" y2="12" />
            </svg>
            Logout
        </a>
    </div>
</aside>