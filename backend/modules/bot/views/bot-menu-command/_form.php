<?php


use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotMenuCommand */
/* @var $form yii\widgets\ActiveForm */

 ?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-10">
        <div class="bg">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-block']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
