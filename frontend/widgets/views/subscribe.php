<?php
/**
 * @var object $model
 */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;


?>
<?php $form = ActiveForm::begin([
    'options' => ['class' => 'footer-form__body col-xl-11 col-12'],
    'enableAjaxValidation' => true,
    'validationUrl' => ['/validate/subscribe'],
]); ?>

<?= $form->field($model, 'email')->textInput(['placeholder' => 'Введите e-mail'])->label(false) ?>

<div class="form-group">
    <?= Html::submitButton('Подписатся', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
