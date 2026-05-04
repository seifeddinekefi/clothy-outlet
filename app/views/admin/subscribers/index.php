<!-- app/views/admin/subscribers/index.php -->

<div class="page-header">
    <div>
        <h1 class="page-header-title">Subscribers</h1>
        <p class="page-header-sub"><?= (int) $total ?> active subscriber<?= $total !== 1 ? 's' : '' ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?= url('admin/subscribers/export') ?>" class="btn btn-outline btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export CSV
        </a>
    </div>
</div>

<div class="card">
    <?php if (empty($subscribers)): ?>
        <p class="empty-state">No subscribers yet. The footer signup form will start collecting emails.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table table-cards">
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
                            <td class="td-muted tc-hide"><?= $i + 1 ?></td>
                            <td class="tc-primary"><?= e($s->email) ?></td>
                            <td data-label="Status">
                                <?php if ($s->is_active): ?>
                                    <span class="badge badge--success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge--muted">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Subscribed" class="td-muted tc-hide">
                                <?= date('d M Y', strtotime($s->created_at)) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
