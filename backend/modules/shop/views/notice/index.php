<?php

use backend\modules\media\models\Img;
use backend\modules\shop\models\Notice;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\go\models\NoticeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Уведомления поле покупки';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= Buttons::create('Добавить оповещение');
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
            'label' => false,
            'format' => 'raw',
            'contentOptions' => ['class' => 'img-table-circle'],
            'headerOptions' => ['width' => '59', 'class' => 'header-name-table'],
            'value' => function ($data) {
                return Img::main(NOTICE, $data->id, $data->img, '400x400');
            },
        ],
        'name',
        [
            'headerOptions' => ['width' => '160'],
            'attribute' => 'idle_time',
        ],
        [
            'headerOptions' => ['width' => '120'],
            'filter' => Notice::statusesAll(),
            'attribute' => 'status',
            'value' => function ($data) {
                return Notice::status($data->status);
            },
            'format' => 'raw'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete}</div>',
            'headerOptions' => ['width' => '100'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/shop/notice/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => 'Изменить',
                        ]);

                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/shop/notice/delete', 'id' => $key],
                        [
                            'title' => 'Удалить',
                            'class' => 'btn btn-outline-dark',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                        ]);
                },
            ],
        ],
    ],
]); ?>
