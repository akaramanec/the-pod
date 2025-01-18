<?php

use backend\modules\bot\models\Bot;
use backend\modules\customer\models\CustomerTag;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\search\CustomerSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="customer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="btn-group d-flex"
         role="group">
        <button type="submit" class="btn btn-outline-info btn-mod" title="Найти"><i class="fas fa-search"></i></button>
    </div>
    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'first_name') ?>
    <?= $form->field($model, 'tag_id')->checkboxList(CustomerTag::listIdName())->label(Yii::t('app', 'Tags')); ?>

    <?php ActiveForm::end(); ?>

</div>
