<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\OrderPoll */

$this->title = 'Create Order Poll';
$this->params['breadcrumbs'][] = ['label' => 'Order Polls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pol-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Заказ №',
                'value' => $model->order->id
            ],
            [
                'label' => 'Редактировал',
                'value' => $model->poll->editor->surname
            ],
            [
                'label' => 'Статус',
                'value' => $model::status($model->status)
            ],
            [
                'label' => 'Ответ №1',
                'value' => function () use ($model){
                    return $model->answer_first ?? '-';
                }
            ],
            [
                'label' => 'Ответ №2',
                'value' => function () use ($model){
                    return $model->answer_second ?? '-';
                }
            ],
            [
                'label' => 'Дата создания/последнего опроса',
                'value' => $model->updated_at
            ],
        ],
    ]) ?>

</div>