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

    public function export(): void
    {
        $model       = new Subscriber();
        $subscribers = $model->findAll();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="subscribers-' . date('Y-m-d') . '.csv"');
        header('Cache-Control: no-cache, must-revalidate');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
        fputcsv($out, ['Email', 'Status', 'Subscribed At']);

        foreach ($subscribers as $s) {
            fputcsv($out, [
                $s->email,
                $s->is_active ? 'Active' : 'Inactive',
                date('Y-m-d H:i:s', strtotime($s->created_at)),
            ]);
        }

        fclose($out);
        exit;
    }
}
