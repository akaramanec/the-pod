<?php

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>

<div class="row align-items-center offset-md-1">
    <div class="col-md-10">
        <div class="bg-login">
            <div class="row">
                <div class="col-md-6 login-img d-none d-md-block d-lg-block d-xl-block">
                    <img src="/img/login.png" alt="login" width="100%">
                </div>
                <div class="col-md-6">
                    <div class="login-form">
                        <h1><?= Html::encode($this->title) ?></h1>

                        <p style="margin-bottom: 15px;">Пожалуйста, заполните следующие поля для входа:</p>

                        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                        <?= $form->field($model, 'password')->passwordInput() ?>

                        <?= $form->field($model, 'rememberMe')->checkbox() ?>

                        <?= Html::submitButton('Войти', ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

