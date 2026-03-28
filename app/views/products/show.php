<?php
/**
 * app/views/products/show.php
 */
$_p = $product ?? null;
$_sizes = $sizes ?? [];
$_inWishlist = (bool) ($inWishlist ?? false);
?>

<?php if (!$_p): ?>
  <div class="container" style="padding:140px 0;">Product not found.</div>
  <?php return; ?>
<?php endif; ?>

<?php $view->startSection('head') ?>
<style>
  .pd-page { padding-top: calc(var(--nav-height, 72px) + 2rem); padding-bottom: 3rem; }
  .pd-wrap { display:grid; grid-template-columns:1.1fr 1fr; gap:2rem; }
  .pd-img { background:#f4f3f1; border-radius:12px; overflow:hidden; aspect-ratio:3/4; }
  .pd-img img { width:100%; height:100%; object-fit:cover; display:block; }
  .pd-title { font-family: Georgia, serif; font-size: clamp(1.8rem, 3.5vw, 2.4rem); font-weight:400; margin:0 0 .5rem; }
  .pd-price { font-size:1.3rem; font-weight:700; margin-bottom:.9rem; }
  .pd-desc { color:#5e5a55; line-height:1.75; font-size:.92rem; margin-bottom:1.1rem; }
  .pd-size-grid { display:flex; flex-wrap:wrap; gap:.5rem; margin:.45rem 0 .9rem; }
  .pd-size { position:relative; }
  .pd-size input { position:absolute; opacity:0; pointer-events:none; }
  .pd-size span { min-width:46px; display:inline-flex; align-items:center; justify-content:center; border:1.5px solid #e8e6e2; border-radius:8px; padding:.45rem .75rem; font-size:.82rem; cursor:pointer; }
  .pd-size input:checked + span { border-color:#0a0a0a; background:#0a0a0a; color:#fff; }
  .pd-row { display:flex; gap:.65rem; align-items:end; }
  .pd-qty { width:92px; padding:.65rem .75rem; border:1.5px solid #e8e6e2; border-radius:8px; }
  .pd-btn { border:none; border-radius:8px; padding:.85rem 1rem; font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; cursor:pointer; }
  .pd-btn.cart { background:#0a0a0a; color:#fff; }
  .pd-btn.wish { background:#fff; border:1.5px solid #e8e6e2; color:#7a7570; }
  .pd-btn.wish.active { border-color:#0a0a0a; color:#0a0a0a; }
  @media (max-width:900px){ .pd-wrap { grid-template-columns:1fr; } }
</style>
<?php $view->endSection() ?>

<div class="pd-page">
  <div class="container">
    <div class="pd-wrap">
      <div class="pd-img">
        <img src="<?= productImg($_p->primary_image ?? null) ?>" alt="<?= e($_p->name) ?>">
      </div>

      <div>
        <h1 class="pd-title"><?= e($_p->name) ?></h1>
        <div class="pd-price">$<?= number_format((float) $_p->price, 2) ?></div>
        <p class="pd-desc"><?= nl2br(e((string) ($_p->description ?? ''))) ?></p>

        <form method="POST" action="<?= url('cart/add') ?>">
          <?= csrfField() ?>
          <input type="hidden" name="product_id" value="<?= (int) $_p->id ?>">

          <?php if (!empty($_sizes)): ?>
            <div>
              <div style="font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#7a7570;">Select Size</div>
              <div class="pd-size-grid">
                <?php foreach ($_sizes as $idx => $s): ?>
                  <label class="pd-size">
                    <input type="radio" name="size" value="<?= e($s->size) ?>" <?= $idx === 0 ? 'checked' : '' ?> required>
                    <span><?= e($s->size) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="pd-row">
            <div>
              <label style="display:block;font-size:.75rem;color:#7a7570;margin-bottom:.35rem;">Quantity</label>
              <input class="pd-qty" type="number" min="1" max="99" name="quantity" value="1">
            </div>
            <button class="pd-btn cart" type="submit">Add to Cart</button>
          </div>
        </form>

        <div style="margin-top:.7rem;">
          <?php if (Session::isLoggedIn()): ?>
            <form method="POST" action="<?= url('account/wishlist/toggle') ?>">
              <?= csrfField() ?>
              <input type="hidden" name="product_id" value="<?= (int) $_p->id ?>">
              <button type="submit" class="pd-btn wish <?= $_inWishlist ? 'active' : '' ?>"><?= $_inWishlist ? 'Remove from Wishlist' : 'Save to Wishlist' ?></button>
            </form>
          <?php else: ?>
            <a href="<?= url('login') ?>" class="pd-btn wish" style="display:inline-block;text-decoration:none;">Sign in to Save</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

