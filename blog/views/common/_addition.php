<?php if (isset($addition)): ?>
    <?php foreach ($addition as $key => $value): ?>
        <div class="form-group">
            <label class="control-label"><?= $key ?></label>
            <textarea class="form-control" name="addition[<?= $key ?>]" rows="2"><?= $value ?></textarea>
        </div>
    <?php endforeach ?>
<?php endif; ?>
