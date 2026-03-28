<?php

/**
 * app/views/auth/forgot-password.php
 */
?>

<div style="max-width:520px;margin:120px auto 40px;padding:24px;background:#fff;border:1px solid #e8e6e2;border-radius:10px;">
  <h1 style="font-family:Georgia,serif;font-weight:400;margin:0 0 10px;">Forgot Password</h1>
  <p style="color:#7a7570;font-size:.9rem;margin:0 0 18px;">Enter your account email and we will generate a reset link.</p>

  <form method="POST" action="<?= url('forgot-password') ?>">
    <?= csrfField() ?>
    <label for="email" style="display:block;font-size:.78rem;font-weight:600;margin-bottom:6px;color:#4a4743;">Email</label>
    <input id="email" name="email" type="email" required placeholder="you@example.com" style="width:100%;padding:.7rem .85rem;border:1.5px solid #e8e6e2;border-radius:8px;margin-bottom:14px;box-sizing:border-box;">

    <button type="submit" style="width:100%;padding:.9rem;background:#0a0a0a;color:#fff;border:none;border-radius:8px;text-transform:uppercase;font-size:.72rem;letter-spacing:.12em;font-weight:700;cursor:pointer;">Generate Reset Link</button>
  </form>

  <p style="margin-top:14px;font-size:.82rem;"><a href="<?= url('login') ?>" style="color:#0a0a0a;">Back to login</a></p>
</div>