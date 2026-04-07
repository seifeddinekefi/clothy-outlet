<?php

/**
 * ============================================================
 * app/controllers/Admin/OrderController.php
 * ============================================================
 * Read and manage customer orders.
 *
 * - Paginated list with optional status filter
 * - Detail view: customer info, line items, totals
 * - Status update (pending → confirmed → shipped → delivered / cancelled)
 * ============================================================
 */

class OrderController extends BaseAdminController
{
    private Order     $orderModel;
    private OrderItem $orderItemModel;

    public function __construct()
    {
        parent::__construct();
        $this->orderModel     = new Order();
        $this->orderItemModel = new OrderItem();
    }

    // ── Index ─────────────────────────────────────────────────

    public function index(): void
    {
        $status = trim($_GET['status'] ?? '');
        $page   = max(1, (int) ($_GET['page'] ?? 1));

        // Only allow valid statuses as filter values
        $validStatus = in_array($status, Order::STATUSES, true) ? $status : null;

        $result = $this->orderModel->paginateOrders($page, 20, $validStatus);

        $this->adminView('orders.index', [
            'pageTitle' => 'Orders',
            'orders'    => $result['data'],
            'page'      => $result['page'],
            'pages'     => $result['pages'],
            'total'     => $result['total'],
            'status'    => $validStatus,
            'statuses'  => Order::STATUSES,
        ]);
    }

    // ── Show ──────────────────────────────────────────────────

    public function show(string $id): void
    {
        $order = $this->orderModel->findWithCustomer((int) $id);
        if (!$order) {
            Session::flash('error', 'Order not found.');
            $this->redirect(url('admin/orders'));
        }

        $items = $this->orderItemModel->findByOrder((int) $id);

        $this->adminView('orders.show', [
            'pageTitle'      => 'Order #' . e($id),
            'order'          => $order,
            'items'          => $items,
            'statuses'       => Order::STATUSES,
            'paymentStatuses' => Order::PAYMENT_STATUSES,
        ]);
    }

    // ── Update Order Status ───────────────────────────────────

    public function updateStatus(string $id): void
    {
        $this->verifyCsrf();

        $order = $this->orderModel->findById((int) $id);
        if (!$order) {
            Session::flash('error', 'Order not found.');
            $this->redirect(url('admin/orders'));
        }

        $status = trim($_POST['status'] ?? '');
        if (!in_array($status, Order::STATUSES, true)) {
            Session::flash('error', 'Invalid order status selected.');
            $this->redirect(url('admin/orders/' . (int) $id));
        }

        $oldStatus = $order->status;
        $this->orderModel->updateStatus((int) $id, $status);

        // Send email notification for shipped/delivered status
        if ($status !== $oldStatus && in_array($status, ['shipped', 'delivered'], true)) {
            $this->sendStatusEmail((int) $id, $status);
        }

        Session::flash('success', 'Order status updated to "' . e($status) . '".');
        $this->redirect(url('admin/orders/' . (int) $id));
    }

    /**
     * Send order status update email
     */
    private function sendStatusEmail(int $orderId, string $status): void
    {
        try {
            $order = $this->orderModel->findWithCustomer($orderId);
            if (!$order || empty($order->customer_email)) {
                return;
            }

            $trackingUrl = !empty($order->tracking_token)
                ? url('order/track/' . $order->tracking_token)
                : null;

            $mailer = new Mailer();

            if ($status === 'shipped') {
                $mailer->sendOrderShipped($order->customer_email, $order, $trackingUrl);
            } elseif ($status === 'delivered') {
                $mailer->sendOrderDelivered($order->customer_email, $order);
            }
        } catch (Exception $e) {
            error_log('Failed to send order status email: ' . $e->getMessage());
        }
    }

    // ── Update Payment Status ───────────────────────────────

    public function updatePaymentStatus(string $id): void
    {
        $this->verifyCsrf();

        $order = $this->orderModel->findById((int) $id);
        if (!$order) {
            Session::flash('error', 'Order not found.');
            $this->redirect(url('admin/orders'));
        }

        $paymentStatus = trim($_POST['payment_status'] ?? '');
        if (!in_array($paymentStatus, Order::PAYMENT_STATUSES, true)) {
            Session::flash('error', 'Invalid payment status selected.');
            $this->redirect(url('admin/orders/' . (int) $id));
        }

        $this->orderModel->updatePaymentStatus((int) $id, $paymentStatus);

        Session::flash('success', 'Payment status updated to "' . e($paymentStatus) . '".');
        $this->redirect(url('admin/orders/' . (int) $id));
    }
}
