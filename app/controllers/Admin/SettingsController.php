<?php

/**
 * ============================================================
 * app/controllers/Admin/SettingsController.php
 * ============================================================
 * Manages admin-only application settings across three tabs:
 *   - store    : Store name, contact info, currency, thresholds
 *   - account  : Current admin's name and email
 *   - security : Change password (requires current password)
 * ============================================================
 */

class SettingsController extends BaseAdminController
{
    private Setting $settingModel;
    private Admin   $adminModel;

    public function __construct()
    {
        parent::__construct();
        $this->settingModel = new Setting();
        $this->adminModel   = new Admin();
    }

    // ── Show settings page ────────────────────────────────────

    public function index(): void
    {
        $tab = in_array($_GET['tab'] ?? '', ['store', 'account', 'security'], true)
            ? ($_GET['tab'])
            : 'store';

        $this->adminView('settings.index', [
            'pageTitle' => 'Settings',
            'tab'       => $tab,
            'settings'  => $this->settingModel->allKeyed(),
        ]);
    }

    // ── Update store settings ─────────────────────────────────

    public function updateStore(): void
    {
        $this->verifyCsrf();

        $storeName      = trim($_POST['store_name']          ?? '');
        $storeTagline   = trim($_POST['store_tagline']        ?? '');
        $storeEmail     = trim($_POST['store_email']          ?? '');
        $storePhone     = trim($_POST['store_phone']          ?? '');
        $storeAddress   = trim($_POST['store_address']        ?? '');
        $currencySymbol = trim($_POST['currency_symbol']      ?? 'TND');
        $perPage        = (int) ($_POST['products_per_page']   ?? 12);
        $lowStock       = (int) ($_POST['low_stock_threshold'] ?? 10);

        if ($storeName === '') {
            Session::flash('error', 'Store name cannot be empty.');
            $this->redirect(url('admin/settings?tab=store'));
        }

        if ($storeEmail !== '' && !filter_var($storeEmail, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid store email address.');
            $this->redirect(url('admin/settings?tab=store'));
        }

        $perPage  = max(1, min(100, $perPage));
        $lowStock = max(0, min(1000, $lowStock));

        $this->settingModel->setMany([
            'store_name'          => $storeName,
            'store_tagline'       => $storeTagline,
            'store_email'         => $storeEmail,
            'store_phone'         => $storePhone,
            'store_address'       => $storeAddress,
            'currency_symbol'     => $currencySymbol ?: 'TND',
            'products_per_page'   => (string) $perPage,
            'low_stock_threshold' => (string) $lowStock,
        ]);

        Session::flash('success', 'Store settings saved.');
        $this->redirect(url('admin/settings?tab=store'));
    }

    // ── Update admin account (name + email) ──────────────────

    public function updateAccount(): void
    {
        $this->verifyCsrf();

        $user  = $this->authUser();
        $id    = (int) ($user['id'] ?? 0);

        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            Session::flash('error', 'Name and email are required.');
            $this->redirect(url('admin/settings?tab=account'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.');
            $this->redirect(url('admin/settings?tab=account'));
        }

        // Check email uniqueness (excluding current admin)
        $existing = $this->adminModel->findByEmail($email);
        if ($existing && (int) $existing->id !== $id) {
            Session::flash('error', 'That email address is already in use by another account.');
            $this->redirect(url('admin/settings?tab=account'));
        }

        $this->adminModel->updateAdmin($id, ['name' => $name, 'email' => $email]);

        // Refresh session data so topbar/sidebar reflects new name immediately
        $updated = $this->adminModel->findWithRole($id);
        if ($updated) {
            Session::set('admin', [
                'id'         => $updated->id,
                'name'       => $updated->name,
                'email'      => $updated->email,
                'admin_role' => $updated->role_name,
                'role'       => 'admin',
            ]);
        }

        Session::flash('success', 'Your account details have been updated.');
        $this->redirect(url('admin/settings?tab=account'));
    }

    // ── Change password ───────────────────────────────────────

    public function updatePassword(): void
    {
        $this->verifyCsrf();

        $user    = $this->authUser();
        $id      = (int) ($user['id'] ?? 0);
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($current === '' || $new === '' || $confirm === '') {
            Session::flash('error', 'All password fields are required.');
            $this->redirect(url('admin/settings?tab=security'));
        }

        $admin = $this->adminModel->findById($id);
        if (!$admin || !$this->adminModel->verifyPassword($current, $admin->password)) {
            Session::flash('error', 'Current password is incorrect.');
            $this->redirect(url('admin/settings?tab=security'));
        }

        if ($new !== $confirm) {
            Session::flash('error', 'New password and confirmation do not match.');
            $this->redirect(url('admin/settings?tab=security'));
        }

        if (strlen($new) < 8) {
            Session::flash('error', 'New password must be at least 8 characters long.');
            $this->redirect(url('admin/settings?tab=security'));
        }

        $hash = password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->adminModel->updateAdmin($id, ['password' => $hash]);

        Session::flash('success', 'Password changed successfully.');
        $this->redirect(url('admin/settings?tab=security'));
    }
}
