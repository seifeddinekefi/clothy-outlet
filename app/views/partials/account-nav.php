<?php

/**
 * app/views/partials/account-nav.php
 * Shared sidebar for all account pages.
 * Expects: $user (array from Session::user()), $activeNav (string)
 */
$_u       = $user ?? Session::user() ?? [];
$_name    = $_u['name']  ?? 'Member';
$_email   = $_u['email'] ?? '';
$_active  = $activeNav   ?? 'dashboard';

// Build avatar initials (up to 2 chars)
$_parts    = preg_split('/\s+/', trim($_name), 2);
$_initials = strtoupper(
  substr($_parts[0] ?? '', 0, 1) .
    substr($_parts[1] ?? '', 0, 1)
);
?>
<aside class="acct-sidebar">

  <div class="acct-sidebar-user">
    <div class="acct-avatar"><?= $_initials ?></div>
    <div class="acct-user-meta">
      <span class="acct-user-name"><?= htmlspecialchars($_name) ?></span>
      <span class="acct-user-email"><?= htmlspecialchars($_email) ?></span>
    </div>
  </div>

  <nav class="acct-nav">

    <a href="<?= url('account') ?>" class="acct-nav-link <?= $_active === 'dashboard' ? 'active' : '' ?>">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="7" height="7" rx="1" />
        <rect x="14" y="3" width="7" height="7" rx="1" />
        <rect x="3" y="14" width="7" height="7" rx="1" />
        <rect x="14" y="14" width="7" height="7" rx="1" />
      </svg>
      Dashboard
    </a>

    <a href="<?= url('account/orders') ?>" class="acct-nav-link <?= $_active === 'orders' ? 'active' : '' ?>">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
        <line x1="3" y1="6" x2="21" y2="6" />
        <path d="M16 10a4 4 0 0 1-8 0" />
      </svg>
      My Orders
    </a>

    <a href="<?= url('account/wishlist') ?>" class="acct-nav-link <?= $_active === 'wishlist' ? 'active' : '' ?>">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z" />
      </svg>
      Wishlist
    </a>

    <a href="<?= url('account/profile') ?>" class="acct-nav-link <?= $_active === 'profile' ? 'active' : '' ?>">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
        <circle cx="12" cy="7" r="4" />
      </svg>
      Profile
    </a>

    <hr class="acct-nav-divider">

    <a href="<?= url('logout') ?>" class="acct-nav-link danger">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
        <polyline points="16 17 21 12 16 7" />
        <line x1="21" y1="12" x2="9" y2="12" />
      </svg>
      Log Out
    </a>

  </nav>
</aside>