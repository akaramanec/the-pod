<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\CustomerTag */

/* @var $form yii\widgets\ActiveForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

