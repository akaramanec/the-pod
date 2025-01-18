<?php

use backend\modules\media\models\Img;
use backend\modules\media\widgets\ImgSaveWidget;
use backend\modules\system\models\Staff;
use kartik\file\FileInput;
use src\helpers\Buttons;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;


/* @var $this yii\web\View */

/* @var $form yii\widgets\ActiveForm */
$this->params['right_content'] = '';
$this->params['right_content'] .= Buttons::create();
?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            <?= $form->field($model, 'sort')->textInput() ?>
            <?= $form->field($model, 'status')->dropDownList(Staff::statusesAll(), ['prompt' => 'Выбрать статус']) ?>
            <?php if ($model->isNewRecord): ?>
                <?= $form->field($model, 'mainImg')->fileInput() ?>
            <?php else: ?>
                <div class="row mt-4">
                    <div class="col-md-2">
                        <div class="main-img-block">
                            <?= Img::main(STAFF, $model->id, $model->img, '400x400') ?>
                            <?php if ($model->img): ?>
                                <?= Img::deleteMainImg($model->id, STAFF) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <?= ImgSaveWidget::mainImg() ?>
                    </div>
                </div>
            <?php endif; ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>



<?= ImgSaveWidget::widget([
    'modal_id' => 'save_main_img',
    'model' => $model,
    'multiple' => false,
]); ?>




