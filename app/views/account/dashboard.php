<?php
/**
 * app/views/account/dashboard.php
 * Logged-in customer dashboard.
 */
?>
<?php $view->startSection('head') ?>
<link rel="stylesheet" href="<?= $view->asset('css/account.css') ?>">
<?php $view->endSection() ?>
<?php
// ── Data helpers ──
$_orders = $orders ?? [];
$_recent = $recentOrders ?? array_slice($_orders, 0, 5);
$_total  = $totalSpent  ?? 0;
$_user   = $user        ?? Session::user() ?? [];
$_cust   = $customer    ?? null;

// Avatar initials
$_parts    = preg_split('/\s+/', trim($_user['name'] ?? 'M'), 2);
$_initials = strtoupper(substr($_parts[0] ?? '', 0, 1) . substr($_parts[1] ?? '', 0, 1));

// First name only
$_firstName = explode(' ', trim($_user['name'] ?? ''))[0] ?? 'there';

// Member since
$_since = '';
if ($_cust && !empty($_cust->created_at)) {
    $_since = date('M Y', strtotime($_cust->created_at));
}

function acct_badge(string $status): string {
    $map = [
        'delivered' => 'status-delivered',
        'confirmed' => 'status-confirmed',
        'shipped'   => 'status-shipped',
        'pending'   => 'status-pending',
        'cancelled' => 'status-cancelled',
    ];
    $cls = $map[strtolower($status)] ?? 'status-pending';
    return '<span class="status-badge ' . $cls . '">' . ucfirst(htmlspecialchars($status)) . '</span>';
}
?>

<div class="acct-page">
  <div class="container">
    <div class="acct-grid">

      <?= $view->partial('partials.account-nav', ['user' => $_user, 'activeNav' => 'dashboard']) ?>

      <div class="acct-content">

        <div>
          <span class="acct-page-eyebrow">My Account</span>
          <h1 class="acct-page-title">Welcome back, <?= htmlspecialchars($_firstName) ?>!</h1>
          <p class="acct-page-sub">Here&rsquo;s an overview of your account activity.</p>
        </div>

        <!-- Stat cards -->
        <div class="acct-stats">
          <div class="acct-stat">
            <div class="acct-stat-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
            <div class="acct-stat-body">
              <div class="acct-stat-num"><?= count($_orders) ?></div>
              <div class="acct-stat-lbl">Total Orders</div>
            </div>
          </div>

          <div class="acct-stat">
            <div class="acct-stat-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="acct-stat-body">
              <div class="acct-stat-num">$<?= number_format($_total, 2) ?></div>
              <div class="acct-stat-lbl">Total Spent</div>
            </div>
          </div>

          <div class="acct-stat">
            <div class="acct-stat-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="acct-stat-body">
              <div class="acct-stat-num"><?= $_since ?: '&mdash;' ?></div>
              <div class="acct-stat-lbl">Member Since</div>
            </div>
          </div>
        </div>

        <!-- Quick actions -->
        <div class="acct-actions">
          <a href="<?= url('account/orders') ?>" class="acct-action">
            <div class="acct-action-icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
            <div>
              <span class="acct-action-title">View All Orders</span>
              <span class="acct-action-sub"><?= count($_orders) ?> order<?= count($_orders) !== 1 ? 's' : '' ?> placed</span>
            </div>
          </a>
          <a href="<?= url('account/profile') ?>" class="acct-action">
            <div class="acct-action-icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
              <span class="acct-action-title">Edit Profile</span>
              <span class="acct-action-sub">Name, email &amp; password</span>
            </div>
          </a>
        </div>

        <!-- Recent orders -->
        <div class="acct-section-label">Recent Orders</div>

        <?php if (empty($_recent)): ?>
          <div class="acct-empty">
            <div class="acct-empty-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
            <h3>No orders yet</h3>
            <p>When you place your first order it will appear here.</p>
            <a href="<?= url('products') ?>" class="btn-save">Start Shopping</a>
          </div>
        <?php else: ?>
          <div class="acct-card-plain">
            <?php foreach ($_recent as $o): ?>
              <div class="acct-order-row">
                <span class="acct-order-id">#<?= str_pad((string)$o->id, 5, '0', STR_PAD_LEFT) ?></span>
                <span class="acct-order-date"><?= date('M j, Y', strtotime($o->created_at)) ?></span>
                <span class="acct-order-total">$<?= number_format((float)$o->total_price, 2) ?></span>
                <?= acct_badge($o->status ?? 'pending') ?>
              </div>
            <?php endforeach ?>
          </div>
          <?php if (count($_orders) > 5): ?>
            <div style="text-align:right;margin-top:.75rem">
              <a href="<?= url('account/orders') ?>" class="btn-outline">View all <?= count($_orders) ?> orders &rarr;</a>
            </div>
          <?php endif ?>
        <?php endif ?>

      </div>
    </div>
  </div>
</div>
