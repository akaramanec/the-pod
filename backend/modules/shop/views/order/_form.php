<?php
/**
 * @var $this \yii\web\View
 * @var $model backend\modules\shop\models\Order
 * @var $form yii\bootstrap4\ActiveForm
 * @var $cart \frontend\models\cart\Cart
 */

use backend\modules\admin\models\AuthAdmin;
use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Delivery;
use backend\modules\shop\models\Order;
use src\behavior\OrderPaymentUpdate;
use src\helpers\Date;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\bootstrap4\Html;
use yii\web\YiiAsset;

Yii::$app->view->registerJsFile('/js/order.js',
    [
        'depends' => [
            YiiAsset::class,
            BootstrapPluginAsset::class,
        ]
    ]);

$this->params['right_content'] = '';
if (!$model->isNewRecord) {
    if ($model->np->printDocumentUrl) {
        $this->params['right_content'] .= Html::a('<i class="far fa-file-pdf"></i>', $model->np->printDocumentUrl,
            [
                'title' => 'ттн pdf',
                'class' => 'btn btn-warning',
                'target' => '_blank',
            ]);
    }
    if ($model->delivery == Delivery::DELIVERY_NP && $cart->items) {
        $this->params['right_content'] .= Html::a('ТТН Новая почта',
            [
                '/shop/order/np',
                'id' => $model->id
            ],
            [
                'title' => 'Перейти на страницу формирования ттн',
                'class' => 'btn btn-info',
            ]);
    }
    if ($model->status != Order::STATUS_CLOSE_SUCCESS) {
        $this->params['left_content'] = $this->render('_search_product', [
            'action' => '/shop/order/add-product',
            'model' => $model,
            'placeholder' => 'Добавить продукт',
        ]);
    }

}
?>

<div class="row">
    <div class="col-md-8">
        <?php if ($cart->items): ?>
            <div class="bg-table">
                <table width="100%" class="table-order">
                    <thead>
                    <tr>
                        <th>Название</th>
                        <th width="5%">К-во</th>
                        <th width="15%">Цена</th>
                        <th width="15%">Цена всего</th>
                        <th width="85"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cart->items as $item): ?>
                        <tr>
                            <td>
                                <?= $item['productName'] ?>
                            </td>
                            <td><?= $item['qtyItem'] ?></td>
                            <td><?= $cart->showPrice($item['productPrice']) ?></td>
                            <td><?= $cart->showPrice($item['priceItemProductTotal']) ?></td>
                            <td>
                                <div class="btn-group base-btn-group float-right" role="group">
                                    <?= Html::button('<i class="far fa-edit"></i>',
                                        [
                                            'class' => 'btn btn-outline-dark edit_mod',
                                            'data-mod_id' => $item['modId'],
                                            'data-order_id' => $model->id,
                                            'title' => 'Изменить',
                                        ]) ?>
                                    <?= Html::a('<i class="fas fa-trash"></i>',
                                        [
                                            '/shop/order/delete-product',
                                            'mod_id' => $item['modId'],
                                            'order_id' => $model->id
                                        ],
                                        [
                                            'title' => 'Удалить',
                                            'class' => 'btn btn-outline-dark',
                                            'data-method' => 'post',
                                            'data-pjax' => '0',
                                            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                                        ]); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="col-md-12">
                <h4>Продуктов нет</h4>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-4">
        <div class="bg">
            <table width="100%">
                <tbody>
                <tr>
                    <td class="font-weight-bold">Заказчик:</td>
                    <td>
                        <?php if ($model->customer_id == Customer::CUSTOMER_SITE): ?>
                            <?= $model->customerFullName() ?> (Сайт)
                        <?php else: ?>
                            <?= Html::a($model->customerFullName(), ['/customer/customer/update', 'id' => $model->customer->id], [
                                'target' => '_blank'
                            ]) ?>
                        <?php endif; ?>
                    </td>
                    <?php if (isset($model->customer->black_list) && $model->customer->black_list) { ?>
                        <td><?= $model->customer->blackList($model->customer->black_list) ?></td>
                    <?php } elseif (isset($model->customer->regular_customer) && $model->customer->regular_customer) { ?>
                        <td><?= $model->customer->regularCustomer($model->customer->regular_customer) ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="font-weight-bold">Телефон:</td>
                    <td><?= $model->customer->showPhone ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Создан:</td>
                    <td><?= Date::format_datetime($model->created_at) ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Источник:</td>
                    <td><?= Order::source('', $model->source) ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Доставка:</td>
                    <td><?= Delivery::listSlugName()[$model->delivery] ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Цена всего:</td>
                    <td><?= $cart->showPrice($cart->sumTotal) ?></td>
                    <td></td>
                </tr>
                <?php if ($cart->sumTotalDiscount): ?>
                    <tr>
                        <td class="font-weight-bold">Цена всего со скидкой:</td>
                        <td><?= $cart->showPrice($cart->sumTotalDiscount - $cart->additionalDiscountSum) ?></td>
                        <td><?= $model->customer->discount ?>%</td>
                    </tr>
                <?php endif; ?>
                <?php if ($cart->sumPayByBonus): ?>
                    <tr>
                        <td class="font-weight-bold">Оплачено бонусами:</td>
                        <td><?= $cart->showPrice($cart->sumPayByBonus) ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ($cart->sumTotalNpUponReceipt): ?>
                    <tr>
                        <td class="font-weight-bold">Цена с учетом наложенного платежа:</td>
                        <td><?= $cart->showPrice($cart->sumTotalNpUponReceipt) ?></td>
                        <td><?= OrderPaymentUpdate::COMMISSION_PERCENTAGE ?>%
                            + <?= $cart->showPrice(OrderPaymentUpdate::COMMISSION_UAH) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="font-weight-bold">Общее количество:</td>
                    <td><?= $cart->qtyTotal ?> шт.</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Оплата:</td>
                    <td>
                        <?= Order::statusPaymentMethod($model->payment_method) ?>
                    </td>
                    <td>
                        <?php if ($model->payment_method == Order::PAYMENT_METHOD_PAY_ONLINE): ?>
                            <span title="Оплачено online"><?= $cart->showPrice($model->paidOnline()) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php $form = ActiveForm::begin(); ?>
            <?php if (!\src\services\Role::check('set-manager')) { ; ?>
            <?= $form->field($model, 'manager_id')->textInput(['value' => $model->manager->surname ?? '', 'readonly'=> true])?>
            <?php } else { ?>
                <?= $form->field($model, 'manager_id')->dropDownList(AuthAdmin::managersAll()) ?>
            <?php } ?>
            <?= $form->field($model, 'status')->dropDownList(Order::statusesAll()) ?>
            <?= $form->field($model, 'payment_method')->dropDownList(Order::statusesPaymentMethod()) ?>
            <?= $form->field($model, 'comment')->textarea() ?>

            <?php if ($model->delivery == Delivery::DELIVERY_NP): ?>
                <?= $form->field($model, 'address')->textarea([
                    'value' => $model->np->city . ' ' . $model->np->branch,
                    'disabled' => true,
                    'rows' => 3
                ]) ?>
            <?php endif; ?>
            <?php if ($model->delivery == Delivery::COURIER_DELIVERY): ?>
                <?= $form->field($model, 'address')->textarea() ?>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary float-right btn-base mb-3">Сохранить</button>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
<?= \common\widgets\ModalWidget::widget(['id' => 'edit_mod']); ?>
<div hidden>
    <div id="order_id" class="order_id"><?= $model->id ?></div>
</div>
