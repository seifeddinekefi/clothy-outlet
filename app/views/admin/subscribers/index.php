<!-- app/views/admin/subscribers/index.php -->

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Newsletter Subscribers</h2>
        <span class="badge badge-info"><?= (int) $total ?> active</span>
    </div>

    <?php if (empty($subscribers)): ?>
        <div style="padding:2.5rem;text-align:center;color:#7a7570;">
            No subscribers yet. The footer signup form will start collecting emails.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Subscribed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscribers as $i => $s): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($s->email) ?></td>
                            <td>
                                <?php if ($s->is_active): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-muted">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d M Y', strtotime($s->created_at)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
