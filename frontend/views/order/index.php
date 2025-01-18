<?php

use backend\modules\media\models\Img;
use backend\modules\shop\models\Delivery;
use backend\modules\shop\models\Order;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use backend\modules\customer\models\Customer;

/**
 * @var $this \yii\web\View
 * @var $cart \frontend\models\cart\Cart
 * @var $order \backend\modules\shop\models\Order
 * @var $orderNp \backend\modules\shop\models\OrderNp
 * @var $customer \frontend\models\OrderCustomer
 * @var $delivery array
 * @var object $page
 */
echo \frontend\widgets\Meta::widget([
    'title' => $page->lang->meta['title'],
    'description' => $page->lang->meta['description'],
    'img' => Img::mainPath(SITE_PAGE, $page->id, $page->img, '450x253')
]);
Yii::$app->view->registerJsFile('/js/order.js?v=1.1',
    [
        'depends' => [
            \yii\web\YiiAsset::class,
            \yii\bootstrap4\BootstrapPluginAsset::class
        ]
    ]);
\frontend\assets\Select2Asset::register($this);
?>
<main class="main order">
    <?= $this->render('@frontend/views/layouts/_head') ?>
    <section class="order__content container">
        <div class="pb-3">
            <?php if ($cart->items): ?>
                <?php foreach ($cart->items as $item): ?>
                    <div class="order__card">
                        <div class="order__card-img">
                            <img src="<?= $item['img'] ?>"
                                 alt="<?= $item['productName'] ?>"
                                 title="<?= $item['productName'] ?>">
                        </div>
                        <p class="order__card-name">
                            <a href="<?= $item['url'] ?>">
                                <?= $item['productName'] ?>
                            </a>
                        </p>
                        <div class="order__card-qty info-wrap">
                            <?= $this->render('@frontend/views/common/_qty', [
                                'modId' => $item['modId'],
                                'minusCss' => 'minus-order',
                                'plusCss' => 'plus-order'
                            ]) ?>
                        </div>
                        <div class="order__card-delete">
                            <a href="<?= Url::to(['/cart/del-item', 'id' => $item['modId']]) ?>"><span></span></a>
                        </div>
                        <div class="order__card-price">
                            <p>Цена: <?= $cart->showPrice($item['productPrice']) ?></p>
                            <p>Всего: <span
                                        class="price_item_total_order_<?= $item['modId'] ?>"><?= $cart->showPrice($item['priceItemProductTotal']) ?></span>
                            </p>
                        </div>
                    </div>
                    <hr>
                <?php endforeach ?>
                <div class="text-right">
                    <h3>Цена всего: <span class="sum_total"><?= $cart->showPrice($cart->sumTotal) ?></span></h3>
                    <h3>Общее количество: <span class="total_qty"><?= $cart->qtyTotal ?></span> шт.</h3>
                </div>
                <?php $form = ActiveForm::begin([
                    'id' => 'order_form',
                    'enableAjaxValidation' => true,
                    'validationUrl' => ['/validate/common', 'class' => get_class($order)]
                ]); ?>
                <?= $form->field($order, 'customer_id')->hiddenInput(['value' => Customer::CUSTOMER_SITE])->label(false) ?>
                <div class="order__data">
                    <h3>Ввод личных данных</h3>
                    <br>
                    <?= $form->field($customer, 'phone')->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '38 099 999 99 99',
                    ]) ?>
                    <?= $form->field($customer, 'email')->textInput() ?>
                    <?= $form->field($customer, 'first_name')->textInput() ?>
                    <?= $form->field($customer, 'last_name')->textInput() ?>
                </div>

                <h3>Выбор доставки</h3>
                <br>
                <?= $form->field($order, 'delivery')->radioList(ArrayHelper::map($delivery, 'slug', 'name'),
                [
                    'itemOptions' => [
                        'class' => 'custom-control-input delivery-item'
                    ],
                ])->label(false) ?>

                <div class="delivery-item-form <?= Delivery::DELIVERY_NP ?>">
                    <div class="search-block-block">
                        <?= $form->field($orderNp, 'city')->textInput(
                            [
                                'onkeyup' => 'searchCity(this.value)',
                                'placeholder' => 'Выбрать город (введите 3 и более символов)',
                                'autofocus' => 'off',
                                'id' => 'city',
                                'autocomplete' => 'off'
                            ]) ?>
                        <div id="search_result_additionally_list"></div>
                        <div id="search_item_id"
                             hidden></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label for="branch_ref" class="col-form-label">Отделение</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select id="branch_ref" class="form-control" name="OrderNp[branch]"></select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="delivery-item-form <?= Delivery::COURIER_DELIVERY ?>" style="display: none;">
                    <p><?= $delivery[Delivery::COURIER_DELIVERY]['description'] ?></p>
                    <?= $form->field($order, 'address')->textarea(['rows' => 3]) ?>
                </div>
                <div class="delivery-item-form <?= Delivery::PICKUP ?>" style="display: none;">
                    <p>Адрес: <?= $delivery[Delivery::PICKUP]['description'] ?></p>
                </div>


                <h3>Выбор способа оплаты</h3>
                <?php $order->payment_method = Order::PAYMENT_METHOD_UPON_RECEIPT ?>
                <?= $form->field($order, 'payment_method')->radioList(
                [
                    Order::PAYMENT_METHOD_UPON_RECEIPT => 'При получении',
//                    Order::PAYMENT_METHOD_PAY_ONLINE_NEW => 'Оплата online',
                ])->label(false) ?>

                <?= $form->field($order, 'comment')->textarea() ?>
                <button class="base-button btn--dark main__button"
                        type="submit">Заказать
                </button>

                <?php ActiveForm::end(); ?>
            <?php else: ?>
                <h1 style="color: black">Корзина пуста</h1>
            <?php endif; ?>
        </div>
    </section>
</main>
<div id="deliveryAllSlug" hidden><?= Json::encode(ArrayHelper::getColumn($delivery, 'slug')) ?></div>
<?php
$script = <<< JS

    $('#branch_ref').select2({
        theme: 'bootstrap4'
    });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
