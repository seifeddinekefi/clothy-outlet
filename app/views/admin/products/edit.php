<!-- app/views/admin/products/edit.php -->

<?php
$_isOnSaleDefault = ((float) ($product->compare_price ?? 0)) > ((float) ($product->price ?? 0));
$_salePctDefault = '';
if ($_isOnSaleDefault && (float) ($product->price ?? 0) > 0) {
    $_salePctDefault = (string) round((1 - ((float) $product->price / (float) $product->compare_price)) * 100);
}
?>

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
                        <p class="form-hint">Original price before discount.</p>
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
                        <input type="checkbox" name="on_sale" id="on_sale" value="1"
                            <?= (isset($_POST['on_sale']) ? $_POST['on_sale'] : $_isOnSaleDefault) ? 'checked' : '' ?>>
                        On sale
                    </label>
                    <p class="form-hint">Sale badge replaces NEW badge automatically.</p>
                </div>

                <div class="form-group">
                    <label for="sale_percent">Sale Percentage (%)</label>
                    <input id="sale_percent" type="number" name="sale_percent" class="form-control"
                        value="<?= e($_POST['sale_percent'] ?? $_salePctDefault) ?>"
                        min="1" max="95" step="1" placeholder="Example: 20">
                    <p class="form-hint">Optional. If set, compare price is auto-calculated from current price.</p>
                </div>

                <div class="form-group">
                    <label for="badge_type">Badge Type</label>
                    <?php $_badgeType = (string) ($_POST['badge_type'] ?? ($product->badge_type ?? 'auto')); ?>
                    <select id="badge_type" name="badge_type" class="form-control">
                        <option value="auto" <?= $_badgeType === 'auto' ? 'selected' : '' ?>>Auto (NEW for featured products)</option>
                        <option value="none" <?= $_badgeType === 'none' ? 'selected' : '' ?>>No badge</option>
                        <option value="new" <?= $_badgeType === 'new' ? 'selected' : '' ?>>New</option>
                        <option value="hot" <?= $_badgeType === 'hot' ? 'selected' : '' ?>>Hot</option>
                        <option value="limited" <?= $_badgeType === 'limited' ? 'selected' : '' ?>>Limited</option>
                        <option value="bestseller" <?= $_badgeType === 'bestseller' ? 'selected' : '' ?>>Bestseller</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="badge_text">Custom Badge Text</label>
                    <input id="badge_text" type="text" name="badge_text" class="form-control"
                        value="<?= e($_POST['badge_text'] ?? ($product->badge_text ?? '')) ?>"
                        maxlength="40" placeholder="Optional, e.g. Limited Drop">
                    <p class="form-hint">Optional text override. Ignored when product is on sale.</p>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1"
                            <?= (isset($_POST['is_active']) ? $_POST['is_active'] : $product->is_active) ? 'checked' : '' ?>>
                        Active (visible in store)
                    </label>
                </div>

                <?php 
                // Build a lookup array for existing sizes
                $sizeStock = [];
                if (!empty($sizes)) {
                    foreach ($sizes as $s) {
                        $sizeStock[$s->size] = (int) $s->stock;
                    }
                }
                ?>
                <div class="form-group">
                    <label>Available Sizes <span class="hint">(enter stock quantity for each)</span></label>
                    <div class="size-grid">
                        <?php foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $sz): ?>
                            <div class="size-input-group">
                                <label for="size_<?= $sz ?>"><?= $sz ?></label>
                                <input type="number" id="size_<?= $sz ?>" name="sizes[<?= $sz ?>]"
                                    class="form-control form-control-sm"
                                    value="<?= e($_POST['sizes'][$sz] ?? $sizeStock[$sz] ?? '0') ?>"
                                    min="0" step="1" placeholder="0">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="form-hint">Set stock quantity > 0 to make a size available. Set to 0 to hide that size.</p>
                </div>

                <!-- Quality tiers -->
                <?php
                $savedQualityTypes  = array_map(fn($q) => $q->quality_type, $qualities ?? []);
                $submittedQualities = $_POST['qualities'] ?? null;
                $activeQualities    = $submittedQualities !== null ? $submittedQualities : $savedQualityTypes;
                ?>
                <div class="form-group">
                    <label>Available Qualities</label>
                    <div class="quality-checkbox-group">
                        <?php foreach (['standard' => 'Standard', '180g' => '180g', '220g' => '220g', '250g' => '250g'] as $val => $label): ?>
                            <label class="checkbox-label quality-check">
                                <input type="checkbox" name="qualities[]" value="<?= $val ?>"
                                    <?= in_array($val, $activeQualities, true) ? 'checked' : '' ?>>
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="form-hint">Leave all unchecked if quality is not a factor. For 220g and 250g only White &amp; Black colors are shown to customers.</p>
                </div>

                <!-- Color swatches -->
                <?php
                $existingColorsJson = json_encode(array_map(fn($c) => ['name' => $c->color_name, 'hex' => $c->color_hex], $colors ?? []), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
                ?>
                <div class="form-group">
                    <label>Available Colors <span class="hint">(for Standard &amp; 180g quality)</span></label>
                    <div id="color-list" class="color-list"></div>
                    <div class="color-add-bar">
                        <input type="text" id="add-color-name" class="form-control form-control-sm"
                               placeholder="Color name, e.g. Red" maxlength="50" style="flex:1">
                        <input type="color" id="add-color-hex" value="#000000" style="height:38px;width:52px;padding:2px 4px;border:1px solid #ddd;border-radius:6px;">
                        <button type="button" class="btn btn-sm btn-outline" onclick="addColor()">+ Add</button>
                    </div>
                    <p class="form-hint">220g and 250g always offer White and Black — no need to add them here.</p>
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

<?php $view->startSection('scripts') ?>
<style>
.quality-checkbox-group { display:flex; flex-wrap:wrap; gap:.75rem; }
.quality-check { display:flex; align-items:center; gap:.35rem; font-size:.875rem; }
.color-list { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:.6rem; }
.color-row { display:flex; align-items:center; gap:.4rem; background:#f8f8f8; border:1px solid #e0e0e0; border-radius:8px; padding:.3rem .5rem .3rem .4rem; }
.color-swatch { width:22px; height:22px; border-radius:50%; border:1px solid rgba(0,0,0,.15); flex-shrink:0; }
.color-row-name { font-size:.82rem; color:#333; max-width:80px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.btn-remove-color { border:none; background:none; color:#999; cursor:pointer; font-size:.9rem; line-height:1; padding:0 2px; }
.btn-remove-color:hover { color:#c23a3a; }
.color-add-bar { display:flex; align-items:center; gap:.5rem; }
</style>
<script>
var _colorIdx = 0;

function addColor() {
    var nameInput = document.getElementById('add-color-name');
    var hexInput  = document.getElementById('add-color-hex');
    var name = nameInput.value.trim();
    var hex  = hexInput.value;
    if (!name) { nameInput.focus(); return; }
    addColorRow(name, hex, _colorIdx++);
    nameInput.value = '';
}

function addColorRow(name, hex, idx) {
    var list = document.getElementById('color-list');
    var row  = document.createElement('div');
    row.className = 'color-row';
    row.id = 'cr-' + idx;
    row.innerHTML =
        '<span class="color-swatch" style="background:' + hex + '"></span>' +
        '<span class="color-row-name" title="' + name + '">' + name + '</span>' +
        '<input type="hidden" name="colors[' + idx + '][name]" value="' + name.replace(/"/g, '&quot;') + '">' +
        '<input type="hidden" name="colors[' + idx + '][hex]" value="' + hex + '">' +
        '<button type="button" class="btn-remove-color" onclick="removeColor(' + idx + ')" title="Remove">&times;</button>';
    list.appendChild(row);
}

function removeColor(idx) {
    var el = document.getElementById('cr-' + idx);
    if (el) el.remove();
}

// Pre-populate existing colors
(function() {
    var existing = <?= $existingColorsJson ?? '[]' ?>;
    _colorIdx = existing.length;
    existing.forEach(function(c, i) { addColorRow(c.name, c.hex, i); });
})();

(function() {
    var saleToggle = document.getElementById('on_sale');
    var salePercent = document.getElementById('sale_percent');
    var priceInput = document.getElementById('price');
    var compareInput = document.getElementById('compare_price');

    function syncSaleFields() {
        var enabled = saleToggle && saleToggle.checked;
        if (salePercent) {
            salePercent.disabled = !enabled;
        }
        if (!enabled && salePercent) {
            salePercent.value = '';
        }
    }

    function syncCompareFromPercent() {
        if (!saleToggle || !saleToggle.checked || !salePercent || !priceInput || !compareInput) {
            return;
        }

        var pct = parseInt(salePercent.value, 10);
        var price = parseFloat(priceInput.value);

        if (!Number.isFinite(pct) || pct < 1 || pct > 95 || !Number.isFinite(price) || price <= 0) {
            return;
        }

        var compare = price / (1 - (pct / 100));
        compareInput.value = compare.toFixed(2);
    }

    if (saleToggle) {
        saleToggle.addEventListener('change', syncSaleFields);
    }
    if (salePercent) {
        salePercent.addEventListener('input', syncCompareFromPercent);
    }
    if (priceInput) {
        priceInput.addEventListener('input', syncCompareFromPercent);
    }

    syncSaleFields();
})();
</script>
<?php $view->endSection() ?>
