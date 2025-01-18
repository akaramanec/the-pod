<?php

use backend\modules\bot\models\Bot;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\bot\models\search\BotSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bot';
$this->params['breadcrumbs'][] = $this->title;
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
        'platform',
        'username',
        'first_name',
        [
            'filter' => Bot::statusesAll(),
            'attribute' => 'status',
            'value' => function ($data) {
                return Bot::status($data->status);
            },
            'format' => 'raw',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update}</div>',
            'headerOptions' => ['width' => '60'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/bot/bot/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => 'Изменить',
                        ]);
                },

            ],
        ],
    ],
]); ?>

