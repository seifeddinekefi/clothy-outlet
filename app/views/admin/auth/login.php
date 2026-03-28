<!DOCTYPE html>
<html lang="<?= APP_LOCALE ?>">

<head>
    <meta charset="<?= APP_CHARSET ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin Login') ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>">
</head>

<body class="admin-login-page">

    <?php
    // Render flash messages without a layout helper
    $flash = $_flash ?? Session::getFlash();
    if (!empty($flash)):
    ?>
        <div class="flash-container login-flash" role="alert">
            <?php foreach ($flash as $type => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="flash flash--<?= e($type) ?>"><?= e($message) ?></div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="login-card">
        <div class="login-brand">
            <span class="login-logo"><?= e(APP_NAME) ?></span>
            <span class="login-label">Admin Panel</span>
        </div>

        <form class="login-form" method="POST" action="<?= url('admin/login') ?>" autocomplete="off" novalidate>
            <?= csrfField() ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    class="form-control"
                    value="<?= e($_POST['email'] ?? '') ?>"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </form>
    </div>

</body>

</html>