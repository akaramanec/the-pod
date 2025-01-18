<?php

use backend\modules\shop\models\Attribute;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Attribute */
/* @var $form yii\widgets\ActiveForm */

$this->params['right_content'] = Buttons::create('Доавить значение', ['create-value', 'id' => $model->id]);
?>

<div class="row">
    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-12 mb-4">
        <div class="bg">
            <?= $form->field($model, 'name')->textInput() ?>
            <?= $form->field($model, 'slug')->textInput() ?>
            <?= $form->field($model, 'type')->radioList(Attribute::listTypeSave()) ?>
            <?= $form->field($model, 'sort')->textInput() ?>
            <?= $form->field($model, 'status')->dropDownList(Attribute::statuses()) ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>


    <div class="col-md-12">
        <div class="bg">
            <h4>Значения атрибутов</h4>
            <?= GridView::widget([
                'dataProvider' => new ActiveDataProvider(['query' => $model->getAttributeValue(), 'pagination' => false,]),
                'filterModel' => false,
                'showHeader' => false,
                'pager' => Common::pager4(),
                'tableOptions' => ['class' => 'table'],
                'rowOptions' => ['class' => 'ui-state-attribute-value'],
                'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive sort-attribute-value', 'id' => 'main-grid'],
                'layout' => "{items}",
                'columns' => [
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return $data->name;
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['width' => '80'],
                        'template' => '<div class="btn-group base-btn-group float-right" role="group">{update}</div>',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="far fa-edit"></i>', ['/shop/attribute/update-value', 'id' => $key],
                                    [
                                        'class' => 'btn btn-outline-dark',
                                        'title' => 'Изменить',
                                    ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>