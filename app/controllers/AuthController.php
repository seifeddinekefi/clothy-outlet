<?php

/**
 * ============================================================
 * app/controllers/AuthController.php
 * ============================================================
 * Handles registration, login, and logout.
 * ============================================================
 */

class AuthController extends Controller
{
    public function registerForm(): void
    {
        if (class_exists('Session') && Session::isLoggedIn()) {
            $this->redirect(url('account'));
        }

        $this->render('auth.register', [
            'pageTitle' => 'Create Account — ' . APP_NAME,
        ]);
    }

    public function register(): void
    {
        $this->verifyCsrf();

        $firstName       = trim($this->post('first_name', ''));
        $lastName        = trim($this->post('last_name', ''));
        $email           = trim($this->post('email', ''));
        $password        = $this->post('password', '');
        $passwordConfirm = $this->post('password_confirm', '');

        // ── Preserve old input on error ───────────────────
        $old = [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
        ];

        // ── Validation ────────────────────────────────────
        $errors = [];

        if ($firstName === '') {
            $errors[] = 'First name is required.';
        }
        if ($lastName === '') {
            $errors[] = 'Last name is required.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            Session::set('_old_register', $old);
            $this->redirect(url('register'));
        }

        // ── Duplicate email check ─────────────────────────
        $customerModel = new Customer();

        if ($customerModel->emailExists($email)) {
            $this->flash('error', 'An account with that email already exists.');
            Session::set('_old_register', $old);
            $this->redirect(url('register'));
        }

        // ── Create customer ───────────────────────────────
        $customerId = $customerModel->create([
            'name'     => $firstName . ' ' . $lastName,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);

        if (!$customerId) {
            $this->flash('error', 'Registration failed. Please try again.');
            Session::set('_old_register', $old);
            $this->redirect(url('register'));
        }

        // ── Log the customer in ───────────────────────────
        Session::login([
            'id'    => (int) $customerId,
            'name'  => $firstName . ' ' . $lastName,
            'email' => $email,
            'role'  => 'customer',
        ]);

        $this->flash('success', 'Welcome to Clothy, ' . htmlspecialchars($firstName) . '!');
        $this->redirect(url('account'));
    }

    public function loginForm(): void
    {
        if (class_exists('Session') && Session::isLoggedIn()) {
            $this->redirect(url('account'));
        }

        $this->render('auth.login', [
            'pageTitle' => 'Sign In — ' . APP_NAME,
        ]);
    }

    public function login(): void
    {
        $this->verifyCsrf();

        // Check rate limit before processing
        $rateLimiter = new RateLimitMiddleware('login');
        $rateLimiter->handle(function() {}); // Will redirect if locked out

        $email    = trim($this->post('email', ''));
        $password = $this->post('password', '');
        $remember = (bool) $this->post('remember', false);

        // ── Basic validation ──────────────────────────────────
        if ($email === '' || $password === '') {
            $this->flash('error', 'Email and password are required.');
            Session::set('_old_login', ['email' => $email]);
            $this->redirect(url('login'));
        }

        // ── Look up customer ──────────────────────────────────
        $customerModel = new Customer();
        $customer      = $customerModel->findByEmail($email);

        if (!$customer || !password_verify($password, $customer->password ?? '')) {
            // Record failed attempt for rate limiting
            RateLimitMiddleware::recordFailedAttempt('login');
            
            $remaining = RateLimitMiddleware::getRemainingAttempts('login');
            $message = 'Invalid email or password.';
            if ($remaining > 0 && $remaining <= 3) {
                $message .= " {$remaining} attempt(s) remaining.";
            }
            
            $this->flash('error', $message);
            Session::set('_old_login', ['email' => $email]);
            $this->redirect(url('login'));
        }

        // ── Clear rate limit on successful login ──────────────
        RateLimitMiddleware::clearAttempts('login');

        // ── Log in ────────────────────────────────────────────
        Session::login([
            'id'    => (int) $customer->id,
            'name'  => $customer->name,
            'email' => $customer->email,
            'role'  => 'customer',
        ]);

        if ($remember) {
            // Extend the session cookie lifetime for "remember me"
            setcookie(
                session_name(),
                session_id(),
                time() + 60 * 60 * 24 * 30, // 30 days
                '/',
                '',
                SESSION_SECURE,
                true
            );
        }

        $this->flash('success', 'Welcome back, ' . htmlspecialchars($customer->name) . '!');
        $this->redirect(url('account'));
    }

    public function logout(): void
    {
        Session::destroy();
        $this->redirect(url());
    }

    public function forgotPasswordForm(): void
    {
        $this->render('auth.forgot-password', [
            'pageTitle' => 'Forgot Password — ' . APP_NAME,
        ]);
    }

    public function sendResetLink(): void
    {
        $this->verifyCsrf();

        // Check rate limit for password reset
        $rateLimiter = new RateLimitMiddleware('password_reset');
        $rateLimiter->handle(function() {});

        $email = trim($this->post('email', ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Please enter a valid email address.');
            $this->redirect(url('forgot-password'));
        }

        $customerModel = new Customer();
        $customer = $customerModel->findByEmail($email);

        // Always return success-like message to avoid email enumeration.
        if ($customer) {
            $resetModel = new PasswordReset();
            $resetModel->purgeForEmail($email);
            $token = $resetModel->createToken($email);

            if ($token) {
                $resetUrl = url('reset-password/' . $token);
                
                // Send password reset email
                $mailer = new Mailer();
                $customerName = $customer->name ?? 'Customer';
                $mailer->sendPasswordReset($email, $resetUrl, $customerName);
                
                // Record attempt for rate limiting
                RateLimitMiddleware::recordFailedAttempt('password_reset');
            }
        }

        $this->flash('success', 'If an account exists for this email, a password reset link has been sent.');
        $this->redirect(url('forgot-password'));
    }

    public function resetPasswordForm(string $token): void
    {
        $resetModel = new PasswordReset();
        $record = $resetModel->findValidByToken($token);

        if (!$record) {
            $this->flash('error', 'This reset link is invalid or expired.');
            $this->redirect(url('forgot-password'));
        }

        $this->render('auth.reset-password', [
            'pageTitle' => 'Reset Password — ' . APP_NAME,
            'token'     => $token,
            'email'     => $record->email,
        ]);
    }

    public function resetPassword(string $token): void
    {
        $this->verifyCsrf();

        $password = $this->post('password', '');
        $confirm  = $this->post('password_confirm', '');

        if (strlen($password) < 8) {
            $this->flash('error', 'Password must be at least 8 characters.');
            $this->redirect(url('reset-password/' . $token));
        }
        if ($password !== $confirm) {
            $this->flash('error', 'Passwords do not match.');
            $this->redirect(url('reset-password/' . $token));
        }

        $resetModel = new PasswordReset();
        $record = $resetModel->findValidByToken($token);
        if (!$record) {
            $this->flash('error', 'This reset link is invalid or expired.');
            $this->redirect(url('forgot-password'));
        }

        $customerModel = new Customer();
        $customerModel->updatePasswordByEmail($record->email, password_hash($password, PASSWORD_BCRYPT));
        $resetModel->consumeToken($token);

        $this->flash('success', 'Password updated. You can now log in.');
        $this->redirect(url('login'));
    }
}
