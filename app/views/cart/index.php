<?php

/**
 * app/views/cart/index.php
 * Shopping cart page.
 */
$_items    = $items    ?? [];
$_subtotal = $subtotal ?? 0;
$_shipping = $_subtotal >= 150 ? 0 : 9.99;
$_total    = $_subtotal + $_shipping;
?>
<?php $view->startSection('head') ?>
<style>
  /* ── Page ── */
  .cart-page {
    padding-top: calc(72px + 2.5rem);
    padding-bottom: 5rem;
    min-height: 80vh;
    background: #f6f5f3;
  }

  .cart-eyebrow {
    font-size: .67rem;
    font-weight: 700;
    letter-spacing: .2em;
    text-transform: uppercase;
    color: #c4a97a;
    display: block;
    margin-bottom: .5rem;
  }

  .cart-title {
    font-family: Georgia, serif;
    font-size: 2rem;
    font-weight: normal;
    color: #0a0a0a;
    margin: 0 0 .35rem;
  }

  .cart-sub {
    font-size: .875rem;
    color: #7a7570;
    margin: 0 0 2rem;
  }

  /* ── Two-col grid ── */
  .cart-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 1.75rem;
    align-items: start;
  }

  /* ── Items card ── */
  .cart-card {
    background: #fff;
    border: 1px solid #e8e6e2;
    border-radius: 12px;
    overflow: hidden;
  }

  .cart-card-header {
    padding: 1rem 1.5rem;
    background: #fafaf9;
    border-bottom: 1px solid #e8e6e2;
    font-size: .67rem;
    font-weight: 700;
    letter-spacing: .15em;
    text-transform: uppercase;
    color: #7a7570;
    display: grid;
    grid-template-columns: 1fr auto auto auto;
    gap: 1rem;
    align-items: center;
  }

  /* ── Cart item row ── */
  .cart-item {
    display: grid;
    grid-template-columns: 72px 1fr auto auto auto;
    gap: 1.1rem;
    align-items: center;
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid #f4f3f1;
    transition: background .12s;
  }

  .cart-item:last-child {
    border-bottom: none;
  }

  .cart-item:hover {
    background: #fafaf9;
  }

  .cart-item-img {
    width: 72px;
    height: 72px;
    border-radius: 8px;
    object-fit: cover;
    background: #f4f3f1;
    display: block;
  }

  .cart-item-img-placeholder {
    width: 72px;
    height: 72px;
    border-radius: 8px;
    background: #f0eeea;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c0bbb6;
    flex-shrink: 0;
  }

  .cart-item-name {
    font-size: .9rem;
    font-weight: 600;
    color: #0a0a0a;
    margin-bottom: .2rem;
    text-decoration: none;
    display: block;
    transition: color .13s;
  }

  .cart-item-name:hover {
    color: #c4a97a;
  }

  .cart-item-meta {
    font-size: .75rem;
    color: #7a7570;
  }

  .cart-item-price {
    font-size: .875rem;
    font-weight: 600;
    color: #0a0a0a;
    white-space: nowrap;
    text-align: right;
  }

  .cart-item-price-unit {
    font-size: .72rem;
    color: #7a7570;
    font-weight: 400;
    display: block;
    text-align: right;
  }

  /* ── Qty stepper ── */
  .qty-form {
    display: flex;
    align-items: center;
    gap: 0;
  }

  .qty-btn {
    width: 30px;
    height: 30px;
    border: 1.5px solid #e8e6e2;
    background: #fff;
    font-size: .95rem;
    line-height: 1;
    cursor: pointer;
    color: #0a0a0a;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .12s, border-color .12s;
  }

  .qty-btn:first-child {
    border-radius: 6px 0 0 6px;
    border-right: none;
  }

  .qty-btn:last-child {
    border-radius: 0 6px 6px 0;
    border-left: none;
  }

  .qty-btn:hover {
    background: #f4f3f1;
    border-color: #c0bcb8;
  }

  .qty-val {
    width: 36px;
    height: 30px;
    border: 1.5px solid #e8e6e2;
    border-radius: 0;
    text-align: center;
    font-size: .85rem;
    font-weight: 600;
    color: #0a0a0a;
    background: #fff;
    outline: none;
    -moz-appearance: textfield;
  }

  .qty-val::-webkit-inner-spin-button,
  .qty-val::-webkit-outer-spin-button {
    -webkit-appearance: none;
  }

  /* ── Remove ── */
  .btn-remove {
    background: none;
    border: none;
    cursor: pointer;
    color: #b0aca6;
    padding: .3rem;
    display: flex;
    align-items: center;
    transition: color .13s;
  }

  .btn-remove:hover {
    color: #c03030;
  }

  /* ── Summary card ── */
  .cart-summary {
    background: #fff;
    border: 1px solid #e8e6e2;
    border-radius: 12px;
    padding: 1.5rem;
    position: sticky;
    top: calc(72px + 1.25rem);
  }

  .summary-title {
    font-size: .67rem;
    font-weight: 700;
    letter-spacing: .17em;
    text-transform: uppercase;
    color: #7a7570;
    margin-bottom: 1.25rem;
    padding-bottom: .65rem;
    border-bottom: 1px solid #e8e6e2;
  }

  .summary-row {
    display: flex;
    justify-content: space-between;
    font-size: .86rem;
    color: #4a4743;
    margin-bottom: .8rem;
  }

  .summary-row.total {
    font-size: .95rem;
    font-weight: 700;
    color: #0a0a0a;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e8e6e2;
    margin-bottom: 1.35rem;
  }

  .summary-free {
    color: #3a7a3a;
    font-weight: 600;
  }

  .summary-shipping-note {
    font-size: .72rem;
    color: #7a7570;
    text-align: center;
    margin-top: -.6rem;
    margin-bottom: 1rem;
  }

  .btn-checkout {
    display: block;
    width: 100%;
    padding: .95rem;
    background: #0a0a0a;
    color: #fff;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .15em;
    text-transform: uppercase;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: background .18s, transform .12s;
    margin-bottom: .75rem;
  }

  .btn-checkout:hover {
    background: #2a2a2a;
    transform: translateY(-1px);
  }

  .btn-continue {
    display: block;
    text-align: center;
    font-size: .78rem;
    color: #7a7570;
    text-decoration: none;
    transition: color .13s;
  }

  .btn-continue:hover {
    color: #0a0a0a;
  }

  /* ── Empty state ── */
  .cart-empty {
    background: #fff;
    border: 1px solid #e8e6e2;
    border-radius: 12px;
    padding: 4rem 2rem;
    text-align: center;
  }

  .cart-empty-icon {
    width: 60px;
    height: 60px;
    background: #f4f3f1;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
    color: #b0aca6;
  }

  .cart-empty h2 {
    font-family: Georgia, serif;
    font-size: 1.4rem;
    font-weight: normal;
    margin: 0 0 .5rem;
    color: #0a0a0a;
  }

  .cart-empty p {
    font-size: .85rem;
    color: #7a7570;
    margin: 0 0 1.5rem;
  }

  .btn-shop {
    display: inline-block;
    padding: .82rem 2rem;
    background: #0a0a0a;
    color: #fff;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    border-radius: 8px;
    text-decoration: none;
    transition: background .18s;
  }

  .btn-shop:hover {
    background: #2a2a2a;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .cart-grid {
      grid-template-columns: 1fr;
    }

    .cart-summary {
      position: static;
    }

    .cart-card-header {
      display: none;
    }

    .cart-item {
      grid-template-columns: 60px 1fr;
      gap: .75rem;
    }

    .cart-item-price,
    .cart-item-price-unit {
      display: none;
    }

    .qty-form,
    .btn-remove {
      margin-top: .4rem;
    }
  }
</style>
<?php $view->endSection() ?>

<div class="cart-page">
  <div class="container">

    <span class="cart-eyebrow">Shopping</span>
    <h1 class="cart-title">Your Cart</h1>
    <p class="cart-sub">
      <?php if (empty($_items)): ?>
        Your cart is empty.
      <?php else: ?>
        <?= count($_items) ?> item<?= count($_items) !== 1 ? 's' : '' ?> in your cart.
      <?php endif ?>
    </p>

    <?php if (empty($_items)): ?>

      <div class="cart-empty">
        <div class="cart-empty-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <path d="M16 10a4 4 0 0 1-8 0" />
          </svg>
        </div>
        <h2>Nothing here yet</h2>
        <p>Browse our collection and add items you love.</p>
        <a href="<?= url('products') ?>" class="btn-shop">Start Shopping</a>
      </div>

    <?php else: ?>

      <div class="cart-grid">

        <!-- Items -->
        <div>
          <div class="cart-card">
            <div class="cart-card-header">
              <span>Product</span>
              <span>Qty</span>
              <span>Price</span>
              <span></span>
            </div>

            <?php foreach ($_items as $item):
              $p = $item['product'];
            ?>
              <div class="cart-item">

                <!-- Thumbnail -->
                <?php if (!empty($p->primary_image) && file_exists(BASE_PATH . '/public/assets/images/' . ltrim($p->primary_image, '/'))): ?>
                  <img
                    class="cart-item-img"
                    src="<?= productImg($p->primary_image) ?>"
                    alt="<?= htmlspecialchars($p->name) ?>"
                    loading="lazy">
                <?php else: ?>
                  <div class="cart-item-img-placeholder">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="3" width="18" height="18" rx="2" />
                      <circle cx="8.5" cy="8.5" r="1.5" />
                      <polyline points="21 15 16 10 5 21" />
                    </svg>
                  </div>
                <?php endif ?>

                <!-- Name / meta -->
                <div>
                  <a class="cart-item-name" href="<?= url('product/' . $p->id) ?>">
                    <?= htmlspecialchars($p->name) ?>
                  </a>
                  <span class="cart-item-meta">
                    $<?= number_format((float)$p->price, 2) ?> each
                    <?php if (!empty($item['size'])): ?> &middot; Size: <?= htmlspecialchars($item['size']) ?><?php endif ?>
                      <?php if (!empty($p->sku)): ?> &middot; <?= htmlspecialchars($p->sku) ?><?php endif ?>
                  </span>
                </div>

                <!-- Qty stepper -->
                <form method="POST" action="<?= url('cart/update') ?>">
                  <?= csrfField() ?>
                  <input type="hidden" name="cart_key" value="<?= htmlspecialchars($item['key']) ?>">
                  <input type="hidden" name="quantity" id="qty-<?= htmlspecialchars($item['key']) ?>" value="<?= $item['qty'] ?>">
                  <div class="qty-form">
                    <button type="button" class="qty-btn" aria-label="Decrease" onclick="updateQty(this, -1)">&#8722;</button>
                    <input class="qty-val" type="number" value="<?= $item['qty'] ?>" min="1" max="99" onchange="syncQty(this)">
                    <button type="button" class="qty-btn" aria-label="Increase" onclick="updateQty(this, 1)">+</button>
                  </div>
                </form>

                <!-- Line total -->
                <div class="cart-item-price">
                  $<?= number_format($item['lineTotal'], 2) ?>
                  <span class="cart-item-price-unit">line total</span>
                </div>

                <!-- Remove -->
                <form method="POST" action="<?= url('cart/remove') ?>">
                  <?= csrfField() ?>
                  <input type="hidden" name="cart_key" value="<?= htmlspecialchars($item['key']) ?>">
                  <button class="btn-remove" type="submit" aria-label="Remove item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="3 6 5 6 21 6" />
                      <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                      <path d="M10 11v6" />
                      <path d="M14 11v6" />
                      <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                    </svg>
                  </button>
                </form>

              </div>
            <?php endforeach ?>
          </div>
        </div>

        <!-- Summary -->
        <div>
          <div class="cart-summary">
            <div class="summary-title">Order Summary</div>

            <div class="summary-row">
              <span>Subtotal</span>
              <span>$<?= number_format($_subtotal, 2) ?></span>
            </div>

            <div class="summary-row">
              <span>Shipping</span>
              <?php if ($_shipping === 0): ?>
                <span class="summary-free">Free</span>
              <?php else: ?>
                <span>$<?= number_format($_shipping, 2) ?></span>
              <?php endif ?>
            </div>

            <?php if ($_shipping > 0): ?>
              <p class="summary-shipping-note">
                Add $<?= number_format(150 - $_subtotal, 2) ?> more for free shipping
              </p>
            <?php endif ?>

            <div class="summary-row total">
              <span>Total</span>
              <span>$<?= number_format($_total, 2) ?></span>
            </div>

            <a href="<?= url('checkout') ?>" class="btn-checkout">Proceed to Checkout &rarr;</a>
            <a href="<?= url('products') ?>" class="btn-continue">&larr; Continue Shopping</a>
          </div>
        </div>

      </div>

    <?php endif ?>

  </div>
</div>

<script>
function updateQty(btn, delta) {
  var form = btn.closest('form');
  var hidden = form.querySelector('input[name="quantity"]');
  var display = form.querySelector('.qty-val');
  var newVal = Math.max(0, Math.min(99, parseInt(display.value) + delta));
  hidden.value = newVal;
  display.value = newVal;
  form.submit();
}

function syncQty(input) {
  var form = input.closest('form');
  var hidden = form.querySelector('input[name="quantity"]');
  var newVal = Math.max(0, Math.min(99, parseInt(input.value) || 1));
  hidden.value = newVal;
  input.value = newVal;
  form.submit();
}
</script>