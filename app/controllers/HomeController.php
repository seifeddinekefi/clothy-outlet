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
        $this->render('home.index', [
            'pageTitle'  => APP_NAME,
            'dbFeatured' => $productModel->findFeatured(12),
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

        // TODO: implement contact form logic
        $this->flash('success', 'Your message has been sent!');
        $this->redirectRoute('contact');
    }
}
