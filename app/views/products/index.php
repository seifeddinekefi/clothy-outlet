<?php

/**
 * app/views/products/index.php
 * Products listing page with real filters and sorting.
 */

$_products   = $products ?? [];
$_categories = $categories ?? [];
$_filters    = $filters ?? [];
$_pagination = $pagination ?? ['page' => 1, 'pages' => 1];
$_wishlistIds = $wishlistProductIds ?? [];

$_q         = (string) ($_filters['q'] ?? '');
$_sort      = (string) ($_filters['sort'] ?? 'newest');
$_category  = (string) ($_filters['category'] ?? '');
$_minPrice  = (string) ($_filters['min_price'] ?? '');
$_maxPrice  = (string) ($_filters['max_price'] ?? '');
?>

<?php $view->startSection('head') ?>
<style>
  .shop-page {
    padding-top: calc(var(--nav-height, 72px) + 2rem);
  }

  .shop-hero {
    background: #0a0a0a;
    color: #fff;
    padding: 3rem 0 2.2rem;
    text-align: center;
  }

  .shop-hero h1 {
    font-family: Georgia, serif;
    font-size: clamp(2rem, 4vw, 3.2rem);
    font-weight: 400;
  }

  .shop-hero p {
    color: rgba(255, 255, 255, .52);
    font-size: .9rem;
    margin-top: .6rem;
  }

  .shop-layout {
    display: grid;
    grid-template-columns: 270px 1fr;
    gap: 1.5rem;
    padding: 2rem 0 3rem;
  }

  .shop-sidebar {
    background: #fff;
    border: 1px solid #e8e6e2;
    border-radius: 10px;
    padding: 1rem;
    position: sticky;
    top: calc(72px + 1rem);
  }

  .f-title {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: #7a7570;
    margin-bottom: .9rem;
  }

  .f-group {
    margin-bottom: 1rem;
  }

  .f-label {
    display: block;
    font-size: .72rem;
    color: #7a7570;
    margin-bottom: .35rem;
    font-weight: 600;
  }

  .f-input,
  .f-select {
    width: 100%;
    padding: .6rem .75rem;
    border: 1.5px solid #e8e6e2;
    border-radius: 8px;
    font-size: .85rem;
    box-sizing: border-box;
  }

  .f-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .5rem;
  }

  .f-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .5rem;
  }

  .btn-filter {
    border: none;
    border-radius: 8px;
    padding: .65rem;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    cursor: pointer;
  }

  .btn-filter.apply {
    background: #0a0a0a;
    color: #fff;
  }

  .btn-filter.clear {
    background: #fff;
    border: 1px solid #e8e6e2;
    color: #7a7570;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .shop-main {
    min-width: 0;
  }

  .shop-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
  }

  .result-meta {
    font-size: .78rem;
    color: #7a7570;
  }

  .products-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
  }

  .p-card {
    background: #fff;
    border: 1px solid #e8e6e2;
    border-radius: 10px;
    overflow: hidden;
  }

  .p-image {
    aspect-ratio: 3/4;
    display: block;
    background: #f4f3f1;
  }

  .p-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .p-body {
    padding: .8rem;
  }

  .p-name {
    display: block;
    color: #0a0a0a;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: .35rem;
  }

  .p-price {
    font-size: .95rem;
    font-weight: 700;
    margin-bottom: .6rem;
  }

  .p-actions {
    display: flex;
    gap: .45rem;
  }

  .btn-p {
    flex: 1;
    border: none;
    border-radius: 7px;
    padding: .58rem .6rem;
    font-size: .69rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    cursor: pointer;
  }

  .btn-p.cart {
    background: #0a0a0a;
    color: #fff;
  }

  .btn-p.wish {
    background: #fff;
    border: 1px solid #e8e6e2;
    color: #7a7570;
  }

  .btn-p.wish.active {
    border-color: #0a0a0a;
    color: #0a0a0a;
  }

  .pagination {
    margin-top: 1.2rem;
    display: flex;
    gap: .4rem;
    flex-wrap: wrap;
  }

  .pagination a,
  .pagination span {
    min-width: 34px;
    text-align: center;
    padding: .45rem .55rem;
    border: 1px solid #e8e6e2;
    border-radius: 6px;
    font-size: .78rem;
    text-decoration: none;
    color: #4a4743;
    background: #fff;
  }

  .pagination .active {
    background: #0a0a0a;
    color: #fff;
    border-color: #0a0a0a;
  }

  @media (max-width: 950px) {
    .shop-layout {
      grid-template-columns: 1fr;
    }

    .shop-sidebar {
      position: static;
    }

    .products-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }
</style>
<?php $view->endSection() ?>

<div class="shop-page">
  <div class="shop-hero">
    <div class="container">
      <h1>Shop Collection</h1>
      <p>Filter by category, budget, and sorting to find the right fit faster.</p>
    </div>
  </div>

  <div class="container">
    <div class="shop-layout">
      <aside class="shop-sidebar">
        <form method="GET" action="<?= url('products') ?>">
          <div class="f-title">Filters</div>

          <div class="f-group">
            <label class="f-label" for="f_q">Search</label>
            <input class="f-input" id="f_q" name="q" value="<?= e($_q) ?>" placeholder="Search products">
          </div>

          <div class="f-group">
            <label class="f-label" for="f_category">Category</label>
            <select class="f-select" id="f_category" name="category">
              <option value="">All categories</option>
              <?php foreach ($_categories as $cat): ?>
                <option value="<?= e($cat->slug) ?>" <?= $_category === (string) $cat->slug ? 'selected' : '' ?>>
                  <?= e($cat->name) ?> (<?= (int) ($cat->product_count ?? 0) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="f-group">
            <label class="f-label">Price Range</label>
            <div class="f-row">
              <input class="f-input" type="number" step="0.01" min="0" name="min_price" value="<?= e($_minPrice) ?>" placeholder="Min">
              <input class="f-input" type="number" step="0.01" min="0" name="max_price" value="<?= e($_maxPrice) ?>" placeholder="Max">
            </div>
          </div>

          <div class="f-group">
            <label class="f-label" for="f_sort">Sort</label>
            <select class="f-select" id="f_sort" name="sort">
              <option value="newest" <?= $_sort === 'newest' ? 'selected' : '' ?>>Newest</option>
              <option value="price_asc" <?= $_sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
              <option value="price_desc" <?= $_sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
              <option value="name" <?= $_sort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
            </select>
          </div>

          <div class="f-actions">
            <button type="submit" class="btn-filter apply">Apply</button>
            <a class="btn-filter clear" href="<?= url('products') ?>">Clear</a>
          </div>
        </form>
      </aside>

      <div class="shop-main">
        <div class="shop-toolbar">
          <div class="result-meta">Showing <?= count($_products) ?> of <?= (int) ($totalProducts ?? 0) ?> products</div>
        </div>

        <?php if (empty($_products)): ?>
          <div style="padding:2rem;background:#fff;border:1px solid #e8e6e2;border-radius:10px;">No products match your filters.</div>
        <?php else: ?>
          <div class="products-grid">
            <?php foreach ($_products as $p):
              $inWish = in_array((int) $p->id, $_wishlistIds, true);
            ?>
              <article class="p-card">
                <a class="p-image" href="<?= url('product/' . (int) $p->id) ?>">
                  <img src="<?= productImg($p->primary_image ?? null) ?>" alt="<?= e($p->name) ?>" loading="lazy">
                </a>
                <div class="p-body">
                  <a class="p-name" href="<?= url('product/' . (int) $p->id) ?>"><?= e($p->name) ?></a>
                  <div class="p-price"><?= formatPrice($p->price) ?></div>

                  <div class="p-actions">
                    <a href="<?= url('product/' . (int) $p->id) ?>" class="btn-p cart" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">Add to Cart</a>

                    <?php if (Session::isLoggedIn()): ?>
                      <form method="POST" action="<?= url('account/wishlist/toggle') ?>" style="flex:1;">
                        <?= csrfField() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $p->id ?>">
                        <button type="submit" class="btn-p wish <?= $inWish ? 'active' : '' ?>"><?= $inWish ? 'Saved' : 'Wishlist' ?></button>
                      </form>
                    <?php else: ?>
                      <a href="<?= url('login') ?>" class="btn-p wish" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">Wishlist</a>
                    <?php endif; ?>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>

          <?php if (($_pagination['pages'] ?? 1) > 1): ?>
            <div class="pagination">
              <?php for ($i = 1; $i <= (int) $_pagination['pages']; $i++):
                $params = array_filter([
                  'q' => $_q,
                  'category' => $_category,
                  'min_price' => $_minPrice,
                  'max_price' => $_maxPrice,
                  'sort' => $_sort,
                  'page' => $i,
                ], fn($v) => $v !== '' && $v !== null);
                $urlPage = url('products') . (empty($params) ? '' : ('?' . http_build_query($params)));
              ?>
                <?php if ($i === (int) $_pagination['page']): ?>
                  <span class="active"><?= $i ?></span>
                <?php else: ?>
                  <a href="<?= $urlPage ?>"><?= $i ?></a>
                <?php endif; ?>
              <?php endfor; ?>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>