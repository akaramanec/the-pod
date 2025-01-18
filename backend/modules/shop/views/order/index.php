<?php

use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderPayBlogger;
use frontend\models\cart\Cart;
use src\helpers\Buttons;
use src\helpers\Common;
use src\services\Role;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/**
 * @var $this \yii\web\View
 * @var $searchModel backend\modules\shop\models\search\OrderSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $orderPayBlogger OrderPayBlogger
 * @var $recursiveLevel array
 */
$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= Buttons::create('Создать заказ');
$this->params['pagination'] = \common\widgets\PaginationWidget::widget();
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => Common::pager4(),
    'tableOptions' => ['class' => 'table'],
    'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
//    'rowOptions' => function ($data) {
//        if ($data->status == Order::STATUS_CONFIRMED) {
//            return ['class' => 'text-white', 'style' => 'background-color: #fd7e14;'];
//        }
//    },
    'columns' => [
        [
            'headerOptions' => ['width' => '100'],
            'attribute' => 'id',
        ],
        [
            'attribute' => 'customer_id',
            'value' => function ($data) {
                return $data->customerFullName();
            },
            'format' => 'raw',
        ],
        [
            'label' => 'Блогер',
            'filter' => Customer::listBlogger(),
            'attribute' => 'blogger',
            'value' => function ($data) {
                return $data->customerFullNameParent();
            },
            'format' => 'raw',
        ],
        [
            'value' => function ($data) {
                return $data->customer->showPhone;
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '200'],
            'attribute' => 'created_at',
            'format' => 'datetime',
        ],
        [
            'headerOptions' => ['width' => '120'],
            'attribute' => 'cache_sum_total',
            'value' => function ($data) {
                return Cart::showPriceStatic($data->cache_sum_total);
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '120'],
            'value' => function ($data) {
                if (isset($data->customer->black_list) && $data->customer->black_list) {
                    return $data->customer->blackList($data->customer->black_list);
                } elseif (isset($data->customer->regular_customer) && $data->customer->regular_customer) {
                    return $data->customer->regularCustomer($data->customer->regular_customer);
                }
                return '';
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '110'],
            'filter' => Order::sources(),
            'attribute' => 'source',
            'value' => function ($data) {
                return Order::source('', $data->source);
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '100'],
            'filter' => Order::deliveries(),
            'attribute' => 'delivery',
            'value' => function ($data) {
                return Order::delivery($data->delivery);
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '100'],
            'filter' => Order::statusesPaymentMethod(),
            'attribute' => 'payment_method',
            'value' => function ($data) {
                return Order::statusPaymentMethod($data->payment_method);
            },
            'format' => 'raw'
        ],
        [
            'headerOptions' => ['width' => '100'],
            'filter' => Order::statusesAll(),
            'attribute' => 'status',
            'value' => function ($data) {
                return Order::status($data->status);
            },
            'format' => 'raw'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '100'],
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete} {pdf}</div>',
            'visibleButtons' => [
                'update' => true,
                'delete' => function ($model) {
                    if (Role::check('order-delete')) {
                        return false;
                    }
                    return true;
                },
                'pdf' => true,
            ],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/shop/order/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => 'Изменить',
                        ]);

                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/shop/order/delete', 'id' => $key],
                        [
                            'title' => 'Удалить',
                            'class' => 'btn btn-outline-dark',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                        ]);
                },
                'pdf' => function ($url, $model, $key) {
                    if ($model->np->printDocumentUrl) {
                        return Html::a('<i class="far fa-file-pdf"></i>', $model->np->printDocumentUrl,
                            [
                                'title' => 'ттн pdf',
                                'class' => 'btn btn-outline-dark',
                                'target' => '_blank',
                            ]);
                    };
                }
            ],
        ],
    ],
]); ?>

