<!-- ============================================================
     app/views/partials/navbar.php
     Site navigation — rendered by the main layout.
     NOTE: No SQL here. Data should be passed via the controller.
     ============================================================ -->
<header class="site-header">
    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <a class="navbar-brand" href="<?= url() ?>"><?= e(APP_NAME) ?></a>

        <ul class="nav-links">
            <li><a href="<?= url() ?>">Home</a></li>
            <li><a href="<?= url('products') ?>">Shop</a></li>
        </ul>

        <div class="nav-actions">
            <a href="<?= url('cart') ?>" class="btn-cart" aria-label="Cart">🛒</a>

            <?php if (Session::isLoggedIn()): ?>
                <a href="<?= url('account/wishlist') ?>">Wishlist</a>
                <a href="<?= url('account') ?>">My Account</a>
                <a href="<?= url('logout') ?>">Logout</a>
            <?php else: ?>
                <a href="<?= url('login') ?>" class="btn btn-outline">Login</a>
                <a href="<?= url('register') ?>" class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>