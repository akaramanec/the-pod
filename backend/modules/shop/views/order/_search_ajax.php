<?php
/**
 * @var array $models
 */

?>
<div class="search-list">
    <?php if ($models): ?>
        <?php foreach ($models as $model): ?>
            <div class="search-item" data-id="<?= $model->id ?>">
                <span>ID - <?= $model->id ?> <?= $model->product->name ?></span>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        <div class="search-item">
            <span>Ничего не найдено</span>
        </div>
    <?php endif; ?>
</div>
