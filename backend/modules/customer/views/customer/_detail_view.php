<?php
/** @var object $customer */

use backend\modules\customer\models\Customer;
use yii\widgets\DetailView;

?>
<div class="bg detail-view">
    <h4><strong>Пользователь</strong></h4>
    <?= DetailView::widget([
        'model' => $customer,
        'options' => ['class' => 'table'],
        'attributes' => [
            'first_name',
            'last_name',
            'username',
            'phone',
            [
                'attribute' => 'created_at',
                'value' => function ($data) {
                    return $data->created_at;
                },
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_at',
                'value' => function ($data) {
                    return $data->updated_at;
                },
                'format' => 'datetime',
            ],
        ]]) ?>
</div>
