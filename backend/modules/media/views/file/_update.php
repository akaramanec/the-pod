<?php
/**
 * @var $this \yii\web\View
 * @var $form yii\bootstrap4\ActiveForm
 */

use yii\bootstrap4\ActiveForm;
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($file, 'name')->textInput(['maxlength' => true]) ?>

<div class="btn-group d-flex" role="group">
    <button type="submit" class="btn btn-success btn-mod">Сохранить</button>
</div>

<?php ActiveForm::end(); ?>
