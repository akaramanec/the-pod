<?php

use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderNp;
use kartik\date\DatePicker;
use src\helpers\Date;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/**
 * @var $this \yii\web\View
 * @var $orderNp \backend\modules\shop\models\OrderNp
 * @var $order \backend\modules\shop\models\Order
 * @var $senderNp \backend\modules\bot\src\SenderNp
 */
$this->title = 'ТТН';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Заказ: №: ' . $order->id, 'url' => ['/shop/order/update', 'id' => $order->id]];
$this->params['breadcrumbs'][] = ['label' => $this->title];
$this->params['right_content'] = '';
if ($orderNp->printDocumentUrl) {
    $this->params['right_content'] .= Html::a('<i class="far fa-file-pdf"></i>', $orderNp->printDocumentUrl,
        [
            'title' => 'ттн pdf',
            'class' => 'btn btn-warning',
            'target' => '_blank',
        ]);
}

$this->params['right_content'] .= Html::a('Заказ',
    [
        '/shop/order/update',
        'id' => $order->id
    ],
    [
        'title' => 'Перейти на страницу заказа',
        'class' => 'btn btn-info',
    ]);
?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-6">
        <div class="bg mb-4">
            <h4>Отправитель</h4>
            <div class="form-group">
                <label for="ContactPersonsDescription">ФИО</label>
                <input type="text" id="ContactPersonsDescription" class="form-control"
                       value="<?= $senderNp->ContactPersonsDescription ?>" disabled>
            </div>
            <div class="form-group">
                <label for="ContactPersonsPhones">Тел</label>
                <input type="text" id="ContactPersonsPhones" class="form-control"
                       value="<?= $senderNp->ContactPersonsPhones ?>" disabled>
            </div>
            <div class="form-group">
                <label for="ContactPersonsEmail">Email</label>
                <input type="text" id="ContactPersonsEmail" class="form-control"
                       value="<?= $senderNp->ContactPersonsEmail ?>" disabled>
            </div>
        </div>
        <div class="bg mb-4">
            <h4>Параметры отправления</h4>
            <?= $form->field($orderNp, 'weight')->textInput() ?>
            <?= $form->field($orderNp, 'service_type')->dropDownList(OrderNp::serviceType(), ['disabled' => true]) ?>
            <?= $form->field($orderNp, 'seats_amount')->textInput() ?>
            <?= $form->field($orderNp, 'description')->textarea() ?>
            <?= $form->field($orderNp, 'departure_date')->widget(DatePicker::classname(), [
                'options' => [
                    'value' => Date::format_date($orderNp->departure_date)
                ],
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ]); ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="bg mb-4">
            <?php if ($orderNp->documentData): ?>
                <p class="mb-1">Номер: <?= $orderNp->documentData['IntDocNumber'] ?></p>
            <?php endif; ?>
            <?= Html::submitButton('Сохранить и сформировать ттн', [
                'class' => 'btn btn-primary btn-base float-left mr-3 mb-2',
                'title' => 'Сохранить и сформировать ттн',
                'name' => 'internetDocument',
                'value' => 'save'
            ]) ?>
            <button type="submit" class="btn btn-primary float-left btn-base">Сохранить</button>
        </div>
        <div class="bg mb-4">

            <h4>Получатель</h4>
            <?= $form->field($orderNp, 'city')->textInput(['disabled' => true]) ?>
            <?= $form->field($orderNp, 'branch')->textInput(['disabled' => true]) ?>
            <?= $form->field($order->customer, 'first_name')->textInput() ?>
            <?= $form->field($order->customer, 'last_name')->textInput() ?>
            <?= $form->field($order->customer, 'phone')->textInput() ?>
            <?= $form->field($order->customer, 'email')->textInput() ?>


        </div>
        <div class="bg mb-4">
            <?= $form->field($order, 'payment_method')->dropDownList(Order::statusesPaymentMethod()) ?>
        </div>
    </div>
</div>


<?php ActiveForm::end(); ?>
