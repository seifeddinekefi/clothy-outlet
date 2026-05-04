<?php

/**
 * ============================================================
 * app/controllers/Admin/DashboardController.php
 * ============================================================
 * Renders the admin dashboard with summary statistics and
 * a list of the most recent orders.
 * ============================================================
 */

class DashboardController extends BaseAdminController
{
    public function index(): void
    {
        $productModel  = new Product();
        $orderModel    = new Order();
        $customerModel = new Customer();
        $settingModel  = new Setting();

        $lowStockThreshold = max(1, (int) $settingModel->get('low_stock_threshold', 10));

        $recentOrders   = $orderModel->paginateOrders(1, 8);
        $revenueByDay   = $orderModel->revenueByDay(30);
        $ordersByStatus = $orderModel->countByStatus();

        $this->adminView('dashboard.index', [
            'pageTitle'          => 'Dashboard',
            'totalProducts'      => $productModel->count(),
            'totalOrders'        => $orderModel->count(),
            'totalCustomers'     => $customerModel->count(),
            'totalRevenue'       => $orderModel->totalRevenue(),
            'revenueThisMonth'   => $orderModel->revenueThisMonth(),
            'pendingOrdersCount' => $orderModel->pendingCount(),
            'recentOrders'       => $recentOrders['data'],
            'revenueByDay'       => $revenueByDay,
            'ordersByStatus'     => $ordersByStatus,
            'lowStockProducts'   => $productModel->lowStock($lowStockThreshold),
            'topSellingProducts' => $productModel->topSelling(5),
        ]);
    }
}
