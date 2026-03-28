<!-- ============================================================
     app/views/errors/404.php
     404 Not Found error page.
     Layout is disabled (plain response) — Controller::notFound()
     sets setLayout(null) before rendering this file.
     ============================================================ -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found | <?= e(APP_NAME) ?></title>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: system-ui, sans-serif;
            background: #f8f8f8;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .error-box {
            text-align: center;
            max-width: 480px;
            padding: 2rem;
        }

        .error-code {
            font-size: 7rem;
            font-weight: 800;
            color: #e2e2e2;
            line-height: 1;
        }

        h1 {
            font-size: 1.5rem;
            margin: 0.5rem 0 1rem;
        }

        p {
            color: #666;
            margin-bottom: 1.5rem;
        }

        a {
            display: inline-block;
            padding: 0.6rem 1.4rem;
            background: #222;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        a:hover {
            background: #444;
        }
    </style>
</head>

<body>
    <div class="error-box">
        <div class="error-code">404</div>
        <h1>Page Not Found</h1>
        <p>The page you are looking for doesn't exist or has been moved.</p>
        <a href="<?= url() ?>">← Back to Home</a>
    </div>
</body>

</html>