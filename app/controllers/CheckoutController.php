<?php

/**
 * ============================================================
 * app/controllers/CheckoutController.php
 * ============================================================
 * Handles the checkout flow for both authenticated and guest customers.
 *
 * Routes (defined in config/routes.php):
 *   GET  /checkout           → index()         — show checkout form
 *   POST /checkout/place     → place()         — process & create order
 *   GET  /checkout/success   → success()       — order confirmation
 *   POST /checkout/register  → registerGuest() — convert guest to user
 *   GET  /order/track/{token}→ trackOrder()    — guest order tracking
 *
 * Protected by GuestCheckoutMiddleware (allows both guests + logged in).
 * ============================================================
 */

class CheckoutController extends Controller
{
    /**
     * Tunisia governorates for city dropdown.
     */
    public const TUNISIA_GOVERNORATES = [
        'Tunis',
        'Ariana',
        'Ben Arous',
        'Manouba',
        'Nabeul',
        'Zaghouan',
        'Bizerte',
        'Béja',
        'Jendouba',
        'Le Kef',
        'Siliana',
        'Sousse',
        'Monastir',
        'Mahdia',
        'Sfax',
        'Kairouan',
        'Kasserine',
        'Sidi Bouzid',
        'Gabès',
        'Medenine',
        'Tataouine',
        'Gafsa',
        'Tozeur',
        'Kebili',
    ];

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Build the cart items array from session + product model,
     * returning [items, subtotal].  Empty cart returns [[], 0].
     */
    private function buildCartItems(): array
    {
        $raw          = Session::get('cart') ?? [];
        $productModel = new Product();
        $qualityModel = new ProductQuality();
        $items        = [];
        $subtotal     = 0;

        foreach ($raw as $key => $row) {
            if (is_array($row)) {
                $productId = (int) ($row['product_id'] ?? 0);
                $qty       = (int) ($row['qty'] ?? 1);
                $size      = isset($row['size'])    && $row['size']    !== '' ? (string) $row['size']    : null;
                $color     = isset($row['color'])   && $row['color']   !== '' ? (string) $row['color']   : null;
                $quality   = isset($row['quality']) && $row['quality'] !== '' ? (string) $row['quality'] : null;
                $cartKey   = (string) $key;
            } else {
                $productId = (int) $key;
                $qty       = (int) $row;
                $size      = null;
                $color     = null;
                $quality   = null;
                $cartKey   = $productId . ':::';
            }

            if ($productId <= 0 || $qty <= 0) {
                continue;
            }

            $product = $productModel->findWithDetails($productId);
            if (!$product) {
                continue;
            }

            $unitPrice = (float) $product->price;
            if ($quality !== null) {
                $qRecord = $qualityModel->findByProductAndType($productId, $quality);
                if ($qRecord && isset($qRecord->price) && $qRecord->price !== null) {
                    $unitPrice = (float) $qRecord->price;
                }
            }

            $lineTotal = $unitPrice * $qty;
            $subtotal += $lineTotal;
            $items[]   = [
                'key'       => $cartKey,
                'product'   => $product,
                'qty'       => $qty,
                'size'      => $size,
                'color'     => $color,
                'quality'   => $quality,
                'unitPrice' => $unitPrice,
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

    /**
     * Check if current checkout is a guest checkout.
     */
    private function isGuestCheckout(): bool
    {
        return !Session::isLoggedIn();
    }

    /**
     * Generate a unique tracking token for orders.
     */
    private function generateTrackingToken(): string
    {
        return bin2hex(random_bytes(32));
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

        $isGuest  = $this->isGuestCheckout();
        $customer = null;
        $user     = null;

        if (!$isGuest) {
            $user          = Session::user();
            $customerModel = new Customer();
            $customer      = $customerModel->findById($user['id']);
        }

        $shipping = defined('SHIPPING_FEE') ? (float) SHIPPING_FEE : 8.00;
        $coupon   = $this->activeCoupon($subtotal);
        $discount = (float) ($coupon['amount'] ?? 0);
        $total    = max(0, $subtotal - $discount + $shipping);

        $this->render('checkout.index', [
            'pageTitle'    => 'Checkout — ' . APP_NAME,
            'items'        => $items,
            'subtotal'     => $subtotal,
            'shipping'     => $shipping,
            'discount'     => $discount,
            'coupon'       => $coupon,
            'total'        => $total,
            'customer'     => $customer,
            'user'         => $user,
            'isGuest'      => $isGuest,
            'governorates' => self::TUNISIA_GOVERNORATES,
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
        $email   = trim($this->post('email',   ''));
        $phone   = trim($this->post('phone',   ''));
        $address = trim($this->post('address', ''));
        $city    = trim($this->post('city',    ''));
        $notes   = trim($this->post('notes',   ''));

        $paymentMethod = $this->post('payment_method', 'cash_on_delivery');
        $allowed = Order::PAYMENT_METHODS;
        if (!in_array($paymentMethod, $allowed, true)) {
            $paymentMethod = 'cash_on_delivery';
        }

        $isGuest = $this->isGuestCheckout();

        $errors = [];
        if ($name === '') {
            $errors[] = 'Full name is required.';
        }
        if ($isGuest && $email === '') {
            $errors[] = 'Email address is required.';
        }
        if ($isGuest && $email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if ($phone === '') {
            $errors[] = 'Phone number is required for delivery.';
        }
        if ($address === '') {
            $errors[] = 'Delivery address is required.';
        }
        if ($city === '') {
            $errors[] = 'City/Governorate is required.';
        }
        if ($city !== '' && !in_array($city, self::TUNISIA_GOVERNORATES, true)) {
            $errors[] = 'Please select a valid governorate.';
        }

        if (!empty($errors)) {
            foreach ($errors as $err) {
                $this->flash('error', $err);
            }
            $this->redirect(url('checkout'));
        }

        $customerModel = new Customer();
        $customerId    = null;
        $trackingToken = null;
        $hasExistingAccount = false;

        if ($isGuest) {
            // Check if email has a registered account
            if ($customerModel->hasRegisteredAccount($email)) {
                $hasExistingAccount = true;
            }

            // Create guest customer
            $guestToken = Session::get('guest_token') ?? bin2hex(random_bytes(32));
            $trackingToken = $this->generateTrackingToken();

            $customerId = $customerModel->createGuest([
                'name'        => $name,
                'email'       => $email,
                'phone'       => $phone,
                'address'     => $address,
                'city'        => $city,
                'notes'       => $notes !== '' ? $notes : null,
                'guest_token' => $guestToken,
            ]);

            if (!$customerId) {
                $this->flash('error', 'Could not process your order. Please try again.');
                $this->redirect(url('checkout'));
            }
        } else {
            // Logged in user - update their profile
            $user       = Session::user();
            $customerId = $user['id'];

            $updateData = [
                'name'    => $name,
                'phone'   => $phone,
                'address' => $address,
                'city'    => $city,
                'notes'   => $notes !== '' ? $notes : null,
            ];
            $customerModel->updateCustomer($customerId, $updateData);
        }

        // ── Compute totals ────────────────────────────────────
        $shippingFee = defined('SHIPPING_FEE') ? (float) SHIPPING_FEE : 8.00;
        $coupon      = $this->activeCoupon($subtotal);
        $discount    = (float) ($coupon['amount'] ?? 0);
        $total       = max(0, $subtotal - $discount + $shippingFee);

        // ── Create order header ───────────────────────────────
        $orderModel = new Order();
        $orderId    = $orderModel->create([
            'customer_id'    => $customerId,
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'shipping_fee'   => $shippingFee,
            'total_price'    => $total,
            'payment_method' => $paymentMethod,
            'notes'          => $notes !== '' ? $notes : null,
            'tracking_token' => $trackingToken,
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
                'price'      => (float) ($item['unitPrice'] ?? $item['product']->price),
                'size'       => $item['size']    ?? null,
                'color'      => $item['color']   ?? null,
                'quality'    => $item['quality'] ?? null,
            ];
        }
        $orderItemModel->bulkCreate((int) $orderId, $lineItems);

        // ── Send order confirmation email ─────────────────────
        $this->sendOrderConfirmationEmail(
            $email ?? ($isGuest ? null : Session::user()['email'] ?? null),
            (int) $orderId,
            $trackingToken
        );

        // ── Clear cart ────────────────────────────────────────
        Session::set('cart', []);
        Session::set('checkout_coupon', null);

        // ── Store order info for success page ─────────────────
        Session::set('last_order_id', (int) $orderId);
        Session::set('last_order_is_guest', $isGuest);
        Session::set('last_order_customer_id', (int) $customerId);
        Session::set('last_order_tracking_token', $trackingToken);
        Session::set('last_order_has_existing_account', $hasExistingAccount);

        $this->redirect(url('checkout/success'));
    }

    /**
     * Send order confirmation email.
     */
    private function sendOrderConfirmationEmail(?string $email, int $orderId, ?string $trackingToken): void
    {
        if (!$email) {
            return;
        }

        try {
            $orderModel = new Order();
            $order = $orderModel->findWithCustomer($orderId);

            if (!$order) {
                return;
            }

            $orderItemModel = new OrderItem();
            $orderItems = $orderItemModel->findByOrder($orderId);

            $trackingUrl = $trackingToken ? url('order/track/' . $trackingToken) : null;

            $mailer = new Mailer();
            $mailer->sendOrderConfirmation($email, $order, $orderItems, $trackingUrl);
        } catch (Exception $e) {
            // Log error but don't interrupt checkout flow
            error_log('Failed to send order confirmation email: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────
    // GET /checkout/success
    // ─────────────────────────────────────────────────────────

    public function success(): void
    {
        $orderId            = Session::get('last_order_id');
        $isGuestOrder       = Session::get('last_order_is_guest', false);
        $guestCustomerId    = Session::get('last_order_customer_id');
        $trackingToken      = Session::get('last_order_tracking_token');
        $hasExistingAccount = Session::get('last_order_has_existing_account', false);

        if (!$orderId) {
            $this->redirect(url());
        }

        // Consume so refreshing doesn't re-show (but we still show the order)
        Session::set('last_order_id', null);
        Session::set('last_order_is_guest', null);
        Session::set('last_order_customer_id', null);
        Session::set('last_order_tracking_token', null);
        Session::set('last_order_has_existing_account', null);

        $orderModel = new Order();
        $order      = $orderModel->findWithCustomer((int) $orderId);

        $orderItemModel = new OrderItem();
        $orderItems     = $orderItemModel->findByOrder((int) $orderId);

        $this->render('checkout.success', [
            'pageTitle'          => 'Order Confirmed — ' . APP_NAME,
            'order'              => $order,
            'orderItems'         => $orderItems,
            'isGuestOrder'       => $isGuestOrder,
            'guestCustomerId'    => $guestCustomerId,
            'trackingToken'      => $trackingToken,
            'hasExistingAccount' => $hasExistingAccount,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // POST /checkout/register (convert guest to user)
    // ─────────────────────────────────────────────────────────

    public function registerGuest(): void
    {
        $this->verifyCsrf();

        $customerId      = (int) $this->post('customer_id', 0);
        $password        = $this->post('password', '');
        $passwordConfirm = $this->post('password_confirm', '');

        $errors = [];

        if ($customerId <= 0) {
            $errors[] = 'Invalid request.';
        }

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            foreach ($errors as $err) {
                $this->flash('error', $err);
            }
            $this->redirect(url('checkout/success'));
        }

        $customerModel = new Customer();
        $customer = $customerModel->findById($customerId);

        if (!$customer || !$customer->is_guest) {
            $this->flash('error', 'Invalid request.');
            $this->redirect(url());
        }

        // Check if email already has a registered account
        if ($customerModel->hasRegisteredAccount($customer->email)) {
            $this->flash('error', 'An account with this email already exists. Please log in instead.');
            $this->redirect(url('login'));
        }

        // Convert guest to registered user
        $converted = $customerModel->convertGuestToUser($customerId, $password);

        if (!$converted) {
            $this->flash('error', 'Could not create account. Please try again.');
            $this->redirect(url());
        }

        // Send welcome email
        try {
            $mailer = new Mailer();
            $mailer->sendWelcome($customer->email, $customer->name);
        } catch (Exception $e) {
            error_log('Failed to send welcome email: ' . $e->getMessage());
        }

        // Log the user in
        Session::login([
            'id'    => $customerId,
            'name'  => $customer->name,
            'email' => $customer->email,
            'role'  => 'customer',
        ]);

        // Clear guest session data
        GuestCheckoutMiddleware::clearGuestSession();

        $this->flash('success', 'Account created successfully! You can now track your orders and enjoy faster checkout.');
        $this->redirect(url('account/orders'));
    }

    // ─────────────────────────────────────────────────────────
    // GET /order/track/{token} (guest order tracking)
    // ─────────────────────────────────────────────────────────

    public function trackOrder(string $token): void
    {
        $orderModel = new Order();
        $order      = $orderModel->findByTrackingToken($token);

        if (!$order) {
            $this->flash('error', 'Order not found or tracking link has expired.');
            $this->redirect(url());
        }

        $orderItemModel = new OrderItem();
        $orderItems     = $orderItemModel->findByOrder((int) $order->id);

        $this->render('checkout.track', [
            'pageTitle'  => 'Track Order #' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) . ' — ' . APP_NAME,
            'order'      => $order,
            'orderItems' => $orderItems,
        ]);
    }
}
