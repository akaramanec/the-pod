<?php
/**
 * @var $this \yii\web\View
 * @var integer $setting_id
 * @var $model \backend\modules\system\models\SettingItem
 * @var $form yii\widgets\ActiveForm
 */


use backend\modules\system\models\SettingItem;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>

<?php $form = ActiveForm::begin([
    'id' => 'form_setting_item',
    'enableAjaxValidation' => true,
    'validationUrl' => ['/validate/common', 'model' => get_class($model)],

]) ?>
<?php if (Yii::$app->user->can('dev')): ?>
    <?= $form->field($model, 'type')->dropDownList(SettingItem::listType(), ['prompt' => 'Выбрать тип']) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?php endif; ?>
<?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
<?php if (Yii::$app->user->can('dev')): ?>
    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
<?php endif; ?>
<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>

<?php ActiveForm::end(); ?>
