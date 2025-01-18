<?php

use backend\modules\system\models\Addition;
use src\helpers\Buttons;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\SitePageL */
/* @var $addition Addition */
/* @var $form yii\widgets\ActiveForm */
$this->params['right_content'] = '';
$this->params['right_content'] .= Buttons::create();
?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'page_id')->textInput(['readonly' => true]) ?>
            <?= $form->field($model, 'lang')->dropDownList(['en' => 'En', 'uk' => 'Uk', 'ru' => 'Ru',], ['prompt' => '']) ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            <?php if ($addition !== null) { ?>
                <?= $form->field($addition, 'email')->textInput() ?>
                <?= $form->field($addition, 'map')->textarea(['rows' => 6]) ?>
                <?= $form->field($addition, 'address')->textInput() ?>
                <?= $form->field($addition, 'workTime')->textInput() ?>
                <?= $form->field($addition, 'life')->textInput() ?>
                <?= $form->field($addition, 'kyivstar')->textInput() ?>
                <?= $form->field($addition, 'landline')->textInput() ?>
            <?php } ?>
            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>