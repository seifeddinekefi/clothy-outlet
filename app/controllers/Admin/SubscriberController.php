<?php

/**
 * app/controllers/Admin/SubscriberController.php
 * Read-only list of newsletter subscribers.
 */

class SubscriberController extends BaseAdminController
{
    public function index(): void
    {
        $model = new Subscriber();

        $this->adminView('subscribers.index', [
            'pageTitle'   => 'Newsletter Subscribers',
            'subscribers' => $model->findAll(),
            'total'       => $model->countActive(),
        ]);
    }
}
