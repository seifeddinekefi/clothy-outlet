<?php

/**
 * app/views/checkout/track.php
 * Guest order tracking page - allows tracking orders without login.
 */
$_order = $order      ?? null;
$_items = $orderItems ?? [];

// Status steps for progress indicator
$_statusSteps = ['pending', 'confirmed', 'shipped', 'delivered'];
$_currentStatus = $_order->status ?? 'pending';
$_currentIndex = array_search($_currentStatus, $_statusSteps);
if ($_currentIndex === false) $_currentIndex = 0;
?>
<?php $view->startSection('head') ?>
<style>
    .track-page {
        padding-top: calc(72px + 3rem);
        padding-bottom: 5rem;
        min-height: 80vh;
        background: #f6f5f3;
    }

    .track-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .track-eyebrow {
        font-size: .67rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: #c4a97a;
        display: block;
        margin-bottom: .5rem;
    }

    .track-title {
        font-family: Georgia, serif;
        font-size: 2rem;
        font-weight: normal;
        color: #0a0a0a;
        margin: 0 0 .35rem;
    }

    .track-order-num {
        font-family: monospace;
        font-size: 1.1rem;
        color: #7a7570;
    }

    /* Progress tracker */
    .track-progress {
        background: #fff;
        border: 1px solid #e8e6e2;
        border-radius: 12px;
        max-width: 680px;
        margin: 0 auto 1.5rem;
        padding: 2rem;
    }

    .track-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 1rem;
    }

    .track-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 10%;
        right: 10%;
        height: 3px;
        background: #e8e6e2;
        z-index: 0;
    }

    .track-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 1;
        flex: 1;
    }

    .track-step-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #f6f5f3;
        border: 3px solid #e8e6e2;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c0bbb6;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .track-step.completed .track-step-icon {
        background: #3a7a3a;
        border-color: #3a7a3a;
        color: #fff;
    }

    .track-step.current .track-step-icon {
        background: #c4a97a;
        border-color: #c4a97a;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(196, 169, 122, 0.2);
    }

    .track-step-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #c0bbb6;
        text-align: center;
    }

    .track-step.completed .track-step-label,
    .track-step.current .track-step-label {
        color: #0a0a0a;
    }

    .track-status-message {
        text-align: center;
        padding-top: 1rem;
        border-top: 1px solid #e8e6e2;
    }

    .track-status-message p {
        margin: 0;
        font-size: 0.9rem;
        color: #4a4743;
    }

    /* Cancelled status */
    .track-cancelled {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .track-cancelled .track-status-message p {
        color: #dc2626;
        font-weight: 600;
    }

    /* Order card - reuse success page styles */
    .track-card {
        background: #fff;
        border: 1px solid #e8e6e2;
        border-radius: 12px;
        max-width: 680px;
        margin: 0 auto 1.5rem;
        text-align: left;
    }

    .track-card-header {
        padding: 1.1rem 1.5rem;
        background: #fafaf9;
        border-bottom: 1px solid #e8e6e2;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .track-card-title {
        font-size: .67rem;
        font-weight: 700;
        letter-spacing: .17em;
        text-transform: uppercase;
        color: #7a7570;
    }

    .track-meta {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0;
        border-bottom: 1px solid #e8e6e2;
    }

    .track-meta-col {
        padding: 1.1rem 1.5rem;
        border-right: 1px solid #e8e6e2;
    }

    .track-meta-col:last-child {
        border-right: none;
    }

    .track-meta-lbl {
        font-size: .67rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #7a7570;
        display: block;
        margin-bottom: .3rem;
    }

    .track-meta-val {
        font-size: .875rem;
        font-weight: 600;
        color: #0a0a0a;
        text-transform: capitalize;
    }

    .track-items {
        padding: 0;
    }

    .track-item {
        display: grid;
        grid-template-columns: 44px 1fr auto;
        gap: .85rem;
        align-items: center;
        padding: .9rem 1.5rem;
        border-bottom: 1px solid #f4f3f1;
    }

    .track-item:last-child {
        border-bottom: none;
    }

    .track-item-img {
        width: 44px;
        height: 44px;
        border-radius: 6px;
        object-fit: cover;
        background: #f0eeea;
    }

    .track-item-img-ph {
        width: 44px;
        height: 44px;
        border-radius: 6px;
        background: #f0eeea;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c0bbb6;
    }

    .track-item-name {
        font-size: .875rem;
        font-weight: 600;
        color: #0a0a0a;
        display: block;
        margin-bottom: .15rem;
    }

    .track-item-meta {
        font-size: .75rem;
        color: #7a7570;
    }

    .track-item-price {
        font-size: .875rem;
        font-weight: 600;
        color: #0a0a0a;
        white-space: nowrap;
    }

    .track-totals {
        padding: 1rem 1.5rem;
        background: #fafaf9;
        border-top: 1px solid #e8e6e2;
        border-radius: 0 0 12px 12px;
    }

    .track-total-row {
        display: flex;
        justify-content: space-between;
        font-size: .86rem;
        color: #4a4743;
        margin-bottom: .5rem;
    }

    .track-total-row.grand {
        font-size: .95rem;
        font-weight: 700;
        color: #0a0a0a;
        padding-top: .65rem;
        border-top: 1px solid #e8e6e2;
        margin-top: .25rem;
        margin-bottom: 0;
    }

    /* Address */
    .track-addr {
        background: #fff;
        border: 1px solid #e8e6e2;
        border-radius: 12px;
        max-width: 680px;
        margin: 0 auto 1.5rem;
        padding: 1.25rem 1.5rem;
    }

    .track-addr-title {
        font-size: .67rem;
        font-weight: 700;
        letter-spacing: .17em;
        text-transform: uppercase;
        color: #7a7570;
        margin-bottom: .75rem;
        padding-bottom: .5rem;
        border-bottom: 1px solid #e8e6e2;
    }

    .track-addr-val {
        font-size: .875rem;
        color: #0a0a0a;
        line-height: 1.6;
    }

    /* Actions */
    .track-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        max-width: 680px;
        margin: 0 auto;
    }

    .btn-track {
        display: inline-block;
        padding: .82rem 2rem;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        border-radius: 8px;
        text-decoration: none;
        transition: all .18s;
    }

    .btn-track-primary {
        background: #0a0a0a;
        color: #fff;
    }

    .btn-track-primary:hover {
        background: #2a2a2a;
    }

    .btn-track-outline {
        background: #fff;
        color: #0a0a0a;
        border: 2px solid #e8e6e2;
    }

    .btn-track-outline:hover {
        border-color: #0a0a0a;
        background: #f6f5f3;
    }

    @media (max-width: 600px) {
        .track-meta {
            grid-template-columns: 1fr 1fr;
        }

        .track-meta-col:nth-child(2) {
            border-right: none;
        }

        .track-meta-col:nth-child(3) {
            grid-column: 1 / -1;
            border-top: 1px solid #e8e6e2;
        }

        .track-steps {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .track-steps::before {
            display: none;
        }

        .track-step {
            flex: 0 0 calc(50% - 0.5rem);
        }
    }
</style>
<?php $view->endSection() ?>

<div class="track-page">
    <div class="container">

        <div class="track-header">
            <span class="track-eyebrow">Order Tracking</span>
            <h1 class="track-title">Track Your Order</h1>
            <?php if ($_order): ?>
                <span class="track-order-num">Order #<?= str_pad((string) $_order->id, 5, '0', STR_PAD_LEFT) ?></span>
            <?php endif ?>
        </div>

        <?php if ($_order): ?>

            <!-- Progress tracker -->
            <div class="track-progress <?= $_currentStatus === 'cancelled' ? 'track-cancelled' : '' ?>">
                <?php if ($_currentStatus !== 'cancelled'): ?>
                    <div class="track-steps">
                        <?php foreach ($_statusSteps as $index => $step): ?>
                            <?php
                            $stepClass = '';
                            if ($index < $_currentIndex) $stepClass = 'completed';
                            elseif ($index === $_currentIndex) $stepClass = 'current';
                            ?>
                            <div class="track-step <?= $stepClass ?>">
                                <div class="track-step-icon">
                                    <?php if ($index < $_currentIndex): ?>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    <?php elseif ($step === 'pending'): ?>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                    <?php elseif ($step === 'confirmed'): ?>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                            <polyline points="22 4 12 14.01 9 11.01" />
                                        </svg>
                                    <?php elseif ($step === 'shipped'): ?>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="1" y="3" width="15" height="13" />
                                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                                            <circle cx="5.5" cy="18.5" r="2.5" />
                                            <circle cx="18.5" cy="18.5" r="2.5" />
                                        </svg>
                                    <?php elseif ($step === 'delivered'): ?>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                            <polyline points="9 22 9 12 15 12 15 22" />
                                        </svg>
                                    <?php endif ?>
                                </div>
                                <span class="track-step-label"><?= ucfirst($step) ?></span>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <div class="track-status-message">
                    <?php if ($_currentStatus === 'pending'): ?>
                        <p>Your order is being processed. We'll confirm it soon!</p>
                    <?php elseif ($_currentStatus === 'confirmed'): ?>
                        <p>Your order has been confirmed and is being prepared for shipping.</p>
                    <?php elseif ($_currentStatus === 'shipped'): ?>
                        <p>Your order is on its way! <?php if (!empty($_order->shipped_at)): ?>Shipped on <?= date('M j, Y', strtotime($_order->shipped_at)) ?><?php endif ?></p>
                    <?php elseif ($_currentStatus === 'delivered'): ?>
                        <p>Your order has been delivered! <?php if (!empty($_order->delivered_at)): ?>Delivered on <?= date('M j, Y', strtotime($_order->delivered_at)) ?><?php endif ?></p>
                    <?php elseif ($_currentStatus === 'cancelled'): ?>
                        <p>This order has been cancelled.</p>
                    <?php endif ?>
                </div>
            </div>

            <!-- Order details -->
            <div class="track-card">
                <div class="track-card-header">
                    <span class="track-card-title">Order Details</span>
                </div>

                <div class="track-meta">
                    <div class="track-meta-col">
                        <span class="track-meta-lbl">Order Date</span>
                        <span class="track-meta-val"><?= date('M j, Y', strtotime($_order->created_at)) ?></span>
                    </div>
                    <div class="track-meta-col">
                        <span class="track-meta-lbl">Payment</span>
                        <span class="track-meta-val"><?= htmlspecialchars(str_replace('_', ' ', $_order->payment_method ?? 'cash on delivery')) ?></span>
                    </div>
                    <div class="track-meta-col">
                        <span class="track-meta-lbl">Payment Status</span>
                        <span class="track-meta-val"><?= ucfirst($_order->payment_status ?? 'unpaid') ?></span>
                    </div>
                </div>

                <!-- Line items -->
                <div class="track-items">
                    <?php foreach ($_items as $item): ?>
                        <div class="track-item">
                            <?php if (!empty($item->product_image) && file_exists(BASE_PATH . '/public/assets/images/' . ltrim($item->product_image, '/'))): ?>
                                <img class="track-item-img"
                                    src="<?= productImg($item->product_image) ?>"
                                    alt="<?= htmlspecialchars($item->product_name ?? '') ?>">
                            <?php else: ?>
                                <div class="track-item-img-ph">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <polyline points="21 15 16 10 5 21" />
                                    </svg>
                                </div>
                            <?php endif ?>
                            <div>
                                <span class="track-item-name"><?= htmlspecialchars($item->product_name ?? 'Product') ?></span>
                                <span class="track-item-meta">
                                    Qty: <?= (int) $item->quantity ?>
                                    <?php if (!empty($item->size)): ?> &middot; Size: <?= htmlspecialchars($item->size) ?><?php endif ?>
                                </span>
                            </div>
                            <span class="track-item-price"><?= formatPrice((float) $item->price * (int) $item->quantity) ?></span>
                        </div>
                    <?php endforeach ?>
                </div>

                <!-- Totals -->
                <div class="track-totals">
                    <div class="track-total-row">
                        <span>Subtotal</span>
                        <span><?= formatPrice($_order->subtotal ?? 0) ?></span>
                    </div>
                    <?php if ((float)($_order->discount ?? 0) > 0): ?>
                        <div class="track-total-row">
                            <span>Discount</span>
                            <span style="color: #3a7a3a;">-<?= formatPrice($_order->discount) ?></span>
                        </div>
                    <?php endif ?>
                    <div class="track-total-row">
                        <span>Shipping</span>
                        <span><?= (float)($_order->shipping_fee ?? 0) === 0.0 ? 'Free' : formatPrice($_order->shipping_fee) ?></span>
                    </div>
                    <div class="track-total-row grand">
                        <span>Total</span>
                        <span><?= formatPrice($_order->total_price ?? 0) ?></span>
                    </div>
                </div>
            </div>

            <!-- Shipping address -->
            <?php if (!empty($_order->customer_address)): ?>
                <div class="track-addr">
                    <div class="track-addr-title">Shipping Address</div>
                    <div class="track-addr-val">
                        <strong><?= htmlspecialchars($_order->customer_name ?? '') ?></strong><br>
                        <?= htmlspecialchars($_order->customer_address ?? '') ?><br>
                        <?= htmlspecialchars($_order->customer_city ?? '') ?>
                        <?php if (!empty($_order->customer_phone)): ?>
                            <br><?= htmlspecialchars($_order->customer_phone) ?>
                        <?php endif ?>
                    </div>
                </div>
            <?php endif ?>

        <?php else: ?>
            <div class="track-card" style="text-align: center; padding: 3rem;">
                <p style="color: #7a7570; margin: 0;">Order not found or tracking link has expired.</p>
            </div>
        <?php endif ?>

        <!-- Actions -->
        <div class="track-actions">
            <a href="<?= url('products') ?>" class="btn-track btn-track-primary">Continue Shopping</a>
            <a href="<?= url() ?>" class="btn-track btn-track-outline">Back to Home</a>
        </div>

    </div>
</div>