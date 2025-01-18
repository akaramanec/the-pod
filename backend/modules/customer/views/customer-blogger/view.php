<?php

use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use frontend\models\cart\Cart;
use src\helpers\Buttons;
use src\helpers\Common;
use src\helpers\Date;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/**
 * @var $this \yii\web\View
 * @var $searchModel backend\modules\shop\models\search\OrderSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $customer Customer
 */
$this->title = 'Блогер';
$this->params['breadcrumbs'][] = ['label' => 'Блогеры', 'url' => ['index']];
$this->params['breadcrumbs'][] = Customer::fullName($customer);
$this->params['pagination'] = \common\widgets\PaginationWidget::widget();
?>
<div class="row">
    <div class="col-md-4">
        <div class="bg">
            <table class="table table-hover">
                <tbody>
                <tr>
                    <th>Блогер</th>
                    <td><?= Customer::fullName($customer) ?></td>
                </tr>
                <tr>
                    <th><?= $customer->getAttributeLabel('phone') ?></th>
                    <td><?= $customer->showPhone ?></td>
                </tr>
                <tr>
                    <th><?= $customer->blog->getAttributeLabel('percent_level_1') ?></th>
                    <td><?= $customer->blog->percent_level_1 ?>%</td>
                </tr>
                <tr>
                    <th><?= $customer->blog->getAttributeLabel('percent_level_2') ?></th>
                    <td><?= $customer->blog->percent_level_2 ?>%</td>
                </tr>
                <?php if ($customer->blog->lvl3Need) { ?>
                    <tr>
                        <th><?= $customer->blog->getAttributeLabel('percent_level_3') ?></th>
                        <td><?= $customer->blog->percent_level_3 ?>%</td>
                    </tr>
                <?php } ?>
                <tr>
                    <th><p><b>Реферальная ссылка:</b></p></th>
                    <td><p><?= $customer->linkRefTm() ?></p></td>
                </tr>
                </tbody>
            </table>
        </div>


    </div>

    <div class="col-md-4">
        <div class="bg table-height">
            <?php if ($customer->orderPayBloggerFixed): ?>
                <p>Выплаты</p>
                <div class="transactions-blogger-view">
                    <table width="100%">
                        <tbody>
                        <?php foreach ($customer->orderPayBloggerFixed as $pay): ?>
                            <tr>
                                <td width="50%"><?= Date::format_datetime($pay->created_at) ?></td>
                                <td><?= Cart::showPriceStatic($pay->sum) ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Выплат нет</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="bg table-height">
            <table class="table table-hover">
                <tbody>
                <tr>
                    <th>Задолженность:</th>
                    <td><?= Cart::showPriceStatic($customer->blog->sumDebt) ?></td>
                </tr>
                <tr>
                    <th>Оплачено:</th>
                    <td><?= Cart::showPriceStatic($customer->blog->sumTotalPayed) ?></td>
                </tr>
                <tr>
                    <th>Доход всего:</th>
                    <td><?= Cart::showPriceStatic($customer->blog->sumTotalOrders) ?></td>
                </tr>
                </tbody>
            </table>
            <form action="#" method="get">
                <input type="hidden" name="order_id" class="input-pay-blogger" value="">
                <input type="hidden" name="blogger_id" id="blogger_id" value="<?= $customer->id ?>">
                <?= Html::button('Оплатить',
                    [
                        'title' => 'Оплатить блогеру',
                        'class' => 'btn btn-outline-success pay_blogger btn-pay-blogger',
                        'data-toggle' => 'modal',
                        'data-target' => '#pay_blogger',
                    ]); ?>
            </form>
        </div>

    </div>


    <div class="col-md-12 table-margin">
        <div class="bg mt-3">
            <table class="table table-hover">
                <thead>
                <tr>
                    <td>Тип</td>
                    <td>Активен</td>
                    <td>Подписан</td>
                    <td>Отписался</td>
                    <td>Все</td>
                    <td>К-во заказов</td>
                    <td>Сумма заказов</td>
                    <td>Начисления</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>Все</th>
                    <td><?= $customer->countCustomerAllActive ?></td>
                    <td><?= $customer->countCustomerAllSubscribed ?></td>
                    <td><?= $customer->countCustomerAllUnsubscribed ?></td>
                    <td><?= $customer->countCustomerAll ?></td>
                    <td><?= $customer->countOrdersAll['count'] ?? 0 ?></td>
                    <td><?= $customer->countOrdersAll['sum'] ?? 0 ?></td>
                    <td><?= $customer->countOrdersAll['percent'] ?? 0 ?></td>
                </tr>
                <tr>
                    <th>Уровня 1</th>
                    <td><?= $customer->countCustomerL1Active ?></td>
                    <td><?= $customer->countCustomerL1Subscribed ?></td>
                    <td><?= $customer->countCustomerL1Unsubscribed ?></td>
                    <td><?= $customer->countCustomerL1All ?></td>
                    <td><?= $customer->countOrdersL1['count'] ?? 0 ?></td>
                    <td><?= $customer->countOrdersL1['sum'] ?? 0 ?></td>
                    <td><?= $customer->countOrdersL1['percent'] ?? 0 ?></td>
                <tr>
                    <th>Уровня 2</th>
                    <td><?= $customer->countCustomerL2Active ?></td>
                    <td><?= $customer->countCustomerL2Subscribed ?></td>
                    <td><?= $customer->countCustomerL2Unsubscribed ?></td>
                    <td><?= $customer->countCustomerL2All ?></td>
                    <td><?= $customer->countOrdersL2['count'] ?? 0 ?></td>
                    <td><?= $customer->countOrdersL2['sum'] ?? 0 ?></td>
                    <td><?= $customer->countOrdersL2['percent'] ?? 0 ?></td>
                </tr>
                <?php if ($customer->blog->lvl3Need) { ?>
                    <tr>
                        <th>Уровня 3</th>
                        <td><?= $customer->countCustomerL3Active ?></td>
                        <td><?= $customer->countCustomerL3Subscribed ?></td>
                        <td><?= $customer->countCustomerL3Unsubscribed ?></td>
                        <td><?= $customer->countCustomerL3All ?></td>
                        <td><?= $customer->countOrdersL3['count'] ?? 0 ?></td>
                        <td><?= $customer->countOrdersL3['sum'] ?? 0 ?></td>
                        <td><?= $customer->countOrdersL3['percent'] ?? 0 ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

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
            'value' => function ($data) {
                return $data->customer->showPhone;
            },
            'format' => 'raw',
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
            'headerOptions' => ['width' => '140'],
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
            'filter' => false,
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
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-eye fa-fw"></i>', ['/shop/order/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => 'Просмотр',
                            'target' => '_blank',
                        ]);

                }
            ],
        ],
    ],
]); ?>
<?= \common\widgets\ModalWidget::widget(['id' => 'pay_blogger']); ?>
<?php
$script = <<< JS
$('.pay_blogger').on('click', function (e) {
    $.ajax({
        url: '/customer/customer-blogger/pay-blogger-fixed',
        data: {customer_id: $('#blogger_id').val()},
        type: 'GET',
        success: function (res) {
            $('#pay_blogger .modal-body').html(res);
            $('#pay_blogger').modal();
        }
    });
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
