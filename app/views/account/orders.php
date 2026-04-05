<?php

/**
 * app/views/account/orders.php
 * Customer order history.
 */
?>
<?php $view->startSection('head') ?>
<link rel="stylesheet" href="<?= $view->asset('css/account.css') ?>">
<?php $view->endSection() ?>
<?php
$_orders = $orders ?? [];
$_user   = $user   ?? Session::user() ?? [];

if (!function_exists('acct_badge')) {
  function acct_badge(string $status): string
  {
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
}
?>

<div class="acct-page">
  <div class="container">
    <div class="acct-grid">

      <?= $view->partial('partials.account-nav', ['user' => $_user, 'activeNav' => 'orders']) ?>

      <div class="acct-content">

        <div>
          <span class="acct-page-eyebrow">My Account</span>
          <h1 class="acct-page-title">My Orders</h1>
          <p class="acct-page-sub">Track and manage all your purchases.</p>
        </div>

        <?php if (empty($_orders)): ?>
          <div class="acct-empty">
            <div class="acct-empty-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                <line x1="3" y1="6" x2="21" y2="6" />
                <path d="M16 10a4 4 0 0 1-8 0" />
              </svg>
            </div>
            <h3>No orders yet</h3>
            <p>You haven&rsquo;t placed any orders. Start browsing our collection!</p>
            <a href="<?= url('products') ?>" class="btn-save">Shop Now</a>
          </div>
        <?php else: ?>
          <table class="ord-table">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($_orders as $o): ?>
                <tr>
                  <td><strong style="font-family:monospace">#<?= str_pad((string)$o->id, 5, '0', STR_PAD_LEFT) ?></strong></td>
                  <td><?= date('M j, Y', strtotime($o->created_at)) ?></td>
                  <td><strong><?= formatPrice($o->total_price) ?></strong></td>
                  <td style="text-transform:capitalize;font-size:.8rem;color:#7a7570"><?= htmlspecialchars(str_replace('_', ' ', $o->payment_status ?? '')) ?></td>
                  <td><?= acct_badge($o->status ?? 'pending') ?></td>
                  <td>
                    <?php if (($o->status ?? '') === 'pending'): ?>
                      <form method="POST" action="<?= url('account/orders/cancel/' . (int) $o->id) ?>" onsubmit="return confirm('Cancel this order?');">
                        <?= csrfField() ?>
                        <button type="submit" style="border:1px solid #e8e6e2;background:#fff;color:#7a7570;border-radius:6px;padding:.35rem .6rem;font-size:.72rem;cursor:pointer;">Cancel</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        <?php endif ?>

      </div>
    </div>
  </div>
</div>