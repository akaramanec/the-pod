<?php
/** @var integer $id */
/** @var object $password */
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>
<div class="modal fade" id="change_password" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['/admin/admin/change-password', 'id' => $id],
                    'enableAjaxValidation' => true,
                    'validationUrl' => ['/validate/password'],
                ]);
                ?>
                <?= $form->field($password, 'password')->passwordInput() ?>

                <?= $form->field($password, 're_password')->passwordInput() ?>

                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-block btn-base']) ?>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
