<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \frontend\models\ResetPasswordForm */
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Сброс пароля';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="bg">

            <h1><?= Html::encode($this->title) ?></h1>
            <p style="margin: 15px 0;">Пожалуйста, выберите новый пароль:</p>

            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

            <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>
            <br>
            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
            <br>
        </div>
    </div>
</div>

