<!-- app/views/admin/customers/edit.php -->

<div class="card card--narrow">
    <div class="card-header">
        <h2 class="card-title">Edit: <?= e($customer->name) ?></h2>
        <a href="<?= url('admin/customers/' . $customer->id) ?>" class="btn btn-sm btn-outline">&larr; Back</a>
    </div>

    <form method="POST" action="<?= url('admin/customers/edit/' . $customer->id) ?>" novalidate>
        <?= csrfField() ?>

        <div class="form-group">
            <label for="name">Full Name <span class="req">*</span></label>
            <input id="name" type="text" name="name" class="form-control"
                value="<?= e($_POST['name'] ?? $customer->name) ?>" required maxlength="150">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" class="form-control"
                value="<?= e($_POST['email'] ?? $customer->email ?? '') ?>" maxlength="180">
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input id="phone" type="text" name="phone" class="form-control"
                value="<?= e($_POST['phone'] ?? $customer->phone ?? '') ?>" maxlength="30">
        </div>

        <div class="form-row">
            <div class="form-group form-group--grow">
                <label for="address">Address</label>
                <input id="address" type="text" name="address" class="form-control"
                    value="<?= e($_POST['address'] ?? $customer->address ?? '') ?>" maxlength="255">
            </div>
            <div class="form-group form-group--half">
                <label for="city">City</label>
                <input id="city" type="text" name="city" class="form-control"
                    value="<?= e($_POST['city'] ?? $customer->city ?? '') ?>" maxlength="100">
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" class="form-control form-textarea"
                rows="3"><?= e($_POST['notes'] ?? $customer->notes ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= url('admin/customers/' . $customer->id) ?>" class="btn btn-outline">Cancel</a>
        </div>

    </form>
</div>