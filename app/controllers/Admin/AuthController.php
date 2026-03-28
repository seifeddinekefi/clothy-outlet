<?php

/**
 * ============================================================
 * app/controllers/Admin/AuthController.php
 * ============================================================
 * Handles admin login and logout.
 *
 * Intentionally NOT an extension of BaseAdminController —
 * login must be accessible before authentication.
 * ============================================================
 */

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Standalone login page — no admin layout
        $this->view->setLayout('');
    }

    /**
     * Show the admin login form.
     */
    public function loginForm(): void
    {
        // Already authenticated admin → straight to dashboard
        if (Session::isLoggedIn() && Session::hasRole('admin')) {
            $this->redirect(url('admin'));
        }

        $this->render('admin.auth.login', [
            'pageTitle' => 'Admin Login — ' . APP_NAME,
            '_flash'    => Session::getFlash(),
        ]);
    }

    /**
     * Process login form submission.
     */
    public function login(): void
    {
        $this->verifyCsrf();

        // Check rate limit before processing
        $rateLimiter = new RateLimitMiddleware('login');
        $rateLimiter->handle(function() {});

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            Session::flash('error', 'Email and password are required.');
            $this->redirect(url('admin/login'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Invalid email address.');
            $this->redirect(url('admin/login'));
        }

        $adminModel = new Admin();
        $admin      = $adminModel->findByEmail($email);

        if (!$admin || !password_verify($password, $admin->password)) {
            // Record failed attempt for rate limiting
            RateLimitMiddleware::recordFailedAttempt('login');
            
            $remaining = RateLimitMiddleware::getRemainingAttempts('login');
            $message = 'Invalid email or password.';
            if ($remaining > 0 && $remaining <= 3) {
                $message .= " {$remaining} attempt(s) remaining.";
            }
            
            Session::flash('error', $message);
            $this->redirect(url('admin/login'));
        }

        if (!(int) $admin->is_active) {
            Session::flash('error', 'This admin account has been disabled.');
            $this->redirect(url('admin/login'));
        }

        // Clear rate limit on successful login
        RateLimitMiddleware::clearAttempts('login');

        $adminWithRole = $adminModel->findWithRole((int) $admin->id);
        $roleName      = $adminWithRole->role_name ?? 'super_admin';

        Session::login([
            'id'         => (int) $admin->id,
            'name'       => $admin->name,
            'email'      => $admin->email,
            'role'       => 'admin',
            'admin_role' => $roleName,
        ]);

        $adminModel->update(
            ['last_login' => date('Y-m-d H:i:s')],
            '`id` = :id',
            [':id' => (int) $admin->id]
        );

        $this->redirect(url('admin'));
    }

    /**
     * Destroy the admin session and redirect to login.
     */
    public function logout(): void
    {
        Session::logout();
        $this->redirect(url('admin/login'));
    }
}
