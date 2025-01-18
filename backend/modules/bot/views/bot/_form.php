<?php
/**
 * @var $this yii\web\View
 * @var $model backend\modules\bot\models\Bot
 * @var yii\bootstrap4\ActiveForm
 */

use backend\modules\bot\models\Bot;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'platform')->dropDownList(Bot::allPlatforms(), ['prompt' => 'Выбрать']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'status')->dropDownList(Bot::statusesAll(), ['prompt' => 'Выбрать']) ?>
                </div>
            </div>
            <?= $form->field($model, 'token')->textInput(); ?>
            <?= $form->field($model, 'username')->textInput(); ?>
            <?= $form->field($model, 'first_name')->textInput(); ?>

            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>
            <?php if ($model->platform == Bot::TELEGRAM && Yii::$app->user->can('dev')): ?>
                <a class="btn btn-info btn-base mt-3"
                   href="https://api.telegram.org/bot<?= $model->token ?>/setWebhook?url=<?= Yii::$app->params['dataUrl'] ?>/bot/hook/telegram"
                   title="Установить вебхук"
                   role="button"
                   target="_blank">
                    SetWebHook
                </a>
                <a class="btn btn-info btn-base mt-3"
                   href="https://api.telegram.org/bot<?= $model->token ?>/getMe"
                   title="GetMe"
                   role="button"
                   target="_blank">
                    GetMe
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
