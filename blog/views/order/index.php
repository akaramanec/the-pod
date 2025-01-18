<?php

use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use frontend\models\cart\Cart;
use src\helpers\Common;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\shop\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $customer Customer */

$this->title = 'Заказы';
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
        [
            'attribute' => 'customer_id',
            'value' => function ($data) {
                return Customer::fullName($data->customer);
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '180'],
            'attribute' => 'cache_sum_total',
            'value' => function ($data) {
                return Cart::showPriceStatic($data->cache_sum_total);
            },
            'format' => 'raw',
        ],
        [
            'label' => 'Доход',
            'headerOptions' => ['width' => '100'],
            'value' => function ($data) use ($customer) {
                if (isset($customer->blog->orders[$data->id])) {
                    return Cart::showPriceStatic($customer->blog->orders[$data->id]['sumItem']);
                }
            },
            'format' => 'raw'
        ],
        [
            'headerOptions' => ['width' => '100'],
            'value' => function ($data) use ($customer) {
                if (isset($customer->blog->customerLevel[$data->customer_id])) {
                    return 'Level ' . $customer->blog->customerLevel[$data->customer_id]['level'];
                }
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Дата',
            'headerOptions' => ['width' => '140'],
            'attribute' => 'updated_at',
            'format' => 'datetime',
        ],
    ],
]); ?>

