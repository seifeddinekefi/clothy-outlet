<!DOCTYPE html>
<html lang="<?= APP_LOCALE ?>">

<head>
    <meta charset="<?= APP_CHARSET ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ffffff">
    <title><?= e($pageTitle ?? APP_NAME) ?></title>
    <meta name="description" content="<?= e($metaDescription ?? '') ?>">

    <!-- ── Favicon ─────────────────────────────────────────── -->
    <link rel="icon" href="<?= $view->asset('images/favicon.ico') ?>" type="image/x-icon">

    <!-- ── CSS ─────────────────────────────────────────────── -->
    <link rel="stylesheet" href="<?= $view->asset('css/app.css') ?>">

    <!-- ── Yield: extra <head> content from view ──────────── -->
    <?= $view->yield('head') ?>
</head>

<body>

    <!-- ── Navigation ──────────────────────────────────────── -->
    <?= $view->partial('partials.navbar') ?>

    <!-- ── Flash Messages ──────────────────────────────────── -->
    <?= $view->partial('partials.flash') ?>

    <!-- ── Main Content ────────────────────────────────────── -->
    <main id="main-content">
        <?= $view->yield('content', $_view_content ?? '') ?>
    </main>

    <!-- ── Footer ──────────────────────────────────────────── -->
    <?= $view->yield('footer', $view->partial('partials.footer')) ?>

    <!-- ── JS ──────────────────────────────────────────────── -->
    <script src="<?= $view->asset('js/app.js') ?>"></script>

    <!-- ── Yield: extra scripts from view ─────────────────── -->
    <?= $view->yield('scripts') ?>

</body>

</html>