<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\CustomerMessage */

/* @var $form yii\widgets\ActiveForm */

use backend\modules\customer\models\Customer;
use backend\modules\customer\models\CustomerMessage;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <?= $form->field($model, 'customer_id')->textInput([
                    'disabled' => true,
                    'value' => Customer::fullName($model->customer),
            ]) ?>

            <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'status')->dropDownList(CustomerMessage::statusesAll(), ['prompt' => 'Выбрать статус']) ?>
            <?= $form->field($model, 'created_at')->textInput(['disabled' => true]) ?>

            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
