<?php

/**
 * app/views/account/wishlist.php
 */
$_items = $items ?? [];
$_user = $user ?? Session::user() ?? [];
?>
<?php $view->startSection('head') ?>
<link rel="stylesheet" href="<?= $view->asset('css/account.css') ?>">
<?php $view->endSection() ?>

<div class="acct-page">
  <div class="container">
    <div class="acct-grid">
      <?= $view->partial('partials.account-nav', ['user' => $_user, 'activeNav' => 'wishlist']) ?>

      <div class="acct-content">
        <div>
          <span class="acct-page-eyebrow">My Account</span>
          <h1 class="acct-page-title">My Wishlist</h1>
          <p class="acct-page-sub">Save items you love and come back anytime.</p>
        </div>

        <?php if (empty($_items)): ?>
          <div class="acct-empty">
            <h3>Your wishlist is empty</h3>
            <p>Browse products and tap the heart to save favorites.</p>
            <a href="<?= url('products') ?>" class="btn-save">Browse Products</a>
          </div>
        <?php else: ?>
          <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem;">
            <?php foreach ($_items as $p): ?>
              <article style="background:#fff;border:1px solid #e8e6e2;border-radius:10px;overflow:hidden;">
                <a href="<?= url('product/' . (int) $p->product_id) ?>" style="display:block;aspect-ratio:3/4;background:#f4f3f1;">
                  <img src="<?= productImg($p->primary_image ?? null) ?>" alt="<?= htmlspecialchars($p->name) ?>" style="width:100%;height:100%;object-fit:cover;display:block;">
                </a>
                <div style="padding:.9rem;">
                  <a href="<?= url('product/' . (int) $p->product_id) ?>" style="display:block;color:#0a0a0a;text-decoration:none;font-weight:600;margin-bottom:.3rem;"><?= htmlspecialchars($p->name) ?></a>
                  <div style="font-size:.95rem;font-weight:700;margin-bottom:.8rem;">$<?= number_format((float) $p->price, 2) ?></div>
                  <div style="display:flex;gap:.5rem;">
                    <form method="POST" action="<?= url('cart/add') ?>" style="flex:1;">
                      <?= csrfField() ?>
                      <input type="hidden" name="product_id" value="<?= (int) $p->product_id ?>">
                      <input type="hidden" name="quantity" value="1">
                      <button type="submit" style="width:100%;padding:.6rem .7rem;border:none;background:#0a0a0a;color:#fff;border-radius:6px;font-size:.72rem;text-transform:uppercase;letter-spacing:.09em;">Add to Cart</button>
                    </form>
                    <form method="POST" action="<?= url('account/wishlist/remove') ?>">
                      <?= csrfField() ?>
                      <input type="hidden" name="product_id" value="<?= (int) $p->product_id ?>">
                      <button type="submit" style="padding:.6rem .7rem;border:1px solid #e8e6e2;background:#fff;color:#7a7570;border-radius:6px;cursor:pointer;">Remove</button>
                    </form>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>