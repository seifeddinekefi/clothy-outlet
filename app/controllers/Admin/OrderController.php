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

        $this->orderModel->updateStatus((int) $id, $status);

        Session::flash('success', 'Order status updated to "' . e($status) . '".');
        $this->redirect(url('admin/orders/' . (int) $id));
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
