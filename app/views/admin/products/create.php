<!-- app/views/admin/products/create.php -->

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Add New Product</h2>
        <a href="<?= url('admin/products') ?>" class="btn btn-sm btn-outline">&larr; Back</a>
    </div>

    <form method="POST" action="<?= url('admin/products/create') ?>"
        enctype="multipart/form-data" novalidate>
        <?= csrfField() ?>

        <div class="form-grid">

            <!-- Left column -->
            <div class="form-main">

                <div class="form-group">
                    <label for="name">Product Name <span class="req">*</span></label>
                    <input id="name" type="text" name="name" class="form-control"
                        value="<?= e($_POST['name'] ?? '') ?>" required maxlength="200">
                </div>

                <div class="form-group">
                    <label for="slug">Slug <span class="hint">(auto-generated if empty)</span></label>
                    <input id="slug" type="text" name="slug" class="form-control"
                        value="<?= e($_POST['slug'] ?? '') ?>" maxlength="220"
                        placeholder="leave-blank-to-auto-generate">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control form-textarea"
                        rows="6"><?= e($_POST['description'] ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group form-group--half">
                        <label for="price">Price ($) <span class="req">*</span></label>
                        <input id="price" type="number" name="price" class="form-control"
                            value="<?= e($_POST['price'] ?? '') ?>"
                            min="0" step="0.01" required placeholder="0.00">
                    </div>
                    <div class="form-group form-group--half">
                        <label for="compare_price">Compare Price ($)</label>
                        <input id="compare_price" type="number" name="compare_price" class="form-control"
                            value="<?= e($_POST['compare_price'] ?? '') ?>"
                            min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group--half">
                        <label for="stock">Stock Quantity</label>
                        <input id="stock" type="number" name="stock" class="form-control"
                            value="<?= e($_POST['stock'] ?? '0') ?>" min="0" step="1">
                    </div>
                    <div class="form-group form-group--half">
                        <label for="sku">SKU</label>
                        <input id="sku" type="text" name="sku" class="form-control"
                            value="<?= e($_POST['sku'] ?? '') ?>" maxlength="80" placeholder="Optional">
                    </div>
                </div>

            </div><!-- /.form-main -->

            <!-- Right column -->
            <div class="form-side">

                <div class="form-group">
                    <label for="category_id">Category <span class="req">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">— Select category —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat->id) ?>"
                                <?= ((int) ($_POST['category_id'] ?? 0) === (int) $cat->id) ? 'selected' : '' ?>>
                                <?= e($cat->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1"
                            <?= isset($_POST['is_featured']) ? 'checked' : '' ?>>
                        Featured product
                    </label>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1"
                            <?= (!isset($_POST['is_active']) || $_POST['is_active']) ? 'checked' : '' ?>>
                        Active (visible in store)
                    </label>
                </div>

                <div class="form-group">
                    <label>Available Sizes <span class="hint">(enter stock quantity for each)</span></label>
                    <div class="size-grid">
                        <?php foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $sz): ?>
                            <div class="size-input-group">
                                <label for="size_<?= $sz ?>"><?= $sz ?></label>
                                <input type="number" id="size_<?= $sz ?>" name="sizes[<?= $sz ?>]"
                                    class="form-control form-control-sm"
                                    value="<?= e($_POST['sizes'][$sz] ?? '0') ?>"
                                    min="0" step="1" placeholder="0">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="form-hint">Set stock quantity > 0 to make a size available. Leave at 0 to hide that size.</p>
                </div>

                <div class="form-group">
                    <label>Product Images <span class="hint">(max <?= UPLOAD_MAX_SIZE / 1024 / 1024 ?>MB each)</span></label>
                    <input type="file" name="images[]" class="form-control-file"
                        accept="image/jpeg,image/png,image/webp,image/gif" multiple>
                    <p class="form-hint">First image becomes the primary thumbnail.</p>
                </div>

            </div><!-- /.form-side -->

        </div><!-- /.form-grid -->

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Product</button>
            <a href="<?= url('admin/products') ?>" class="btn btn-outline">Cancel</a>
        </div>

    </form>
</div>