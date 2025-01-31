<?php

use backend\modules\media\widgets\FileInputWidget;
use backend\modules\shop\models\Category;
use backend\modules\shop\models\Product;
use kartik\select2\Select2;

use src\helpers\Common;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotCommand */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <?= $form->field($model, 'category_id')->widget(Select2::class, [
                    'data' => Category::asMap(),
                    'options' => ['placeholder' => Yii::t('app', 'Select category')],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            ?>
            <?= $form->field($model, 'name')->textInput() ?>
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            <?= $form->field($model, 'status')->dropDownList(Product::statuses()) ?>
            <?= $form->field($model, 'qty_total')->textInput() ?>
            <?= $form->field($model, 'price')->textInput() ?>
            <?= $form->field($model, 'code')->textInput() ?>


            <?= $form->field($model, 'multiImg[]')->widget(FileInputWidget::class, [
                'entity' => SHOP_PRODUCT,
                'mode' => 'multiple',
                'width' => '100'
            ])->label(Yii::t('app', 'Image')) ?>


            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?= $this->render('/product-mod/index', ['model' => $model]) ?>
