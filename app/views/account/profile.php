<?php
/**
 * app/views/account/profile.php
 * Customer profile edit.
 */
?>
<?php $view->startSection('head') ?>
<link rel="stylesheet" href="<?= $view->asset('css/account.css') ?>">
<?php $view->endSection() ?>
<?php
$_user = $user     ?? Session::user() ?? [];
$_cust = $customer ?? null;
?>

<div class="acct-page">
  <div class="container">
    <div class="acct-grid">

      <?= $view->partial('partials.account-nav', ['user' => $_user, 'activeNav' => 'profile']) ?>

      <div class="acct-content">

        <div>
          <span class="acct-page-eyebrow">My Account</span>
          <h1 class="acct-page-title">My Profile</h1>
          <p class="acct-page-sub">Update your personal details and password.</p>
        </div>

        <!-- Personal information -->
        <div class="acct-card">
          <div class="acct-card-title">Personal Information</div>
          <form method="POST" action="<?= url('account/profile') ?>" novalidate>
            <?= csrfField() ?>

            <div class="field-group">
              <label class="field-lbl" for="name">Full Name</label>
              <input class="field-inp" type="text" id="name" name="name"
                     required autocomplete="name"
                     value="<?= htmlspecialchars($_cust->name ?? $_user['name'] ?? '') ?>">
            </div>

            <div class="field-row-2">
              <div class="field-group">
                <label class="field-lbl" for="email">Email Address</label>
                <input class="field-inp" type="email" id="email" name="email"
                       required autocomplete="email"
                       value="<?= htmlspecialchars($_cust->email ?? $_user['email'] ?? '') ?>">
              </div>
              <div class="field-group">
                <label class="field-lbl" for="phone">Phone Number</label>
                <input class="field-inp" type="tel" id="phone" name="phone"
                       autocomplete="tel" placeholder="+1 (555) 000-0000"
                       value="<?= htmlspecialchars($_cust->phone ?? '') ?>">
              </div>
            </div>

            <button class="btn-save" type="submit">Save Changes</button>
          </form>
        </div>

        <!-- Change password -->
        <div class="acct-card">
          <div class="acct-card-title">Change Password</div>
          <form method="POST" action="<?= url('account/profile') ?>" novalidate>
            <?= csrfField() ?>
            <!-- Re-send name/email so the controller update logic keeps them -->
            <input type="hidden" name="name"  value="<?= htmlspecialchars($_cust->name  ?? $_user['name']  ?? '') ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($_cust->email ?? $_user['email'] ?? '') ?>">

            <div class="field-group">
              <label class="field-lbl" for="password_current">Current Password</label>
              <input class="field-inp" type="password" id="password_current" name="password_current"
                     autocomplete="current-password" placeholder="Enter your current password">
            </div>

            <div class="field-row-2">
              <div class="field-group">
                <label class="field-lbl" for="password_new">New Password</label>
                <input class="field-inp" type="password" id="password_new" name="password_new"
                       autocomplete="new-password" placeholder="Min. 8 characters">
              </div>
              <div class="field-group">
                <label class="field-lbl" for="password_confirm">Confirm Password</label>
                <input class="field-inp" type="password" id="password_confirm" name="password_confirm"
                       autocomplete="new-password" placeholder="Repeat new password">
              </div>
            </div>

            <p class="field-hint">Leave the fields above blank if you don&rsquo;t want to change your password.</p>

            <button class="btn-save" type="submit" style="margin-top:.5rem">Update Password</button>
          </form>
        </div>

        <!-- Danger zone -->
        <div class="acct-card" style="border-color:#fde8e8">
          <div class="acct-card-title" style="color:#c03030;border-color:#fde8e8">Account</div>
          <p style="font-size:.82rem;color:#7a7570;margin:0 0 1rem">Need to leave? You can log out of all devices at any time.</p>
          <a href="<?= url('logout') ?>" class="btn-outline" style="border-color:#f0caca;color:#c03030">Log Out</a>
        </div>

      </div>
    </div>
  </div>
</div>
