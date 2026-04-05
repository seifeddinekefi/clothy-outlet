<?php
/**
 * app/views/products/show.php
 * Enhanced product detail page
 */
$_p = $product ?? null;
$_sizes = $sizes ?? [];
$_inWishlist = (bool) ($inWishlist ?? false);
$_hasComparePrice = isset($_p->compare_price) && $_p->compare_price > $_p->price;
$_discount = $_hasComparePrice ? round((1 - $_p->price / $_p->compare_price) * 100) : 0;
?>

<?php if (!$_p): ?>
  <div class="container" style="padding:140px 0;">Product not found.</div>
  <?php return; ?>
<?php endif; ?>

<?php $view->startSection('head') ?>
<style>
  :root {
    --pd-accent: #0a0a0a;
    --pd-accent-hover: #1a1a1a;
    --pd-border: #e8e6e2;
    --pd-muted: #7a7570;
    --pd-bg: #f8f7f5;
    --pd-success: #2d7a4f;
  }

  .pd-page {
    padding-top: calc(var(--nav-height, 72px) + 1rem);
    padding-bottom: 4rem;
    background: linear-gradient(180deg, var(--pd-bg) 0%, #fff 50%);
    min-height: 100vh;
  }

  /* Breadcrumb */
  .pd-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: var(--pd-muted);
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }
  .pd-breadcrumb a {
    color: var(--pd-muted);
    text-decoration: none;
    transition: color 0.2s;
  }
  .pd-breadcrumb a:hover { color: var(--pd-accent); }
  .pd-breadcrumb span { color: #bbb; }

  /* Main Layout */
  .pd-wrap {
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    gap: 3rem;
    align-items: start;
    max-width: 1100px;
    margin: 0 auto;
  }

  /* Image Gallery */
  .pd-gallery {
    position: sticky;
    top: calc(var(--nav-height, 72px) + 1rem);
  }
  .pd-img-main {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    aspect-ratio: 3/4;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    position: relative;
    max-height: 520px;
  }
  .pd-img-main img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s ease;
  }
  .pd-img-main:hover img {
    transform: scale(1.02);
  }
  .pd-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: var(--pd-accent);
    color: #fff;
    padding: 0.4rem 0.85rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
  }
  .pd-badge.sale { background: #c23a3a; }

  /* Product Info */
  .pd-info {
    padding-top: 0.5rem;
  }
  .pd-category {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--pd-muted);
    margin-bottom: 0.6rem;
  }
  .pd-title {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.8rem, 4vw, 2.6rem);
    font-weight: 400;
    line-height: 1.2;
    margin: 0 0 1rem;
    color: var(--pd-accent);
  }

  /* Price */
  .pd-price-wrap {
    display: flex;
    align-items: baseline;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
  }
  .pd-price {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--pd-accent);
  }
  .pd-price-original {
    font-size: 1rem;
    color: #999;
    text-decoration: line-through;
  }
  .pd-price-discount {
    font-size: 0.75rem;
    font-weight: 700;
    color: #fff;
    background: #c23a3a;
    padding: 0.25rem 0.6rem;
    border-radius: 4px;
  }

  /* Description */
  .pd-desc {
    color: #5e5a55;
    line-height: 1.8;
    font-size: 0.95rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--pd-border);
  }

  /* Section Labels */
  .pd-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--pd-muted);
    margin-bottom: 0.65rem;
  }

  /* Size Selector */
  .pd-size-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    margin-bottom: 1.5rem;
  }
  .pd-size {
    position: relative;
  }
  .pd-size input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }
  .pd-size span {
    min-width: 52px;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--pd-border);
    border-radius: 10px;
    padding: 0 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    background: #fff;
  }
  .pd-size span:hover {
    border-color: #bbb;
    background: #fafafa;
  }
  .pd-size input:checked + span {
    border-color: var(--pd-accent);
    background: var(--pd-accent);
    color: #fff;
    transform: scale(1.02);
  }
  .pd-size.out-of-stock span {
    opacity: 0.4;
    cursor: not-allowed;
    text-decoration: line-through;
  }

  /* Quantity */
  .pd-qty-wrap {
    margin-bottom: 1.5rem;
  }
  .pd-qty-control {
    display: inline-flex;
    align-items: center;
    border: 2px solid var(--pd-border);
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
  }
  .pd-qty-btn {
    width: 44px;
    height: 44px;
    border: none;
    background: transparent;
    font-size: 1.2rem;
    cursor: pointer;
    color: var(--pd-accent);
    transition: background 0.2s;
  }
  .pd-qty-btn:hover { background: #f5f5f5; }
  .pd-qty-input {
    width: 60px;
    height: 44px;
    border: none;
    border-left: 1px solid var(--pd-border);
    border-right: 1px solid var(--pd-border);
    text-align: center;
    font-size: 1rem;
    font-weight: 600;
    -moz-appearance: textfield;
  }
  .pd-qty-input::-webkit-outer-spin-button,
  .pd-qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  /* Action Buttons */
  .pd-actions {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
  }
  .pd-btn {
    flex: 1;
    border: none;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }
  .pd-btn.cart {
    background: var(--pd-accent);
    color: #fff;
  }
  .pd-btn.cart:hover {
    background: var(--pd-accent-hover);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  }
  .pd-btn.wish {
    flex: 0 0 auto;
    width: 54px;
    padding: 1rem;
    background: #fff;
    border: 2px solid var(--pd-border);
    color: var(--pd-muted);
  }
  .pd-btn.wish:hover {
    border-color: var(--pd-accent);
    color: var(--pd-accent);
  }
  .pd-btn.wish.active {
    border-color: #e74c3c;
    color: #e74c3c;
    background: #fef5f5;
  }
  .pd-btn.wish svg {
    width: 20px;
    height: 20px;
  }

  /* Trust Badges */
  .pd-trust {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    padding: 1.25rem 0;
    border-top: 1px solid var(--pd-border);
    border-bottom: 1px solid var(--pd-border);
  }
  .pd-trust-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: var(--pd-muted);
  }
  .pd-trust-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--pd-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
  }

  /* Accordion */
  .pd-accordion {
    margin-top: 1.5rem;
  }
  .pd-accordion-item {
    border-bottom: 1px solid var(--pd-border);
  }
  .pd-accordion-btn {
    width: 100%;
    padding: 1rem 0;
    border: none;
    background: transparent;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--pd-accent);
    cursor: pointer;
    transition: color 0.2s;
  }
  .pd-accordion-btn:hover { color: var(--pd-muted); }
  .pd-accordion-icon {
    font-size: 1.2rem;
    transition: transform 0.3s;
  }
  .pd-accordion-item.open .pd-accordion-icon {
    transform: rotate(45deg);
  }
  .pd-accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
  }
  .pd-accordion-item.open .pd-accordion-content {
    max-height: 200px;
  }
  .pd-accordion-inner {
    padding-bottom: 1rem;
    font-size: 0.88rem;
    color: #5e5a55;
    line-height: 1.7;
  }

  /* Stock Status */
  .pd-stock {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--pd-success);
    margin-bottom: 1rem;
  }
  .pd-stock-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--pd-success);
    animation: pulse 2s infinite;
  }
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .pd-wrap { gap: 2rem; }
  }
  @media (max-width: 900px) {
    .pd-wrap { grid-template-columns: 1fr; }
    .pd-gallery { position: static; }
    .pd-trust { grid-template-columns: 1fr; gap: 0.5rem; }
  }
</style>
<?php $view->endSection() ?>

<div class="pd-page">
  <div class="container">
    <!-- Breadcrumb -->
    <nav class="pd-breadcrumb">
      <a href="<?= url() ?>">Home</a>
      <span>›</span>
      <a href="<?= url('products') ?>">Shop</a>
      <?php if (!empty($_p->category_name)): ?>
        <span>›</span>
        <a href="<?= url('products?category=' . e($_p->category_slug ?? '')) ?>"><?= e($_p->category_name) ?></a>
      <?php endif; ?>
      <span>›</span>
      <span style="color: var(--pd-accent);"><?= e($_p->name) ?></span>
    </nav>

    <div class="pd-wrap">
      <!-- Image Gallery -->
      <div class="pd-gallery">
        <div class="pd-img-main">
          <img src="<?= productImg($_p->primary_image ?? null) ?>" alt="<?= e($_p->name) ?>">
          <?php if ($_hasComparePrice): ?>
            <span class="pd-badge sale">-<?= $_discount ?>%</span>
          <?php elseif (!empty($_p->is_featured)): ?>
            <span class="pd-badge">New</span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Product Info -->
      <div class="pd-info">
        <?php if (!empty($_p->category_name)): ?>
          <span class="pd-category"><?= e($_p->category_name) ?></span>
        <?php endif; ?>
        
        <h1 class="pd-title"><?= e($_p->name) ?></h1>

        <!-- Price -->
        <div class="pd-price-wrap">
          <span class="pd-price"><?= formatPrice($_p->price) ?></span>
          <?php if ($_hasComparePrice): ?>
            <span class="pd-price-original"><?= formatPrice($_p->compare_price) ?></span>
            <span class="pd-price-discount">Save <?= $_discount ?>%</span>
          <?php endif; ?>
        </div>

        <!-- Stock Status -->
        <div class="pd-stock">
          <span class="pd-stock-dot"></span>
          In Stock — Ready to ship
        </div>

        <!-- Description -->
        <p class="pd-desc"><?= nl2br(e((string) ($_p->description ?? 'Premium quality product crafted with care. Perfect for any occasion.'))) ?></p>

        <form method="POST" action="<?= url('cart/add') ?>" id="addToCartForm">
          <?= csrfField() ?>
          <input type="hidden" name="product_id" value="<?= (int) $_p->id ?>">

          <!-- Size Selection -->
          <?php if (!empty($_sizes)): ?>
            <div class="pd-label">Select Size</div>
            <div class="pd-size-grid">
              <?php foreach ($_sizes as $idx => $s): 
                $outOfStock = (int)($s->stock ?? 1) <= 0;
              ?>
                <label class="pd-size <?= $outOfStock ? 'out-of-stock' : '' ?>">
                  <input type="radio" name="size" value="<?= e($s->size) ?>" 
                         <?= $idx === 0 && !$outOfStock ? 'checked' : '' ?> 
                         <?= $outOfStock ? 'disabled' : '' ?>
                         required>
                  <span><?= e($s->size) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- Quantity -->
          <div class="pd-qty-wrap">
            <div class="pd-label">Quantity</div>
            <div class="pd-qty-control">
              <button type="button" class="pd-qty-btn" onclick="updateQty(-1)">−</button>
              <input type="number" class="pd-qty-input" id="qtyInput" name="quantity" value="1" min="1" max="99">
              <button type="button" class="pd-qty-btn" onclick="updateQty(1)">+</button>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="pd-actions">
            <button type="submit" class="pd-btn cart">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 01-8 0"/>
              </svg>
              Add to Cart
            </button>
            <?php if (Session::isLoggedIn()): ?>
              <button type="button" class="pd-btn wish <?= $_inWishlist ? 'active' : '' ?>" 
                      onclick="this.closest('div').querySelector('.wish-form').submit()">
                <svg viewBox="0 0 24 24" fill="<?= $_inWishlist ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
                  <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                </svg>
              </button>
            <?php else: ?>
              <a href="<?= url('login') ?>" class="pd-btn wish">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                </svg>
              </a>
            <?php endif; ?>
          </div>
        </form>

        <?php if (Session::isLoggedIn()): ?>
          <form method="POST" action="<?= url('account/wishlist/toggle') ?>" class="wish-form" style="display:none;">
            <?= csrfField() ?>
            <input type="hidden" name="product_id" value="<?= (int) $_p->id ?>">
          </form>
        <?php endif; ?>

        <!-- Trust Badges -->
        <div class="pd-trust">
          <div class="pd-trust-item">
            <span class="pd-trust-icon">🚚</span>
            <span>Flat shipping: <?= formatPrice(defined('SHIPPING_FEE') ? SHIPPING_FEE : 8) ?></span>
          </div>
          <div class="pd-trust-item">
            <span class="pd-trust-icon">↺</span>
            <span>7-day easy returns</span>
          </div>
          <div class="pd-trust-item">
            <span class="pd-trust-icon">📦</span>
            <span>Inspect before payment</span>
          </div>
        </div>

        <!-- Accordion -->
        <div class="pd-accordion">
          <div class="pd-accordion-item">
            <button type="button" class="pd-accordion-btn" onclick="toggleAccordion(this)">
              Product Details
              <span class="pd-accordion-icon">+</span>
            </button>
            <div class="pd-accordion-content">
              <div class="pd-accordion-inner">
                <?= nl2br(e((string) ($_p->description ?? 'High-quality materials and expert craftsmanship ensure lasting comfort and style.'))) ?>
              </div>
            </div>
          </div>
          <div class="pd-accordion-item">
            <button type="button" class="pd-accordion-btn" onclick="toggleAccordion(this)">
              Shipping & Returns
              <span class="pd-accordion-icon">+</span>
            </button>
            <div class="pd-accordion-content">
              <div class="pd-accordion-inner">
                Flat shipping fee of <?= formatPrice(defined('SHIPPING_FEE') ? SHIPPING_FEE : 8) ?>. Standard delivery takes 3-5 business days.
                Easy returns within 7 days of delivery — no questions asked.
              </div>
            </div>
          </div>
          <div class="pd-accordion-item">
            <button type="button" class="pd-accordion-btn" onclick="toggleAccordion(this)">
              Care Instructions
              <span class="pd-accordion-icon">+</span>
            </button>
            <div class="pd-accordion-content">
              <div class="pd-accordion-inner">
                Machine wash cold with similar colors. Tumble dry low. Do not bleach. 
                Iron on low heat if needed.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function updateQty(delta) {
  var input = document.getElementById('qtyInput');
  var val = parseInt(input.value) || 1;
  val = Math.max(1, Math.min(99, val + delta));
  input.value = val;
}

function toggleAccordion(btn) {
  var item = btn.closest('.pd-accordion-item');
  item.classList.toggle('open');
}
</script>

