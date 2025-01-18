<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/** @var boolean $multiple */
/** @var string $modal_id */

?>
<div class="modal fade" id="<?= $modal_id; ?>" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                        'action' =>
                            [
                                '/media/img/img-save',
                                'id' => $model->getPrimaryKey(),
                                'model' => get_class($model)
                            ],
                        'options' => ['enctype' => 'multipart/form-data'],
                        'enableAjaxValidation' => true,
                        'validationUrl' => ['/media/img/validate', 'model' => get_class($model)],
                    ]
                ) ?>
                <?php if ($multiple): ?>
                    <?= $form->field($model, 'multiImg[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
                <?php else: ?>
                    <?= $form->field($model, 'mainImg')->fileInput()->label(false) ?>
                <?php endif; ?>

                <?= Html::button('Загрузить',
                    [
                        'type' => 'submit',
                        'class' => 'btn btn-primary float-right',
                        'title' => 'Зберегти'
                    ]) ?>

                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

