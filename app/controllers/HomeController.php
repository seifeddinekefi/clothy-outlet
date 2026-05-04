<?php

/**
 * ============================================================
 * app/controllers/HomeController.php
 * ============================================================
 * Handles public-facing homepage routes.
 * STUB — implement business logic in later steps.
 * ============================================================
 */

class HomeController extends Controller
{
    public function index(): void
    {
        $productModel = new Product();
        $categoryModel = new Category();

        $allProducts = $productModel->findActive();
        $featuredProducts = $productModel->findFeatured(12);
        $categories = $categoryModel->findActive();

        $this->render('home.index', [
            'pageTitle'    => APP_NAME,
            'dbFeatured'   => $featuredProducts,
            'allProducts'  => $allProducts,
            'categories'   => $categories,
            'totalProducts' => count($allProducts),
        ]);
    }

    public function about(): void
    {
        $this->render('home.about', [
            'pageTitle' => 'About Us — ' . APP_NAME,
        ]);
    }

    public function contact(): void
    {
        $this->render('home.contact', [
            'pageTitle' => 'Contact — ' . APP_NAME,
        ]);
    }

    public function sendContact(): void
    {
        $this->verifyCsrf();

        $name    = trim(strip_tags($this->post('name',    '')));
        $email   = trim($this->post('email',   ''));
        $subject = trim(strip_tags($this->post('subject', 'Contact Form Message')));
        $message = trim(strip_tags($this->post('message', '')));

        if ($name === '' || $message === '') {
            $this->flash('error', 'Name and message are required.');
            $this->redirect(url('contact'));
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Please enter a valid email address.');
            $this->redirect(url('contact'));
        }

        // Resolve destination: store email from settings, fallback to env
        $settingModel = new Setting();
        $storeEmail   = $settingModel->get('store_email', '')
                        ?: EnvLoader::get('MAIL_FROM_ADDRESS', '');

        if ($storeEmail === '') {
            // No destination configured — log it and show success anyway
            error_log("Contact form submitted (no store_email configured): from={$email} subject={$subject}");
            $this->flash('success', 'Your message has been sent. We\'ll get back to you shortly!');
            $this->redirect(url('contact'));
        }

        $appName     = APP_NAME;
        $escapedName = htmlspecialchars($name,    ENT_QUOTES, 'UTF-8');
        $escapedFrom = htmlspecialchars($email,   ENT_QUOTES, 'UTF-8');
        $escapedSubj = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
        $escapedMsg  = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

        $body = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Contact Form — {$appName}</title></head>
<body style="font-family:Arial,sans-serif;line-height:1.6;color:#333;background:#f3f4f6;margin:0;padding:0;">
  <div style="max-width:600px;margin:0 auto;padding:20px;">
    <div style="background:#0a0a0a;color:#fff;padding:20px 25px;border-radius:8px 8px 0 0;">
      <h2 style="margin:0;font-size:18px;">{$appName} — New Contact Message</h2>
    </div>
    <div style="background:#fff;padding:25px 30px;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 8px 8px;">
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
        <tr>
          <td style="padding:6px 0;font-weight:700;color:#6b7280;font-size:12px;text-transform:uppercase;width:90px;">From</td>
          <td style="padding:6px 0;">{$escapedName} &lt;{$escapedFrom}&gt;</td>
        </tr>
        <tr>
          <td style="padding:6px 0;font-weight:700;color:#6b7280;font-size:12px;text-transform:uppercase;">Subject</td>
          <td style="padding:6px 0;">{$escapedSubj}</td>
        </tr>
      </table>
      <hr style="border:none;border-top:1px solid #e5e7eb;margin:0 0 20px;">
      <p style="margin:0;white-space:pre-wrap;">{$escapedMsg}</p>
    </div>
    <p style="text-align:center;font-size:11px;color:#9ca3af;margin-top:15px;">
      Sent via the contact form on {$appName}. Reply directly to {$escapedFrom}.
    </p>
  </div>
</body>
</html>
HTML;

        try {
            $mailer = new Mailer();
            $mailer->send(
                $storeEmail,
                "Contact: {$subject}",
                $body,
                ['from' => $email, 'from_name' => $name]
            );
        } catch (Exception $e) {
            error_log('Contact form mail failed: ' . $e->getMessage());
        }

        $this->flash('success', 'Your message has been sent. We\'ll get back to you shortly!');
        $this->redirect(url('contact'));
    }
}
