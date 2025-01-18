<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Poll */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="bg">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'question')->textarea(['rows' => 6]) ?>

                <?= $form->field($model, 'first_send_after')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'second_send_after')->textInput(['maxlength' => true]) ?>

                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>