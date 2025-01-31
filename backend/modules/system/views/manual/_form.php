<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="manual-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->widget(\yii2jodit\JoditWidget::className(), [
        'settings' => [
            'buttons' => [
                'source', 'bold', 'italic', 'underline', '|', 'image', '|', 'hr', 'fontsize', 'paragraph'
            ],
        ],
    ]); ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
