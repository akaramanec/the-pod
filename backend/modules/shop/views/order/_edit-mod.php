<?php
/**
 * @var $this \yii\web\View
 * @var $orderItem \backend\modules\shop\models\OrderItem
 * @var $form yii\bootstrap4\ActiveForm;
 */


use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>
<?php $form = ActiveForm::begin([
    'id' => 'form_product_edit_move',
    'enableAjaxValidation' => true,
    'validationUrl' => ['/validate/common', 'model' => get_class($orderItem)],
]) ?>

<h2 class="mb-3"><?= $orderItem->mod->product->name ?></h2>

<?= $form->field($orderItem, 'qty')->textInput()->label('Количество') ?>

<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>

<?php ActiveForm::end(); ?>
