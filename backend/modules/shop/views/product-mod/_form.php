<?php

use backend\modules\media\widgets\FileInputWidget;
use backend\modules\shop\models\AttributeValue;
use backend\modules\shop\models\Category;
use backend\modules\shop\models\Product;
use \backend\modules\shop\models\Attribute;
use backend\modules\shop\models\ProductMod;
use kartik\select2\Select2;

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\ProductMod */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="row">
    <div class="col-md-12">
        <div class="bg">
            <?= $form->field($model, 'product')->staticControl(['value' => $model->product->name]) ?>
            <?= $form->field($model, 'name')->textInput() ?>
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            <?= $form->field($model, 'status')->dropDownList(ProductMod::statuses()) ?>

            <?php
            // Fetch all attributes
            $attributes = Attribute::find()->all();
            $attributeValueArr = [];
            /** @var Attribute $attribute */
            foreach ($attributes as $attribute) {
                $attributeValue = $model->getAttributeValue($attribute->id)->one();
                if ($attributeValue) {
                    $attributeValue = $attributeValue->id;
                }
                echo $form->field($model, 'attributeValues[' . $attribute->id . ']')->widget(Select2::class, [
                    'data' => AttributeValue::asMapByAttribute($attribute),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Select ' . $attribute->name),
                        'value' => $attributeValue,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label($attribute->name);
            }
            ?>

            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-base float-right mt-3']) ?>
            <?= Html::a(Yii::t('app', 'Back'), ['product/update', 'id' => $model->product_id], ['class' => 'btn btn-secondary btn-base float-right mt-3 mr-2']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>