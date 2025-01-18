<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\LinkBot */

/* @var $form yii\widgets\ActiveForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php if (!$model->isNewRecord): ?>
    <div class="row">
        <div class="col-md-6 mt-4">
            <div class="bg">
                <h4 class="mb-3">Ссылка Telegram</h4>
                <h4 class="text-warning">
                    <strong>
                        <?= Yii::$app->params['chatTm']; ?>?start=<?= $model->name ?>
                    </strong>
                </h4>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="bg">
                <h4 class="mb-3">Ссылка Viber</h4>
                <h4 class="text-warning">
                    <strong>
                        <?= Yii::$app->params['homeUrl']; ?>/link-bot/<?= $model->name ?>
                    </strong>
                </h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mt-4">
            <div class="bg">
                <h4>
                    Количество пользователей зарегистрированных по ссылкам: <?= $model->countCustomer() ?>
                </h4>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="bg">
                <h4>
                    Количество пользователей купивших продукт: <?= $model->countOrder() ?>
                </h4>
            </div>
        </div>
    </div>
<?php endif; ?>

