<?php

/**
 * ============================================================
 * app/controllers/AccountController.php
 * ============================================================
 * Handles authenticated customer account pages.
 * Protected by AuthMiddleware (defined in routes.php group).
 * ============================================================
 */

class AccountController extends Controller
{
    public function dashboard(): void
    {
        $user          = Session::user();
        $customerModel = new Customer();
        $orderModel    = new Order();

        $customer = $customerModel->findById($user['id']);
        $orders   = $orderModel->findByCustomer($user['id']);

        $totalSpent = 0;
        foreach ($orders as $o) {
            $totalSpent += (float) ($o->total_price ?? 0);
        }

        $this->render('account.dashboard', [
            'pageTitle'   => 'My Account — ' . APP_NAME,
            'user'        => $user,
            'customer'    => $customer,
            'orders'      => $orders,
            'totalSpent'  => $totalSpent,
            'recentOrders' => array_slice($orders, 0, 5),
        ]);
    }

    public function orders(): void
    {
        $user       = Session::user();
        $orderModel = new Order();
        $orders     = $orderModel->findByCustomer($user['id']);

        $this->render('account.orders', [
            'pageTitle' => 'My Orders — ' . APP_NAME,
            'user'      => $user,
            'orders'    => $orders,
        ]);
    }

    public function cancelOrder(string $id): void
    {
        $this->verifyCsrf();

        $user = Session::user();
        $orderId = (int) $id;

        if ($orderId <= 0) {
            $this->flash('error', 'Invalid order id.');
            $this->redirect(url('account/orders'));
        }

        $orderModel = new Order();
        $order = $orderModel->findByCustomerAndId((int) $user['id'], $orderId);

        if (!$order) {
            $this->flash('error', 'Order not found.');
            $this->redirect(url('account/orders'));
        }

        if (($order->status ?? '') !== 'pending') {
            $this->flash('error', 'Only pending orders can be cancelled.');
            $this->redirect(url('account/orders'));
        }

        $orderModel->cancelByCustomer((int) $user['id'], $orderId);
        $this->flash('success', 'Your order has been cancelled.');
        $this->redirect(url('account/orders'));
    }

    public function profile(): void
    {
        $user          = Session::user();
        $customerModel = new Customer();
        $customer      = $customerModel->findById($user['id']);

        $this->render('account.profile', [
            'pageTitle' => 'My Profile — ' . APP_NAME,
            'user'      => $user,
            'customer'  => $customer,
        ]);
    }

    public function updateProfile(): void
    {
        $this->verifyCsrf();

        $user  = Session::user();
        $name  = trim($this->post('name', ''));
        $email = trim($this->post('email', ''));
        $phone = trim($this->post('phone', ''));

        $passwordCurrent = $this->post('password_current', '');
        $passwordNew     = $this->post('password_new', '');
        $passwordConfirm = $this->post('password_confirm', '');

        // ── Basic validation ──────────────────────────────
        if ($name === '') {
            $this->flash('error', 'Full name is required.');
            $this->redirect(url('account/profile'));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'A valid email address is required.');
            $this->redirect(url('account/profile'));
        }

        $customerModel = new Customer();

        if ($customerModel->emailExists($email, $user['id'])) {
            $this->flash('error', 'That email address is already in use.');
            $this->redirect(url('account/profile'));
        }

        $data = [
            'name'  => $name,
            'email' => $email,
            'phone' => $phone !== '' ? $phone : null,
        ];

        // ── Optional password change ──────────────────────
        if ($passwordCurrent !== '') {
            $customer = $customerModel->findById($user['id']);

            if (!password_verify($passwordCurrent, $customer->password ?? '')) {
                $this->flash('error', 'Current password is incorrect.');
                $this->redirect(url('account/profile'));
            }
            if (strlen($passwordNew) < 8) {
                $this->flash('error', 'New password must be at least 8 characters.');
                $this->redirect(url('account/profile'));
            }
            if ($passwordNew !== $passwordConfirm) {
                $this->flash('error', 'New passwords do not match.');
                $this->redirect(url('account/profile'));
            }

            $data['password'] = password_hash($passwordNew, PASSWORD_BCRYPT);
        }

        $customerModel->updateCustomer($user['id'], $data);

        // ── Refresh session with updated name/email ───────
        Session::set('auth_user', array_merge($user, [
            'name'  => $name,
            'email' => $email,
        ]));

        $this->flash('success', 'Profile updated successfully.');
        $this->redirect(url('account/profile'));
    }
}
