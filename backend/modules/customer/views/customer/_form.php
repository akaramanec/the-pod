<?php

use backend\modules\customer\models\Customer;
use backend\modules\customer\models\CustomerTag;
use backend\modules\media\models\GetImg;
use blog\models\CustomerBlog;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\Customer */
/* @var $form yii\widgets\ActiveForm */
/* @var $customerBlog CustomerBlog */
$this->registerJs(
    "
    $('#regular_customer').on('click', function() { $('#black_list').prop('checked', false); });
    $('#black_list').on('click', function() { $('#regular_customer').prop('checked', false); });
    ",
    View::POS_READY,
    'my-button-handler'
);
?>

<?php $form = ActiveForm::begin(); ?>
<div class="row content-admin">
    <div class="col-md-12">
        <div class="bg">
            <?= $form->field($model, 'first_name')->textInput() ?>
            <?= $form->field($model, 'last_name')->textInput() ?>
            <?= $form->field($model, 'username')->textInput(['disabled' => true]) ?>
            <?= $form->field($model, 'discount')->textInput() ?>
            <?= $form->field($model, 'phone')->textInput() ?>
            <?= $form->field($model, 'email')->textInput() ?>
            <?= $form->field($model, 'tags')->widget(Select2::class, [
                'data' => CustomerTag::listIdName(),
                'options' => [
                    'id' => 'tags',
                    'multiple' => true,
                    'placeholder' => ' '
                ],
                'pluginOptions' => ['allowClear' => false],
            ]); ?>
            <?= $form->field($model, 'black_list')->checkbox(['id' => 'black_list']) ?>
            <?= $form->field($model, 'regular_customer')->checkbox(['id' => 'regular_customer']) ?>
            <?= $form->field($model, 'blogger')->dropDownList(Customer::statusesBloggerAll()) ?>
            <?php if ($model->blogger == Customer::BLOGGER_TRUE): ?>
                <?= $form->field($customerBlog, 'percent_level_1')->textInput() ?>
                <?= $form->field($customerBlog, 'percent_level_2')->textInput() ?>
                <?= $form->field($customerBlog, 'username')->textInput() ?>
                <?= $form->field($customerBlog, 'pass')->textInput() ?>
                <?= $form->field($customerBlog, 'customer_id')->hiddenInput(['value' => $model->id])->label(false) ?>
                <div class="row">
                    <div class="col-md-6">
                        <p>Реферальная ссылка Telegram</p>
                        <strong><?= $model->linkRefTm() ?></strong>
                    </div>
                    <div class="col-md-6">
                        <p>Реферальная ссылка Viber</p>
                        <strong><?= $model->linkRefVb() ?></strong>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row mt-3">
                <div class="col-md-6">
                    <?= GetImg::bannerBot($model->bot->platform) ?>
                </div>
                <div class="col-md-6">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right ml-2']) ?>
                    <?php if ($model->blogger == Customer::BLOGGER_TRUE): ?>
                        <?= Html::submitButton('Сохранить и отправить блогеру', [
                            'class' => 'btn btn-primary btn-base float-right',
                            'name' => 'blogger',
                            'value' => 'send'
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


