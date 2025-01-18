<?php

use backend\modules\admin\models\AuthAdmin;
use backend\modules\shop\models\Poll;
use common\widgets\PaginationWidget;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\shop\models\search\PollSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Конструткор опросов';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= Buttons::create();
$this->params['pagination'] = PaginationWidget::widget();
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => Common::pager4(),
    'tableOptions' => ['class' => 'table'],
    'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
    'columns' => [
        [
            'headerOptions' => ['width' => '100'],
            'attribute' => 'id',
        ],
        [
            'headerOptions' => ['width' => '200'],
            'attribute' => 'name',
            'format' => 'raw',
        ],
        [
            'attribute' => 'question',
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '150'],
            'label' => 'Редактировал',
            'filter' => AuthAdmin::managersAll(),
            'attribute' => 'updated_at',
            'value' => function ($data) {
                return AuthAdmin::getById($data->updated_by)->surname;
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '120'],
            'filter' => Poll::statusesAll(),
            'attribute' => 'status',
            'value' => function ($data) {
                return Poll::status($data->status);
            },
            'format' => 'raw'
        ],
        [
            'headerOptions' => ['width' => '150'],
            'attribute' => 'updated_at',
            'filter' => false,
            'format' => 'datetime',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '100'],
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete}</div>',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/shop/poll/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => 'Изменить',
                        ]);

                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/shop/poll/delete', 'id' => $key],
                        [
                            'title' => 'Удалить',
                            'class' => 'btn btn-outline-dark',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                        ]);
                }
            ],
        ],
    ],
]); ?>