<?php

use backend\modules\admin\models\AuthAdmin;
use backend\modules\admin\models\AuthItem;
use backend\modules\media\widgets\FileInputWidget;
use yii\base\BaseObject;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/** @var $model AuthAdmin */

?>
<?php $form = ActiveForm::begin([
    'id' => 'form_admin',
    'layout' => 'horizontal',
    'options' => ['enctype' => 'multipart/form-data'],
]) ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <?= $form->field($model, 'status')->dropDownList(AuthAdmin::statusesAll()) ?>

            <?= Html::activeCheckboxList($model, 'rolesArr', AuthItem::rolesAll()); ?>

            <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::class, [
                'mask' => '+38-099-999-99-99',
            ]) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?php if ($model->isNewRecord): ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
            <?php else: ?>
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label">Сменить пароль</div>
                    <div class="col-sm-10">
                        <button type="button" class="btn btn-info btn-block" title="Сменить пароль" data-toggle="modal"
                                data-target="#change_password">Сменить пароль
                        </button>

                    </div>
                </div>
            <?php endif; ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?= \backend\widgets\ChangePassword::widget(['id' => $model->id]); ?>

