<?php

use backend\modules\bot\models\BotMenuCommand;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotMenu */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-md-10">
        <div class="bg">
            <?= $form->field($model, 'command_id')->dropDownList(BotMenuCommand::listIdName(), ['prompt' => 'Выбрать..']) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-block']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

