<?php

/**
 * app/views/checkout/success.php
 * Order confirmation page shown after a successful checkout.
 */
$_order              = $order              ?? null;
$_items              = $orderItems         ?? [];
$_isGuestOrder       = $isGuestOrder       ?? false;
$_guestCustomerId    = $guestCustomerId    ?? null;
$_trackingToken      = $trackingToken      ?? null;
$_hasExistingAccount = $hasExistingAccount ?? false;
?>
<?php $view->startSection('head') ?>
<style>
    /* ── Page ── */
    .suc-page {
        padding-top: calc(72px + 3rem);
        padding-bottom: 5rem;
        min-height: 80vh;
        background: #f6f5f3;
        text-align: center;
    }

    /* ── Check icon ── */
    .suc-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #edf7ed;
        border: 2px solid #a8d8a8;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: #3a7a3a;
    }

    .suc-eyebrow {
        font-size: .67rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: #c4a97a;
        display: block;
        margin-bottom: .5rem;
    }

    .suc-title {
        font-family: Georgia, serif;
        font-size: 2rem;
        font-weight: normal;
        color: #0a0a0a;
        margin: 0 0 .5rem;
    }

    .suc-sub {
        font-size: .9rem;
        color: #7a7570;
        margin: 0 0 2.25rem;
    }

    /* ── Order details card ── */
    .suc-card {
        background: #fff;
        border: 1px solid #e8e6e2;
        border-radius: 12px;
        max-width: 680px;
        margin: 0 auto 1.5rem;
        text-align: left;
    }

    .suc-card-header {
        padding: 1.1rem 1.5rem;
        background: #fafaf9;
        border-bottom: 1px solid #e8e6e2;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .suc-card-header-title {
        font-size: .67rem;
        font-weight: 700;
        letter-spacing: .17em;
        text-transform: uppercase;
        color: #7a7570;
    }

    .suc-order-num {
        font-family: monospace;
        font-size: .9rem;
        font-weight: 700;
        color: #0a0a0a;
    }

    /* Meta grid */
    .suc-meta {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0;
        border-bottom: 1px solid #e8e6e2;
    }

    .suc-meta-col {
        padding: 1.1rem 1.5rem;
        border-right: 1px solid #e8e6e2;
    }

    .suc-meta-col:last-child {
        border-right: none;
    }

    .suc-meta-lbl {
        font-size: .67rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #7a7570;
        display: block;
        margin-bottom: .3rem;
    }

    .suc-meta-val {
        font-size: .875rem;
        font-weight: 600;
        color: #0a0a0a;
        text-transform: capitalize;
    }

    /* Status badge */
    .suc-badge {
        display: inline-block;
        padding: .2rem .65rem;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        background: #fef3cd;
        color: #856404;
    }

    /* Items table */
    .suc-items {
        padding: 0;
    }

    .suc-item {
        display: grid;
        grid-template-columns: 44px 1fr auto;
        gap: .85rem;
        align-items: center;
        padding: .9rem 1.5rem;
        border-bottom: 1px solid #f4f3f1;
    }

    .suc-item:last-child {
        border-bottom: none;
    }

    .suc-item-img {
        width: 44px;
        height: 44px;
        border-radius: 6px;
        object-fit: cover;
        background: #f0eeea;
    }

    .suc-item-img-ph {
        width: 44px;
        height: 44px;
        border-radius: 6px;
        background: #f0eeea;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c0bbb6;
    }

    .suc-item-name {
        font-size: .875rem;
        font-weight: 600;
        color: #0a0a0a;
        display: block;
        margin-bottom: .15rem;
    }

    .suc-item-meta {
        font-size: .75rem;
        color: #7a7570;
    }

    .suc-item-price {
        font-size: .875rem;
        font-weight: 600;
        color: #0a0a0a;
        white-space: nowrap;
    }

    /* Totals footer */
    .suc-totals {
        padding: 1rem 1.5rem;
        background: #fafaf9;
        border-top: 1px solid #e8e6e2;
        border-radius: 0 0 12px 12px;
    }

    .suc-total-row {
        display: flex;
        justify-content: space-between;
        font-size: .86rem;
        color: #4a4743;
        margin-bottom: .5rem;
    }

    .suc-total-row.grand {
        font-size: .95rem;
        font-weight: 700;
        color: #0a0a0a;
        padding-top: .65rem;
        border-top: 1px solid #e8e6e2;
        margin-top: .25rem;
        margin-bottom: 0;
    }

    .suc-free {
        color: #3a7a3a;
        font-weight: 600;
    }

    /* ── Actions ── */
    .suc-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        max-width: 680px;
        margin: 0 auto;
    }

    .btn-primary-link {
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

    .btn-primary-link:hover {
        background: #2a2a2a;
    }

    .btn-outline-link {
        display: inline-block;
        padding: .82rem 2rem;
        background: #fff;
        color: #0a0a0a;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        border: 2px solid #e8e6e2;
        border-radius: 8px;
        text-decoration: none;
        transition: border-color .18s, background .18s;
    }

    .btn-outline-link:hover {
        border-color: #0a0a0a;
        background: #f6f5f3;
    }

    /* Shipping address card */
    .suc-addr {
        background: #fff;
        border: 1px solid #e8e6e2;
        border-radius: 12px;
        max-width: 680px;
        margin: 0 auto 1.5rem;
        text-align: left;
        padding: 1.25rem 1.5rem;
    }

    .suc-addr-title {
        font-size: .67rem;
        font-weight: 700;
        letter-spacing: .17em;
        text-transform: uppercase;
        color: #7a7570;
        margin-bottom: .75rem;
        padding-bottom: .5rem;
        border-bottom: 1px solid #e8e6e2;
    }

    .suc-addr-val {
        font-size: .875rem;
        color: #0a0a0a;
        line-height: 1.6;
    }

    /* Guest account creation card */
    .suc-create-account {
        background: linear-gradient(135deg, #f8f6f0 0%, #fff 100%);
        border: 2px solid #c4a97a;
        border-radius: 12px;
        max-width: 680px;
        margin: 0 auto 1.5rem;
        padding: 1.5rem;
        text-align: left;
    }

    .suc-create-account-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .suc-create-account-icon {
        width: 40px;
        height: 40px;
        background: #c4a97a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }

    .suc-create-account-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #0a0a0a;
        margin: 0;
    }

    .suc-create-account-desc {
        font-size: 0.85rem;
        color: #7a7570;
        margin: 0;
    }

    .suc-create-account-benefits {
        display: flex;
        gap: 1.5rem;
        margin: 1rem 0;
        flex-wrap: wrap;
    }

    .suc-benefit {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: #4a4743;
    }

    .suc-benefit svg {
        color: #3a7a3a;
        flex-shrink: 0;
    }

    .suc-create-account-form {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 0.75rem;
        align-items: end;
    }

    .suc-create-account-form .field-group {
        margin: 0;
    }

    .suc-create-account-form .field-lbl {
        font-size: 0.75rem;
        margin-bottom: 0.3rem;
    }

    .suc-create-account-form .field-inp {
        padding: 0.6rem 0.75rem;
        font-size: 0.85rem;
    }

    .suc-create-btn {
        padding: 0.65rem 1.25rem;
        background: #0a0a0a;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.18s;
        white-space: nowrap;
    }

    .suc-create-btn:hover {
        background: #2a2a2a;
    }

    .suc-skip-link {
        display: block;
        text-align: center;
        margin-top: 0.75rem;
        font-size: 0.8rem;
        color: #7a7570;
    }

    .suc-skip-link a {
        color: #0a0a0a;
        text-decoration: underline;
    }

    /* Tracking link card */
    .suc-tracking {
        background: #edf7ed;
        border: 1px solid #a8d8a8;
        border-radius: 12px;
        max-width: 680px;
        margin: 0 auto 1.5rem;
        padding: 1rem 1.5rem;
        text-align: center;
    }

    .suc-tracking-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #2d5f2d;
        margin: 0 0 0.5rem;
    }

    .suc-tracking-link {
        font-size: 0.8rem;
        color: #3a7a3a;
        word-break: break-all;
    }

    .suc-tracking-link a {
        color: #3a7a3a;
        text-decoration: underline;
    }

    .suc-tracking-note {
        font-size: 0.75rem;
        color: #5a8a5a;
        margin: 0.5rem 0 0;
    }

    /* Existing account notice */
    .suc-existing-account {
        background: #fef9e7;
        border: 1px solid #f5e6a3;
        border-radius: 12px;
        max-width: 680px;
        margin: 0 auto 1.5rem;
        padding: 1rem 1.5rem;
        text-align: center;
    }

    .suc-existing-account p {
        margin: 0;
        font-size: 0.9rem;
        color: #856404;
    }

    .suc-existing-account a {
        color: #0a0a0a;
        font-weight: 600;
    }

    @media (max-width: 600px) {
        .suc-meta {
            grid-template-columns: 1fr 1fr;
        }

        .suc-meta-col:nth-child(2) {
            border-right: none;
        }

        .suc-meta-col:nth-child(3) {
            grid-column: 1 / -1;
            border-right: none;
            border-top: 1px solid #e8e6e2;
        }

        .suc-create-account-form {
            grid-template-columns: 1fr;
        }

        .suc-create-account-benefits {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
<?php $view->endSection() ?>

<div class="suc-page">
    <div class="container">

        <!-- Check icon -->
        <div class="suc-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12" />
            </svg>
        </div>

        <span class="suc-eyebrow">Order Confirmed</span>
        <h1 class="suc-title">Thank You<?= $_order ? ', ' . htmlspecialchars(explode(' ', $_order->customer_name ?? '')[0]) : '' ?>!</h1>
        <p class="suc-sub">
            Your order has been placed and is being processed.<br>
            We&rsquo;ll notify you when it&rsquo;s on its way.
        </p>

        <?php if ($_order): ?>

            <!-- Order details -->
            <div class="suc-card">
                <div class="suc-card-header">
                    <span class="suc-card-header-title">Order Details</span>
                    <span class="suc-order-num">#<?= str_pad((string) $_order->id, 5, '0', STR_PAD_LEFT) ?></span>
                </div>

                <div class="suc-meta">
                    <div class="suc-meta-col">
                        <span class="suc-meta-lbl">Date</span>
                        <span class="suc-meta-val"><?= date('M j, Y', strtotime($_order->created_at)) ?></span>
                    </div>
                    <div class="suc-meta-col">
                        <span class="suc-meta-lbl">Payment</span>
                        <span class="suc-meta-val"><?= htmlspecialchars(str_replace('_', ' ', $_order->payment_method ?? 'cash on delivery')) ?></span>
                    </div>
                    <div class="suc-meta-col">
                        <span class="suc-meta-lbl">Status</span>
                        <span class="suc-badge">Pending</span>
                    </div>
                </div>

                <!-- Line items -->
                <div class="suc-items">
                    <?php foreach ($_items as $item): ?>
                        <div class="suc-item">
                            <?php if (!empty($item->product_image) && file_exists(BASE_PATH . '/public/assets/images/' . ltrim($item->product_image, '/'))): ?>
                                <img class="suc-item-img"
                                    src="<?= productImg($item->product_image) ?>"
                                    alt="<?= htmlspecialchars($item->product_name ?? '') ?>">
                            <?php else: ?>
                                <div class="suc-item-img-ph">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <polyline points="21 15 16 10 5 21" />
                                    </svg>
                                </div>
                            <?php endif ?>
                            <div>
                                <span class="suc-item-name"><?= htmlspecialchars($item->product_name ?? 'Product') ?></span>
                                <span class="suc-item-meta">
                                    Qty: <?= (int) $item->quantity ?>
                                    <?php if (!empty($item->size)): ?> &middot; Size: <?= htmlspecialchars($item->size) ?><?php endif ?>
                                </span>
                            </div>
                            <span class="suc-item-price"><?= formatPrice((float) $item->price * (int) $item->quantity) ?></span>
                        </div>
                    <?php endforeach ?>
                </div>

                <!-- Totals -->
                <div class="suc-totals">
                    <div class="suc-total-row">
                        <span>Subtotal</span>
                        <span><?= formatPrice($_order->subtotal ?? 0) ?></span>
                    </div>
                    <div class="suc-total-row">
                        <span>Shipping</span>
                        <?php if ((float)($_order->shipping_fee ?? 0) === 0.0): ?>
                            <span class="suc-free">Free</span>
                        <?php else: ?>
                            <span><?= formatPrice($_order->shipping_fee) ?></span>
                        <?php endif ?>
                    </div>
                    <div class="suc-total-row grand">
                        <span>Total Charged</span>
                        <span><?= formatPrice($_order->total_price ?? 0) ?></span>
                    </div>
                </div>
            </div>

            <!-- Shipping address -->
            <?php if (!empty($_order->customer_address)): ?>
                <div class="suc-addr">
                    <div class="suc-addr-title">Shipping Address</div>
                    <div class="suc-addr-val">
                        <strong><?= htmlspecialchars($_order->customer_name ?? '') ?></strong><br>
                        <?= htmlspecialchars($_order->customer_address ?? '') ?><br>
                        <?= htmlspecialchars($_order->customer_city ?? '') ?>
                        <?php if (!empty($_order->customer_phone)): ?>
                            <br><?= htmlspecialchars($_order->customer_phone) ?>
                        <?php endif ?>
                    </div>
                </div>
            <?php endif ?>

            <?php if ($_isGuestOrder && $_trackingToken): ?>
                <!-- Guest order tracking link -->
                <div class="suc-tracking">
                    <div class="suc-tracking-title">📬 Bookmark Your Order Tracking Link</div>
                    <div class="suc-tracking-link">
                        <a href="<?= url('order/track/' . $_trackingToken) ?>"><?= url('order/track/' . $_trackingToken) ?></a>
                    </div>
                    <p class="suc-tracking-note">Use this link to check your order status anytime without logging in.</p>
                </div>
            <?php endif ?>

            <?php if ($_isGuestOrder && $_hasExistingAccount): ?>
                <!-- Existing account notice -->
                <div class="suc-existing-account">
                    <p>
                        <strong>You already have an account with this email!</strong><br>
                        <a href="<?= url('login') ?>">Log in</a> to view all your orders and enjoy faster checkout next time.
                    </p>
                </div>
            <?php elseif ($_isGuestOrder && $_guestCustomerId && !$_hasExistingAccount): ?>
                <!-- Guest account creation prompt -->
                <div class="suc-create-account">
                    <div class="suc-create-account-header">
                        <div class="suc-create-account-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="suc-create-account-title">Save your details for next time</h3>
                            <p class="suc-create-account-desc">Create an account with just a password</p>
                        </div>
                    </div>

                    <div class="suc-create-account-benefits">
                        <div class="suc-benefit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Track all your orders
                        </div>
                        <div class="suc-benefit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Faster checkout
                        </div>
                        <div class="suc-benefit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Save your wishlist
                        </div>
                    </div>

                    <form method="POST" action="<?= url('checkout/register') ?>" class="suc-create-account-form">
                        <?= csrfField() ?>
                        <input type="hidden" name="customer_id" value="<?= (int) $_guestCustomerId ?>">
                        
                        <div class="field-group">
                            <label class="field-lbl" for="reg-password">Password</label>
                            <input class="field-inp" type="password" id="reg-password" name="password" 
                                required minlength="8" placeholder="Min. 8 characters">
                        </div>
                        
                        <div class="field-group">
                            <label class="field-lbl" for="reg-password-confirm">Confirm Password</label>
                            <input class="field-inp" type="password" id="reg-password-confirm" name="password_confirm" 
                                required minlength="8" placeholder="Confirm password">
                        </div>
                        
                        <button type="submit" class="suc-create-btn">Create Account</button>
                    </form>

                    <p class="suc-skip-link">
                        No thanks, <a href="<?= url('products') ?>">continue shopping</a>
                    </p>
                </div>
            <?php endif ?>

        <?php endif ?>

        <!-- WhatsApp share -->
        <?php
        $_waText = 'I just ordered from ' . APP_NAME . '! Check them out 🛍️ ' . url('products');
        $_waUrl  = 'https://wa.me/?text=' . rawurlencode($_waText);
        ?>
        <div style="text-align:center;margin-bottom:1rem;">
            <a href="<?= e($_waUrl) ?>" target="_blank" rel="noopener noreferrer"
               style="display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.25rem;background:#25d366;color:#fff;border-radius:8px;font-size:.82rem;font-weight:700;text-decoration:none;transition:background .2s;"
               onmouseover="this.style.background='#1ebe5d'" onmouseout="this.style.background='#25d366'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.138.564 4.14 1.544 5.875L0 24l6.341-1.524A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.882a9.874 9.874 0 01-5.031-1.378l-.36-.214-3.733.898.933-3.64-.235-.374A9.86 9.86 0 012.118 12C2.118 6.534 6.534 2.118 12 2.118S21.882 6.534 21.882 12 17.466 21.882 12 21.882z"/>
                </svg>
                Share on WhatsApp
            </a>
        </div>

        <!-- Actions -->
        <div class="suc-actions">
            <?php if ($_isGuestOrder): ?>
                <a href="<?= url('products') ?>" class="btn-primary-link">Continue Shopping</a>
                <?php if ($_trackingToken): ?>
                    <a href="<?= url('order/track/' . $_trackingToken) ?>" class="btn-outline-link">Track Order</a>
                <?php endif ?>
            <?php else: ?>
                <a href="<?= url('account/orders') ?>" class="btn-primary-link">View My Orders</a>
                <a href="<?= url('products') ?>" class="btn-outline-link">Continue Shopping</a>
            <?php endif ?>
        </div>

    </div>
</div>