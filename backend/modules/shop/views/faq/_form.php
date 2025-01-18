<?php

use backend\modules\media\models\Img;
use backend\modules\media\widgets\ImgSaveWidget;
use backend\modules\shop\models\Faq;
use yii\bootstrap4\ActiveForm;


/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([
        'id' => 'form-notice',
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => true,
        'validationUrl' => ['/validate/common', 'model' => get_class($model)],
    ]
) ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <?= $form->field($model, 'name')->textInput() ?>
            <?= $form->field($model, 'text')->textarea(['rows' => 8]) ?>
            <?= $form->field($model, 'sort')->textInput() ?>
            <?= $form->field($model, 'status')->dropDownList(Faq::statusesAll(), ['prompt' => 'Выбрать статус']) ?>

            <?php if ($model->isNewRecord): ?>
                <?= $form->field($model, 'mainImg')->fileInput() ?>
            <?php else: ?>
                <div class="row mt-4">
                    <div class="col-md-2">
                        <div class="main-img-block">
                            <?= Img::main(POD_FAQ, $model->id, $model->img, '400x400') ?>
                            <?php if ($model->img): ?>
                                <?= Img::deleteMainImg($model->id, POD_FAQ) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <?= ImgSaveWidget::mainImg() ?>
                    </div>
                </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary float-right btn-base mb-2">Сохранить</button>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?= ImgSaveWidget::widget([
    'modal_id' => 'save_main_img',
    'model' => $model,
    'multiple' => false,
]); ?>
