<!DOCTYPE html>
<html lang="<?= APP_LOCALE ?>">

<head>
    <meta charset="<?= APP_CHARSET ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin — ' . APP_NAME) ?></title>
    <meta name="robots" content="noindex, nofollow">

    <!-- ── Fonts ────────────────────────────────────────────── -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- ── CSS ─────────────────────────────────────────────── -->
    <link rel="stylesheet" href="<?= $view->asset('css/admin.css') ?>">
    <?= $view->yield('head') ?>
</head>

<body class="admin-body">

    <!-- ── Admin Sidebar ───────────────────────────────────── -->
    <?= $view->partial('partials.admin-sidebar', ['adminUser' => $adminUser ?? null, 'pendingOrdersCount' => $pendingOrdersCount ?? 0]) ?>

    <!-- Mobile overlay (shown via JS class) -->
    <div id="sidebar-overlay"></div>

    <div class="admin-wrapper">

        <!-- ── Admin Top Bar ─────────────────────────────────── -->
        <?= $view->partial('partials.admin-topbar') ?>

        <!-- ── Flash Messages ────────────────────────────────── -->
        <?= $view->partial('partials.flash') ?>

        <!-- ── Admin Main Content ────────────────────────────── -->
        <main class="admin-content">
            <?= $_view_content ?? '' ?>
        </main>

    </div><!-- /.admin-wrapper -->

    <!-- ── JS ──────────────────────────────────────────────── -->
    <script src="<?= $view->asset('js/admin.js') ?>"></script>
    <?= $view->yield('scripts') ?>

</body>

</html>