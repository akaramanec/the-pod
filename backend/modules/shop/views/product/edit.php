<?php
/**
 * @var $this     \yii\web\View
 * @var $form     ActiveForm
 * @var $base_id  integer
 */

use dosamigos\tinymce\TinyMce;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = $model->id ? Yii::t('app', 'Edit product') : Yii::t('app', 'Add product');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Products list'), 'url' => ['product/list', 'base_id' => $base_id],
];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('
$( ".image-up input" ).change(function() {
    $("img.img-up").attr("src",$(this).val());
});
$( ".clear-image" ).click(function() {
    $( ".image-up input" ).val("");
    $("img.img-up").attr("src", "");
});
');

$tinyMCEInitFunction = "function (editor) {
      $(editor.getContainer()).find('button.tox-statusbar__wordcount').click();
   }";
?>

<style>
    .nav.nav-tabs.nav-underline .nav-item.active a.nav-link:before {
        -webkit-transform: translate3d(0, 0, 0);
        -moz-transform: translate3d(0, 0, 0);
        transform: translate3d(0, 0, 0);
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><?= $this->title; ?></h4>
                <a class="heading-elements-toggle"><i class="la la-ellipsis font-medium-3"></i></a>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <?php $form = ActiveForm::begin(
                        [
                            'layout' => 'horizontal',
                            'options' => ['class' => 'form form-horizontal'],
                        ]
                    ); ?>
                    <div class="form-body">
                        <?= $form->field($model,
                            'base_id',
                            ['options' => ['class' => 'hidden']])->hiddenInput(['value' => $base_id])->label(false); ?>
                        <?= $form->field($model, 'name', ['options' => ['class' => 'form-group row']])->textInput(); ?>
<!--                        --><?php //= $form->field($model, 'description', ['options' => ['class' => 'form-group row']])->textarea(['rows' => 12]); ?>

                        <?= $form->field($model, 'description', ['options' => ['class' => 'form-group row']])->widget(TinyMce::className(), [
                            'options' => ['rows' => 12],
                            'language' => Yii::$app->language,
                            'clientOptions' => [
                                'selector' => '#textarea-description',
                                'plugins' => 'emoticons',
                                'menubar' => false,
                                'toolbar' => 'emoticons | bold italic strikethrough',
                                'branding' => false,
                                'init_instance_callback' => new \yii\web\JsExpression($tinyMCEInitFunction)
                            ]
                        ]); ?>

                        <?= $form->field($model, 'product_categories',
                            ['options' => ['class' => 'form-group row']])->widget(Select2::className(),
                            [
                                'data' => Category::getCategoriesTree($base_id),
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Choose a category'),
                                    'multiple' => true
                                ],
                            ])
                        ?>
                        <?= $form->field($model,
                            'image', ['options' => ['class' => 'image-up form-group row image-uploader']])->widget(\app\redefined\InputFileEl::className(),
                            [
                                'buttonOptions' => ['class' => 'btn btn-info'],
                                'buttonName' => Yii::t('app', 'Attach image'),
                                'startPath' => 'product',
                                'controller' => 'elfinder',
                                'filter' => ['image/png', 'image/jpeg', 'image/gif'],
                                'template' => '<br><div class="hidden">{input}</div><span class="btn-group">{button}<button type="button" class="btn btn-outline-danger clear-image"><i class="la la-times"></i></button></span>',
                                'options' => ['class' => 'form-control'],
                                'multiple' => false
                            ])->label((Yii::t('app', 'Image') . '<br><img class="img-up" src="' . $model->image . '" width="100px">')); ?>
                        <div class="col-6 offset-3">
                            <p><?= Yii::t('app', '<b>Attention!</b> You should not upload 2 files with the same name into one folder. The name should be written with Latin letters. We strongly recommend you to create different folders for different categories. It is better not to move files from one folder to another.'); ?></p>
                        </div>
                        <?= $form->field($model, 'price',
                            ['options' => ['class' => 'form-group row']])
                            ->textInput(); ?>
                        <?php if (Product::isQuantityMechanicNeed($base_id)) { ?>
                        <?= $form->field($model, 'quantity',
                            ['options' => ['class' => 'form-group row']])
                            ->textInput(); ?>
                        <?php } ?>
                        <?= $form->field($model, 'unit_id',
                            ['options' => ['class' => 'form-group row']])
                            ->dropDownList($model::getUnitsList(Yii::$app->language)); ?>
                        <?= $form->field($model, 'unit_lang')->hiddenInput(['value' => Yii::$app->language])->label(false); ?>
                        <?= $form->field($model, 'discount',
                            ['options' => ['class' => 'form-group row']])
                            ->textInput(); ?>
                        <?= $form->field($model, 'discount_type',
                            ['options' => ['class' => 'form-group row']])
                            ->dropDownList(Product::getDiscountTypes()); ?>
                        <?= $form->field($model, 'sort',
                            ['options' => ['class' => 'form-group row']])
                            ->textInput(['type' => 'number'])->label(Yii::t('app',
                                'Sort Order')); ?>
                        <?= $form->field($model, 'status',
                            ['options' => ['class' => 'form-group row']])
                            ->dropDownList(Helper::getStatuses()); ?>

                        <?php if (IntegrationSetting::getSettingIsActive($base_id, 'sales_drive_status')) { ?>
                            <?= $form->field($model, 'sales_drive_id',
                                ['options' => ['class' => 'form-group row']])
                                ->textInput(); ?>
                        <?php } ?>

                        <?= Html::submitButton('<i class="ft-save"></i> ' . Yii::t('app', 'Save'),
                            ['class' => 'btn btn-success full-width', 'name' => 'save']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
