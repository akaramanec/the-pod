<?php

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotCommand;
use backend\modules\media\models\Img;
use backend\modules\media\widgets\ImgSaveWidget;
use backend\modules\shop\models\Category;
use src\helpers\Common;

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotCommand */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">

            <?= $form->field($model, 'name')->textInput() ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'status')->dropDownList(Category::statuses()) ?>
            <?= $form->field($model, 'sort')->textInput() ?>

            <?php if ($model->isNewRecord): ?>
                <?= $form->field($model, 'mainImg')->fileInput() ?>
            <?php else: ?>
                <div class="row mt-4">
                    <div class="col-md-2">
                        <div class="main-img-block">
                            <?= Img::main(SHOP_CATEGORY, $model->id, $model->img, '400x400') ?>
                            <?php if ($model->img): ?>
                                <?= Img::deleteMainImg($model->id, SHOP_CATEGORY) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <?= ImgSaveWidget::mainImg() ?>
                    </div>
                </div>
            <?php endif; ?>


            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?= ImgSaveWidget::widget([
    'modal_id' => 'save_main_img',
    'model' => $model,
    'multiple' => false,
]); ?>