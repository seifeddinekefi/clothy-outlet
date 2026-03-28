<?php

/**
 * app/views/auth/reset-password.php
 */
$_token = $token ?? '';
$_email = $email ?? '';
?>

<div style="max-width:520px;margin:120px auto 40px;padding:24px;background:#fff;border:1px solid #e8e6e2;border-radius:10px;">
  <h1 style="font-family:Georgia,serif;font-weight:400;margin:0 0 10px;">Reset Password</h1>
  <p style="color:#7a7570;font-size:.9rem;margin:0 0 18px;">Set a new password for <strong><?= htmlspecialchars($_email) ?></strong>.</p>

  <form method="POST" action="<?= url('reset-password/' . $_token) ?>">
    <?= csrfField() ?>

    <label for="password" style="display:block;font-size:.78rem;font-weight:600;margin-bottom:6px;color:#4a4743;">New Password</label>
    <input id="password" name="password" type="password" required minlength="8" placeholder="Minimum 8 characters" style="width:100%;padding:.7rem .85rem;border:1.5px solid #e8e6e2;border-radius:8px;margin-bottom:12px;box-sizing:border-box;">

    <label for="password_confirm" style="display:block;font-size:.78rem;font-weight:600;margin-bottom:6px;color:#4a4743;">Confirm Password</label>
    <input id="password_confirm" name="password_confirm" type="password" required minlength="8" placeholder="Repeat your password" style="width:100%;padding:.7rem .85rem;border:1.5px solid #e8e6e2;border-radius:8px;margin-bottom:14px;box-sizing:border-box;">

    <button type="submit" style="width:100%;padding:.9rem;background:#0a0a0a;color:#fff;border:none;border-radius:8px;text-transform:uppercase;font-size:.72rem;letter-spacing:.12em;font-weight:700;cursor:pointer;">Update Password</button>
  </form>
</div>