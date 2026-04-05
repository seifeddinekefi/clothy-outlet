<?php

/**
 * ============================================================
 * app/controllers/CheckoutController.php
 * ============================================================
 * Handles the checkout flow for authenticated customers.
 *
 * Routes (defined in config/routes.php):
 *   GET  /checkout          → index()   — show checkout form
 *   POST /checkout/place    → place()   — process & create order
 *   GET  /checkout/success  → success() — order confirmation
 *
 * Protected by AuthMiddleware.
 * ============================================================
 */

class CheckoutController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────

    /**
     * Build the cart items array from session + product model,
     * returning [items, subtotal].  Empty cart returns [[], 0].
     */
    private function buildCartItems(): array
    {
        $raw          = Session::get('cart') ?? [];
        $productModel = new Product();
        $items        = [];
        $subtotal     = 0;

        foreach ($raw as $key => $row) {
            if (is_array($row)) {
                $productId = (int) ($row['product_id'] ?? 0);
                $qty       = (int) ($row['qty'] ?? 1);
                $size      = isset($row['size']) && $row['size'] !== '' ? (string) $row['size'] : null;
                $cartKey   = (string) $key;
            } else {
                $productId = (int) $key;
                $qty       = (int) $row;
                $size      = null;
                $cartKey   = $productId . ':';
            }

            if ($productId <= 0 || $qty <= 0) {
                continue;
            }

            $product = $productModel->findWithDetails($productId);
            if (!$product) {
                continue;
            }
            $lineTotal = (float) $product->price * $qty;
            $subtotal += $lineTotal;
            $items[]   = [
                'key'       => $cartKey,
                'product'   => $product,
                'qty'       => $qty,
                'size'      => $size,
                'lineTotal' => $lineTotal,
            ];
        }

        return [$items, $subtotal];
    }

    /**
     * @return array{code:string,amount:float}|null
     */
    private function activeCoupon(float $subtotal): ?array
    {
        $couponState = Session::get('checkout_coupon');
        if (!is_array($couponState) || empty($couponState['code'])) {
            return null;
        }

        $couponModel = new Coupon();
        $coupon = $couponModel->findValidByCode((string) $couponState['code']);
        if (!$coupon) {
            Session::set('checkout_coupon', null);
            return null;
        }

        $discount = $couponModel->calculateDiscount($coupon, $subtotal);
        if ($discount <= 0) {
            return null;
        }

        return [
            'code'   => (string) $coupon->code,
            'amount' => $discount,
        ];
    }

    // ─────────────────────────────────────────────────────────
    // GET /checkout
    // ─────────────────────────────────────────────────────────

    public function index(): void
    {
        [$items, $subtotal] = $this->buildCartItems();

        if (empty($items)) {
            $this->flash('info', 'Your cart is empty. Add some items before checking out.');
            $this->redirect(url('cart'));
        }

        $user          = Session::user();
        $customerModel = new Customer();
        $customer      = $customerModel->findById($user['id']);

        $shipping = defined('SHIPPING_FEE') ? (float) SHIPPING_FEE : 8.00;
        $coupon   = $this->activeCoupon($subtotal);
        $discount = (float) ($coupon['amount'] ?? 0);
        $total    = max(0, $subtotal - $discount + $shipping);

        $this->render('checkout.index', [
            'pageTitle' => 'Checkout — ' . APP_NAME,
            'items'     => $items,
            'subtotal'  => $subtotal,
            'shipping'  => $shipping,
            'discount'  => $discount,
            'coupon'    => $coupon,
            'total'     => $total,
            'customer'  => $customer,
            'user'      => $user,
        ]);
    }

    public function applyCoupon(): void
    {
        $this->verifyCsrf();

        $code = strtoupper(trim($this->post('coupon_code', '')));
        if ($code === '') {
            $this->flash('error', 'Please enter a coupon code.');
            $this->redirect(url('checkout'));
        }

        [, $subtotal] = $this->buildCartItems();
        $couponModel = new Coupon();
        $coupon = $couponModel->findValidByCode($code);

        if (!$coupon) {
            $this->flash('error', 'Invalid or expired coupon code.');
            $this->redirect(url('checkout'));
        }

        $discount = $couponModel->calculateDiscount($coupon, $subtotal);
        if ($discount <= 0) {
            $this->flash('error', 'This coupon does not apply to your cart.');
            $this->redirect(url('checkout'));
        }

        Session::set('checkout_coupon', ['code' => (string) $coupon->code]);
        $this->flash('success', 'Coupon applied successfully.');
        $this->redirect(url('checkout'));
    }

    public function removeCoupon(): void
    {
        $this->verifyCsrf();
        Session::set('checkout_coupon', null);
        $this->flash('success', 'Coupon removed.');
        $this->redirect(url('checkout'));
    }

    // ─────────────────────────────────────────────────────────
    // POST /checkout/place
    // ─────────────────────────────────────────────────────────

    public function place(): void
    {
        $this->verifyCsrf();

        [$items, $subtotal] = $this->buildCartItems();

        if (empty($items)) {
            $this->flash('error', 'Your cart is empty.');
            $this->redirect(url('cart'));
        }

        // ── Collect & validate shipping fields ────────────────
        $name    = trim($this->post('name',    ''));
        $phone   = trim($this->post('phone',   ''));
        $address = trim($this->post('address', ''));
        $city    = trim($this->post('city',    ''));
        $notes   = trim($this->post('notes',   ''));

        $paymentMethod = $this->post('payment_method', 'cash_on_delivery');
        $allowed = Order::PAYMENT_METHODS;
        if (!in_array($paymentMethod, $allowed, true)) {
            $paymentMethod = 'cash_on_delivery';
        }

        $errors = [];
        if ($name    === '') {
            $errors[] = 'Full name is required.';
        }
        if ($address === '') {
            $errors[] = 'Delivery address is required.';
        }
        if ($city    === '') {
            $errors[] = 'City is required.';
        }

        if (!empty($errors)) {
            foreach ($errors as $err) {
                $this->flash('error', $err);
            }
            $this->redirect(url('checkout'));
        }

        // ── Update customer shipping profile ──────────────────
        $user          = Session::user();
        $customerModel = new Customer();

        $updateData = [
            'name'    => $name,
            'phone'   => $phone  !== '' ? $phone  : null,
            'address' => $address,
            'city'    => $city,
            'notes'   => $notes !== '' ? $notes : null,
        ];
        $customerModel->updateCustomer($user['id'], $updateData);

        // ── Compute totals ────────────────────────────────────
        $shippingFee = defined('SHIPPING_FEE') ? (float) SHIPPING_FEE : 8.00;
        $coupon      = $this->activeCoupon($subtotal);
        $discount    = (float) ($coupon['amount'] ?? 0);
        $total       = max(0, $subtotal - $discount + $shippingFee);

        // ── Create order header ───────────────────────────────
        $orderModel = new Order();
        $orderId    = $orderModel->create([
            'customer_id'    => $user['id'],
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'shipping_fee'   => $shippingFee,
            'total_price'    => $total,
            'payment_method' => $paymentMethod,
            'notes'          => $notes !== '' ? $notes : null,
        ]);

        if (!$orderId) {
            $this->flash('error', 'Could not place your order. Please try again.');
            $this->redirect(url('checkout'));
        }

        // ── Create order items ────────────────────────────────
        $orderItemModel = new OrderItem();
        $lineItems      = [];
        foreach ($items as $item) {
            $lineItems[] = [
                'product_id' => (int)   $item['product']->id,
                'quantity'   => (int)   $item['qty'],
                'price'      => (float) $item['product']->price,
                'size'       => $item['size'] ?? null,
            ];
        }
        $orderItemModel->bulkCreate((int) $orderId, $lineItems);

        // ── Clear cart ────────────────────────────────────────
        Session::set('cart', []);
        Session::set('checkout_coupon', null);

        // ── Store order ID for success page ──────────────────
        Session::set('last_order_id', (int) $orderId);

        $this->redirect(url('checkout/success'));
    }

    // ─────────────────────────────────────────────────────────
    // GET /checkout/success
    // ─────────────────────────────────────────────────────────

    public function success(): void
    {
        $orderId = Session::get('last_order_id');

        if (!$orderId) {
            $this->redirect(url());
        }

        // Consume so refreshing doesn't re-show (but we still show the order)
        Session::set('last_order_id', null);

        $orderModel = new Order();
        $order      = $orderModel->findWithCustomer((int) $orderId);

        $orderItemModel = new OrderItem();
        $orderItems     = $orderItemModel->findByOrder((int) $orderId);

        $this->render('checkout.success', [
            'pageTitle'  => 'Order Confirmed — ' . APP_NAME,
            'order'      => $order,
            'orderItems' => $orderItems,
        ]);
    }
}
