<?php

/**
 * ============================================================
 * app/controllers/Admin/BaseAdminController.php
 * ============================================================
 * Abstract base controller for all Admin-area controllers.
 *
 * - Sets the admin layout
 * - Enforces admin role on every action (defence-in-depth)
 * - Provides admin-specific helpers
 *
 * Admin controllers MUST extend this class, not Controller.
 * ============================================================
 */

class BaseAdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Use the admin layout wrapper for all admin views
        $this->view->setLayout('layouts.admin');

        // Defence-in-depth: enforce admin role even if middleware is bypassed
        $this->requireRole('admin', url('admin/login'));
    }

    /**
     * Render a view from the admin/ subdirectory.
     *
     * @param  string               $view   Relative to views/admin/ (dot notation)
     * @param  array<string, mixed> $data
     */
    protected function adminView(string $view, array $data = [], int $status = 200): void
    {
        // Always resolve pending order count so the sidebar badge is available on every page
        $pendingOrdersCount = $data['pendingOrdersCount'] ?? null;
        if ($pendingOrdersCount === null) {
            try {
                $orderModel = new Order();
                $pendingOrdersCount = $orderModel->pendingCount();
            } catch (\Throwable $e) {
                $pendingOrdersCount = 0;
            }
        }

        $this->render('admin.' . $view, array_merge($data, [
            'adminUser'          => $this->authUser(),
            'pendingOrdersCount' => $pendingOrdersCount,
        ]), $status);
    }
}
