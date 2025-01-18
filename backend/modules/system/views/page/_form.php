<?php
/**
 * @var object $page
 * @var object $pageL
 */

use backend\modules\media\models\Img;
use backend\modules\media\widgets\ImgSaveWidget;
use backend\modules\system\models\SitePage;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($pageL, 'name')->textInput() ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($page, 'slug')->textInput([
                        'disabled' => Yii::$app->user->can('dev') ? false : true
                    ]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($pageL, 'meta_title')->textarea(['rows' => 5]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($pageL, 'meta_description')->textarea(['rows' => 5]) ?>
                </div>
            </div>
            <?= $this->render('@backend/views/common/_addition', [
                'addition' => $pageL->addition,
            ]) ?>
            <button type="submit" class="btn btn-primary float-right btn-base mb-2">Сохранить</button>
            <?= $form->field($page, 'id')->hiddenInput()->label(false) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?= ImgSaveWidget::widget(['model' => $page,
    'multiple' => false,]); ?>

