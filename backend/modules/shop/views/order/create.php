<?php

use backend\modules\shop\models\Delivery;
use backend\modules\shop\models\Order;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @var $this \yii\web\View
 * @var $customer \frontend\models\OrderCustomer
 * @var $order \backend\modules\shop\models\Order
 * @var $orderNp \backend\modules\shop\models\OrderNp
 * @var $delivery Delivery
 */

$this->title = 'Создать заказы';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->view->registerJsFile(Yii::$app->params['homeUrl'] . '/js/order.js',
    [
        'depends' => [
            \yii\bootstrap4\BootstrapPluginAsset::class,
        ]
    ]);
\backend\assets\Select2Asset::register($this);
?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <h4 class="mb-3">Ввод личных данных</h4>
            <?= $form->field($customer, 'phone')->widget(\yii\widgets\MaskedInput::class, [
                'mask' => '+38-099-999-99-99',
            ]) ?>
            <?= $form->field($customer, 'email')->textInput() ?>
            <?= $form->field($customer, 'first_name')->textInput() ?>
            <?= $form->field($customer, 'last_name')->textInput() ?>
            <?= $form->field($customer, 'discount')->textInput() ?>
            <h4 class="mb-3">Выбор доставки</h4>
            <?= $form->field($order, 'delivery')->radioList(ArrayHelper::map($delivery, 'slug', 'name'),
                [
                    'itemOptions' => [
                        'class' => 'custom-control-input delivery-item'
                    ],
                ])->label(false) ?>

            <div class="search-block-block">
                <?= $form->field($orderNp, 'city')->textInput(
                    [
                        'onkeyup' => 'searchCity(this.value)',
                        'autofocus' => 'off',
                        'id' => 'city',
                        'autocomplete' => 'off'
                    ]) ?>
                <div id="search_result_additionally_list"></div>
                <div id="search_item_id" hidden></div>
            </div>

            <div class="form-group">
                <label for="branch_ref" class="col-form-label">Отделение</label>
                <select id="branch_ref"
                        class="form-control"
                        name="branch_ref">
                    <option value=""></option>
                </select>
            </div>

            <div class="delivery-item-form <?= Delivery::COURIER_DELIVERY ?>" style="display: none;">
                <?= $form->field($order, 'address')->textarea(['rows' => 3]) ?>
            </div>
            <div class="delivery-item-form <?= Delivery::PICKUP ?>" style="display: none;">
                <?= $form->field($order, 'address')->textarea([
                    'value' => $delivery[Delivery::PICKUP]['description'],
                    'disabled' => true,
                    'rows' => 3
                ]) ?>
            </div>

            <h4 class="mb-3 mt-3">Выбор способа оплаты</h4>
            <?= $form->field($order, 'payment_method')->radioList(
                [
                    Order::PAYMENT_METHOD_UPON_RECEIPT => 'При получении',
                    Order::PAYMENT_METHOD_PAY_ONLINE_NEW => 'Оплата online',
                ])->label(false) ?>

            <?= $form->field($order, 'comment')->textarea() ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right']) ?>

        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<div id="deliveryAllSlug" hidden><?= Json::encode(ArrayHelper::getColumn($delivery, 'slug')) ?></div>



<?php
$script = <<< JS
      $('#branch_ref').select2({
            theme: 'bootstrap4'
        });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>






