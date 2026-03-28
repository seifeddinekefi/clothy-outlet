<?php

/**
 * app/views/checkout/success.php
 * Order confirmation page shown after a successful checkout.
 */
$_order     = $order      ?? null;
$_items     = $orderItems ?? [];
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
                            <span class="suc-item-price">$<?= number_format((float) $item->price * (int) $item->quantity, 2) ?></span>
                        </div>
                    <?php endforeach ?>
                </div>

                <!-- Totals -->
                <div class="suc-totals">
                    <div class="suc-total-row">
                        <span>Subtotal</span>
                        <span>$<?= number_format((float) ($_order->subtotal ?? 0), 2) ?></span>
                    </div>
                    <div class="suc-total-row">
                        <span>Shipping</span>
                        <?php if ((float)($_order->shipping_fee ?? 0) === 0.0): ?>
                            <span class="suc-free">Free</span>
                        <?php else: ?>
                            <span>$<?= number_format((float) $_order->shipping_fee, 2) ?></span>
                        <?php endif ?>
                    </div>
                    <div class="suc-total-row grand">
                        <span>Total Charged</span>
                        <span>$<?= number_format((float) ($_order->total_price ?? 0), 2) ?></span>
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

        <?php endif ?>

        <!-- Actions -->
        <div class="suc-actions">
            <a href="<?= url('account/orders') ?>" class="btn-primary-link">View My Orders</a>
            <a href="<?= url('products') ?>" class="btn-outline-link">Continue Shopping</a>
        </div>

    </div>
</div>