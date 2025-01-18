<?php

use kartik\select2\Select2;

/**
 * @var array $listWarehouses
 * @var string $value
 */
?>
<label class="control-label">Отделение</label>
<?= Select2::widget([
    'name' => 'OrderNp[branch]',
    'data' => $listWarehouses,
    'theme' => Select2::THEME_BOOTSTRAP,
    'value' => $value ? $value : '',
    'options' => [
        'id' => 'branch_ref',
        'multiple' => false
    ],
]) ?>
