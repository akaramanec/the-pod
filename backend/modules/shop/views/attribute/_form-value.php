<?php

use backend\modules\shop\models\Attribute;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\AttributeValue */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="bg">
                <?= $form->field($model->shopAttribute, 'name')->textInput(['disabled' => true]) ?>
                <?= $form->field($model, 'name')->textInput() ?>
                <?= $form->field($model, 'sort')->textInput() ?>
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>