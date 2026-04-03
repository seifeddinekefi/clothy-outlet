<?php

/**
 * app/views/checkout/index.php
 * Checkout page — shipping info + payment method + order summary.
 */
$_items    = $items    ?? [];
$_subtotal = $subtotal ?? 0;
$_shipping = $shipping ?? 0;
$_discount = $discount ?? 0;
$_coupon   = $coupon   ?? null;
$_total    = $total    ?? 0;
$_customer = $customer ?? null;
?>
<?php $view->startSection('head') ?>
<style>
    /* ── Page ── */
    .co-page {
        padding-top: calc(72px + 2.5rem);
        padding-bottom: 5rem;
        min-height: 80vh;
        background: #f6f5f3;
    }

    .co-eyebrow {
        font-size: .67rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: #c4a97a;
        display: block;
        margin-bottom: .5rem;
    }

    .co-title {
        font-family: Georgia, serif;
        font-size: 2rem;
        font-weight: normal;
        color: #0a0a0a;
        margin: 0 0 .35rem;
    }

    .co-sub {
        font-size: .875rem;
        color: #7a7570;
        margin: 0 0 2rem;
    }

    /* ── Two-col grid ── */
    .co-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.75rem;
        align-items: start;
    }

    /* ── Cards ── */
    .co-card {
        background: #fff;
        border: 1px solid #e8e6e2;
        border-radius: 12px;
        padding: 1.75rem;
        margin-bottom: 1.25rem;
    }

    .co-card:last-child {
        margin-bottom: 0;
    }

    .co-card-title {
        font-size: .67rem;
        font-weight: 700;
        letter-spacing: .17em;
        text-transform: uppercase;
        color: #7a7570;
        margin-bottom: 1.25rem;
        padding-bottom: .65rem;
        border-bottom: 1px solid #e8e6e2;
    }

    /* ── Form fields ── */
    .field-group {
        margin-bottom: 1rem;
    }

    .field-lbl {
        display: block;
        font-size: .78rem;
        font-weight: 600;
        color: #4a4743;
        margin-bottom: .4rem;
    }

    .field-inp,
    .field-select,
    .field-textarea {
        width: 100%;
        padding: .65rem .85rem;
        border: 1.5px solid #e8e6e2;
        border-radius: 8px;
        font-size: .875rem;
        color: #0a0a0a;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
    }

    .field-inp:focus,
    .field-select:focus,
    .field-textarea:focus {
        outline: none;
        border-color: #c4a97a;
        box-shadow: 0 0 0 3px rgba(196, 169, 122, .12);
    }

    .field-textarea {
        resize: vertical;
        min-height: 80px;
    }

    .field-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    /* ── Payment radio ── */
    .pay-options {
        display: flex;
        flex-direction: column;
        gap: .7rem;
    }

    .pay-option {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: .85rem 1rem;
        border: 1.5px solid #e8e6e2;
        border-radius: 8px;
        cursor: pointer;
        transition: border-color .14s, background .14s;
    }

    .pay-option:has(input:checked),
    .pay-option.selected {
        border-color: #c4a97a;
        background: #fdfaf5;
    }

    .pay-option input[type="radio"] {
        accent-color: #c4a97a;
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .pay-option-info {
        flex: 1;
    }

    .pay-option-name {
        font-size: .875rem;
        font-weight: 600;
        color: #0a0a0a;
        display: block;
    }

    .pay-option-desc {
        font-size: .75rem;
        color: #7a7570;
        display: block;
        margin-top: .1rem;
    }

    .pay-option-icon {
        color: #b0aca6;
        flex-shrink: 0;
    }

    /* ── Order Summary card ── */
    .co-summary {
        background: #fff;
        border: 1px solid #e8e6e2;
        border-radius: 12px;
        padding: 1.5rem;
        position: sticky;
        top: calc(72px + 1.25rem);
    }

    .co-summary-title {
        font-size: .67rem;
        font-weight: 700;
        letter-spacing: .17em;
        text-transform: uppercase;
        color: #7a7570;
        margin-bottom: 1.25rem;
        padding-bottom: .65rem;
        border-bottom: 1px solid #e8e6e2;
    }

    /* Items in summary */
    .co-sum-items {
        margin-bottom: 1rem;
    }

    .co-sum-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .5rem 0;
        border-bottom: 1px solid #f4f3f1;
    }

    .co-sum-item:last-child {
        border-bottom: none;
    }

    .co-sum-img {
        width: 44px;
        height: 44px;
        border-radius: 6px;
        object-fit: cover;
        background: #f0eeea;
        flex-shrink: 0;
    }

    .co-sum-img-ph {
        width: 44px;
        height: 44px;
        border-radius: 6px;
        background: #f0eeea;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c0bbb6;
        flex-shrink: 0;
    }

    .co-sum-name {
        font-size: .82rem;
        font-weight: 600;
        color: #0a0a0a;
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .co-sum-qty {
        font-size: .75rem;
        color: #7a7570;
        display: block;
        margin-top: .1rem;
    }

    .co-sum-price {
        font-size: .82rem;
        font-weight: 600;
        color: #0a0a0a;
        white-space: nowrap;
    }

    /* Totals */
    .co-sum-row {
        display: flex;
        justify-content: space-between;
        font-size: .86rem;
        color: #4a4743;
        margin-bottom: .75rem;
    }

    .co-sum-row.total {
        font-size: .95rem;
        font-weight: 700;
        color: #0a0a0a;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e8e6e2;
        margin-bottom: 1.35rem;
    }

    .co-sum-free {
        color: #3a7a3a;
        font-weight: 600;
    }

    .btn-place {
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

    .btn-place:hover {
        background: #2a2a2a;
        transform: translateY(-1px);
    }

    .btn-back-cart {
        display: block;
        text-align: center;
        font-size: .78rem;
        color: #7a7570;
        text-decoration: none;
        transition: color .13s;
    }

    .btn-back-cart:hover {
        color: #0a0a0a;
    }

    .co-secure-note {
        display: flex;
        align-items: center;
        gap: .4rem;
        font-size: .72rem;
        color: #7a7570;
        justify-content: center;
        margin-top: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .co-grid {
            grid-template-columns: 1fr;
        }

        .co-summary {
            position: static;
        }

        .field-row-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
<?php $view->endSection() ?>

<div class="co-page">
    <div class="container">

        <span class="co-eyebrow">Checkout</span>
        <h1 class="co-title">Complete Your Order</h1>
        <p class="co-sub">Review your items, enter shipping details, and choose how you'd like to pay.</p>

        <form method="POST" action="<?= url('checkout/place') ?>" novalidate>
            <?= csrfField() ?>

            <div class="co-grid">

                <!-- ── Left column ────────────────────────────────── -->
                <div>

                    <!-- Shipping information -->
                    <div class="co-card">
                        <div class="co-card-title">Shipping Information</div>

                        <div class="field-group">
                            <label class="field-lbl" for="co-name">Full Name <span style="color:#c03030">*</span></label>
                            <input class="field-inp" type="text" id="co-name" name="name" required autocomplete="name"
                                value="<?= htmlspecialchars($_customer->name ?? '') ?>">
                        </div>

                        <div class="field-row-2">
                            <div class="field-group">
                                <label class="field-lbl" for="co-email">Email Address</label>
                                <input class="field-inp" type="email" id="co-email"
                                    autocomplete="email" disabled
                                    value="<?= htmlspecialchars($_customer->email ?? '') ?>"
                                    title="Email cannot be changed here">
                            </div>
                            <div class="field-group">
                                <label class="field-lbl" for="co-phone">Phone</label>
                                <input class="field-inp" type="tel" id="co-phone" name="phone"
                                    autocomplete="tel" placeholder="+1 (555) 000-0000"
                                    value="<?= htmlspecialchars($_customer->phone ?? '') ?>">
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-lbl" for="co-address">Street Address <span style="color:#c03030">*</span></label>
                            <input class="field-inp" type="text" id="co-address" name="address" required
                                autocomplete="street-address" placeholder="123 Main St, Apt 4B"
                                value="<?= htmlspecialchars($_customer->address ?? '') ?>">
                        </div>

                        <div class="field-group">
                            <label class="field-lbl" for="co-city">City <span style="color:#c03030">*</span></label>
                            <input class="field-inp" type="text" id="co-city" name="city" required
                                autocomplete="address-level2" placeholder="New York"
                                value="<?= htmlspecialchars($_customer->city ?? '') ?>">
                        </div>

                        <div class="field-group">
                            <label class="field-lbl" for="co-notes">Delivery Notes <span style="font-weight:400;color:#7a7570">(optional)</span></label>
                            <textarea class="field-textarea" id="co-notes" name="notes"
                                placeholder="E.g. leave at the door, ring bell twice…"><?= htmlspecialchars($_customer->notes ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Payment method -->
                    <div class="co-card">
                        <div class="co-card-title">Payment Method</div>

                        <div class="pay-options">

                            <label class="pay-option">
                                <input type="radio" name="payment_method" value="cash_on_delivery" checked>
                                <span class="pay-option-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="7" width="20" height="14" rx="2" />
                                        <path d="M16 7V5a4 4 0 0 0-8 0v2" />
                                    </svg>
                                </span>
                                <span class="pay-option-info">
                                    <span class="pay-option-name">Cash on Delivery</span>
                                    <span class="pay-option-desc">Pay when your order arrives</span>
                                </span>
                            </label>

                            <label class="pay-option">
                                <input type="radio" name="payment_method" value="card">
                                <span class="pay-option-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="5" width="20" height="14" rx="2" />
                                        <line x1="2" y1="10" x2="22" y2="10" />
                                    </svg>
                                </span>
                                <span class="pay-option-info">
                                    <span class="pay-option-name">Credit / Debit Card</span>
                                    <span class="pay-option-desc">Visa, Mastercard, Amex</span>
                                </span>
                            </label>

                            <label class="pay-option">
                                <input type="radio" name="payment_method" value="paypal">
                                <span class="pay-option-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 11C7 11 6 17 9 19s8 1 10-3-2-6-5-6H7z" />
                                        <path d="M7 11C6 11 5 17 5 20h2" />
                                    </svg>
                                </span>
                                <span class="pay-option-info">
                                    <span class="pay-option-name">PayPal</span>
                                    <span class="pay-option-desc">Fast and secure via PayPal</span>
                                </span>
                            </label>

                        </div>
                    </div>

                </div>

                <!-- ── Right column: summary ──────────────────────── -->
                <div>
                    <div class="co-summary">
                        <div class="co-summary-title">Order Summary</div>

                        <div class="co-sum-items">
                            <?php foreach ($_items as $item):
                                $p = $item['product'];
                                $_productImgSrc = productImg($p->primary_image ?? null);
                                $_hasImage = !empty($p->primary_image);
                            ?>
                                <div class="co-sum-item">
                                    <?php if ($_hasImage): ?>
                                        <img class="co-sum-img"
                                            src="<?= $_productImgSrc ?>"
                                            alt="<?= htmlspecialchars($p->name) ?>">
                                    <?php else: ?>
                                        <div class="co-sum-img-ph">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                                <circle cx="8.5" cy="8.5" r="1.5" />
                                                <polyline points="21 15 16 10 5 21" />
                                            </svg>
                                        </div>
                                    <?php endif ?>
                                    <div style="flex:1;min-width:0">
                                        <span class="co-sum-name"><?= htmlspecialchars($p->name) ?></span>
                                        <span class="co-sum-qty">
                                            Qty: <?= $item['qty'] ?>
                                            <?php if (!empty($item['size'])): ?> · Size: <?= htmlspecialchars($item['size']) ?><?php endif ?>
                                        </span>
                                    </div>
                                    <span class="co-sum-price">$<?= number_format($item['lineTotal'], 2) ?></span>
                                </div>
                            <?php endforeach ?>
                        </div>

                        <div class="field-group" style="margin-bottom:1rem;">
                            <label class="field-lbl" for="coupon_code">Promo Code</label>
                            <div style="display:flex;gap:.5rem;">
                                <input class="field-inp" type="text" id="coupon_code" name="coupon_code" placeholder="e.g. WELCOME10" value="<?= htmlspecialchars($_coupon['code'] ?? '') ?>">
                                <button type="submit" class="btn-place" style="width:auto;margin:0;padding:.65rem 1rem;font-size:.65rem;"
                                    formaction="<?= url('checkout/coupon') ?>" formnovalidate>
                                    Apply
                                </button>
                            </div>
                            <?php if (!empty($_coupon['code'])): ?>
                                <button type="submit" style="margin-top:.45rem;border:none;background:none;color:#7a7570;font-size:.75rem;cursor:pointer;padding:0;"
                                    formaction="<?= url('checkout/coupon/remove') ?>" formnovalidate>
                                    Remove coupon (<?= htmlspecialchars($_coupon['code']) ?>)
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="co-sum-row">
                            <span>Subtotal</span>
                            <span>$<?= number_format($_subtotal, 2) ?></span>
                        </div>
                        <?php if ($_discount > 0): ?>
                            <div class="co-sum-row">
                                <span>Discount</span>
                                <span style="color:#3a7a3a;">-$<?= number_format($_discount, 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="co-sum-row">
                            <span>Shipping</span>
                            <?php if ($_shipping === 0.0 || $_shipping === 0): ?>
                                <span class="co-sum-free">Free</span>
                            <?php else: ?>
                                <span>$<?= number_format($_shipping, 2) ?></span>
                            <?php endif ?>
                        </div>

                        <div class="co-sum-row total">
                            <span>Total</span>
                            <span>$<?= number_format($_total, 2) ?></span>
                        </div>

                        <button type="submit" class="btn-place">Place Order &rarr;</button>
                        <a href="<?= url('cart') ?>" class="btn-back-cart">&larr; Back to Cart</a>

                        <div class="co-secure-note">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            Secure &amp; encrypted checkout
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

<?php $view->startSection('scripts') ?>
<script>
    // Highlight selected payment option
    document.querySelectorAll('.pay-option input[type="radio"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.pay-option').forEach(function(el) {
                el.classList.remove('selected');
            });
            if (this.checked) {
                this.closest('.pay-option').classList.add('selected');
            }
        });
    });

    // Mark default selected on load
    var checked = document.querySelector('.pay-option input[type="radio"]:checked');
    if (checked) {
        checked.closest('.pay-option').classList.add('selected');
    }
</script>
<?php $view->endSection() ?>