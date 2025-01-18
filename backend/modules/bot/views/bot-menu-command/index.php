<?php

use common\widgets\PaginationWidget;
use src\helpers\Buttons;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\bot\models\search\BotMenuCommandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bot Menu Commands';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= Buttons::create();
$this->params['pagination'] = PaginationWidget::widget();
?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'headerOptions' => ['width' => '80'],
            ],
            'name',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete}</div>',
                'headerOptions' => ['width' => '105'],
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-pencil-alt"></i>', ['/bot/bot-menu-command/update', 'id' => $key],
                            [
                                'class' => 'btn btn-primary',
                                'title' => 'Изменить',
                            ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-trash-alt"></i>', ['/bot/bot-menu-command/delete', 'id' => $key],
                            [
                                'title' => 'Удалить',
                                'class' => 'btn btn-danger',
                                'data-method' => 'post',
                                'data-pjax' => '0',
                                'data-confirm' => 'Ви впевнені, що хочете видалити цей елемент?'
                            ]);
                    },
                ],
            ],
        ],
    ]); ?>

