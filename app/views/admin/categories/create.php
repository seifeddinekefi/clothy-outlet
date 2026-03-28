<!-- app/views/admin/categories/create.php -->

<div class="card card--narrow">
    <div class="card-header">
        <h2 class="card-title">Add Category</h2>
        <a href="<?= url('admin/categories') ?>" class="btn btn-sm btn-outline">&larr; Back</a>
    </div>

    <form method="POST" action="<?= url('admin/categories/create') ?>" novalidate>
        <?= csrfField() ?>

        <div class="form-group">
            <label for="name">Category Name <span class="req">*</span></label>
            <input id="name" type="text" name="name" class="form-control"
                value="<?= e($_POST['name'] ?? '') ?>" required maxlength="100">
        </div>

        <div class="form-group">
            <label for="slug">Slug <span class="hint">(auto-generated if empty)</span></label>
            <input id="slug" type="text" name="slug" class="form-control"
                value="<?= e($_POST['slug'] ?? '') ?>" maxlength="110"
                placeholder="leave-blank-to-auto-generate">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control form-textarea"
                rows="3"><?= e($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group form-group--half">
                <label for="sort_order">Sort Order</label>
                <input id="sort_order" type="number" name="sort_order" class="form-control"
                    value="<?= e($_POST['sort_order'] ?? '0') ?>" min="0" step="1">
            </div>
            <div class="form-group form-group--half form-group--center-v">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1"
                        <?= (!isset($_POST['is_active']) || $_POST['is_active']) ? 'checked' : '' ?>>
                    Active
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Category</button>
            <a href="<?= url('admin/categories') ?>" class="btn btn-outline">Cancel</a>
        </div>

    </form>
</div>