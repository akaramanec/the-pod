<?php

use backend\modules\media\models\Img;
use backend\modules\shop\models\NoticeNp;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel \backend\modules\shop\models\search\NoticeNpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Уведомления НП';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= Html::tag('a', 'Актуальные статусы трекинга',
    [
        'href' => 'https://devcenter.novaposhta.ua/docs/services/556eef34a0fe4f02049c664e/operations/55702cbba0fe4f0cf4fc53ee',
        'class' => 'btn btn-outline-success btn-dashboard',
        'target' => '_blank',
        'title' => 'Актуальные статусы трекинга'
    ]);
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
            'contentOptions' => ['class' => 'img-table'],
            'headerOptions' => ['width' => '59', 'class' => 'header-name-table'],
            'value' => function ($data) {
                return Img::main(NOTICE_NP, $data->id, $data->img, '400x400');
            },
        ],
        'name',
        [
            'label' => 'Код НП',
            'headerOptions' => ['width' => '160'],
            'attribute' => 'status_code',
            'value' => function ($data) {
                return implode(', ', $data->status_code);
            },
        ],
        [
            'headerOptions' => ['width' => '120'],
            'filter' => NoticeNp::statusesAll(),
            'attribute' => 'status',
            'value' => function ($data) {
                return NoticeNp::status($data->status);
            },
            'format' => 'raw'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete}</div>',
            'headerOptions' => ['width' => '100'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/shop/notice-np/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => 'Изменить',
                        ]);

                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/shop/notice-np/delete', 'id' => $key],
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
