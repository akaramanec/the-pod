<?php
/**
 * @var $this \yii\web\View
 * @var $model \backend\modules\customer\models\Newsletter
 */

use backend\modules\customer\models\CustomerTag;
use backend\modules\media\models\File;
use backend\modules\media\models\Img;
use backend\modules\media\widgets\FileSaveWidget;
use backend\modules\media\widgets\ImgSaveWidget;
use kartik\datetime\DateTimePicker;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Json;
use yii\helpers\Url;

$this->params['right_content'] = '';
$this->params['right_content'] .= Html::button('<i class="fas fa-users"></i> Користувачів: <span id="qty-customer">' . $model->qtyCustomer . '</span>', [
    'title' => 'Количество пользователей которым будет отправлена рассылка',
    'class' => 'btn btn btn-outline-secondary',
    'disabled' => true
]);
?>
<?php $form = ActiveForm::begin([
        'id' => 'form_newsletter',
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => true,
        'validationUrl' => ['/validate/common', 'model' => get_class($model)],
    ]
) ?>
<div class="row">
    <div class="col-md-6">
        <div class="bg">
            <?= $form->field($model, 'text')->textarea([
                'rows' => 10,
                'id' => 'textarea-message'
            ]); ?>
            <ul>
                <li><code>&lt;b&gt;&lt;/b&gt;</code> - оберните текст для того, что бы текст был <b>жирным</b></li>
                <li><code>&lt;i&gt;&lt;/i&gt;</code> - оберните текст для того, что бы текст был <i>курсивом</i>
                </li>
                <li>Ссылки: <code>&lt;a href="ССЫЛКА"&gt;текст&lt;/a&gt;</code></li>
            </ul>
            <div class="row mt-3">
                <div class="col-xl-7 file-width" >
                    <div class="main-file-block">
                        <?= $form->field($model, 'mainFile')->label(false)->widget(FileInput::class, [
                            'options' => [
                                'accept' => 'video/*'
                            ],
                            'pluginOptions' => [
                                'theme' => 'fas',
                                'showUpload' => false,
                                'showCaption' => false,
                                'showRemove' => false,
                                'showCancel' => false,
                                'browseLabel' => 'Загрузить видео',
                                'uploadUrl' => Url::to(['/media/file/save', 'id' => $model->id, 'model' => get_class($model)]),
                                'deleteUrl' => Url::to(['/media/file/delete', 'id' => $model->id, 'model' => get_class($model)]),
                                'uploadAsync' => true,
                                'previewFileType' => 'any',
                                'initialPreview' => [File::main(NEWSLETTER, $model->id, $model->video)],
                                'overwriteInitial' => true,
                                'browseOnZoneClick' => true,
                                'initialPreviewAsData' => true,
                                'initialPreviewFileType' => 'video',
                                'allowedFileExtensions' => ['mp4', 'mpeg'],
                                'initialPreviewConfig' => [
                                    ['filetype' => "video/mp4"],
                                    ['caption' => $model->video ?: '']
                                ],
                                'maxFileCount' => 1,
                                'browseClass' => 'btn btn-outline-primary btn-block',
//                                'browseLabel' => '',
                                'removeLabel' => '',
                                'removeIcon' => '<i class="fas fa-trash"></i> '
                            ],
                            'pluginEvents' => [
                                'filebatchselected' => 'function(event, files) {$(this).fileinput("upload");}'
                            ]
                        ]) ?>
                    </div>
                </div>
                <div class=" col-xl-5 ">
                    <div class="row container-buttom-height">
                        <div class="main-img-block">
                            <?= Img::main(NEWSLETTER, $model->id, $model->img, '400x400') ?>
                            <?php if ($model->img): ?>
                                <?= Img::deleteMainImg($model->id, NEWSLETTER) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row ">
                        <div>
                            <?= ImgSaveWidget::mainImg() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg mb-4">
            <h3 class="mb-3">Применить фильтр</h3>
            <?= $form->field($model, 'sendTelegram')->checkbox(['id' => 'sendTelegram']) ?>
            <?= $form->field($model, 'sendViber')->checkbox(['id' => 'sendViber']) ?>
            <?= $form->field($model, 'customerBlogger')->checkbox(['id' => 'customerBlogger']) ?>
            <?= $form->field($model, 'notCustomerBlogger')->checkbox(['id' => 'notCustomerBlogger']) ?>
            <?= $form->field($model, 'activeCustomer')->checkbox(['id' => 'activeCustomer']) ?>
            <?= $form->field($model, 'subscribedCustomer')->checkbox(['id' => 'subscribedCustomer']) ?>
            <?= $form->field($model, 'sendEmail')->checkbox(['id' => 'sendEmail']) ?>
        </div>
        <div class="bg mb-4">
            <?= $form->field($model, 'tagsId')->widget(Select2::class, ['data' => CustomerTag::listIdName(),
                'options' => ['id' => 'tagsId',
                    'multiple' => true],
                'pluginOptions' => ['allowClear' => false],]); ?>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg">
            <?= $form->field($model, 'sendNow')->checkbox(['id' => 'sendNow']) ?>
            <?= $form->field($model, 'date_departure')->widget(DateTimePicker::class, ['pluginOptions' => ['autoclose' => true]]); ?>
            <button type="submit" class="btn btn-primary btn-block btn-base">Сохранить и отправить</button>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?= ImgSaveWidget::widget(['modal_id' => 'save_main_img',
    'model' => $model,
    'multiple' => false,]); ?>
<div hidden>
    <div id="newsletter_id"><?= $model->id ?></div>
</div>
