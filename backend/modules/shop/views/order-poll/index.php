<?php

use backend\modules\admin\models\AuthAdmin;
use backend\modules\shop\models\OrderPoll;
use common\widgets\PaginationWidget;
use src\helpers\Common;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\shop\models\search\OrderPollSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Опросы';
$this->params['breadcrumbs'][] = $this->title;
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
            'headerOptions' => ['width' => '100'],
            'attribute' => 'order_id',
            'value' => function($data) {
                return Html::a($data->order->id, Url::toRoute(['/shop/order/update?id=' . $data->order->id]));
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '300'],
            'attribute' => 'customer',
            'label' => 'Пользователь',
            'value' => function($data) {
                $fullName = $data->order->customer->fullName($data->order->customer);
                return Html::a($fullName, Url::toRoute(['/customer/customer/update?id=' . $data->order->customer->id]));
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '100'],
            'attribute' => 'answer_first',
            'filter' => OrderPoll::answersAll(),
            'value' => function($data) {
                return $data->answer_first !== null ? $data->answer($data->answer_first) : '-';
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '100'],
            'attribute' => 'answer_second',
            'filter' => OrderPoll::answersAll(),
            'value' => function($data) {
                return $data->answer_second !== null ? $data->answer($data->answer_second) : '-';
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '150'],
            'attribute' => 'status',
            'filter' => OrderPoll::statusesAll(),
            'value' => function($data) {
                return $data->status($data->status);
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '200'],
            'attribute' => 'updated_at',
            'filter' => [
                'quarter' => 'За квартал',
                'month' => 'За месяц',
                'week' => 'За неделю',
                'day' => 'За день',
            ],
            'format' => 'raw',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '100'],
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{delete}</div>',
            'buttons' => [
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
