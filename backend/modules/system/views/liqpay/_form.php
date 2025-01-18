<?php

use backend\modules\system\models\Liqpay;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Liqpay */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-8">
        <div class="bg">
            <?= $form->field($model, 'test_public_key')->textInput() ?>

            <?= $form->field($model, 'test_private_key')->textInput() ?>

            <?= $form->field($model, 'public_key')->textInput() ?>

            <?= $form->field($model, 'private_key')->textInput() ?>

            <?= $form->field($model, 'status')->dropDownList(Liqpay::statusesAll()) ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="btn-group d-flex" role="group">
            <button type="submit" class="btn btn-success btn-mod">Сохранить</button>
        </div>
    </div>
</div>


<?php ActiveForm::end(); ?>

