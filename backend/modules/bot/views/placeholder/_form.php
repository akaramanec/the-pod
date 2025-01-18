<?php


use backend\modules\bot\models\BotPlaceholder;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotPlaceholder */
/* @var $form yii\widgets\ActiveForm */

?>
<?php $form = ActiveForm::begin(); ?>
<div class="bg">
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>
            <?php if (Yii::$app->user->can('dev')): ?>
                <?= $form->field($model, 'text_example')->textarea(['rows' => 6]) ?>
            <?php else: ?>
                <?= $model->text_example ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?php if (Yii::$app->user->can('dev')): ?>
                <?= $form->field($model, 'sort')->textInput() ?>
                <?= $form->field($model, 'status')->dropDownList(BotPlaceholder::statusesAll(), ['prompt' => 'Выбрать статус']) ?>
                <?= $form->field($model, 'slug')->textInput() ?>
            <?php endif; ?>
        </div>
        <div class="col-md-12">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
