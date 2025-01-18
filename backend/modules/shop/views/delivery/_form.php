<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Delivery */
/* @var $form yii\widgets\ActiveForm */

?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
