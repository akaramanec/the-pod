<?php

use backend\modules\customer\models\Customer;
use backend\modules\customer\models\CustomerMessage;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\customer\models\search\CustomerMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сообщения от пользователя';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
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
            'attribute' => 'customer_id',
            'value' => function ($data) {
                return Customer::fullName($data->customer);
            },
            'format' => 'raw'
        ],
        [
            'headerOptions' => ['width' => '140'],
            'attribute' => 'created_at',
            'format' => 'datetime'
        ],
        [
            'headerOptions' => ['width' => '130'],
            'filter' => CustomerMessage::statusesAll(),
            'attribute' => 'status',
            'value' => function ($data) {
                return CustomerMessage::status($data->status);
            },
            'format' => 'raw'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '100'],
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete}</div>',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/customer/customer-message/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'data-category_id' => $key,
                            'title' => 'Изменить',
                        ]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/customer/customer-message/delete', 'id' => $key],
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

