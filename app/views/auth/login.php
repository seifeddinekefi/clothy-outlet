<?php

/**
 * app/views/auth/login.php
 * Customer login page.
 */
$old = Session::get('_old_login', []);
Session::delete('_old_login');
?>
<?php $view->startSection('head') ?>
<style>
  /* ── Layout ── */
  .auth-wrap {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 1fr;
  }

  /* ── Left panel ── */
  .auth-panel {
    background: #0a0a0a;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 3rem;
    position: relative;
    overflow: hidden;
  }

  .auth-panel::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
      radial-gradient(ellipse at 20% 80%, rgba(196, 169, 122, .18) 0%, transparent 55%),
      radial-gradient(ellipse at 80% 10%, rgba(196, 169, 122, .10) 0%, transparent 50%);
    pointer-events: none;
  }

  .panel-brand {
    font-family: Georgia, serif;
    font-size: 1.4rem;
    letter-spacing: .22em;
    text-transform: uppercase;
    color: #fff;
    text-decoration: none;
  }

  .panel-quote {
    position: relative;
    z-index: 1;
  }

  .panel-quote blockquote {
    font-family: Georgia, serif;
    font-size: clamp(1.5rem, 2.5vw, 2.1rem);
    font-weight: normal;
    color: #fff;
    line-height: 1.45;
    margin: 0 0 1.25rem;
  }

  .panel-quote cite {
    font-size: .75rem;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: #c4a97a;
    font-style: normal;
  }

  .panel-dots {
    display: flex;
    gap: .45rem;
  }

  .panel-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .25);
  }

  .panel-dot.active {
    background: #c4a97a;
    width: 22px;
    border-radius: 3px;
  }

  /* ── Right / form side ── */
  .auth-form-side {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fafaf9;
    padding: 3rem 2rem;
  }

  .auth-box {
    width: 100%;
    max-width: 420px;
    animation: authFadeUp .4s ease both;
  }

  @keyframes authFadeUp {
    from {
      opacity: 0;
      transform: translateY(16px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .auth-box-header {
    margin-bottom: 2.25rem;
  }

  .auth-step-label {
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .2em;
    text-transform: uppercase;
    color: #c4a97a;
    display: block;
    margin-bottom: .6rem;
  }

  .auth-heading {
    font-family: Georgia, serif;
    font-size: 2rem;
    font-weight: normal;
    color: #0a0a0a;
    margin: 0 0 .4rem;
    line-height: 1.15;
  }

  .auth-sub {
    font-size: .85rem;
    color: #7a7570;
    margin: 0;
  }

  /* ── Field ── */
  .field {
    margin-bottom: 1.15rem;
    position: relative;
  }

  .field-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .11em;
    text-transform: uppercase;
    color: #7a7570;
    margin-bottom: .45rem;
  }

  .field-label a {
    font-weight: 500;
    text-transform: none;
    letter-spacing: 0;
    font-size: .75rem;
    color: #c4a97a;
    text-decoration: none;
    transition: color .15s;
  }

  .field-label a:hover {
    color: #a88650;
  }

  .field-input-wrap {
    position: relative;
  }

  .field-icon {
    position: absolute;
    left: .9rem;
    top: 50%;
    transform: translateY(-50%);
    color: #b0aca6;
    pointer-events: none;
    display: flex;
    align-items: center;
  }

  .field-input {
    display: block;
    width: 100%;
    padding: .82rem 2.8rem .82rem 2.75rem;
    border: 1.5px solid #e8e6e2;
    border-radius: 8px;
    font-size: .9rem;
    background: #fff;
    color: #0a0a0a;
    transition: border-color .18s, box-shadow .18s;
    outline: none;
    box-sizing: border-box;
  }

  .field-input::placeholder {
    color: #c0bbb6;
  }

  .field-input:focus {
    border-color: #0a0a0a;
    box-shadow: 0 0 0 3px rgba(10, 10, 10, .07);
  }

  .field-input.has-error {
    border-color: #d94f4f;
    box-shadow: 0 0 0 3px rgba(217, 79, 79, .08);
  }

  .pwd-toggle {
    position: absolute;
    right: .85rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: .25rem;
    color: #b0aca6;
    display: flex;
    align-items: center;
    transition: color .15s;
  }

  .pwd-toggle:hover {
    color: #0a0a0a;
  }

  /* ── Remember / extras row ── */
  .field-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.4rem;
    font-size: .8rem;
  }

  .check-label {
    display: flex;
    align-items: center;
    gap: .45rem;
    color: #7a7570;
    cursor: pointer;
    user-select: none;
  }

  .check-label input[type=checkbox] {
    width: 15px;
    height: 15px;
    accent-color: #0a0a0a;
    cursor: pointer;
  }

  /* ── Submit button ── */
  .btn-auth {
    display: block;
    width: 100%;
    padding: .95rem;
    background: #0a0a0a;
    color: #fff;
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: .16em;
    text-transform: uppercase;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background .2s, transform .15s, box-shadow .2s;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
  }

  .btn-auth:hover {
    background: #2a2a2a;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(10, 10, 10, .18);
  }

  .btn-auth:active {
    transform: translateY(0);
  }

  /* ── Switch ── */
  .auth-switch {
    text-align: center;
    font-size: .82rem;
    color: #7a7570;
  }

  .auth-switch a {
    color: #0a0a0a;
    font-weight: 700;
    text-decoration: none;
    border-bottom: 1.5px solid #0a0a0a;
    padding-bottom: 1px;
    transition: opacity .15s;
  }

  .auth-switch a:hover {
    opacity: .6;
  }

  /* ── Responsive ── */
  @media (max-width: 768px) {
    .auth-wrap {
      grid-template-columns: 1fr;
    }

    .auth-panel {
      display: none;
    }

    .auth-form-side {
      min-height: 100vh;
      padding: 2rem 1.25rem;
    }
  }
</style>
<?php $view->endSection() ?>

<div class="auth-wrap">

  <!-- Left brand panel -->
  <div class="auth-panel">
    <a href="<?= url() ?>" class="panel-brand">Clothy</a>

    <div class="panel-quote">
      <blockquote>&ldquo;Style is a way to say who you are without having to speak.&rdquo;</blockquote>
      <cite>Rachel Zoe</cite>
    </div>

    <div class="panel-dots">
      <div class="panel-dot active"></div>
      <div class="panel-dot"></div>
      <div class="panel-dot"></div>
    </div>
  </div>

  <!-- Right form panel -->
  <div class="auth-form-side">
    <div class="auth-box">

      <div class="auth-box-header">
        <span class="auth-step-label">Welcome back</span>
        <h1 class="auth-heading">Sign in to<br>your account</h1>
        <p class="auth-sub">Enter your credentials to continue shopping.</p>
      </div>

      <form method="POST" action="<?= url('login') ?>" novalidate>
        <?= csrfField() ?>

        <!-- Email -->
        <div class="field">
          <div class="field-label">
            <span>Email address</span>
          </div>
          <div class="field-input-wrap">
            <span class="field-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="4" width="20" height="16" rx="2" />
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
              </svg>
            </span>
            <input class="field-input" type="email" id="email" name="email"
              required autocomplete="email" placeholder="you@example.com"
              value="<?= htmlspecialchars($old['email'] ?? '') ?>">
          </div>
        </div>

        <!-- Password -->
        <div class="field">
          <div class="field-label">
            <span>Password</span>
            <a href="<?= url('forgot-password') ?>">Forgot password?</a>
          </div>
          <div class="field-input-wrap">
            <span class="field-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
            </span>
            <input class="field-input" type="password" id="loginPwd" name="password"
              required autocomplete="current-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
            <button type="button" class="pwd-toggle" onclick="togglePwd('loginPwd', this)" aria-label="Show password">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="icon-eye">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Remember me -->
        <div class="field-row">
          <label class="check-label">
            <input type="checkbox" name="remember">
            Remember me for 30 days
          </label>
        </div>

        <button class="btn-auth" type="submit">Sign In &rarr;</button>
      </form>

      <p class="auth-switch">New to Clothy? <a href="<?= url('register') ?>">Create an account</a></p>

    </div>
  </div>

</div>

<?php $view->startSection('footer') ?><?php $view->endSection() ?>

<?php $view->startSection('scripts') ?>
<script>
  function togglePwd(inputId, btn) {
    var input = document.getElementById(inputId);
    var isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    var eye = btn.querySelector('.icon-eye');
    eye.style.opacity = isText ? '1' : '.4';
  }
</script>
<?php $view->endSection() ?>