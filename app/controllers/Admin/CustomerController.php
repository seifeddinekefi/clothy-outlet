<?php

/**
 * ============================================================
 * app/controllers/Admin/CustomerController.php
 * ============================================================
 * Read and manage registered customers.
 *
 * - Paginated / searchable list with order stats
 * - Detail view: profile info + order history
 * - Edit profile (name, email, phone, address, city, notes)
 * - Delete (guarded: cannot delete customers who have orders)
 * ============================================================
 */

class CustomerController extends BaseAdminController
{
    private Customer $customerModel;
    private Order    $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->customerModel = new Customer();
        $this->orderModel    = new Order();
    }

    // ── Index ─────────────────────────────────────────────────

    public function index(): void
    {
        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');

        if ($search !== '') {
            $customers = $this->customerModel->searchWithStats($search);
            $total     = count($customers);
            $pages     = 1;
        } else {
            $result    = $this->customerModel->paginateWithStats($page, 20);
            $customers = $result['data'];
            $total     = $result['total'];
            $pages     = $result['pages'];
        }

        $this->adminView('customers.index', [
            'pageTitle' => 'Customers',
            'customers' => $customers,
            'page'      => $page,
            'pages'     => $pages,
            'total'     => $total,
            'search'    => $search,
        ]);
    }

    // ── Show ──────────────────────────────────────────────────

    public function show(string $id): void
    {
        $customer = $this->customerModel->findById((int) $id);
        if (!$customer) {
            Session::flash('error', 'Customer not found.');
            $this->redirect(url('admin/customers'));
        }

        $orders = $this->orderModel->findByCustomer((int) $id);

        $this->adminView('customers.show', [
            'pageTitle' => 'Customer: ' . $customer->name,
            'customer'  => $customer,
            'orders'    => $orders,
        ]);
    }

    // ── Edit ──────────────────────────────────────────────────

    public function edit(string $id): void
    {
        $customer = $this->customerModel->findById((int) $id);
        if (!$customer) {
            Session::flash('error', 'Customer not found.');
            $this->redirect(url('admin/customers'));
        }

        $this->adminView('customers.edit', [
            'pageTitle' => 'Edit Customer',
            'customer'  => $customer,
        ]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();

        $customer = $this->customerModel->findById((int) $id);
        if (!$customer) {
            Session::flash('error', 'Customer not found.');
            $this->redirect(url('admin/customers'));
        }

        [$data, $error] = $this->resolveCustomerInput((int) $id);
        if ($error) {
            Session::flash('error', $error);
            $this->redirect(url('admin/customers/edit/' . (int) $id));
        }

        $this->customerModel->updateCustomer((int) $id, $data);

        Session::flash('success', 'Customer updated.');
        $this->redirect(url('admin/customers/' . (int) $id));
    }

    // ── Delete ────────────────────────────────────────────────

    public function destroy(string $id): void
    {
        $this->verifyCsrf();

        $customer = $this->customerModel->findById((int) $id);
        if (!$customer) {
            Session::flash('error', 'Customer not found.');
            $this->redirect(url('admin/customers'));
        }

        // Guard: refuse deletion if customer has any orders
        $orderCount = $this->orderModel->count('`customer_id` = :cid', [':cid' => (int) $id]);
        if ($orderCount > 0) {
            Session::flash('error', 'Cannot delete a customer who has existing orders. Remove their orders first.');
            $this->redirect(url('admin/customers/' . (int) $id));
        }

        $this->customerModel->deleteById((int) $id);

        Session::flash('success', 'Customer deleted.');
        $this->redirect(url('admin/customers'));
    }

    // ── Private Helpers ───────────────────────────────────────

    /**
     * Parse and validate POST customer fields.
     *
     * @return array{0: array<string,mixed>, 1: string|null}
     */
    private function resolveCustomerInput(int $excludeId): array
    {
        $name    = trim(strip_tags($_POST['name']    ?? ''));
        $email   = trim(strip_tags($_POST['email']   ?? ''));
        $phone   = trim(strip_tags($_POST['phone']   ?? ''));
        $address = trim(strip_tags($_POST['address'] ?? ''));
        $city    = trim(strip_tags($_POST['city']    ?? ''));
        $notes   = trim(strip_tags($_POST['notes']   ?? ''));

        if ($name === '') {
            return [[], 'Customer name is required.'];
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [[], 'Please enter a valid email address.'];
        }

        if ($email !== '' && $this->customerModel->emailExists($email, $excludeId)) {
            return [[], 'This email address is already used by another customer.'];
        }

        return [[
            'name'    => $name,
            'email'   => $email   ?: null,
            'phone'   => $phone   ?: null,
            'address' => $address ?: null,
            'city'    => $city    ?: null,
            'notes'   => $notes   ?: null,
        ], null];
    }
}
