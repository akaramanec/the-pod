<?php

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotCommand;
use src\helpers\Common;

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotCommand */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">

            <?= $form->field($model, 'name')->textInput(
                [
                    'disabled' => Yii::$app->user->can('dev') ? false : true
                ]) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'status')->dropDownList(BotCommand::statusesAll()) ?>


            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
