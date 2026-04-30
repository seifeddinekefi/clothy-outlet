<?php

/**
 * app/controllers/NewsletterController.php
 * Handles newsletter subscription from the footer form.
 */

class NewsletterController extends Controller
{
    public function subscribe(): void
    {
        $this->verifyCsrf();

        $email = strtolower(trim($this->post('email', '')));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Please enter a valid email address.');
            $this->redirectBack(url());
        }

        $model = new Subscriber();

        if ($model->emailExists($email)) {
            $this->flash('info', 'You are already subscribed — thank you!');
            $this->redirectBack(url());
        }

        $model->subscribe($email);

        $this->flash('success', 'You\'re subscribed! We\'ll keep you updated.');
        $this->redirectBack(url());
    }
}
