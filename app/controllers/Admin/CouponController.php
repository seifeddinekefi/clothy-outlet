<?php

/**
 * ============================================================
 * app/controllers/Admin/CouponController.php
 * ============================================================
 * Full CRUD management for discount coupons.
 * ============================================================
 */

class CouponController extends BaseAdminController
{
    private Coupon $couponModel;

    public function __construct()
    {
        parent::__construct();
        $this->couponModel = new Coupon();
    }

    // ── Index ─────────────────────────────────────────────────

    public function index(): void
    {
        $this->adminView('coupons.index', [
            'pageTitle' => 'Coupons',
            'coupons'   => $this->couponModel->findAll(),
        ]);
    }

    // ── Create ────────────────────────────────────────────────

    public function create(): void
    {
        $this->adminView('coupons.create', [
            'pageTitle' => 'Add Coupon',
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();

        [$data, $error] = $this->resolveCouponInput();
        if ($error) {
            Session::flash('error', $error);
            $this->redirect(url('admin/coupons/create'));
        }

        if ($this->couponModel->codeExists($data['code'])) {
            Session::flash('error', 'A coupon with this code already exists.');
            $this->redirect(url('admin/coupons/create'));
        }

        $this->couponModel->createCoupon($data);

        Session::flash('success', 'Coupon created successfully.');
        $this->redirect(url('admin/coupons'));
    }

    // ── Edit ──────────────────────────────────────────────────

    public function edit(string $id): void
    {
        $coupon = $this->couponModel->findById((int) $id);
        if (!$coupon) {
            Session::flash('error', 'Coupon not found.');
            $this->redirect(url('admin/coupons'));
        }

        $this->adminView('coupons.edit', [
            'pageTitle' => 'Edit Coupon',
            'coupon'    => $coupon,
        ]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();

        $coupon = $this->couponModel->findById((int) $id);
        if (!$coupon) {
            Session::flash('error', 'Coupon not found.');
            $this->redirect(url('admin/coupons'));
        }

        [$data, $error] = $this->resolveCouponInput();
        if ($error) {
            Session::flash('error', $error);
            $this->redirect(url('admin/coupons/edit/' . $id));
        }

        if ($this->couponModel->codeExists($data['code'], (int) $id)) {
            Session::flash('error', 'That code is already used by another coupon.');
            $this->redirect(url('admin/coupons/edit/' . $id));
        }

        $this->couponModel->updateCoupon((int) $id, $data);

        Session::flash('success', 'Coupon updated.');
        $this->redirect(url('admin/coupons'));
    }

    // ── Delete ────────────────────────────────────────────────

    public function destroy(string $id): void
    {
        $this->verifyCsrf();

        $coupon = $this->couponModel->findById((int) $id);
        if (!$coupon) {
            Session::flash('error', 'Coupon not found.');
            $this->redirect(url('admin/coupons'));
        }

        $this->couponModel->deleteCoupon((int) $id);

        Session::flash('success', 'Coupon deleted.');
        $this->redirect(url('admin/coupons'));
    }

    // ── Private Helpers ───────────────────────────────────────

    /**
     * Parse and validate POST coupon fields.
     *
     * @return array{0: array<string,mixed>, 1: string|null}
     */
    private function resolveCouponInput(): array
    {
        $code          = trim(strip_tags($_POST['code'] ?? ''));
        $discountType  = ($_POST['discount_type'] ?? 'fixed') === 'percent' ? 'percent' : 'fixed';
        $discountValue = (float) ($_POST['discount_value'] ?? 0);
        $minOrder      = trim($_POST['min_order_amount'] ?? '');
        $maxDiscount   = trim($_POST['max_discount_amount'] ?? '');
        $startsAt      = trim($_POST['starts_at'] ?? '');
        $expiresAt     = trim($_POST['expires_at'] ?? '');
        $active        = isset($_POST['is_active']) ? 1 : 0;

        if ($code === '') {
            return [[], 'Coupon code is required.'];
        }

        if ($discountValue <= 0) {
            return [[], 'Discount value must be greater than zero.'];
        }

        if ($discountType === 'percent' && $discountValue > 100) {
            return [[], 'Percentage discount cannot exceed 100%.'];
        }

        return [[
            'code'                => strtoupper($code),
            'discount_type'       => $discountType,
            'discount_value'      => $discountValue,
            'min_order_amount'    => $minOrder !== '' ? (float) $minOrder : null,
            'max_discount_amount' => $maxDiscount !== '' ? (float) $maxDiscount : null,
            'starts_at'           => $startsAt !== '' ? $startsAt : null,
            'expires_at'          => $expiresAt !== '' ? $expiresAt : null,
            'is_active'           => $active,
        ], null];
    }
}
