<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;


$this->title = 'Запросить сброс пароля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="bg">
            <h1><?= Html::encode($this->title) ?></h1>

            <p style="margin: 15px 0;">Пожалуйста, заполните вашу электронную почту. Ссылка для сброса пароля будет
                отправлена туда.</p>

            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <br>
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>


            <?php ActiveForm::end(); ?>
            <br>
        </div>
    </div>
</div>

