<?php
/**
 * app/views/auth/register.php
 * Customer registration page.
 */
?>
<?php
// Old input is stored in a dedicated session key (not flash) so it survives
// exactly one redirect without being consumed by the flash partial.
$old = Session::get('_old_register', []);
Session::delete('_old_register');
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
      radial-gradient(ellipse at 20% 80%, rgba(196,169,122,.18) 0%, transparent 55%),
      radial-gradient(ellipse at 80% 10%, rgba(196,169,122,.10) 0%, transparent 50%);
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

  .panel-perks {
    position: relative;
    z-index: 1;
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
  }

  .panel-perk {
    display: flex;
    align-items: flex-start;
    gap: .85rem;
  }

  .perk-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: rgba(196,169,122,.15);
    border: 1px solid rgba(196,169,122,.25);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #c4a97a;
  }

  .perk-text strong {
    display: block;
    font-size: .85rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: .15rem;
  }

  .perk-text span {
    font-size: .75rem;
    color: rgba(255,255,255,.45);
    line-height: 1.5;
  }

  .panel-dots {
    display: flex;
    gap: .45rem;
  }

  .panel-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255,255,255,.25);
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
    overflow-y: auto;
  }

  .auth-box {
    width: 100%;
    max-width: 460px;
    animation: authFadeUp .4s ease both;
  }

  @keyframes authFadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .auth-box-header {
    margin-bottom: 2rem;
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
    margin-bottom: 1.1rem;
    position: relative;
  }

  .field-label {
    display: block;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .11em;
    text-transform: uppercase;
    color: #7a7570;
    margin-bottom: .45rem;
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

  .field-input.no-icon {
    padding-left: .95rem;
  }

  .field-input::placeholder { color: #c0bbb6; }

  .field-input:focus {
    border-color: #0a0a0a;
    box-shadow: 0 0 0 3px rgba(10,10,10,.07);
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

  .pwd-toggle:hover { color: #0a0a0a; }

  /* ── 2-col name row ── */
  .field-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  /* ── Password strength ── */
  .pwd-strength {
    margin-top: .45rem;
    display: flex;
    gap: .3rem;
    align-items: center;
  }

  .pwd-bar {
    height: 3px;
    border-radius: 2px;
    flex: 1;
    background: #e8e6e2;
    transition: background .25s;
  }

  .pwd-bar.active-1 { background: #d94f4f; }
  .pwd-bar.active-2 { background: #e8a83a; }
  .pwd-bar.active-3 { background: #3aaa5e; }

  .pwd-strength-label {
    font-size: .67rem;
    color: #b0aca6;
    white-space: nowrap;
    min-width: 3.5rem;
    text-align: right;
    transition: color .25s;
  }

  /* ── Terms ── */
  .terms-row {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    font-size: .8rem;
    color: #7a7570;
    margin-bottom: 1.35rem;
    line-height: 1.55;
  }

  .terms-row input[type=checkbox] {
    width: 15px;
    height: 15px;
    accent-color: #0a0a0a;
    margin-top: 2px;
    flex-shrink: 0;
    cursor: pointer;
  }

  .terms-row a {
    color: #0a0a0a;
    font-weight: 600;
    text-decoration: none;
    border-bottom: 1px solid rgba(10,10,10,.3);
    transition: border-color .15s;
  }

  .terms-row a:hover { border-color: #0a0a0a; }

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
  }

  .btn-auth:hover {
    background: #2a2a2a;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(10,10,10,.18);
  }

  .btn-auth:active { transform: translateY(0); }

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

  .auth-switch a:hover { opacity: .6; }

  /* ── Responsive ── */
  @media (max-width: 768px) {
    .auth-wrap { grid-template-columns: 1fr; }
    .auth-panel { display: none; }
    .auth-form-side { min-height: 100vh; padding: 2rem 1.25rem; }
    .field-row-2 { grid-template-columns: 1fr; gap: 0; }
  }
</style>
<?php $view->endSection() ?>

<div class="auth-wrap">

  <!-- Left brand panel -->
  <div class="auth-panel">
    <a href="<?= url() ?>" class="panel-brand">Clothy</a>

    <ul class="panel-perks">
      <li class="panel-perk">
        <div class="perk-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
        </div>
        <div class="perk-text">
          <strong>Exclusive Member Deals</strong>
          <span>Early access to sales and member-only discounts.</span>
        </div>
      </li>
      <li class="panel-perk">
        <div class="perk-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </div>
        <div class="perk-text">
          <strong>Fast Checkout</strong>
          <span>Saved addresses and one-click reorder.</span>
        </div>
      </li>
      <li class="panel-perk">
        <div class="perk-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div class="perk-text">
          <strong>Order Tracking</strong>
          <span>Real-time updates on every shipment.</span>
        </div>
      </li>
    </ul>

    <div class="panel-dots">
      <div class="panel-dot"></div>
      <div class="panel-dot active"></div>
      <div class="panel-dot"></div>
    </div>
  </div>

  <!-- Right form panel -->
  <div class="auth-form-side">
    <div class="auth-box">

      <div class="auth-box-header">
        <span class="auth-step-label">Step 1 of 1</span>
        <h1 class="auth-heading">Create your<br>free account</h1>
        <p class="auth-sub">Join thousands of style-conscious shoppers.</p>
      </div>

      <form method="POST" action="<?= url('register') ?>" novalidate>
        <?= csrfField() ?>

        <!-- Name row -->
        <div class="field-row-2">
          <div class="field">
            <label class="field-label" for="first_name">First name</label>
            <div class="field-input-wrap">
              <input class="field-input no-icon" type="text" id="first_name" name="first_name"
                     required autocomplete="given-name" placeholder="Jane"
                     value="<?= htmlspecialchars($old['first_name'] ?? '') ?>">
            </div>
          </div>
          <div class="field">
            <label class="field-label" for="last_name">Last name</label>
            <div class="field-input-wrap">
              <input class="field-input no-icon" type="text" id="last_name" name="last_name"
                     required autocomplete="family-name" placeholder="Doe"
                     value="<?= htmlspecialchars($old['last_name'] ?? '') ?>">
            </div>
          </div>
        </div>

        <!-- Email -->
        <div class="field">
          <label class="field-label" for="email">Email address</label>
          <div class="field-input-wrap">
            <span class="field-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            </span>
            <input class="field-input" type="email" id="email" name="email"
                   required autocomplete="email" placeholder="you@example.com"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>">
          </div>
        </div>

        <!-- Password -->
        <div class="field">
          <label class="field-label" for="password">Password</label>
          <div class="field-input-wrap">
            <span class="field-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </span>
            <input class="field-input" type="password" id="password" name="password"
                   required autocomplete="new-password" placeholder="Min. 8 characters"
                   oninput="checkStrength(this.value)">
            <button type="button" class="pwd-toggle" onclick="togglePwd('password', this)" aria-label="Show password">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="icon-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <div class="pwd-strength">
            <div class="pwd-bar" id="bar1"></div>
            <div class="pwd-bar" id="bar2"></div>
            <div class="pwd-bar" id="bar3"></div>
            <span class="pwd-strength-label" id="pwdLabel"></span>
          </div>
        </div>

        <!-- Confirm password -->
        <div class="field">
          <label class="field-label" for="password_confirm">Confirm password</label>
          <div class="field-input-wrap">
            <span class="field-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </span>
            <input class="field-input" type="password" id="password_confirm" name="password_confirm"
                   required autocomplete="new-password" placeholder="Repeat password">
            <button type="button" class="pwd-toggle" onclick="togglePwd('password_confirm', this)" aria-label="Show password">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="icon-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <!-- Terms -->
        <div class="terms-row">
          <input type="checkbox" name="terms" id="terms" required>
          <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</label>
        </div>

        <button class="btn-auth" type="submit">Create Account &rarr;</button>
      </form>

      <p class="auth-switch">Already have an account? <a href="<?= url('login') ?>">Sign in</a></p>

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
    btn.querySelector('.icon-eye').style.opacity = isText ? '1' : '.4';
  }

  function checkStrength(val) {
    var score = 0;
    if (val.length >= 8)                          score++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val))  score++;
    if (/[0-9]/.test(val) || /[^A-Za-z0-9]/.test(val)) score++;

    var labels = ['', 'Weak', 'Fair', 'Strong'];
    var cls    = ['', 'active-1', 'active-2', 'active-3'];
    var colors = ['#b0aca6', '#d94f4f', '#e8a83a', '#3aaa5e'];

    for (var i = 1; i <= 3; i++) {
      var bar = document.getElementById('bar' + i);
      bar.className = 'pwd-bar' + (i <= score ? ' ' + cls[score] : '');
    }
    var lbl = document.getElementById('pwdLabel');
    lbl.textContent = val.length ? labels[score] : '';
    lbl.style.color = val.length ? colors[score] : '#b0aca6';
  }
</script>
<?php $view->endSection() ?>
