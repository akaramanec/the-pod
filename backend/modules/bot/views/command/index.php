<?php

use backend\modules\bot\models\BotCommand;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\bot\models\search\BotCommandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Bot Commands');
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
if (Yii::$app->user->can('dev')) {
    $this->params['right_content'] .= Buttons::create();
}
$this->params['pagination'] = \common\widgets\PaginationWidget::widget();
?>


<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => Common::pager4(),
    'tableOptions' => ['class' => 'table'],
    'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
    'columns' => [
        [
            'headerOptions' => ['width' => '80'],
            'attribute' => 'id',
        ],
        'name',
        'description',
        [
            'filter' => BotCommand::statusesAll(),
            'attribute' => 'status',
            'value' => function ($data) {
                return BotCommand::status($data->status);
            },
            'format' => 'raw',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update}</div>',
            'headerOptions' => ['width' => '65'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/bot/command/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => 'Изменить',
                        ]);

                },
            ],
        ],
    ],
]); ?>



