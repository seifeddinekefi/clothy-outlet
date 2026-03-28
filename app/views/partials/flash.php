<!-- ============================================================
     app/views/partials/flash.php
     Renders flash messages injected by Session::getFlash().
     $_flash is automatically available via View::render().
     ============================================================ -->
<?php if (!empty($_flash)): ?>
    <div class="flash-container" role="alert" aria-live="polite">
        <?php foreach ($_flash as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="flash flash--<?= e($type) ?>">
                    <?= e($message) ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>