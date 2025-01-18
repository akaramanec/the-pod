<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Setting */

/* @var $form yii\widgets\ActiveForm */

use backend\modules\system\models\SettingItem;
use src\helpers\Common;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-4">
        <div class="bg">
            <?= $form->field($model, 'name')->textInput() ?>
            <?php if (Yii::$app->user->can('dev')): ?>
                <?= $form->field($model, 'slug')->textInput() ?>
            <?php endif; ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right']) ?>
        </div>
    </div>
    <div class="col-md-8">
        <?php if (!$model->isNewRecord): ?>
            <div class="bg">
                <?= Html::button('<i class="fas fa-plus"></i> Добавить элемент настройки',
                    [
                        'class' => 'btn btn-outline-success btn-dashboard add_setting_item float-right',
                        'data-toggle' => 'modal',
                        'data-target' => '#add_setting_item',
                        'title' => 'Добавить элемент настройки',
                    ]); ?>
                <h4>Элементы настройки</h4>
            </div>
            <?= GridView::widget([
                'dataProvider' => new ActiveDataProvider([
                    'query' => $model->getSettingItem(),
                    'pagination' => false
                ]),
                'pager' => Common::pager4(),
                'tableOptions' => ['class' => 'table', 'style' => 'font-size: 14px;'],
                'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
                'layout' => "{items}",
                'columns' => [
                    'name',
                    'value',
                    'slug',
                    [
                        'attribute' => 'type',
                        'value' => function ($data) {
                            return SettingItem::listType()[$data->type];
                        },
                        'format' => 'raw'
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'contentOptions' => ['width' => '100'],
                        'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete}</div>',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::button('<i class="fas fa-pencil-alt"></i>',
                                    [
                                        'class' => 'btn btn-outline-dark edit_setting_item',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#edit_setting_item',
                                        'title' => 'Изменить',
                                        'data-slug' => $model->slug,
                                        'data-setting_id' => $model->setting_id,
                                    ]);
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-trash-alt"></i>', ['/system/setting/delete-item', 'slug' => $model->slug, 'setting_id' => $model->setting_id],
                                    [
                                        'title' => 'Удалить',
                                        'role' => 'button',
                                        'class' => 'btn btn-outline-dark',
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                        'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                                    ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        <?php endif; ?>

    </div>
</div>
<?php ActiveForm::end(); ?>
<div hidden>
    <div class="setting_id"><?= $model->id ?></div>
</div>
<?= \common\widgets\ModalWidget::widget(['id' => 'add_setting_item']); ?>
<?= \common\widgets\ModalWidget::widget(['id' => 'edit_setting_item']); ?>
