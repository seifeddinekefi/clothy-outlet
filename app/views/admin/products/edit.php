<!-- app/views/admin/products/edit.php -->

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit: <?= e($product->name) ?></h2>
        <a href="<?= url('admin/products') ?>" class="btn btn-sm btn-outline">&larr; Back</a>
    </div>

    <form method="POST" action="<?= url('admin/products/edit/' . $product->id) ?>"
        enctype="multipart/form-data" novalidate>
        <?= csrfField() ?>

        <div class="form-grid">

            <!-- Left column -->
            <div class="form-main">

                <div class="form-group">
                    <label for="name">Product Name <span class="req">*</span></label>
                    <input id="name" type="text" name="name" class="form-control"
                        value="<?= e($_POST['name'] ?? $product->name) ?>" required maxlength="200">
                </div>

                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input id="slug" type="text" name="slug" class="form-control"
                        value="<?= e($_POST['slug'] ?? $product->slug) ?>" maxlength="220">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control form-textarea"
                        rows="6"><?= e($_POST['description'] ?? $product->description ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group form-group--half">
                        <label for="price">Price ($) <span class="req">*</span></label>
                        <input id="price" type="number" name="price" class="form-control"
                            value="<?= e($_POST['price'] ?? $product->price) ?>"
                            min="0" step="0.01" required>
                    </div>
                    <div class="form-group form-group--half">
                        <label for="compare_price">Compare Price ($)</label>
                        <input id="compare_price" type="number" name="compare_price" class="form-control"
                            value="<?= e($_POST['compare_price'] ?? $product->compare_price ?? '') ?>"
                            min="0" step="0.01">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group--half">
                        <label for="stock">Stock Quantity</label>
                        <input id="stock" type="number" name="stock" class="form-control"
                            value="<?= e($_POST['stock'] ?? $product->stock) ?>" min="0" step="1">
                    </div>
                    <div class="form-group form-group--half">
                        <label for="sku">SKU</label>
                        <input id="sku" type="text" name="sku" class="form-control"
                            value="<?= e($_POST['sku'] ?? $product->sku ?? '') ?>" maxlength="80">
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
                                <?= ((int) ($_POST['category_id'] ?? $product->category_id) === (int) $cat->id) ? 'selected' : '' ?>>
                                <?= e($cat->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1"
                            <?= (isset($_POST['is_featured']) ? $_POST['is_featured'] : $product->is_featured) ? 'checked' : '' ?>>
                        Featured product
                    </label>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1"
                            <?= (isset($_POST['is_active']) ? $_POST['is_active'] : $product->is_active) ? 'checked' : '' ?>>
                        Active (visible in store)
                    </label>
                </div>

                <!-- Existing images -->
                <?php if (!empty($images)): ?>
                    <div class="form-group">
                        <label>Current Images</label>
                        <div class="image-grid">
                            <?php foreach ($images as $img): ?>
                                <div class="image-thumb <?= $img->is_primary ? 'image-thumb--primary' : '' ?>">
                                    <img src="<?= url($img->image_path) ?>"
                                        alt="<?= e($img->alt_text ?? '') ?>"
                                        loading="lazy">
                                    <div class="image-thumb-actions">
                                        <label class="radio-inline" title="Set as primary">
                                            <input type="radio" name="primary_image_id"
                                                value="<?= e($img->id) ?>"
                                                <?= $img->is_primary ? 'checked' : '' ?>>
                                            Primary
                                        </label>
                                        <label class="checkbox-inline delete-check" title="Delete this image">
                                            <input type="checkbox" name="delete_images[]" value="<?= e($img->id) ?>">
                                            Delete
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Upload New Images <span class="hint">(max <?= UPLOAD_MAX_SIZE / 1024 / 1024 ?>MB each)</span></label>
                    <input type="file" name="images[]" class="form-control-file"
                        accept="image/jpeg,image/png,image/webp,image/gif" multiple>
                </div>

            </div><!-- /.form-side -->

        </div><!-- /.form-grid -->

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= url('admin/products') ?>" class="btn btn-outline">Cancel</a>
        </div>

    </form>
</div>