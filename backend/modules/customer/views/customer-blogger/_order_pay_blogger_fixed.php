<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\search\CustomerSearch */
/* @var $form yii\widgets\ActiveForm */

?>


<?php $form = ActiveForm::begin([
    'action' => [
        '/customer/customer-blogger/pay-blogger-fixed',
        'customer_id' => $model->customer_id
    ],
    'id' => 'form-pay-blogger-fixed',
    'enableAjaxValidation' => true,
    'validationUrl' => ['/validate/common', 'model' => get_class($model)],
]); ?>
<?= $form->field($model, 'sum') ?>
<?= Html::submitButton('Сохранить', [
    'class' => 'btn btn-primary btn-base float-right'
]) ?>
<?php ActiveForm::end(); ?>

