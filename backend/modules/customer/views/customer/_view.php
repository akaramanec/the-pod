<?php

use yii\widgets\DetailView;

?>

<?= DetailView::widget([
    'model' => $model,
    'options' => ['class' => 'table detail-view'],
    'attributes' => [
        'username',
        'first_name',
        'last_name',
        'phone',
        [
            'attribute' => 'created_at',
            'format' => 'datetime',
            'headerOptions' => ['width' => '150'],
        ],
    ],
]) ?>
