<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\BloggerWithdrawalRequest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="blogger-withdrawal-request-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'bot_customer_id')->textInput() ?>

    <?= $form->field($model, 'bot_customer_card_id')->textInput() ?>

    <?= $form->field($model, 'sum')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
