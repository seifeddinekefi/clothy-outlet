<!-- ============================================================
     app/views/partials/admin-topbar.php
     Admin top navigation bar.
     ============================================================ -->
<?php $__topbarUser = $adminUser ?? Session::user(); ?>
<div class="admin-topbar">
    <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar" aria-controls="admin-sidebar">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
    </button>

    <span class="topbar-title"><?= e($pageTitle ?? 'Dashboard') ?></span>

    <div class="topbar-right">
        <!-- View storefront -->
        <a href="<?= url('') ?>" class="topbar-store-link" target="_blank" rel="noopener" title="View storefront">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                <polyline points="15 3 21 3 21 9"/>
                <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
            <span>Store</span>
        </a>

        <!-- Quick action shortcut -->
        <a href="<?= url('admin/products/create') ?>" class="topbar-quick-btn" title="Add new product">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            <span>New Product</span>
        </a>

        <div class="topbar-divider"></div>

        <?php if ($__topbarUser): ?>
            <div class="topbar-user">
                <span class="topbar-avatar"><?= strtoupper(substr($__topbarUser['name'] ?? 'A', 0, 1)) ?></span>
                <div class="topbar-user-info d-sm">
                    <span class="topbar-name"><?= e($__topbarUser['name'] ?? $__topbarUser['email'] ?? 'Admin') ?></span>
                    <?php
                    $__role = str_replace('_', ' ', $__topbarUser['admin_role'] ?? 'admin');
                    if ($__role):
                    ?>
                        <span class="topbar-role"><?= e(ucwords($__role)) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <a href="<?= url('admin/logout') ?>" class="topbar-logout" title="Logout">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                <polyline points="16 17 21 12 16 7" />
                <line x1="21" y1="12" x2="9" y2="12" />
            </svg>
        </a>
    </div>
</div>