<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\OrderPoll */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="bg">
                <?= $form->field($model, 'order_id')->textInput() ?>
                <?= $form->field($model, 'poll_id')->textInput() ?>
                <?= $form->field($model, 'status')->textInput() ?>
                <?= $form->field($model, 'answer_first')->textInput() ?>
                <?= $form->field($model, 'answer_second')->textInput() ?>
                <?= $form->field($model, 'created_at')->textInput() ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>