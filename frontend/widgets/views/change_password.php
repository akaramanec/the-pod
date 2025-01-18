<?php

/** @var integer $id */

/** @var object $password */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>
<div class="modal fade" id="change_password" tabindex="-1" role="dialog" aria-labelledby="change_passwordLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Сменить пароль</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['/auth/change-password'],
                    'enableAjaxValidation' => true,
                    'validationUrl' => ['/validate/change-password'],
                ]);
                ?>

                <?= $form->field($password, 'password')->passwordInput() ?>
                <?= $form->field($password, 're_password')->passwordInput() ?>
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
