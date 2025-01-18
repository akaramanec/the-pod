<?php

use backend\modules\bot\models\BotPlaceholder;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\bot\models\search\BotPlaceholderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Placeholders';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
if (Yii::$app->user->can('dev')) {
    $this->params['right_content'] .= Buttons::create('Добавить форму');
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
            'visible' => Yii::$app->user->can('dev'),
            'attribute' => 'slug',
            'format' => 'raw',
        ],
        'name',
        'text:ntext',
        [
            'headerOptions' => ['width' => '250'],
            'attribute' => 'text_example',
            'format' => 'html',
        ],
        [
            'visible' => Yii::$app->user->can('dev'),
            'attribute' => 'sort',
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '50'],
            'filter' => BotPlaceholder::statusesAll(),
            'attribute' => 'status',
            'value' => function ($data) {
                return BotPlaceholder::status($data->status);
            },
            'format' => 'raw'
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '100'],
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete}</div>',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/bot/placeholder/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'data-category_id' => $key,
                            'title' => 'Изменить',
                        ]);
                },
                'delete' => function ($url, $model, $key) {
                    if (Yii::$app->user->can('dev')) {
                        return Html::a('<i class="fas fa-trash-alt"></i>', ['/bot/placeholder/delete', 'id' => $key],
                            [
                                'title' => 'Удалить',
                                'class' => 'btn btn-outline-dark',
                                'data-method' => 'post',
                                'data-pjax' => '0',
                                'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                            ]);
                    }
                },
            ],
        ],
    ],
]); ?>
